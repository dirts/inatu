<?php
namespace Inauth\Modules\User;

use \Inauth\Package\User\User;
use \Inauth\Package\App\App;
use \Inauth\Package\Session\Session;
use \Inauth\Queue\Test as Queue;

/**
 * 获取用户信息
 */
class Privilege_login extends \Frame\Module {
    
    public function run() {
        $user_id		= (int)$this->request->post('user_id', ''); 
        $default_ticket_id   	= (string)$this->request->post('default_ticket_id', '');
        
	$app_id     = (int)$this->request->post('app_id', 0); 
        $sec_key    = (string)$this->request->post('sec_key', '');

        if (empty($user_id) || empty($app_id) || empty($sec_key)) {
            return $this->response->error(40001, '参数错误!');
        }

        /*
        $app = App::query(array('app_id' => $app_id), '*' , false, 'app_id');
        if (empty($app[$app_id]['sec_key']) || $app[$app_id]['sec_key'] != $sec_key) {
            return $this->response->error(40002, '不能使用的授权!');
        }
         */

        /*
	$userinfo   = User::query($param = array('user_id' => $user_id), 'user_id, password');
        //验证用户账号密码
        if (empty($userinfo)) {
            return $this->response->error(1001, '账户名或密码错误!');
        }
	*/
        /*触发登陆动作*/
        $hash = Session::create_ticket($user_id, $app_id, 0, $default_ticket_id);
        if (!$hash) {
            return $this->response->error(1100, '亲出了点小状况，请稍后再试!');
        }

        //Queue::push($user_id);        
        return $this->response->success(array('user_id' => $user_id, 'session' => $hash));
    }
    
    public function asyncJob() {
        $_REQUEST['sec_key'] = 'xx';
    }

}
