<?php
namespace Inauth\Package\Session\Helper;

use \Inauth\Libs\Db\Table;

class DBSwanHelper extends Table {
    const _DATABASE_ = 'swan';
    
    //删掉session
    static function del_session ($token) {
        self::getConn()->table('t_swan_oauth_access_token')->where(array('token' => $token))->remove();    
        return $a = self::getConn()->table('t_swan_oauth_access_token_new')->where(array('token' => $token))->remove();    
    }
}
