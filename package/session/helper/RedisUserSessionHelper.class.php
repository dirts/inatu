<?php

/**
 * 用于存储单个用户某个 app_id 的所有 session_id
 *
 * 数据类型 list
 *
 * 存储结构
 *      KEY UserSession:$user_id:$app_id
 *      VALUES
 *      session_id_1
 *      session_id_2
 *      ...
 */

namespace Inauth\Package\Session\Helper;

class RedisUserSessionHelper extends \Libs\Redis\RedisNutProxy {
    static $prefix = 'UserSession';

    public static function getUserSession($user_id, $app_id, $start = 0, $stop = -1) {
        if (empty($user_id) || !is_numeric($app_id)) {
            return FALSE;
        }
        $key = $user_id . ":" . $app_id;
        return parent::lRange($key, $start, $stop);
    }

    public static function getUserSessionCount($user_id, $app_id) {
        if (empty($user_id) || !is_numeric($app_id)) {
            return FALSE;
        }
        $key = $user_id . ":" . $app_id;
        return parent::lLen($key);
    }

    public static function addUserSession($user_id, $app_id, $session_id) {
        if (empty($user_id) || !is_numeric($app_id) || empty($session_id)) {
            return FALSE;
        }
        $key = $user_id . ":" . $app_id;
        return parent::lPush($key, $session_id);
    }

    // TODO 这里可以使用multi操作保证一次登录产生的session都保存成功或失败
    public static function addMultiUserSession($user_id, $app_id, $session_ids) {
        if (empty($user_id) || !is_numeric($app_id) || empty($session_ids) || !is_array($session_ids)) {
            return FALSE;
        }
        $key = $user_id . ":" . $app_id;
        foreach ($session_ids as $sid) {
            $ret = parent::lPush($key, $sid);
        }
        return $ret;
    }

    public static function removeUserSession($user_id, $app_id, $session_id) {
        if (empty($user_id) || !is_numeric($app_id) || empty($session_id)) {
            return FALSE;
        }
        $key = $user_id . ":" . $app_id;
        return parent::lRem($key, 0, $session_id);
    }

    // TODO 这里可以使用multi操作保证退出登录的session都删除成功或失败
    public static function removeMultiUserSession($user_id, $app_id, $session_ids) {
        if (empty($user_id) || !is_numeric($app_id) || empty($session_ids) || !is_array($session_ids)) {
            return FALSE;
        }
        $key = $user_id . ":" . $app_id;
        foreach ($session_ids as $sid) {
            $ret = parent::lRem($key, 0, $session_id);
        }
        return $ret;
    }

    public static function getNeedTrimSessionIds($user_id, $app_id, $maxlen) {
        if (empty($user_id) || !is_numeric($app_id)) {
            return FALSE;
        }
        if ($maxlen < 1) {
            return FALSE;
        }
        $key = $user_id . ":" . $app_id;
        return parent::lRange($key, $maxlen, -1);
    }

    public static function trimUserSession($user_id, $app_id, $length) {
        if (empty($user_id) || !is_numeric($app_id)) {
            return FALSE;
        }
        $length = (int)$length;
        if ($length < 1) {
            return FALSE;
        }
        $key = $user_id . ":" . $app_id;
        return self::lTrim($key, 0, $length - 1);
    }

    public static function delUserSession($user_id, $app_id) {
        if (empty($user_id) || !is_numeric($app_id)) {
            return FALSE;
        }
        $key = $user_id . ":" . $app_id;
        return parent::del($key);
    }

    public static function expireUserSession($user_id, $app_id, $expire) {
        if (empty($user_id) || !is_numeric($app_id) || empty($expire)) {
            return FALSE;
        }
        $key = $user_id . ":" . $app_id;
        $expire = (int)$expire;
        return parent::expire($key, $expire);
    }
}
