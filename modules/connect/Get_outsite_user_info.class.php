<?php
/***************************************************************************
 *
 * Copyright (c) 2015 Meilishuo.com, Inc. All Rights Reserved
 *
 **************************************************************************/



/**
 * @file   modules/modules/Get_outsite_user_info.class.php
 * @author 荣小龙(xiaolongrong@meilishuo.com)
 * @date   2015/12/23
 * @brief  passport-sdk-connect 获取外站用户信息接口
 *
 **/

namespace Inauth\Modules\Connect;

use \Inauth\Package\Connect\Helper\RedisOutsiteUserInfo;
use \Inauth\Libs\Constant;
use \Inauth\Libs\ErrorCodes;

class Get_outsite_user_info extends \Inauth\Libs\Module {

    public function execute() {
        $channel = (string)$this->request->request('channel', '');
        $uids    = (string)$this->request->request('uids', '');

        if ( empty($channel) || empty($uids) ) {
            return ErrorCodes::getErrorResult(ErrorCodes::PARAM_ERROR);
        }

        $uids = explode(',', $uids);

        $res = RedisOutsiteUserInfo::getOutsiteUserInfo($channel, $uids);

        if (Constant::RET_SUCCESS !== $res['error_code']) {
            $this->setView($res['error_code'], $res['message'], $res['data']);
            return FALSE;
        }
        $this->setView(0, '', $res['data']);
        return TRUE;
    }
}
