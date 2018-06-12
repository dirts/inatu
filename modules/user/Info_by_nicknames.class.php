<?php
namespace Inauth\Modules\User;

use \Inauth\Package\User\User;
use \Inauth\Package\User\UserMobile;

/**
 * 获取用户信息
 */
class Info_by_nicknames extends \Frame\Module {

    public function run() {
        $nicknames   = (string)$this->request->post('nicknames', ''); 
        $fields     = (string)$this->request->post('fields', 'user_id, nickname');
        $hash       = (int)$this->request->post('hash', 1);
        
        if (empty($nicknames)) {
            return $this->response->error(40001, '参数错误!');
        }
        
        $nicknames = explode(',', $nicknames);
        $userinfos = User::query($param = array('nickname' => $nicknames), $fields, false, $hash ? 'nickname' : null);
        return $this->response->success($userinfos);
            
    }

}
