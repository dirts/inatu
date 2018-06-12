<?php
/**
 * Created by PhpStorm.
 * User: changlinli
 * Date: 14-10-21
 * Time: 上午11:26
 */

namespace Snake\Package\Sync;
Use \Snake\libs\Cache\Memcache;
Use \Snake\Package\Sync\Helper\DBDolphinHelper as DBDolphinHelper;
Use \Snake\Package\Sync\Helper\DBCouponHelper as DBCouponHelper;
class MysqlSync
{
    static $queue = array('name'=>"mysql",
        'durable'=>false);

    static function sync($data)
    {
        Sync::sync(self::$queue['name'], $data);
    }

    static function dolphin()
    {
        self::sync(array('op'=>'dolphin', 'data'=>func_get_args()));
    }
    static function coupon()
    {
        self::sync(array('op'=>'coupon', 'data'=>func_get_args()));
    }

    static function delete()
    {
        self::sync(array('op'=>'delete', 'data'=>func_get_args()));
    }

    static function consume($prefix)
    {
        self::$queue['name'] = $prefix.".".self::$queue['name'];
        Sync::consume(self::$queue, "\\Snake\\Package\\Sync\\MysqlSync::handle");
    }

    static function handle()
    {
        $args = (func_get_args());

        $sync = json_decode($args[0], true);
        $data = ($sync['data']);

        $logHelper = new \Snake\Libs\Base\SnakeLog('sync_queue_consume_'.self::$queue['name'], 'normal');
        $logHelper->w_log(print_r($sync, TRUE));

        if($sync["op"] == 'dolphin')
        {
            $result = DBDolphinHelper::getConn()->write($data[0], $data[1]);
        }
        elseif($sync["op"] == 'coupon')
        {
            try
            {
                $result = DBCouponHelper::getConn()->write($data[0], $data[1]);
            }
            catch(Exception $e)
            {
                file_put_contents("/tmp/mysql_logs", $args[0]."\n", FILE_APPEND);
                $logHelper = new
                \Snake\Libs\Base\SnakeLog('sync_queue_consume_error_'.self::$queue['name'], 'normal');
                $logHelper->w_log(print_r(array('sync'=>$sync, 'e'=>$e), TRUE));
            }
            if(empty($result))
            {
                file_put_contents("/tmp/mysql_logs", $args[0]."\n", FILE_APPEND);
                $logHelper = new
                \Snake\Libs\Base\SnakeLog('sync_queue_consume_error_'.self::$queue['name'], 'normal');
                $logHelper->w_log(print_r(array('sync'=>$sync, 'e'=>$e), TRUE));
            }
        }
        elseif($sync["op"] == 'delete')
        {
            $mem = \Snake\libs\Cache\Memcache::instance();
            $mem->delete($data[0]);
        }

    }
}
