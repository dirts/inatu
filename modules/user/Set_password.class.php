<?php
/***************************************************************************
*
* Copyright (c) 2015 Meilishuo.com, Inc. All Rights Reserved
*
**************************************************************************/

/**
* @file   Set_password.class.php
* @author 李守岩(shouyanli@meilishuo.com)
* @date   2015/12/29
* @brief  用户设置密码 
*
**/

namespace Inauth\Modules\User;

use \Inauth\Package\User\User;
use \Inauth\Package\User\UserMobile;

use \Inauth\Libs\ErrorCodes;
use \Inauth\Libs\Util\Kafka;

class Set_password extends \Inauth\Libs\Module {

    public function execute() {

        $user_id      = (int)$this->request->post('user_id', 0);

        $old_pass     = (string)$this->request->post('old_password', '');
        $new_pass     = (string)$this->request->post('new_password', '');
        $cfm_pass     = (string)$this->request->post('confirm_password', '');

        $access_token = (string)$this->request->post('access_token', '');
        $email        = (string)$this->request->post('email', '');

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
                ErrorCodes::getStandardErrorMessage(ErrorCodes::NEW_PASSOWRD_SAME_AS_OLD)
            );
            return FALSE; 
        }

        if ($cfm_pass !== $new_pass) {
            $this->setView(
                ErrorCodes::CONFIRM_PASSOWRD_ERROR, 
                ErrorCodes::getStandardErrorMessage(ErrorCodes::CONFIRM_PASSOWRD_ERROR)
            );
            return FALSE;
        }

        $infos = User::query(array('user_id' => $user_id));
        if (empty($infos)) {
            $this->setView(
                ErrorCodes::USER_NOT_EXISTS, 
                ErrorCodes::getStandardErrorMessage(ErrorCodes::USER_NOT_EXISTS)
            );
            return FALSE;
        }

        if ($infos[0]['password'] != md5($old_pass)) {
            $infos[0]['password'] = 'xx';
            $this->setView(
                ErrorCodes::PASSWORD_ERROR, 
                ErrorCodes::getStandardErrorMessage(ErrorCodes::PASSWORD_ERROR),
                $infos
            );
            return FALSE;
        }

        $infos[0]['password'] = 'xx';
        $where  = array('user_id'  => $user_id);
        $update = array('password' => md5($new_pass));

        if (!empty($email)) $update['email'] = $email;

        if (!empty($update)) {
            $res = User::update($where, $update);
            if (!empty($res)) {
                $kafka_res = Kafka::push('kick_user', array('user_id' =>  $user_id, 'access_token' => $access_token), $user_id);
            }
        }

        $this->setView(0, 'success!', $infos);
        return TRUE;
    }
}

/* vim: set expandtab ts=4 sw=4 sts=4 tw=100: */
