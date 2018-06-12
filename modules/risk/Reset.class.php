<?php
namespace Inauth\Modules\Risk;

use \Inauth\Package\Risk\Risk;

/**
 * 获取用户信息
 */
class Reset extends \Frame\Module {

    public function run() {
        $rule_id     = (int)$this->request->request('rule_id', 0);
        $user_id     = (int)$this->request->request('user_id', 0);
        
        
        if (empty($user_id) || empty($rule_id)) {
            return $this->response->error('40001', '参数错误');
        }

        $risk = Risk::reset($rule_id, $user_id);
        return $this->response->success($risk);
    }

}
