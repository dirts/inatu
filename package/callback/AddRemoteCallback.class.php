<?php
namespace Inauth\Package\callback;
/**
 * 更新用户cookie并记录用户登录ipBy:Kafka
 * Created by PhpStorm.
 * User: xiaolongrong
 * Date: 15/11/4
 * Time: 下午4:10
 */
use \Inauth\Package\User\User;
use \Inauth\Package\Util\Utilities as U;
use \Inauth\Package\User\Helper\DBDugongHelper;
use \Inauth\Package\User\Helper\DBDolphinStatHelper;

class AddRemoteCallback
{
    private static $user_profile_table = 't_dolphin_user_profile';
    private static $user_login_ip_table = 't_dolphin_user_login_ip';

    //更新用户最后登录时间和登录次数
    public static function update_user_login_info_time($user_id) {
        $res = User::update_login_times($user_id);
        if (!$res) {
            U::log('inauth.comment_login_detail',"update_user_login_info_time fail,user_id=".$user_id);
        }
        return $res;
    }

    //批量更新用户最后登录时间和登录次数
    public static function update_user_login_info_cookie ($data) {
        if (empty($data) || !is_array($data)) {
            return FALSE;
        }
        $sql = "UPDATE " .self::$user_profile_table. " SET login_times = login_times + 1, last_logindate = now(), cookie = :cookie where user_id = ".$data['user_id'];
        $res = DBDugongHelper::getConn()->write($sql, array('cookie'=>$data['cookie']));
        if (!$res) {
            U::log('inauth.comment_login_detail', "update_user_login_info_cookie fail,sql=".$sql);
            U::log('inauth.comment_login_detail', print_r($data,true));
        }
        return $res;
    }

    //批量更新t_dolphin_user_login_ip 当出现user_id,ip的重复项,更新times和login_time
    public static function update_user_login_ip ($data) {
        if (empty($data) || !is_array($data)) {
            return FALSE;
        }
        $userIdArr = array_keys($data);
        if (empty($userIdArr)) {
            return FALSE;
        }
        $sql = "INSERT INTO " .self::$user_login_ip_table. " (user_id, ip, times, login_time) VALUES ";
        foreach($data as $userId => $ip) {
            $ip = ip2long($ip);
            $sql .= " ({$userId}, {$ip}, 1, now()), ";
        }
        $sql = trim($sql);
        !empty($sql) && $sql = rtrim($sql, ',');
        $sql .= " ON DUPLICATE KEY UPDATE times = times + 1, login_time = now()";
        $res = DBDolphinStatHelper::getConn()->write($sql, array());
        if (!$res) {
            U::log('inauth.comment_login_detail', "update_user_login_ip fail,sql=".$sql);
            U::log('inauth.comment_login_detail', print_r($data,true));
        }
        return TRUE;
    }
}