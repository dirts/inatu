<?php
namespace Inauth\Modules\Auth;

use \Inauth\Package\User\User;
use \Inauth\Package\App\App;
use \Inauth\Package\Session\Session;
use \Inauth\Queue\Test as Queue;
use \Inauth\Package\Sync\RedisSync;

/**
 * 将access_token绑定用户信息
 */
class Bind extends \Frame\Module {
    
    public function run() {
        $user_id    = (int)$this->request->request('user_id', 0); 
        $ticket_id  = (string)$this->request->request('ticket_id', ''); 
        
        $sec_key    = (string)$this->request->request('sec_key', '');
        $app_id     = (int)$this->request->request('app_id', 0); 

        if (empty($app_id) || empty($sec_key)) {
            //return $this->response->error(40001, '参数错误!');
        }

        if ($user_id) {
            $userinfo = User::query($param = array('user_id' => $user_id), 'user_id, password');
            //用户是否存在
            if (empty($userinfo)) {
                return $this->response->error(1001, '用户不存在!');
            }
        }

        /*触发登陆动作*/
        $hash = Session::bind_ticket($ticket_id, $user_id, $app_id);
        RedisSync::sync_session('bind_ticket', $ticket_id, $user_id);
        if (!$hash) {
            //return $this->response->error(1100, '亲出了点小状况，请稍后再试!');
        }

        //Queue::push($user_id);        
        return $this->response->success(1);
    }
    
    public function asyncJob() {
        $_REQUEST['sec_key'] = 'xx';
    }

}
