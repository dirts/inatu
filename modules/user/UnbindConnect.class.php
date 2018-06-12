<?php

/***************************************************************************
 *
 * Copyright (c) 2016 Meilishuo.com, Inc. All Rights Reserved
 *
 **************************************************************************/



/**
 * @file   UnbindConnect.class.php
 * @author 荣小龙(xiaolongrong@meilishuo.com)
 * @date   2016/01/14
 * @brief  passport-sdk-user 解除互联帐号绑定：参数user_id，解绑的互联类型$channel
 *
 **/

namespace Inauth\Modules\User;

use \Inauth\Libs\Constant;
use \Inauth\Libs\ErrorCodes;
use \Inauth\Package\User\UserMobile;
use \Inauth\Package\Connect\Helper\ConnectFactory;

class UnbindConnect extends \Inauth\Libs\Module {

    public function execute() {
        $user_id = (int)$this->request->request('user_id', 0);
        $channel = (string)$this->request->request('channel', '');

        if ( !$this->inputVerify('user_id', $user_id, 'int', FALSE /* != 0 */) ) {
            return ErrorCodes::getErrorResult(ErrorCodes::PARAM_ERROR);
        }

        $res = ConnectFactory::unbindConnect($user_id, $channel);
        if ( Constant::RET_SUCCESS !== $res['error_code'] ) {
            $this->setView($res['error_code'], $res['message'], $res['data']);
            return FALSE;
        }
        $this->setView(0, 'success', $res['data']);
        return TRUE;
    }
}