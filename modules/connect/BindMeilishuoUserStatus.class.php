<?php
/***************************************************************************
 *
 * Copyright (c) 2015 Meilishuo.com, Inc. All Rights Reserved
 *
 **************************************************************************/



/**
 * @file   BindMeilishuoUserStatus.class.php
 * @author 荣小龙(xiaolongrong@meilishuo.com)
 * @date   2015/12/29
 * @brief  passport-sdk-connect 根据auth查询该第三方帐号是否绑定过美丽说帐号
 *
 **/

namespace Inauth\Modules\Connect;

use \Inauth\Package\Connect\Helper\ConnectFactory;
use \Inauth\Libs\Constant;

class BindMeilishuoUserStatus extends \Inauth\Libs\Module {

    public function execute() {
        $channel = (string)$this->request->request('channel', '');
        $auth    = (string)$this->request->request('auth', '');
        $status  = (int)$this->request->request('status', 0);

        $input_fault_state = 0;
        $this->inputVerify('channel', $channel, 'string', FALSE /* != 0 */) ? 1 : $input_fault_state = 1;
        $this->inputVerify('auth', $auth, 'string', FALSE /* != 0 */) ? 1 : $input_fault_state = 1;
        $this->inputVerify('status', $status, 'int', TRUE /* = 0 */) ? 1 : $input_fault_state = 1;
        if ($input_fault_state) { return FALSE; }

        $res = ConnectFactory::bindMeilishuoUserStatus($channel, $auth, $status, FALSE/*master*/);
        if (Constant::RET_SUCCESS !== $res['error_code']) {
            $this->setView($res['error_code'], $res['message'], $res['data']);
            return FALSE;
        }
        $this->setView(0, '', $res['data']);
        return TRUE;
    }
}

/* vim: set expandtab ts=4 sw=4 sts=4 tw=100: */
