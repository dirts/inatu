<?php
namespace Inauth\Modules\User;

use \Inauth\Package\Session\Session;
use \Inauth\Package\Sync\RedisSync;
use \Inauth\Package\Util\Utilities;
/**
 * 获取用户信息
 */
class Delay extends \Frame\Module {
    

    public function run() {
        $session_id   = (string)$this->request->post('session_id', ''); 
        $expire       = (int)$this->request->post('expire', 0); 

        if (empty($session_id) || empty($expire)) {
            return $this->response->error(40001, '参数错误!');
        }

        $res = Session::delay($session_id, $expire);
        RedisSync::sync_session('delay', $session_id, $expire);
        return $this->response->success($res);
    }
    
    public function asyncJob() {
    }

}
