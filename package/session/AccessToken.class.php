<?php
namespace Inauth\Package\Session;


use \Inauth\Package\Session\Helper\DBSwanHelper;
use \Inauth\Package\Session\Helper\DBSwanOldHelper;
use \Inauth\Package\Session\Helper\DBPassportHelper;
use \Inauth\Package\Util\Utilities as U;

/**
 * 老的access_token类
 */
class AccessToken {

    static public function get_token_data($token) {
        $param = array('token' => $token);
        $res = DBSwanHelper::getConn()->table('t_swan_oauth_access_token_new')->where($param)->query('*');

        if (!empty($res)) {
            return $res;                
        }
        
        $res = DBSwanHelper::getConn()->table('t_swan_oauth_access_token')->where($param)->query('*');
        if (!empty($res)) {
            return $res;                
        }
        
        $res = DBSwanOldHelper::getConn()->table('t_swan_oauth_access_token')->where($param)->query('*');
        if (!empty($res)) {
            return $res;                
        }
        
        U::log('passport.kafka.callback', print_r(array($token), true));

        return null;
    }
    
        
}
