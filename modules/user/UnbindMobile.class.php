<?php

/***************************************************************************
 *
 * Copyright (c) 2016 Meilishuo.com, Inc. All Rights Reserved
 *
 **************************************************************************/



/**
 * @file   UnbindMobile.class.php
 * @author 荣小龙(xiaolongrong@meilishuo.com)
 * @date   2016/01/14
 * @brief  passport-sdk-user 解除手机号绑定：参数user_id
 *
 **/

namespace Inauth\Modules\User;

use \Inauth\Libs\Constant;
use \Inauth\Libs\ErrorCodes;
use \Inauth\Package\User\UserMobile;

class UnbindMobile extends \Inauth\Libs\Module {

    public function execute() {
        $user_id = (int)$this->request->request('user_id', 0);

        if ( !$this->inputVerify('user_id', $user_id, 'int', FALSE /* != 0 */) ) {
            return ErrorCodes::getErrorResult(ErrorCodes::PARAM_ERROR);
        }

        $res = UserMobile::unbindMobile($user_id);
        if ( Constant::RET_SUCCESS !== $res['error_code'] ) {
            $this->setView($res['error_code'], $res['message'], $res['data']);
            return FALSE;
        }
        $this->setView(0, 'success', $res['data']);
        return TRUE;
    }
}