<?php
namespace Inauth\Modules\User;

use \Inauth\Package\User\User;
use \Inauth\Package\User\UserMobile;

/**
 * 获取用户信息
 */
class Info_by_mobiles extends \Frame\Module {

    public function run() {
        $mobiles    = (string)$this->request->post('mobiles', ''); 
        $fields     = (string)$this->request->post('fields', 'user_id,nickname');
        $hash       = (int)$this->request->post('hash', 0);
        
        if (empty($mobiles)) {
            return $this->response->error(40001, '参数错误!');
        }

        $mobiles = explode(',', $mobiles);
        $datas   = UserMobile::query($param = array('mobile' => $mobiles), 'user_id, mobile', $master = false, 'mobile'); 

        foreach ($datas as $mobile => $item) {
            $userinfo = User::get_user_info($item['user_id'], $fields);
            $datas[$mobile] = $userinfo;
        }

        return $this->response->success($datas);
            
    }

}
