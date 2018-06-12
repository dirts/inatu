<?php
/***************************************************************************
*
* Copyright (c) 2015 Meilishuo.com, Inc. All Rights Reserved
*
**************************************************************************/

/**
* @file   Reset_password.class.php
* @author 李守岩(shouyanli@meilishuo.com)
* @date   2015/12/29
* @brief  用户设置密码 
*
**/

namespace Inauth\Modules\User;

use \Inauth\Package\User\User;
use \Inauth\Package\User\UserProfile;
use \Inauth\Package\User\UserMobile;

use \Inauth\Libs\ErrorCodes;
use \Inauth\Libs\Constant;
use \Inauth\Libs\Util\Kafka;
use \Inauth\Package\Util\Utilities as U;

class Reset_password extends \Inauth\Libs\Module {

    public function execute() {

        $user_id       = (int)$this->request->post('user_id', 0);

        $old_pass      = (string)$this->request->post('old_password', '');
        $new_pass      = (string)$this->request->post('new_password', '');
        $cfm_pass      = (string)$this->request->post('confirm_password', '');

        $access_token  = (string)$this->request->post('access_token', '');
        $email         = (string)$this->request->post('email', '');
        
        $app_id        = (int)$this->request->post('app_id', 0);
        $app_key       = (string)$this->request->post('app_key', '');

        $input_fault_state = 0;
        $this->inputVerify('user_id',  $user_id,  'int',     false /* != 0 */)  ? 1 : $input_fault_state = 1;
        $this->inputVerify('old_pass', $old_pass, 'string',  false /* != 0 */)  ? 1 : $input_fault_state = 1;
        $this->inputVerify('new_pass', $new_pass, 'string',  false /* != 0 */)  ? 1 : $input_fault_state = 1;
        $this->inputVerify('cfm_pass', $cfm_pass, 'string',  false /* != 0 */)  ? 1 : $input_fault_state = 1;
        //$this->inputVerify('email',    $email,    'string',  false /* != 0 */)  ? 1 : $input_fault_state = 1;
        if ($input_fault_state) { return FALSE; }
        
        if ($old_pass === $new_pass) {
            $this->setView(
                ErrorCodes::NEW_PASSOWRD_SAME_AS_OLD, 
                ErrorCodes::getErrorMessage(ErrorCodes::NEW_PASSOWRD_SAME_AS_OLD)
            );
            return FALSE; 
        }

        if ($cfm_pass !== $new_pass) {
            $this->setView(
                ErrorCodes::CONFIRM_PASSOWRD_ERROR, 
                ErrorCodes::getErrorMessage(ErrorCodes::CONFIRM_PASSOWRD_ERROR)
            );
            return FALSE;
        }

        $res = UserProfile::resetPassword($user_id, ''/*email*/,''/*nickname*/,$old_pass, $new_pass);
        
        U::log('passport.reset_pwd', json_encode(array('user_id'=>$user_id, $res)));
        
        if (Constant::RET_SUCCESS !== $res['error_code']) {
            $this->setView($res['error_code'], $res['message'], $res['data']);
            return FALSE;    
        }
        
        $kafka_res = Kafka::push('kick_user', array('user_id' =>  $user_id, 'access_token' => $access_token), $user_id);

        $this->setView(0, '', $res['data']);
        return TRUE;
    }
}

/* vim: set expandtab ts=4 sw=4 sts=4 tw=100: */
