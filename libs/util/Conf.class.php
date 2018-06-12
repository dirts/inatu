<?php
/***************************************************************************
 *
 * Copyright (c) 2015 Meilishuo.com, Inc. All Rights Reserved
 *
 **************************************************************************/



/**
 * @file libs/util/Conf.class.php
 * @author 李守岩(shouyanli@meilishuo.com)
 * @date 2015/12/04
 * @brief  passport sdk session
 *
 **/


namespace Inauth\Libs\Util;

class Conf {

    const CFG_FILE_PATH = '/home/work/inauth/config/passport/xxx.cfg.php'; //xxx用来替换key
    private static $cfg = array();

    public static function get($base, $service, $nodes) {

    self::get_cfg_by_skey($base, $service);

    $nodes = explode('.', $nodes);
    $tmp = self::$cfg[$base][$service];
    foreach ($nodes as $node) {
            if(!isset($tmp[$node])) return array();
        $tmp = $tmp[$node];
    }
    return $tmp;
    }

    private static function get_cfg_by_skey($base, $service) {
        if (empty($service) || empty($base)) {
            return array();
        }

        if (!empty(self::$cfg[$base][$service])) {
            return self::$cfg[$base][$service];
        }

        $data = self::get_file($base, $service);

        self::$cfg[$base][$service] = $data;
        return self::$cfg[$base][$service];
    }

    private static function get_file($base, $service) {
        $file = str_replace('xxx', $service, self::CFG_FILE_PATH);

        if (!is_file($file)) {
            return array();
        }

        $config = include $file;
    if (is_array($config)) {
            return $config;
        }
        return array();
    }
}
