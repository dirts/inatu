<?php
namespace Inauth\Modules\Auth;

use \Inauth\Package\User\User;
use \Inauth\Package\App\App;
use \Inauth\Package\Session\Session;
use \Inauth\Queue\Test as Queue;

/**
 * 创建access_token
 */
class Token extends \Frame\Module {
    
    public function run() {
        $app_id     = (int)$this->request->request('app_id', 0); 
        $sec_key    = (string)$this->request->request('sec_key', '');

        if (empty($app_id) || empty($sec_key)) {
            return $this->response->error(40001, '参数错误!');
        }

        /*触发登陆动作*/
        $hash = Session::create_ticket($user_id = 0, $app_id, 0);
        if (!$hash) {
            return $this->response->error(1100, '亲出了点小状况，请稍后再试!');
        }

        return $this->response->success(array('session' => $hash));
    }
    
    public function asyncJob() {
        $_REQUEST['sec_key'] = 'xx';
    }

}
