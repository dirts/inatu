<?php
namespace Inauth\Package\Risk;

use \Inauth\Libs\Util\Util;
use \Inauth\Libs\ErrorCodes;
use \Libs\Cache\Memcache as Memcache;
use \Inauth\Package\User\Helper\DBDugongHelper;
use \Inauth\Package\User\Helper\DBDolphinHelper;
use \Inauth\Package\Risk\Helper\DBRiskStatHelper;

/**
 * 用户信息-模块
 */
class IpAddress {
    private static $Cities_table = "t_risk_stat_user_common_login_cities";
    /*
     * 根据user_id获取常用登录城市
     */
    static public function getCommonLoginCities($user_id) {

        $key = "common_login_cities:".$user_id;

        $cities = Memcache::instance()->get($key);
        if(empty($cities)) {
            $sqlData['user_id'] = $user_id;

            $cities = array();
            $sql = "select * from ".self::$Cities_table." where user_id=:user_id";
            $result = DBRiskStatHelper::getConn()->read($sql, $sqlData);
            $result && $login_cities = json_decode($result[0]['login_cities'], TRUE);
            !empty($login_cities) && $cities = Util::DataToArray($login_cities, 'city');

            if(!empty($cities)) {
                Memcache::instance()->set($key, $cities, 3600);
            }
        }

        return $cities;
    }

    /*
    * 根据ip获取所在城市
    */
    public static function getAddress($ipStr) {
        if (!is_numeric($ipStr)) {
            $ipStr = ip2long($ipStr);
        }
        $sql = "select province,city,district,country from t_dolphin_ip_list_new where :ip between ip_begin_int and ip_end_int order by ip_end_int-ip_begin_int limit 1";
        $ipObject = DBDolphinHelper::getConn()->read($sql, array("ip" => $ipStr));
        if (empty($ipObject)) {
            return array();
        }
        return array('province' => $ipObject[0]['province'], 'city' => $ipObject[0]['city'], 'district' => $ipObject[0]['district'], 'country' => $ipObject[0]['country']);
    }

    /***
     * 判断当前ip是否在常用登录地
     */
    public static function isCommonCity($ipStr, $user_id) {
        $address = self::getAddress($ipStr);
        if (!$address) {
            return FALSE;
        }
        $city = $address['city'];

        $common_cities = self::getCommonLoginCities($user_id);
        if ($common_cities && (!in_array($city, $common_cities))) {
            return FALSE;
        }
        return TRUE;
    }

    /***
     * 判断用户是否是异地登录
     */
    public static function judgeRemoteLogin($user_id, $client_ip) {
        $address = self::getAddress($client_ip);
        if ( empty($address) ) {
            return ErrorCodes::getErrorResult(ErrorCodes::NO_RECORD_IP);
        }
        $city = $address['city'];
        $common_cities = self::getCommonLoginCities($user_id);
        if ( $common_cities && (!in_array($city, $common_cities)) ) {
            if( $city == "其他" || empty($city) ) {
                $city = "异地";
            }
            return array('error_code' => 0, 'message' => '', 'data' => array('city' => $city));
        }
        return array('error_code' => 0, 'message' => '', 'data' => array());
    }

}

/* vim: set expandtab ts=4 sw=4 sts=4 tw=100: */
