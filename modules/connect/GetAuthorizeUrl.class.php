<?php
/***************************************************************************
 *
 * Copyright (c) 2015 Meilishuo.com, Inc. All Rights Reserved
 *
 **************************************************************************/



/**
 * @file   Get_authorize_url.class.php
 * @author 荣小龙(xiaolongrong@meilishuo.com)
 * @date   2015/12/28
 * @brief  passport-sdk-connect 构造第三方授权url接口（返回前端作跳转，获取授权code）
 *
 **/

namespace Inauth\Modules\Connect;

use \Inauth\Package\App\App;
use Inauth\Package\Connect\Helper\ConnectFactory;

class GetAuthorizeUrl extends \Inauth\Libs\Module {

    public function execute() {
        $channel    = (string)$this->request->request('channel', '');
        $ext_params = (array)$this->request->request('ext_params', array());
        $app_id     = (int)$this->request->request('app_id', 0);
        $app_key    = (string)$this->request->request('app_key', '');

        if ( empty($channel) || empty($app_id) || empty($app_key) ) {
            return $this->response->error(40001, '参数错误');
        }
        if ( !App::validate($app_id, $app_key) ) {
            return $this->response->error(40002, '未授权');
        }

        $connect_obj = new ConnectFactory($channel);
        $res = $connect_obj->getAuthorizeUrl($ext_params);

        return $this->response->success($res);
    }
}

/* vim: set expandtab ts=4 sw=4 sts=4 tw=100: */
