<?php
/**
 * 更新用户cookie并记录用户登录ipBy:Kafka
 * xiaolongrong
 */
namespace Inauth\Modules\Callback;
use \Inauth\Package\callback\AddRemoteCallback;

class Comment_login_detail extends \Frame\Module {

    public function run() {
        $data = $this->request->request('data', array());
        if (empty($data)) {
            return $this->request->error('40001', '参数错误');
        }
        $this->process($data);
        return true;
    }

    public function process($data) {
        $updateIp = array();
        if (empty($data['cookie'])) {
            //更新login_time和last_login_date
            AddRemoteCallback::update_user_login_info_time($data['user_id']);
        }
        else {
            //更新cookie,login_time和last_login_date
            AddRemoteCallback::update_user_login_info_cookie($data);
        }
        //更新t_dolphin_user_login_ip
        if (!empty($data['ip'])) {
            $updateIp[$data['user_id']] = $data['ip'];
            AddRemoteCallback::update_user_login_ip($updateIp);
        }
    }
}
