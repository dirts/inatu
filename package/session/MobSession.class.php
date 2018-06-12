<?php
/***************************************************************************
 *
 * Copyright (c) 2015 Meilishuo.com, Inc. All Rights Reserved
 *
 **************************************************************************/



/**
 * @file package/session/MobSession.class.php
 * @author 李守岩(shouyanli@meilishuo.com)
 * @date 2015/12/09
 * @brief  passport sdk session
 *
 **/

namespace Inauth\Package\Session;

use \Libs\Util\Utilities;
use \Inauth\Package\Session\Helper\RedisSession;
use \Inauth\Package\Session\Helper\DBPassportHelper;

/**
 * mob session
 */
class MobSession {

    //获取mob session
    static public function get_session($access_token) {

        $redis   = RedisSession::get_token_data($access_token);
        if (!empty($redis)) {
            return $redis;
        }

        $table  = DBPassportHelper::get_table_sharp_name($access_token);
        $res    = DBPassportHelper::getConn()->table($table)->where(array('token' => $access_token))->query('*', false, 'token');

        if (empty($res[$access_token])) {
            return false;
        }
        
        RedisSession::set_token_data($access_token, $res[$access_token]); 

        return $res[$access_token];

    }

}
