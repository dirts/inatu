<?php
namespace Inauth\Package\Risk;

use \Inauth\Libs\Util\Util;
use Libs\Cache\Memcache as Memcache;

/**
 * PWD RISK
 */
class City {

    static $user_lock = 0;
    static $ip_h_lock = 0;
    static $ip_d_lock = 0;
    
    /* 验证密码错误次数 */
    public static function risk($rule_id, $param) {
        $uid    = $param['uid'];
        $ip     = $param['ip'];
        
        $status = array(
        );


        $status['pwd_err_count']    = $pwd_err_count;
        $status['pwd_err_ip_h_count']  = $pwd_h_err_count;
        $status['pwd_err_ip_d_count']  = $pwd_d_err_count;

        return $status;
    
    }
    
    public static function unrisk($rule_id, $param) {
        if (empty($param['uid']) || empty($param['ip'])) {
            return 0;
        }

        $uid = $param['uid'];
        $ip  = $param['ip'];
        $key    = "Risk::$rule_id::abvoad_session::$ip::$uid";
        $mc     = Memcache::instance();
        $count  = $mc->set($key, 1, 3600 * 24);
        return $count;
    }

    public static function abvoad_session_exists($rule_id, $param) {
        if (empty($param['uid']) || empty($param['ip'])) {
            return 0;
        }
        $uid = $param['uid'];
        $ip  = $param['ip'];
        $key    = "Risk::$rule_id::abvoad_session::$ip::$uid";
        $mc     = Memcache::instance();
        $count  = $mc->get($key);
        return (int)$count;
    }


}
