<?php
/**
 * 注册时和device_id 关联
 * Created by PhpStorm.
 * User: xiaolongrong
 * Date: 15/11/4
 * Time: 下午6:30
 */

namespace Inauth\Modules\Callback;
use \Inauth\Package\User\Helper\DBAccessTokenHelper;
use \Inauth\Package\Util\Utilities as U;

class User_login_device_id extends \Frame\Module {
    private static $swan_device_id_table = 't_swan_device_id';

    public function run() {
        $data = $this->request->request('data', array());
        if (empty($data)) {
            return $this->request->error('40001', '参数错误');
        }
        $this->process($data);
        return true;
    }

    public function process($data) {
        if(empty($data['user_id']) || empty($data['device_id'])) {
            return TRUE;
        }
        $sql = "update " .self::$swan_device_id_table. " set user_id = :user_id where device_id=:device_id";
        $res = DBAccessTokenHelper::getConn()->write($sql, $data);
        if (!$res) {
            U::log('inauth.user_login_device_id', "update user device_id fail,sql=".$sql);
            U::log('inauth.user_login_device_id', print_r($data,true));
        }
        return $res;
    }
}
