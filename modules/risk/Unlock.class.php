<?php
namespace Inauth\Modules\Risk;

use \Inauth\Package\Risk\Risk;

/**
 * 获取用户信息
 */
class Unlock extends \Frame\Module {

    public function run() {
        $rule_id    = (string)$this->request->post('rule_id', ''); 
        $rule_param = (array)$this->request->request('rule_param', array());

        $risk = Risk::risk($rule_id, $rule_param);
        return $this->response->success($risk);
    }

}
