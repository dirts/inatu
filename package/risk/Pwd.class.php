<?php
namespace Inauth\Package\Risk;

use \Inauth\Libs\Util\Util;
use Libs\Cache\Memcache as Memcache;
use \Inauth\Package\Util\Utilities as U;

/**
 * PWD RISK
 */
class Pwd {

    static $user_lock = 0;
    static $ip_h_lock = 0;
    static $ip_d_lock = 0;
    
    /* 验证密码错误次数 */
    public static function risk($rule_id, $param) {
        if (!empty($param['uid'])) {
            $uid    = $param['uid'];
        } else {
            $uid = 0;
        }
        $ip     = $param['ip'];
        
        $status = array(
            //用户密码风控状态
            'pwd_err_count'         => 0,
            'pwd_err_min_risk'      => 0,
            'pwd_err_max_risk'      => 0,

            //ip密码错误一小时统计状态
            'pwd_err_ip_h_count'    => 0,

            //ip密码错误一天统计状态
            'pwd_err_ip_d_count'    => 0,
            'pwd_err_ip_risk'     => 0,
        );

        $pwd_err_count = Pwd::pwd_err_count($rule_id, $uid, $param['pwd_err']); 
        $status['pwd_err_count']    = $pwd_err_count;

        if ( !empty($param['pwd_err']) && $pwd_err_count > 3 && $pwd_err_count <= 10) {
            $is_pwd_err_tmp_unrisk = Pwd::pwd_err_count_minrisk($rule_id, $uid); 
            $status['pwd_err_min_risk'] = $is_pwd_err_tmp_unrisk;
        } else if ($pwd_err_count > 10) {
            $pwd_err_lock = Pwd::pwd_err_count_risk($rule_id, $uid); 
            $status['pwd_err_max_risk'] = $pwd_err_lock;
        }

        //ip错误频次验证
        //if (!empty($param['pwd_err']) && !Pwd::ip_session_exists($rule_id, $param)) {
        if (!empty($param['pwd_err'])) {
            $pwd_h_err_count = Pwd::pwd_err_ip_h_count($rule_id, $ip, $param['pwd_err']); 
            $status['pwd_err_ip_h_count']  = $pwd_h_err_count;

            $pwd_d_err_count = Pwd::pwd_err_ip_d_count($rule_id, $ip, $param['pwd_err']);
            $status['pwd_err_ip_d_count']  = $pwd_d_err_count;
            
            if ($pwd_d_err_count > 400 || $pwd_h_err_count > 200) {
                $status['pwd_err_ip_risk']    = Pwd::ip_risk($rule_id, $ip, $uid);
            }
        }

        return $status;
    
    }
    

    public static function max_unrisk($rule_id, $param) {
        if (empty($param['uid'])) {
            return 0;
        }

        $uid = $param['uid'];
        $mc     = Memcache::instance();
        
        //干掉密码计数
        $key    = "Risk::$rule_id::pwd_err_count::$uid";
        $count  = $mc->delete($key);

        //解除密码10次风控
        $key    = "Risk::$rule_id::pwd_err_count_risk::$uid";
        $res  = $mc->delete($key);

        //清除密码3次的解风控
        $key    = "Risk::$rule_id::pwd_err_tmp_unrisk::$uid";
        $count  = $mc->delete($key);
        
        return (int)$res;
    }
    
    public static function min_unrisk($rule_id, $param) {
        if (empty($param['uid'])) {
            return 1;
        }

        $uid = $param['uid'];
        $key    = "Risk::$rule_id::pwd_err_count_minrisk::$uid";
        $mc     = Memcache::instance();
        $count  = $mc->delete($key);
       
        return 1; 
        return (int)$count;
    }


    //id密码错误增加计数
    //=============================================================
    public static function pwd_err_count($rule_id, $uid, $pwd_err) {
        $key    = "Risk::$rule_id::pwd_err_count::$uid";
        $mc     = Memcache::instance();
        $count  = (int)$mc->get($key);
        U::log('inauth.risk', print_r(array($key, $count), true));
        if ($pwd_err) {
            $mc->set($key, ++$count, 3600 * 24);
        }
        
        return $count;
    }

