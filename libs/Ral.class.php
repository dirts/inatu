<?php
/***************************************************************************
 *
 * Copyright (c) 2015 Meilishuo.com, Inc. All Rights Reserved
 *
 **************************************************************************/



/**
 * @file   Ral.class.php
 * @author CHEN Yijie(yijiechen@meilishuo.com)
 * @date   2015/00/07
 * @brief  后端HTTP交互封装类 主动打印交互日志
 *
 **/

namespace Inauth\Libs;

use \Libs\Serviceclient\RemoteHeaderCreator;

class Ral {

    public static function call($service='', $module='', $interface='', $param=array(), $option=array()) {
        if (!$service || !$module || !$interface) {
            return false;
        }
        $ral = new \Libs\Serviceclient\Client();
        //\Libs\Serviceclient\RemoteHeaderCreator::setHeaders('Host', 'service.higo.meilishuo.com');
        //RemoteHeaderCreator::setHeaders('Appkey','hO3GO4atae');
        $back_interface = $module .'/' . $interface;
        $startTime = microtime(true);
        if (empty($option)) { $option = array('method'=>'POST','timeout' => 1); }
        $ral_result = $ral->call($service, $back_interface, $param, $option);
        $finisTime = microtime(true);
        $time_cost = round(($finisTime - $startTime), 4);
        $lastLogger = MLog::getLastLogger();
        MLog::setLogApp('ral');
        $info = array(
            'service'   => $service,
            'module'    => $module,
            'interface' => $interface,
            'backend_http_code' => $ral_result['httpcode'],
            'time_cost' => $time_cost,
        );
        if (intval($ral_result['content']['error_code']) !== 0
                || empty($ral_result['content']['data'])) {
                    MLog::warning($info['service'] . " " . $info['module'] . '/' . $info['interface']. " CALL FAILED result is " .
                    json_encode($ral_result['content']) .' input params is '. json_encode($param), $ral_result['content']['error_code'], $info);
            return false;
        } else {
            MLog::notice(' ',0,$info);
        }
        MLog::setLogApp($lastLogger);
        return $ral_result['content']['data'];

    }

    public static function curl($service='', $module='', $interface='', $param=array()) {
        if (!$service || !$module || !$interface) {
            return false;
        }
        $back_interface = $module .'/' . $interface;
        if (empty($service)) {
            return array();
        }
        $url = Constant::$remote[$service] . $back_interface;
        $startTime = microtime(true);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        if (!empty($param)) {
            $param = http_build_query($param);
        $back_interface = $module .'/' . $interface;
            curl_setopt($ch, CURLOPT_POSTFIELDS, $param);
        }
        curl_setopt($ch, CURLOPT_URL, $url);
        $response = curl_exec($ch);
        $curl_result = array();
        $curl_result['httpcode'] = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_result['content'] = json_decode($response, TRUE);
        curl_close($ch);
        $finisTime = microtime(true);
        $time_cost = round(($finisTime - $startTime), 4);
        if($curl_result['content']['error_code'] === NULL) {
            $curl_result['content']['error_code'] = $curl_result['content']['code'];
            unset($curl_result['content']['code']);
        }
        $lastLogger = MLog::getLastLogger();
        MLog::setLogApp('curl');
        $info = array(
                'service'   => $service,
                'module'    => $module,
                'interface' => $interface,
                'backend_http_code' => $curl_result['httpcode'],
                'time_cost' => $time_cost,
                );
        if (intval($curl_result['content']['error_code']) !== 0) {
                    MLog::warning($info['service'] . " " . $info['module'] . '/' . $info['interface']. " CALL FAILED result is " .
                    json_encode($curl_result['content']) .' input params is '. json_encode($param), $curl_result['content']['error_code'], $info);
            return false;
        } else {
            MLog::notice(' ',0,$info);
        }
        MLog::setLogApp($lastLogger);

        return $curl_result['content'];
    }
}

/* vim: set expandtab ts=4 sw=4 sts=4 tw=100: */
