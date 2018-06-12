<?php
namespace Inauth\Package\Risk;

use \Inauth\Libs\Util\Util;
use Libs\Cache\Memcache as Memcache;
use Libs\Cache\HMemcache as HMemcache;
use \Inauth\Package\Util\Utilities as U;

/**
 * 用户信息-模块
 */
class Risk {

    /*
     * 验证风控
     */
    public static function risk($rule_id, $param) {
        $token = array(); 
        if (self::in_wlist($rule_id, $param)) {
            return self::report($rule_id, $token, $param);
        }

        //检查密码风控
        $pwd = self::pwd_err_count($rule_id, $param);       

        if (!empty($pwd['pwd_err_min_risk'])) {
            $token[] = "pwd:min_unrisk"; 
        }

        if (!empty($pwd['pwd_err_max_risk'])) {
            $token[] = "pwd:max_unrisk"; 
        }

        if (!empty($pwd['pwd_err_ip_risk'])) {
            $token[] = "pwd:ip_unrisk"; 
        }

        //检查密码风控
        $city  = self::city_risk($rule_id, $param);
        if (!empty($city['not_comm_city'])) {
            $token[] = "city:unrisk"; 
        }

        U::log('inauth.risk', print_r(array($rule_id, $param, $pwd, $city), true));
        return self::report($rule_id, $token, $param);
        
    }

    /*检查用户是不是在白名单中*/
    public static function in_wlist($rule_id, $param) {

        if (isset($param['uid']) && !empty($param['uid'])) {
            $uid = $param['uid'];
            
            $i = in_array($uid, array(326452327,61144463));
            return $i;
            $a = HMemcache::instance()->get('risk:white:list:userId:'.$uid); 
            return $a;
        }
        return false;
    }

    
    public static function unrisk($rule_id, $param = array()) {
        if (empty($param['santorini_mm'])) return false;
        
        $key    = "Risk::$rule_id::{$param['santorini_mm']}";
        $mc     = Memcache::instance();
        $report = $mc->get($key);


        $return = array();
        if (empty($report['risks'])) return false;

        if (!empty($param['risks'])) {
            foreach ($param['risks'] as $risk) {
                $unrisk = __NAMESPACE__."\\" .ucfirst(str_replace(':', '::', $risk));
                $return[$risk] = call_user_func_array($unrisk, array($rule_id, $report['args']));
            }
        } else {
            foreach ($report['risks'] as $risk) {
                $unrisk = __NAMESPACE__."\\" .ucfirst(str_replace(':', '::', $risk));
                $return[$risk] = call_user_func_array($unrisk, array($rule_id, $report['args']));
            }
        }
        
        U::log('inauth.unrisk', print_r(array($rule_id, $param, $report, $return), true));
 
        return $return;
    }

    public static function reset($rule_id, $user_id = 0, $ip = 0) {
        $param = array();
        if ($user_id) {
            $param['uid'] = $user_id;
        }
        
        $a = Pwd::max_unrisk($rule_id, $param); 

        $mc     = Memcache::instance();
        $key    = "Risk::$rule_id::pwd_err_ip_session::$user_id";
        $x      = (int)$mc->delete($key);

        if ($ip) {
            $key    = "Risk::$rule_id::abvoad_session::$ip::$uid";
            $c      = (int)$mc->delete($key);
        } else {
            $c = 0;
        }
        return array('max' => $a, 'ip' => $x, 'city' => $c);  
    }

    /*
     *  验证风控结果
     */ 
    public static function report($rule_id = 0, $risks, $param) {
        
        $data  = array(
            'rule_id'       => (int)$rule_id,
            //'uid'           => $param['uid'],
            'risks'         => $risks,
            'rule_punish'   => 0,
        );

        if (
            in_array('pwd:min_unrisk', $risks)
        ) {
            $data['rule_punish'] = 1;
        }

        if (
            in_array('pwd:max_unrisk', $risks)
            || in_array('pwd:ip_unrisk', $risks)
            || in_array('city:unrisk', $risks)
        ) {
            $data['rule_punish'] = 2;
        }

        if (!empty($data['rule_punish']) && !empty($param['santorini_mm'])) {
            $key    = "Risk::$rule_id::{$param['santorini_mm']}";
            $mc     = Memcache::instance();
            $cache['risks'] = $risks;
            $cache['args'] = $param;
            $mc->set($key, $cache, 1800);
        }

        return $data;
    }

    /* 验证常用城市 */
    public static function city_risk($id, $param) {
        $city['not_comm_city']   = 0;
        $city['abvoad_session']  = 0;


        if (empty($param['uid'])) {
            return $city;
        }

        //异地登陆24小时临时票
        $city['abvoad_session'] = City::abvoad_session_exists($id, $param);
        if (!empty($city['abvoad_session'])) {
            return $city;
        }

        $is_comm_city = IpAddress::isCommonCity($param['ip'], $param['uid']); 
        if ($is_comm_city) {
            $city['not_comm_city'] = 0;
        } else {
            $city['not_comm_city'] = 1;
        }
        
        $city['not_comm_city'] = !$is_comm_city;
        return $city;
    }

    /* 验证密码错误次数 */
    public static function pwd_err_count($rule_id, $param) {
        return Pwd::risk($rule_id, $param); 
    }

}
