<?php

namespace Inauth\Package\Sync;
use \Inauth\Package\Session\Session;

class RedisSync
{
    static $queue = array('name'=>"redis", 'durable'=>false);

    static function sync($data)
    {
        Sync::sync(self::$queue['name'], $data);
    }

    static function sync_session($op, $a, $b, $c=0, $d=0)
    {
        self::sync(array($op, $a, $b, $c, $d));
    }

    public static function consume($prefix) {
        self::$queue['name'] = $prefix.".".self::$queue['name'];
        Sync::consume(self::$queue, "\\Inauth\\Package\\Sync\\RedisSync::handle");
    }

    static function handle()
    {
        $args = (func_get_args());

        \Inauth\Package\Util\Utilities::Log('redis_sync_consume.log', $args[0]);
//        file_put_contents("/tmp/redis_sync_logs", $args[0]."\n", FILE_APPEND);

        $sync = json_decode($args[0], true);

        if(!empty($sync)){
            $op = $sync[0];

            if($op == 'delete_user_ticket')
            {
                $user_id = $sync[1];
                $app_id = $sync[2];
                $res = Session::delete_user_ticket($user_id, $app_id);
            }
            elseif($op == 'delete_ticket') {
                $session_id = $sync[1];
                $app_id = $sync[2];
                $res = Session::delete_ticket($session_id, $app_id);
            }
            elseif($op == 'add_need_sync'){
                $app_id = $sync[1];
                $user_id = $sync[2];
                $ticket_id = $sync[3];
                $session = json_decode($sync[4], true);
                Session::add_need_sync($app_id, $user_id, $ticket_id, $session);
            }
            elseif($op == 'bind_ticket'){
                $ticket_id = $sync[1];
                $user_id = $sync[2];
                Session::bind_ticket($ticket_id, $user_id);
            }
            elseif($op == 'delay'){
                $session_id = $sync[1];
                $expire = $sync[2];
                Session::delay($session_id, $expire);
            }
        }
    }
}
