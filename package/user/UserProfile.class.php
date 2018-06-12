<?php
/***************************************************************************
 *
 * Copyright (c) 2015 Meilishuo.com, Inc. All Rights Reserved
 *
 **************************************************************************/



/**
 * @file    UserProfile.class.php
 * @author  李守岩(shouyanli@meilishuo.com)
 * @date    2015/12/30
 * @brief   用户信息
 *
 **/

namespace Inauth\Package\User;

use \Inauth\Package\User\Dao\DUserProfile;
use \Inauth\Libs\ErrorCodes;
use \Inauth\Libs\Constant;
use \Inauth\Libs\Util\Util;

class UserProfile {

    public static function getUserProfile($uid = 0, $email = '', 
                $nickname ='', $master = 0, $hash_key = 'user_id' ) {
        $_daoUserProfile = new DUserProfile();
        $fields = array('*');
        $conds = array();
        if ($uid !== 0) {
            $conds['user_id'] = $uid;
        }
        if (!empty($email)) {
            $conds['email'] = $email;
        }
        if (!empty($nickname)) {
            $conds['nickname'] = $nickname;
        }
        if (empty($conds)) {
            return ErrorCodes::getErrorResult(ErrorCodes::PARAM_ERROR,
                'uid email nickname cannot all be empty');
        }
        $result = $_daoUserProfile->getByConds($fields,$conds,null,$master);
 
        return array('error_code' => 0, 'message' => '', 'data' => $result[0]);
    }

    /***
     * 修改用户密码
     *
     * @author          lishouyan@meishuo.com
     * @param int       $user_id     用户id
     * @param string    $emial      邮箱
     * @return data
     *                    array 成功时返回用户信息
                                失败的时候为 null
     **/
    public static function resetPassword($uid = 0, $email = '', $nickname = '', 
                $old_pwd, $new_pwd, $pwd_must = true) {
        
        $conds = array();
        if ($uid !== 0) {
            $conds['user_id'] = $uid;
        }
        if (!empty($email)) {
            $conds['email'] = $email;
        }
        if (!empty($nickname)) {
            $conds['nickname'] = $nickname;
        }
        if (empty($conds)) {
            return ErrorCodes::getErrorResult(ErrorCodes::PARAM_ERROR,
                'uid email nickname cannot all be empty');
        }
        
        if (empty($new_pwd)) {
            return ErrorCodes::getErrorResult(ErrorCodes::PARAM_ERROR,
                'new password cannot be empty');
        }

        $result = self::getUserProfile($uid, $email, $nickname); 
        if (Constant::RET_SUCCESS !== $result['error_code']) {
            return $result;
        }

        if (empty($result['data'])) {
            return ErrorCodes::getErrorResult(ErrorCodes::USER_NOT_EXISTS);
        }

        $info = $result['data'];

        if ($pwd_must && $info['password'] !== $old_pwd) {
            if ( $info['password'] != 'd41d8cd98f00b204e9800998ecf8427e' ) {
                return ErrorCodes::getErrorResult(ErrorCodes::PASSWORD_ERROR);
            }
        }

        $fields = array(
            'password' => $new_pwd,
            'cookie'   => Util::getUniqueId()
        );

        $_daoUserProfile = new DUserProfile();
        $res = $_daoUserProfile->updateByConds($fields, $conds);
        unset($info['password']);
        return array('error_code' => 0, 'message' => '', 'data' => $info);
    }


}

/* vim: set expandtab ts=4 sw=4 sts=4 tw=100: */
