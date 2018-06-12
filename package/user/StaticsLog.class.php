<?php
/***************************************************************************
 *
 * Copyright (c) 2015 Meilishuo.com, Inc. All Rights Reserved
 *
 **************************************************************************/



/**
 * @file    StaticsLog.class.php
 * @author  李守岩(shouyanli@meilishuo.com)
 * @date    2016/02/24
 * @brief   \inauth\package\user\StaticsLog
 *
 **/
 
namespace Inauth\Package\User;
 
use \Inauth\Package\Util\Utilities;
 
class StaticsLog {

    const SHOWLOG_LOGIN = 'Statistics_login';
    const SHOWLOG_REG   = 'Statistics_register'; 
    
    public static function StatForlogin ($log_t) {
        $fields = array(
            'plat', 'is_success', 'login_type', 'open_type', 'login_way', 'path',
            'user_id', 'session_id', 'login_err', 'frm', 'version', 'ip', 'refer','flash_id', 
        );

        $log = self::get_log_str($log_t, $fields);

        Utilities::log(self::SHOWLOG_LOGIN, $log);
        
    }

    public static function get_log_str($log_t, $fields) {
        $log = '';
        foreach ($fields as $field) {
            $log  .= "[" . ( isset($log_t[$field]) ? $log_t[$field] : "") ."]\t";
        }
        return $log;
    }
}
 
/* vim: set expandtab ts=4 sw=4 sts=4 tw=100: */

