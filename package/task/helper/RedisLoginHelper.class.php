<?php

namespace Inauth\Package\Task\Helper;

class RedisLoginHelper extends \Libs\Redis\RedisProxy {
    
    static $prefix = 'Task:login:queue';

    static function pop($key) {
        $return = array();
        $len = self::llen($key);
        if ($len) {
            if ($len > 1000) $len = 1000;
            for($i = 0; $i < $len; $i++) {
                $data =json_decode(self::rpop($key), true);
                if (!empty($data)) {
                    $return[] = $data;
                }
            }
        }
        return $return;
    }
}
