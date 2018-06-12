<?php
namespace Inauth\Modules\User;

use \Inauth\Package\User\User;

/**
 * 获取用户信息
 */
class Base extends \Frame\Module {

    public function run() {

        $uids   = (string)$this->request->post('user_ids', ''); 
        $fields = (string)$this->request->post('fields', 'nickname');
        $hash   = (int)$this->request->post('hash', 0);

        $uids = explode(",", $uids);
        
        if (empty($uids)) {
            return $this->response->error(40001, '参数错误!');
        }

        if (empty($fields)) {
            return $this->response->error(40001, '参数错误!');
        } 

        $userinfos = User::get_base_user_infos($uids);
        return $this->response->success($userinfos);
    
    }

}
