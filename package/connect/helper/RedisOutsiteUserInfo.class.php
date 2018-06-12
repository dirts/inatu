<?php
/***************************************************************************
 *
 * Copyright (c) 2015 Meilishuo.com, Inc. All Rights Reserved
 *
 **************************************************************************/



/**
 * @file   /package/connect/RedisOutsiteUserInfo.class.php
 * @author xiaolongrong(xiaolongrong@meilishuo.com)
 * @date   2015/12/22
 * @brief  外站用户信息redis工具类
 *
 **/

namespace Inauth\Package\Connect\Helper;
use \Inauth\Libs\ErrorCodes;

class RedisOutsiteUserInfo extends \Libs\Redis\RedisProxy {
    //Redis前缀
    protected static $prefix = '';

    //外站用户信息redis_key前缀
    protected static $outsite_prefix = array(
        'renren'                   => 'renren:info',
        'weibo'                    => 'weibo:info',
        'qzone'                    => 'qzone:info',
        'baidu'                    => 'baidu:info',
        'taobao'                   => 'taobao:info',
        'txweibo'                  => 'txweibo:info',
        'douban'                   => 'douban:info',
        'weixin'                   => 'weixin:info',
        'fanli'                    => 'fanli:info',
    );
    //外站用户信息redis_key过期时间
    protected static $outsite_ttl = array(
        'renren'                   => 2592000,
        'weibo'                    => 2592000,
        'qzone'                    => 2592000,
        'baidu'                    => 2592000,
        'taobao'                   => 2592000,
        'txweibo'                  => 2592000,
        'douban'                   => 2592000,
        'weixin'                   => 2592000,
        'fanli'                    => 2592000,
    );

    /***
     * 存储外站用户信息
     *
     * @param  string      $channel     互联渠道
     *         int         $user_id     用户id
     *         array       $outsite_user_info
     * @return boolean
     *                     FALSE        参数错误或操作失败
     *                     TRUE         操作成功
     **/
    public static function setOutsiteUserInfo($channel, $user_id, $outsite_user_info = array()) {
        if (empty($user_id) || empty($outsite_user_info) || !isset(self::$outsite_prefix[$channel])) {
            return ErrorCodes::getErrorResult(ErrorCodes::PARAM_ERROR);
        }

        self::setConfigFile('main_site_redis');    //主站redis配置
        self::$prefix = self::$outsite_prefix[$channel];

        $redis_key = self::$outsite_prefix[$channel] . $user_id;
        $res = self::setex($redis_key, self::$outsite_ttl[$channel], urlencode(base64_encode(serialize($outsite_user_info))));
        return array('error_code' => 0, 'message' => '', 'data' => $res);
    }

    /***
     * 批量获取外站用户信息:目前仅支持$channel为weixin,fanli
     *
     * @param  string      $channel     互联渠道
     *         array       $uids        用户id数组
     * @return data
     *                     FALSE        参数错误
     *                     array        成功时返回外站用户信息
     **/
    public static function getOutsiteUserInfo($channel, $uids) {
        if (empty($uids) || !is_array($uids) || !isset(self::$outsite_prefix[$channel])) {
            return ErrorCodes::getErrorResult(ErrorCodes::PARAM_ERROR);
        }

        if ( $channel == 'weixin' ) {
            self::setConfigFile('main_site_redis');    //主站redis配置
            self::$prefix = self::$outsite_prefix[$channel]. ":";
            $redis_key_prefix = self::$outsite_prefix[$channel]. ":";
        } else {
            self::setConfigFile('main_site_redis');
            self::$prefix = self::$outsite_prefix[$channel];
            $redis_key_prefix = self::$outsite_prefix[$channel];
        }

        $values = array();
        foreach ($uids as $val) {
            $values[$val] = unserialize(base64_decode(self::get($redis_key_prefix.$val)));
            if ( $values[$val] == FALSE ) {
                $values[$val] = array();
            }
        }
        return array('error_code' => 0, 'message' => '', 'data' => $values);
    }
}
