<?php
namespace Inauth\Package\Util;

class Utilities {
    //写日志
    public static function Log($mark, $str) {
        $logWriter = new \Inauth\Libs\Util\Logger();
        $log = new \Libs\Log\Log($logWriter);
        $log->log($mark, $str);
    }
}
