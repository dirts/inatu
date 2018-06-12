<?php
namespace Inauth\Package\Session;

use \Libs\Util\Utilities;
use \Inauth\Package\User\Helper\DBDugongHelper;
use \Inauth\Package\Session\Helper\DBOctopusHelper;
use \Inauth\Package\App\App;
use \Inauth\Package\Session\Helper\RedisSessionDataHelper as SessionData;
use \Inauth\Package\Session\Helper\RedisUserSessionHelper as UserSession;
use \Inauth\Package\Sync\RedisSync;
use \Inauth\Package\Util\Utilities as U;
use \Inauth\Libs\Util\Kafka;

/**
 * 票据管理类
 */
class Session {

    static $user_session_max =  50;
    static $key =  '93b7b794bf239800fd96d321a56c93d5';

    //创建票据
    static public function create_ticket($user_id, $app_id, $remember) {

        $timestamp = time();
        $app_configs = App::session_config($app_id, $user_id, $remember);

        $sessions = array();
        foreach ($app_configs as $key => $session) {
            $ticket_id  = self::create_ticket_id($user_id, time() + $session['expire']);
            $app_id = $session['app_id'];
            $domains = $session['domain'];
            unset ($session['domain']);

            $ret = self::add_need_sync($app_id, $user_id, $ticket_id, $session);
            if (!$ret) {
                return false;
            }

            foreach ($domains as $domain) {
                $sessions[] = array('key' => $key, 'value' => $ticket_id, 'domain' => $domain, 'expire' => $session['expire']);
            }
        }


        return $sessions;
    }


    //写入session 需要跨机房同步
    static function add_need_sync($app_id, $user_id, $ticket_id, $session) {
        $expire = $session['expire'];
        $session['expire'] = $session['login'] + $expire;

        $session_db = $session;
        
        //写入db
        $session_db['session_id']  = $ticket_id;
        $session_db['login']       = date('Y-m-d H:i:s', $session['login']);
        $session_db['expire']      = date('Y-m-d H:i:s', $session['expire']);
        //没有过期时间
        if (empty($expire)) {
            unset($session_db['expire']);
        }
        $db_res = DBOctopusHelper::getConn()->table('t_octopus_session_info')->insert($session_db);
        
        if (empty($db_res)) {
            U::log('inauth.login', "[error:db]\t[$app_id]\t[$user_id]\t[$ticket_id]");
            return false;    
        }

        $redis_res = SessionData::setSessionData($ticket_id, $expire, $session);
       
        if (empty($redis_res)) {
            U::log('inauth.login', "[error:redis]\t[$app_id]\t[$user_id]\t[$ticket_id]");
        }
       
        $msg = array('access_token' => $ticket_id, 'session'=> $session, 'expire' => $expire);
        $kafka_res = Kafka::push('higo_sync_access_token',  $msg,  $ticket_id);

        return $db_res;
    }

    /* 关联用户和票据 */
    static public function bind_ticket($ticket_id, $user_id, $app_id = 0) {
        //self::check_login($ticket_id, $app_id);
        $ret = DBOctopusHelper::getConn()->table('t_octopus_session_info')->where(array('session_id' => $ticket_id))->update(array('user_id'=> $user_id));

        $ret = SessionData::hset($ticket_id, 'user_id', $user_id);
        return $ret;
    }

    //根据session_id 清除session
    static public function rm_sessions($session_ids) {
        if (!empty($session_ids)) {
            foreach ($session_ids as $session_id) {
                SessionData::delSessionData($session_id);
            }
            $res = DBOctopusHelper::getConn()->table('t_octopus_session_info')->where(array('session_id' => $session_ids))->update(array('status' => 0));
        }
    }

    //清除过期的session
    static public function delete_timeout_ticket($user_id, $app_id, $session_list) {
        if (is_array($session_list)) {
            foreach ($session_list as $session_id) {
                SessionData::delSessionData($session_id);
                UserSession::removeUserSession($user_id, $app_id, $session_id);
            }
            $res = DBOctopusHelper::getConn()->table('t_octopus_session_info')->where(array('session_id' => $session_list))->update(array('status' => 0));
        }
    }

    static public function get_timeout_ticket($session_list) {
        $list = array();
        if (is_array($session_list)) {
            $now = time();
            foreach ($session_list as $session_id) {
                $session  = self::get_ticket($session_id);
                if (empty($session) || $session['expire'] < $now) {
                    $list[] = $session_id;
                }
            }
        }
        return $list;
    }

    //踢掉用户
    static public function kick ($user_id) {

        $app_ids = App::get_all_app_ids();

        $ss = array();
        foreach ($app_ids as $app_id) {
            $user_sessions = self::get_user_tickets($user_id, $app_id);
            $ss = array_merge($ss, $user_sessions);
            $res = UserSession::delUserSession($user_id, $app_id);
        }

        if (!empty($ss)) {
            foreach ($ss as $session_id) {
                SessionData::delSessionData($session_id);
            }
            $res = DBOctopusHelper::getConn()->table('t_octopus_session_info')->where(array('session_id' => $ss))->update(array('status' => 0));
        }

        return $res;
    }

