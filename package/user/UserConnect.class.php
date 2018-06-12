<?php
namespace Inauth\Package\User;

use \Libs\Util\Utilities;
use \Inauth\Package\User\Helper\DBDugongHelper;

/**
 * 用户信息-模块
 */
class UserConnect {

    static $table = 't_dolphin_user_profile_connect';
    
    static public function get_uid_by_auth($mobile) {
        $data = DBDugongHelper::getConn()->table(self::$table)->where(array('mobile' => $mobile))->query('*', $master = true, 'mobile');
        if (!empty($data)) {
            return (int)$data[$mobile]['user_id'];
        } else {
            return false;
        }
    }
    
    /* 注册用户信息 */
    static public function bind($uid, $auth) {
        $param  = array('user_id' => $uid, 'auth'=> $auth);
        $data   = DBDugongHelper::getConn()->table(self::table)->replace_into($param);
        return $data;
    }
        
    
}
