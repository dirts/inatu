<?php

/**
 * 用于存储session内容
 *
 * SessionData存储结构
 * 数据类型 hash
 *      KEY SessionData:$session_id
 *      FIELD       VALUE
 *      user_id     int     用户id
 *      app_id      int     应用id
 *      login       int     登录时间点(session生成时间)
 *      expire      int     过期时间点
 */

namespace Inauth\Package\Session\Helper;

class RedisSessionDataHelper extends \Libs\Redis\RedisNutProxy {
    static $prefix = 'SessionData';

    private static $useMulti = FALSE; // hMset时，有expire时使用multi或者hMset+expire
    private static $all_fields = array(
        'user_id'   => 0,
        'app_id'    => 0,
        'login'     => 0,
        'expire'    => 0,
    );

    public static function getSessionData($session_id, $fields = array()) {
        if (empty($session_id) || !is_array($fields)) {
            return FALSE;
        }

        $query_fields = array();
        if (empty($fields)) {
            $query_fields = array_keys(self::$all_fields);
        } else {
            foreach ($fields as $field) {
                isset(self::$all_fields[$field]) && $query_fields[] = $field;
            }
            if (empty($query_fields)) {
                return FALSE;
            }
        }

        return parent::hMget($session_id, $query_fields);
    }

    public static function getSessionSingleField($session_id, $field) {
        if (empty($session_id) || empty($field)) {
            return FALSE;
        }

        if (!isset(self::$all_fields[$field])) {
            return FALSE;
        }

        return parent::hget($session_id, $field);
    }

    public static function existsSession($session_id) {
        if (empty($session_id)) {
            return FALSE;
        }
        return parent::exists($session_id);
    }

    public static function setSessionData($session_id, $expire = 0, $values = array()) {
        if (empty($session_id) || !is_array($values)) {
            return FALSE;
        }

        $values = array_intersect_key($values, self::$all_fields);
        if (empty($values)) {
            return FALSE;
        }

        if (self::$useMulti) {
            $expire = (int)$expire;
            if (!empty($expire)) {
                $params = array();
                foreach ($values as $field => $val) {
                    $params[] = array('hset', $field, $val);
                }
                $params[] = array('expire', $expire);

                return parent::multi($session_id, $params);
            } else {
                return parent::hMset($session_id, $values);
            }
        } else {
            $ret = parent::hMset($session_id, $values);
            $expire = (int)$expire;
            if (!empty($expire)) {
                parent::expire($session_id, $expire);
            }
            return $ret;
        }
    }

    public static function setSessionSingleField($session_id, $field, $value) {
        if (empty($session_id) || empty($field) || empty($value)) {
            return FALSE;
        }

        if (!isset(self::$all_fields[$field])) {
            return FALSE;
        }

        return parent::hset($session_id, $field, $value);
    }

    public static function expireSessionData($session_id, $expire) {
        if (empty($session_id) || empty($expire)) {
            return FALSE;
        }

        $expire = (int)$expire;
        return parent::expire($session_id, $expire);
    }

    public static function delSessionData($session_id) {
        if (empty($session_id)) {
            return FALSE;
        }
        return parent::del($session_id);
    }
}
