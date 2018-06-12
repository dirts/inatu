<?php
/***************************************************************************
 *
 * Copyright (c) 2015 Meilishuo.com, Inc. All Rights Reserved
 *
 **************************************************************************/



/**
 * @file package/user/UserLoginIp.class.php
 * @author 李守岩(shouyanli@meilishuo.com)
 * @date 2015/12/11
 * @brief  passport sdk session
 *
 **/

namespace Inauth\Package\User;

use \Libs\Util\Utilities;
use \Inauth\Package\User\Helper\DBDolphinStatHelper;
use \Inauth\Package\User\Helper\DBDolphinHelper;
use Libs\Cache\Memcache as Memcache;

/**
 * 用户信息扩展-模块
 */
class UserLoginIp extends User {

    static $table = 't_dolphin_user_login_ip';

    /* 从db 获取登 */
    static public function get_last_login_ip($user_id) {
        
        $redis_key = "Mc:last_login_ip:{$user_id}";
        $mc        = Memcache::instance();
        $mc_data = $mc->get($redis_key);

        if (!empty($mc_data)) {
            return $mc_data; 
        }
        $data   = DBDolphinStatHelper::getConn()->table(self::$table)->where(array('user_id' => $user_id))->query('*');
        $mc->set($redis_key, $data, 300);
        return $data;
    }
    
    /* 从db 获取登 */
    static public function get_last_login_ips($user_ids) {
        foreach ($user_ids as $user_id) {
            $ipinfo = self::get_last_login_ip($user_id);
            if (empty($ipinfo)) continue;
            $city   = self::get_city_by_ip($ipinfo[0]['ip']); 
            $res[$user_id] = $city;
        }
        return $res;
    }

    static public function get_city_by_ip($ip) {
        
        $redis_key = "Mc:city_ip:{$ip}";
        $mc        = Memcache::instance();
        $mc_data = $mc->get($redis_key);

        if (!empty($mc_data)) {
            return $mc_data; 
        }

        $sql = "select province,city,district,country from t_dolphin_ip_list_new where :ip between ip_begin_int and ip_end_int order by ip_end_int-ip_begin_int limit 1";
        $data = DBDolphinHelper::getConn()->read($sql, array("ip" => $ip)); 
        
        $mc->set($redis_key, $data, 3600);
        return $data;
    }

}
