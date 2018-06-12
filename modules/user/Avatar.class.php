<?php
namespace Inauth\Modules\User;

use \Inauth\Package\User\UserExt;

/**
 * 获取用户信息
 */
class Avatar extends \Frame\Module {
    
    public function run() {
        $user_id     = (string)$this->request->post('user_id', ''); 
        $avatar      = (string)$this->request->post('avatar', '');

        if (empty($user_id) || empty($avatar)) {
            return $this->response->error(40001, '参数错误!');
        }

        $arr = range('a','e');
        $default  = array (
            'is_uploaded' => 1,
            );
        
        foreach($arr as $key) {
            $default['avatar_'. $key] = str_replace("_o", $key, $avatar);
        } 
        
        $where  = array('user_id' => $user_id);
        $return = UserExt::update($where, $default);

        return $this->response->success($return);
    }

}
