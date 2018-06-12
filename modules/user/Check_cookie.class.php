<?php
namespace Inauth\Modules\User;

use \Inauth\Package\App\App;
use \Inauth\Package\Session\Session;
use \Inauth\Libs\Util\Logger;

/**
 * 获取用户信息
 */
class Check_cookie extends \Frame\Module {
    

    public function run() {

        $app_id     = (int)$this->request->post('app_id', 0); 
        $cookie     = (array)$this->request->post('cookie', array()); 

        if (empty($cookie) || empty($app_id)) {
            return $this->response->error(40001, '参数错误!');
        }

        $sessions = App::session_config($app_id, 0);
        $keys = array_keys($sessions);  

        foreach ($keys as $key) {
            if (!empty($cookie[$key])) {
                $id  = $cookie[$key];
                $user_id = Session::check_login($id, $app_id, 'session');
            }
        }
        if (empty($user_id)) { 
            return $this->response->error(1100, '未知错误!');
        }
        return $this->response->success($user_id);

    }

    public function asyncJob() {
    }

}
