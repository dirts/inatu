<?php
namespace Inauth\Modules\User;

use \Inauth\Package\User\UserMobile;

/**
 * 获取用户信息
 */
class Get_mobile_by_uid extends \Frame\Module {

    public function run() {
        $user_id   = (int)$this->request->post('user_id', ''); 

        $userinfos = UserMobile::find_mobile($user_id);
        return $this->response->success($userinfos);
    }

}
