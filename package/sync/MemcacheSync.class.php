<?php
/**
 * Created by PhpStorm.
 * User: changlinli
 * Date: 14-10-21
 * Time: 上午11:26
 */

namespace Snake\Package\Sync;
Use \Snake\libs\Cache\Memcache;

class MemcacheSync
{
    static $queue = array('name'=>"memcache",
        'durable'=>false);

    static function sync($data)
    {
        Sync::sync(self::$queue['name'], $data);
    }

    static function set()
    {
        self::sync(array('op'=>'set', 'data'=>func_get_args()));
    }
    static function delete()
    {
        self::sync(array('op'=>'delete', 'data'=>func_get_args()));
    }

    static function consume($prefix)
    {
        self::$queue['name'] = $prefix.".".self::$queue['name'];
        Sync::consume(self::$queue, "\\Snake\\Package\\Sync\\MemcacheSync::handle");
    }

    static function handle()
    {
        $args = (func_get_args());

        $sync = json_decode($args[0], true);
        $data = ($sync['data']);

        $logHelper = new \Snake\Libs\Base\SnakeLog('sync_queue_consume_'.self::$queue['name'], 'normal');
        $logHelper->w_log(print_r($sync, TRUE));

        if($sync["op"] == 'set')
        {
            $mem = \Snake\libs\Cache\Memcache::instance();
            if(count($data) === 2)
            {
                $mem->set($data[0], $data[1]);
            }
            elseif(count($data) == 3)
            {
                $mem->set($data[0], $data[1], $data[2]);
            }
            elseif(count($data) == 4)
            {
                $mem->set($data[0], $data[1], $data[2], $data[3]);
            }
        }
        elseif($sync["op"] == 'delete')
        {
            $mem = \Snake\libs\Cache\Memcache::instance();
            $mem->delete($data[0]);
        }

    }
}
