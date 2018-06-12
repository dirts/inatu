<?php
namespace Inauth\Package\Risk;

use \Inauth\Libs\Util\Util;
use Libs\Cache\Memcache as Memcache;

/**
 * 用户信息-模块
 */

/**
 * 频率限制
 */
class Limit {

    static  $single     = null;
    static  $is_open    = 1; //是否开启限流，1，开启， 0全部关闭
    private $apiname    = '';
    static  $api_config = array(
            '101' => array(3600 * 24, 3),
            '103' => array(3600 * 24, 200),
            '104' => array(3600 * 24, 10),
        );

    public function __construct () {}


    /* 检查阈值 */
    public function is_reach($uid, $cid) {
                
    }

}
