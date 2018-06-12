<?php
namespace Inauth\Package\User;

use \Libs\Util\Utilities;
use \Inauth\Package\User\Helper\DBDugongHelper;
use \Libs\Cache\Memcache as Memcache;
use \Inauth\Libs\ErrorCodes;
use \Inauth\Package\User\Dao\DUserMobile;

/**
 * 用户信息-模块
 */
class UserMobile extends User{

    static $table = 't_dolphin_user_mobile';

    static public function get_uid_by_mobile($mobile) {
        $data = DBDugongHelper::getConn()->table('t_dolphin_user_mobile')->where(array('mobile' => $mobile))->query('*', $master = true, 'mobile');
        if (!empty($data)) {
            return (int)$data[$mobile]['user_id'];
        } else {
            return false;
        }
    }
    
    /* 注册用户信息 */
    static public function bind($uid, $mobile) {
        $param = array('user_id' => $uid, 'mobile'=> $mobile);
        $data = DBDugongHelper::getConn()->table('t_dolphin_user_mobile')->replace_into($param);
        return $data;
    }

    /* 注册用户信息 */
    static public function find_mobile($uid) {
        $param = array('user_id' => $uid);
        $data = DBDugongHelper::getConn()->table('t_dolphin_user_mobile')->where($param)->query('*', $master = true, 'user_id');
        if (!empty($data[$uid])) {
            return $data[$uid]['mobile'];
        }
        return 0;
    }

    static public function get_mobile_by_uid($uid) {
        $mc = Memcache::instance(); 
        $mobile = $mc->get("mc:mobile:uid:$uid");
        if (!empty($mobile)) {
            return $mobile;
        }

        $param = array('user_id' => $uid);
        $data = DBDugongHelper::getConn()->table('t_dolphin_user_mobile')->where($param)->query('*', $master = false, 'user_id');
        if (!empty($data[$uid])) {
            $mc->set("mc:mobile:uid:$uid", $data[$uid]['mobile'], 300);
            return $data[$uid]['mobile'];
        }
        return 0;
        
    }

    /**
     * 解除帐号与手机号码的绑定
     *
     * @author xiaolongrong@meilishuo.com
     *
     * @param  int   $user_id  用户id
     *
     * @return data  boolean   成功时返回帐号绑定的手机号码的信息
     * 因流式调用的删除db记录没有调试稳定，暂时使用DUserMobile类进行DB操作
     **/
    public static function unbindMobile($user_id){
        if ( empty($user_id) ) {
            return ErrorCodes::getErrorResult(ErrorCodes::PARAM_ERROR);
        }
        $mobile = self::find_mobile($user_id);
        if ( empty($mobile) ) {
            return ErrorCodes::getErrorResult(ErrorCodes::NOT_FIND);
        }
        $dao_user_mobile = new DUserMobile();
        $conds = array('user_id' => $user_id);
        $res = $dao_user_mobile->delByConds($conds);
        if( !$res ) {
            return ErrorCodes::getErrorResult(ErrorCodes::DB_ERROR);
        } else {
            return array('error_code' => 0, 'message' => 'success', 'data' => $mobile);
        }
    }
}
