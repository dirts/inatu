<?php
/**
 * Created by PhpStorm.
 * User: carrotli
 * Date: 14-10-23
 * Time: 下午12:16
 */

namespace Snake\Package\Sync;


class Dlb extends \Snake\Libs\Redis\Redis {

    static $prefix = "DlbSync";
    static $room;
    private static $quota_prefix = "quota_prefix";
    private static $queue_size = "queue_size";
    private static $quotas = array();
    private static $keys = array();
    private static $quotas_checked = array();
    private static $nextquotas = array();
    private static $max_size = 1000;//队列长度
    private static $min_size = 15;//队列长度

    private static $incr_interval = 1;
    private static $desc_interval = -15;

    public static function init($room, $keys)
    {//初始化队列
        self::$keys = array_unique($keys);
        self::$room = $room;
        if(empty($room))
        {
            return false;
        }

        foreach(self::$keys as $val)
        {
            $opts[] = array('hget', $val);
        }
        self::$quotas = self::multi(self::$quota_prefix.self::$room, $opts);

        $i = 0;
        foreach(self::$keys as $val)
        {
            if(self::$quotas[$i++] < self::$min_size)
            {
                self::$quotas_checked[$val] = self::$min_size;
            }
            else
            {
                self::$quotas_checked[$val] = $val;
            }
        }
        return true;
    }

    public static function get_a_key()
    {
        $sum = array_sum(self::$quotas_checked);
        $rand_quota = rand(0, $sum-1);
        $smaller = 0;
        foreach(self::$quotas_checked as $key=>$val)
        {
            $smaller += $val;
            if($rand_quota < $smaller)
            {
                return $key;
            }
        }
        return array_rand(self::$keys);
    }
    public static function allot_quota($key, $yes)
    {//重新分配配额
        if($yes)
        {
            if(self::$quotas_checked[$key] < self::$max_size)
            {
                self::hincrby(self::$quota_prefix.self::$room, $key, self::$incr_interval);
            }
        }
        else
        {
            if(self::$quotas_checked[$key] > self::$min_size)
            {
                self::hincrby(self::$quota_prefix.self::$room, $key, self::$desc_interval);
            }
        }
    }

} 