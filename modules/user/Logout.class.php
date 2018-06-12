<?php
namespace Inauth\Modules\User;

use \Inauth\Package\User\User;
use \Inauth\Package\App\App;
use \Inauth\Package\Session\Session;
use \Inauth\Package\Sync\RedisSync;

/**
 * 推出登陆
 */
class Logout extends \Frame\Module {
    
    public function run() {
        $session_id = (string)$this->request->post('session_id', ''); 
        $user_id    = (int)$this->request->post('user_id', 0); 
        $app_id     = (int)$this->request->post('app_id', 0); 

        if (empty($session_id) && empty($user_id)) {
            return $this->response->error(40001, '参数错误!');
        }

        if (empty($app_id)) {
            return $this->response->error(40001, '参数错误!');
        } 
        
        if (!Session::check_login($user_id, $app_id, 'user_id')) {
            //return $this->response->error(40004, '您还未登录');
        }
        
        if (!empty($user_id)) {
            $res = Session::delete_user_ticket($user_id, $app_id);
            RedisSync::sync_session('delete_user_ticket', $user_id, $app_id);
        } else {
            $res = Session::delete_ticket($session_id, $app_id);
            RedisSync::sync_session('delete_ticket', $session_id, $app_id);
        }

        return $this->response->success($res);
    }

}
