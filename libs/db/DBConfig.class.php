<?php
/***************************************************************************
*
* Copyright (c) 2015 Meilishuo.com, Inc. All Rights Reserved
*
**************************************************************************/

/**
* @file   DBConfig.php
* @author CHEN Yijie(yijiechen@meilishuo.com)
* @date   2015/12/29
* @brief  DB读取配置类
*
**/

namespace Inauth\Libs\Db;

class DBConfig {

    private $configs;

    public static function instance() {
        static $cf = null;
        is_null($cf) && $cf = new DBConfig();
        return $cf;
    }

    private function __construct() {
        $this->configs = \Frame\ConfigFilter::instance()->getConfig('db');
    }

    public function loadConfig($database, $type) {
        switch ($type) {
            case 'MASTER':
            return new \ArrayIterator(array($this->configs[$database][$type]));
            default:
            return new \ArrayIterator(\Frame\Helper\Util::ssort($this->configs[$database][$type]));
        }
    }
}

/* vim: set expandtab ts=4 sw=4 sts=4 tw=100: */
