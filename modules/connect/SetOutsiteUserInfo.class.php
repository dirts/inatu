<?php
/***************************************************************************
 *
 * Copyright (c) 2016 Meilishuo.com, Inc. All Rights Reserved
 *
 **************************************************************************/



/**
 * @file   SetOutsiteUserInfo.class.php
 * @author 荣小龙(xiaolongrong@meilishuo.com)
 * @date   2016/01/11
 * @brief  passport-sdk-connect 存储外站用户信息接口
 *
 **/

namespace Inauth\Modules\Connect;

use \Inauth\Package\Connect\Helper\RedisOutsiteUserInfo;
use \Inauth\Libs\Constant;
use \Inauth\Libs\ErrorCodes;

class SetOutsiteUserInfo extends \Inauth\Libs\Module {

    public function execute() {
        $channel           = (string)$this->request->post('channel', '');
        $user_id           = (int)$this->request->post('user_id', 0);
        $outsite_user_info = (array)$this->request->post('outsite_user_info', array());

        $input_fault_state = 0;
        $this->inputVerify('channel', $channel, 'string', FALSE /* != 0 */) ? 1 : $input_fault_state = 1;
        $this->inputVerify('user_id', $user_id, 'int', FALSE /* != 0 */) ? 1 : $input_fault_state = 1;

        if ( empty($outsite_user_info) || !is_array($outsite_user_info) ) {
            return ErrorCodes::getErrorResult(ErrorCodes::PARAM_ERROR);
        }

        if ($input_fault_state) { return FALSE; }

        $res = RedisOutsiteUserInfo::setOutsiteUserInfo($channel, $user_id, $outsite_user_info);

        if (Constant::RET_SUCCESS !== $res['error_code']) {
            $this->setView($res['error_code'], $res['message'], $res['data']);
            return FALSE;
        }
        $this->setView(0, '', $res['data']);
        return TRUE;
    }
}

/* vim: set expandtab ts=4 sw=4 sts=4 tw=100: */