    //清密码错误计数
    public static function pwd_err_uncount($rule_id, $uid) {
        $key    = "Risk::$rule_id::pwd_err_count::$uid";
        $mc     = Memcache::instance();
        $count  = $mc->delete($key);
        return $count;
    }

    //师傅已经姐密码三次风控
    public static function pwd_err_tmp_unrisk_exists($rule_id, $uid) {
        $key    = "Risk::$rule_id::pwd_err_tmp_unrisk::$uid";
        $mc     = Memcache::instance();
        $count  = (int)!$mc->get($key);
        return $count;
    }
    
    public static function pwd_err_count_risk($rule_id, $uid) {
        $key    = "Risk::$rule_id::pwd_err_count_risk::$uid";
        $mc     = Memcache::instance();
        $count  = $mc->set($key, 1,  1800);
        self::$user_lock = 1;
        return 1;
    }

   //ip冻结12小时 
    public static function ip_risk($rule_id, $ip, $uid) {
        $key    = "Risk::$rule_id::ip_risk::$ip:$uid";
        $mc     = Memcache::instance();
        $count  = $mc->set($key, 1,  3600 * 12);
        return 1;
    }
    
    public static function pwd_err_count_minrisk($rule_id, $uid) {
        $key    = "Risk::$rule_id::pwd_err_count_minrisk::$uid";
        $mc     = Memcache::instance();
        $count  = $mc->set($key, 1,  1800);
        self::$user_lock = 1;
        return 1;
    }
    
    public static function is_pwd_err_count_risk($rule_id, $uid) {
        $key    = "Risk::$rule_id::pwd_err_count_risk::$uid";
        $mc     = Memcache::instance();
        $count  = $mc->get($key);
        return $count;
    }


    //1小时ip密码错误计数
    //===============================================================
    public static function pwd_err_ip_h_count($rule_id, $ip, $pwd_err) {
        $key    = "Risk::$rule_id::pwd_err_ip_h_count::$ip";
        $mc     = Memcache::instance();
        $count  = (int)$mc->get($key);
        if ($pwd_err) {
            $mc->set($key, ++$count, 3600);
        }
        return $count; 
    } 
    
    //1小时ip密码错误计数 冻结12h
    public static function pwd_err_ip_h_risk($rule_id, $uid) {
        $key    = "Risk::$rule_id::pwd_err_ip_h_risk::$uid";
        $mc     = Memcache::instance();
        $count = $mc->set($key, 1, 3600 * 24);
        return (int)$count;
    }
   
    //24小时（1天) 密码错误计数na 
    //==============================================================
    public static function pwd_err_ip_d_count($rule_id, $ip) {
        $key    = "Risk::$rule_id::pwd_err_ip_d_count::$ip";
        $mc     = Memcache::instance();
        $count  = (int)$mc->get($key);
        $mc->set($key, ++$count, 3600 * 24);
        return $count; 
    } 

    //1小时ip密码错误计数 冻结12h
    public static function pwd_err_ip_d_risk($rule_id, $uid) {
        $key    = "Risk::$rule_id::pwd_err_ip_d_risk::$uid";
        $mc     = Memcache::instance();
        $count  = $mc->set($key, 1,  3600 * 12);
        return (int)$count;
    }

    public static function ip_unrisk($rule_id, $param) {
        if (empty($param['uid'])) {
            return 0;
        }

        $uid = $param['uid'];
        $key    = "Risk::$rule_id::pwd_err_ip_session::$uid";
        $mc     = Memcache::instance();
        $count  = $mc->set($key, 1,  3600 * 12);
        if (!empty($param['ip']))  {       
            $ip = $param['ip'];
            $key    = "Risk::$rule_id::pwd_err_ip_d_count::$ip";
            $mc->delete($key);
            $key    = "Risk::$rule_id::pwd_err_ip_h_count::$ip";
            $mc->delete($key);
        }

        return (int)$count;
    }
    
    public static function ip_session_exists($rule_id, $param) {
        if (empty($param['uid'])) {
            return 0;
        }
        $uid = $param['uid'];
        $key    = "Risk::$rule_id::pwd_err_ip_session::$uid";
        $mc     = Memcache::instance();
        $count  = $mc->get($key);
        return (int)$count;
    }

}
