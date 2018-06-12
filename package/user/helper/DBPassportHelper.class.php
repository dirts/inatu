<?php
/**
 * Created by PhpStorm.
 * User: MLS
 * Date: 15/11/5
 * Time: 上午11:40
 */

namespace Inauth\Package\User\Helper;
use \Inauth\Package\Util\Utilities as U;
use \Inauth\Libs\Db\Table;

class DBPassportHelper extends Table {
    const _DATABASE_ = 'passport';
    public static $passport_token_element = 't_passport_access_token_';

    /**
     * 允许更新的字段
     */
    private static $update_fields = array(
        'user_id' => 'int',
        'device_token' => 'string',
        'imei' => 'string',
        'client_id' => 'int',
        'version' => 'string',
        'udid' => 'string',
        'mac' => 'string',
        'push_num' => 'int',
    );

    public static function switchPassportTokenTable ($access_token) {
        $switchElement = substr($access_token,-1);
        return self::$passport_token_element.$switchElement;
    }

    //从passport库的token分表中查询数据
    public static function getTokenDataFromPassportDB ($access_token, $matser = FALSE) {
        $sql = "SELECT * FROM " . self::switchPassportTokenTable($access_token) . " WHERE token = :token AND (expiration = 0 || expiration >= :_timestamp)";
        $params = array(
            'token' => $access_token,
            '_timestamp' => $_SERVER['REQUEST_TIME'],
        );
        $result = self::getConn()->read($sql, $params, $matser);
        !empty($result) && $result = $result[0];
        return $result;
    }

    //插入新记录到passport库的token分表中
    public static function insertTokenDataIntoPassportDB ($access_token, $params = array()) {
        if (empty($params)) {
            return FALSE;
        }
        if(!empty($params['id'])) {
            unset($params['id']);
        }
        $rand = 150 + rand(1,30);
        $params['expiration'] = strtotime("+".$rand." day");
        $cols = '';
        $values = '';
        foreach ($params as $paramKey => $paramValue) {
            $cols .= "`{$paramKey}`,";
            $values .= ":{$paramKey},";
        }
        $cols = rtrim($cols, ',');
        $values = rtrim($values, ',');
        $insertSql = "INSERT INTO " . self::switchPassportTokenTable($access_token) . "({$cols}) VALUES({$values})";
        $res = self::getConn()->write($insertSql, $params);
        if (!$res) {
            U::log('inauth.insert_access_token_err', "insertTokenDataIntoPassportDB fail:".$access_token);
            U::log('inauth.insert_access_token_err', print_r($params,true));
        }
        return $res;
    }

    //更新记录到passport库的token分表中
    public static function updateTokenDataInPassportDB ($access_token, $params = array()) {
        if (empty($params) || !is_array($params)) {
            return FALSE;
        }
        $tokenData = self::getTokenDataFromPassportDB($access_token);
        if (empty($tokenData)) {
            return FALSE;
        }
        $params = array_intersect_key($params, self::$update_fields);
        $sqlComm = "UPDATE ". self::switchPassportTokenTable($access_token) ." SET";
        $sqlData = array();
        foreach ($params as $key => $value) {
            switch (self::$update_fields[$key]) {
                case 'int':
                    $sqlComm .= "`{$key}` = :_{$key},";
                    $sqlData["_{$key}"] = $value;
                    break;
                case 'string':
                    $sqlComm .= "`{$key}` = :{$key},";
                    $sqlData[$key] = $value;
                    break;
                default:
                    break;
            }
        }
        $sqlComm = rtrim($sqlComm, ",");
        $sqlComm .= " WHERE token = :token LIMIT 1";
        $sqlData['token'] = $access_token;
        $res = self::getConn()->write($sqlComm, $sqlData);
        if (!$res) {
            U::log('inauth.insert_access_token_err', "updateTokenDataInPassportDB fail:".$access_token);
            U::log('inauth.insert_access_token_err', print_r($sqlData,true));
        }
        return $res;
    }

    public static function deleteTokenDataInPassportDB ($access_token) {
        $tokenData = self::getTokenDataFromPassportDB($access_token);
        if (!empty($tokenData)) {
            $sqlComm = "DELETE FROM " . self::switchPassportTokenTable($access_token) . " WHERE token = :token";
            $sqlData = array('token' => $access_token);
            $delRes = self::getConn()->write($sqlComm, $sqlData);
            if (!$delRes) {
                U::log('inauth.insert_access_token_err', "deleteTokenDataInPassportDB fail:".$access_token);
            }
        }
    }
}