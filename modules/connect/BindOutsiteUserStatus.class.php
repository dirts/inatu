<?php
/***************************************************************************
 *
 * Copyright (c) 2015 Meilishuo.com, Inc. All Rights Reserved
 *
 **************************************************************************/



/**
 * @file   BindOutsiteUserStatus.class.php
 * @author 荣小龙(xiaolongrong@meilishuo.com)
 * @date   2015/12/29
 * @brief  passport-sdk-connect 根据user_id查询美丽说帐号是否绑定过第三方帐号
 *
 **/

namespace Inauth\Modules\Connect;

use \Inauth\Package\Connect\Helper\ConnectFactory;
use \Inauth\Libs\Constant;

class BindOutsiteUserStatus extends \Inauth\Libs\Module {

    public function execute() {
        $channel = (string)$this->request->post('channel', '');
        $user_id = (int)$this->request->post('user_id', 0);
        $status  = (int)$this->request->post('status', 0);

        $input_fault_state = 0;
        $this->inputVerify('channel', $channel, 'string', TRUE /* = 0 */) ? 1 : $input_fault_state = 1;
        $this->inputVerify('user_id', $user_id, 'int', FALSE /* != 0 */) ? 1 : $input_fault_state = 1;
        $this->inputVerify('status', $status, 'int', TRUE /* = 0 */) ? 1 : $input_fault_state = 1;
        if ($input_fault_state) { return FALSE; }

        $res = ConnectFactory::bindOutsiteUserStatus($channel, $user_id, $status, FALSE/*master*/);
        if (Constant::RET_SUCCESS !== $res['error_code']) {
            $this->setView($res['error_code'], $res['message'], $res['data']);
            return FALSE;
        }
        $this->setView(0, '', $res['data']);
        return TRUE;
    }
}

/* vim: set expandtab ts=4 sw=4 sts=4 tw=100: */
