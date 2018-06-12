<?php
/***************************************************************************
 *
 * Copyright (c) 2016 Meilishuo.com, Inc. All Rights Reserved
 *
 **************************************************************************/



/**
 * @file   GenerateGotAuth.class.php
 * @author 荣小龙(xiaolongrong@meilishuo.com)
 * @date   2015/01/04
 * @brief  passport-sdk-connect 构造got_auth（是否是新互联用户，外站昵称、头像，auth等）
 *
 **/

namespace Inauth\Modules\Connect;

use \Inauth\Package\Connect\Helper\ConnectFactory;
use \Inauth\Libs\Constant;

class GenerateGotAuth extends \Inauth\Libs\Module {

    public function execute() {
        $channel      = (string)$this->request->post('channel', '');
        $got_auth_url = (string)$this->request->post('got_auth_url', '');
        $code         = (string)$this->request->post('code', '');

        $input_fault_state = 0;
        $this->inputVerify('channel', $channel, 'string', FALSE /* != 0 */) ? 1 : $input_fault_state = 1;
        $this->inputVerify('got_auth_url', $got_auth_url, 'string', FALSE /* != 0 */) ? 1 : $input_fault_state = 1;
        $this->inputVerify('code', $code, 'string', FALSE /* != 0 */) ? 1 : $input_fault_state = 1;
        if ($input_fault_state) { return FALSE; }

        $res = ConnectFactory::generateGotAuth($channel, $got_auth_url, $code);
        if (Constant::RET_SUCCESS !== $res['error_code']) {
            $this->setView($res['error_code'], $res['message'], $res['data']);
            return FALSE;
        }
        $this->setView(0, '', $res['data']);
        return TRUE;
    }
}

/* vim: set expandtab ts=4 sw=4 sts=4 tw=100: */