    //删除票据
    static public function delete_user_ticket ($user_id, $app_id) {

        $app_configs = App::session_config($app_id, $user_id);

        $ss = array();
        foreach ($app_configs as $key => $session) {
            $app_id = $session['app_id'];
            $user_sessions = self::get_user_tickets($user_id, $app_id);
            $ss = array_merge($ss, $user_sessions);
            //$res = UserSession::delUserSession($user_id, $app_id);
        }

        if (!empty($ss)) {
            foreach ($ss as $session_id) {
                SessionData::delSessionData($session_id);
            }
            $res = DBOctopusHelper::getConn()->table('t_octopus_session_info')->where(array('session_id' => $ss))->update(array('status' => 0));
        }
        return $res;
    }

    //删除session
    static public function delete_ticket ($session_id, $app_id) {

        $session = self::get_ticket($session_id);
        $del = SessionData::delSessionData($session_id);
        if ($session_id) {
            $res = DBOctopusHelper::getConn()->table('t_octopus_session_info')->where(array('session_id' => $session_id))->update(array('status' => 0));
        }
        $res = UserSession::removeUserSession($session['user_id'], $app_id, $session_id);
        return $del;
    }

    //验证登陆函数
    static public function check_login($id, $app_id, $type = 'session') {
        //通过session id验证是否登陆
        if ($type == 'session') {

            $session_id = $id;
            $session = self::get_ticket($session_id);

            //直接返回user_id
            if (!empty($session) && isset($session['user_id']) && is_numeric($session['user_id'])) {
                return (int)$session['user_id'];
            }

            $session = DBOctopusHelper::getConn()->table('t_octopus_session_info')->where(array('session_id' => $id, 'status'=> 1 ))->query('*', false, 'session_id');
            if (!empty($session)) {

                $session = $session[$id];

        //判断永不过期
        if ($session['expire'] == '0000-00-00 00:00:00') {
                    $expire = 0;
            unset($session['id']);
                    unset($session['session_id']);
                    unset($session['status']);
                    $session['login'] = (int)strtotime($session['login']);
                    $session['expire'] = (int)strtotime($session['login']);
                    $data = SessionData::setSessionData($id, $expire, $session);
                    return (int)$session['user_id'];

        }
        if ( ($expire = strtotime($session['expire']) - time()) > 0 ) {
                    unset($session['id']);
                    unset($session['session_id']);
                    unset($session['status']);
                    $session['login'] = (int)strtotime($session['login']);
                    $session['expire'] = (int)strtotime($session['expire']);
                    $data = SessionData::setSessionData($id, $expire, $session);
                    return (int)$session['user_id'];
                }
            }
            return 0;

            if (!empty($session['app_id']) && $session['app_id'] == $app_id && !empty($session['user_id'])) {

                //return (int)$session['user_id'];
                $session_list = self::get_user_tickets($session['user_id'], $app_id);
                if (!empty($session_list) && in_array($session_id, $session_list)) {
                    return (int)$session['user_id'];
                }
            }
            return 0;

        } else {

            $user_id = $id;
            $session_list = self::get_user_tickets($user_id, $app_id);
            if (!empty($session_list)) {
                foreach ($session_list as $session_id) {
                    $session = self::get_ticket($session_id);
                    if (isset($session['app_id']) && $session['app_id'] == $app_id)  {
                        return (int)true;
                    }
                }
            }
            return (int)false;
        }
    }


    //查询票据
    static public function get_ticket($ticket_id) {
        $fields = array('user_id', 'app_id', 'login', 'expire');
        $data = SessionData::getSessionData($ticket_id);
        $hash = array_combine($fields, $data);
        if (is_null($hash['user_id'])) {
            return false;
        }
        return $hash;
    }

    //查询票据
    static public function get_user_tickets($user_id, $app_id) {
        $data = UserSession::getUserSession($user_id, $app_id);
        return $data;
    }

    //创建唯一自增session id
    public  static function create_ticket_id($user_id, $expire) {
        return $id = md5(uniqid(mt_rand(), TRUE) . $_SERVER['REQUEST_TIME'] . mt_rand());
        return self::encrypt("$id\t$user_id\t$expire", self::$key);
    }

    static public function encrypt($plain_text, $key) {
            $plain_text = trim($plain_text);
            $iv         = substr(md5($key), 0, mcrypt_get_iv_size (MCRYPT_CAST_256,MCRYPT_MODE_CFB));
            $c_t        = mcrypt_cfb (MCRYPT_CAST_256, $key, $plain_text, MCRYPT_ENCRYPT, $iv);
            $s = trim(chop(base64_encode($c_t)));
            return $s;
    }

    public static function decrypt($c_t, $key) {
        $c_t    = trim(chop(base64_decode($c_t)));
        $iv     = substr(md5($key), 0, mcrypt_get_iv_size (MCRYPT_CAST_256,MCRYPT_MODE_CFB));
        $p_t    = mcrypt_cfb (MCRYPT_CAST_256, $key, $c_t, MCRYPT_DECRYPT, $iv);
        return trim(chop($p_t));
    }

    static public function delay($session_id, $expire) {
        $ret = DBOctopusHelper::getConn()->table('t_octopus_session_info')->where(array('session_id' => $session_id))->update(array('expire' => date('Y-m-d H:i:s', time() + $expire)));
        return (int)SessionData::expireSessionData($session_id, $expire);
    }

}
