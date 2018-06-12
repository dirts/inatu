<?php
/***************************************************************************
 *
 * Copyright (c) 2016 Meilishuo.com, Inc. All Rights Reserved
 *
 **************************************************************************/



/**
 * @file   ConnectLogin.class.php
 * @author 荣小龙(xiaolongrong@meilishuo.com)
 * @date   2016/01/05
 * @brief  passport-sdk-connect 使用互联auth查询到已绑定的美丽说帐号user_id，（调用user/login）登录美丽说帐号
 *
 **/

namespace Inauth\Modules\Connect;

use \Inauth\Package\Connect\Helper\ConnectFactory;
use \Inauth\Libs\Constant;

class ConnectLogin extends \Inauth\Libs\Module {

    public function execute() {
        $channel = (string)$this->request->request('channel', '');
        $auth    = (string)$this->request->request('auth', '');

        $input_fault_state = 0;
        $this->inputVerify('channel', $channel, 'string', FALSE /* != 0 */) ? 1 : $input_fault_state = 1;
        $this->inputVerify('auth', $auth, 'string', FALSE /* != 0 */) ? 1 : $input_fault_state = 1;
        if ($input_fault_state) { return FALSE; }

        $res = ConnectFactory::connectLogin($channel, $auth);
        if (Constant::RET_SUCCESS !== $res['error_code']) {
            $this->setView($res['error_code'], $res['message'], $res['data']);
            return FALSE;
        }
        $this->setView(0, 'success', $res['data']);
        return TRUE;
    }
}

/* vim: set expandtab ts=4 sw=4 sts=4 tw=100: */
