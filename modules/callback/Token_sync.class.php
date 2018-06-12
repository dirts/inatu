<?php
namespace Inauth\Modules\Callback;

use \Inauth\Package\Session\Helper\RedisSession;

/**
 * 获取用户信息
 */
class Token_sync extends \Frame\Module {

    public function run() {

        $token   = (string)$this->request->post('token', ''); 
        return $this->response->success($userinfos);
    
    }

}
