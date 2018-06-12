<?php
/***************************************************************************
 *
 * Copyright (c) 2015 Meilishuo.com, Inc. All Rights Reserved
 *
 **************************************************************************/



/**
 * @file modules/user/Get_user_by_token.class.php
 * @author 李守岩(shouyanli@meilishuo.com)
 * @date 2015/12/08
 * @brief  passport sdk session
 *
 **/
namespace Inauth\Modules\User;

use \Inauth\Package\User\User;
use \Inauth\Package\User\UserMobile;
use \Inauth\Package\Session\Helper\RedisSessionWeb;
use \Inauth\Package\Session\MobSession;

/**
 * 获取用户信息
 */

class Get_user_by_token extends \Frame\Module {

    public function run() {
        $token        = (string)$this->request->request('access_token', '');
        $santorini_mm = (string)$this->request->request('santorini_mm', '');

        if (empty($token) && empty($santorini_mm)) {
            return $this->response->error(40001, '参数错误!');
        }

        if (!empty($token)) {
            $token_data     = MobSession::get_session($token);
            $res['token']   = $token_data;

            $user_id = $res['token']['user_id'];
            $from    = 'access_token';
        } else {
            $sessionData    = RedisSessionWeb::get_session_data($santorini_mm);
            $res['session'] = $sessionData;

            $user_id = $sessionData['session_data']['user_id'];
            $from    = 'santorini_mm';
        }


        if (empty($user_id)) {
            return $this->response->error(40003, 'session无效');   
        }

        if (!empty($user_id)) {
            $userinfos = User::get_user_infos(array($user_id), "*", $hash = 1);
            $res['userinfo'] = $userinfos[$user_id];
        }

        if (!empty($res['userinfo'])) {
           $res['userinfo']['mobile'] = UserMobile::get_mobile_by_uid($user_id);    
        }

        $res['from'] = $from;
        return $this->response->success($res);
    }

}
