<?php
/***************************************************************************
 *
 * Copyright (c) 2016 Meilishuo.com, Inc. All Rights Reserved
 *
 **************************************************************************/



/**
 * @file   Judge_remote_login.class.php
 * @author 荣小龙(xiaolongrong@meilishuo.com)
 * @date   2016/02/17
 * @brief  passport-sdk-risk 判断用户是否是异地登录
 *
 **/

namespace Inauth\Modules\Risk;
use \Inauth\Libs\Constant;
use \Inauth\Libs\ErrorCodes;
use \Inauth\Package\Risk\IpAddress;

class Judge_remote_login extends \Inauth\Libs\Module {

    public function execute() {
        $user_id   = (int)$this->request->request('user_id', 0);
        $client_ip = (string)$this->request->request('client_ip', '');
        if ( empty($user_id) || empty($client_ip) ) {
            return ErrorCodes::getErrorResult(ErrorCodes::PARAM_ERROR);
        }
        $res = IpAddress::judgeRemoteLogin($user_id, $client_ip);
        if (Constant::RET_SUCCESS !== $res['error_code']) {
            $this->setView($res['error_code'], $res['message'], $res['data']);
            return FALSE;
        }
        $this->setView(0, '', $res['data']);
        return TRUE;
    }

}

/* vim: set expandtab ts=4 sw=4 sts=4 tw=100: */
