<?php
namespace Inauth\Modules\Risk;

use \Inauth\Package\Risk\Risk;

/**
 * 获取用户信息
 */
class Unrisk extends \Frame\Module {

    public function run() {
        $custom     = (array)$this->request->request('customParams', array());
        $cookie     = (array)$this->request->request('userCookie', array());
        
        $param = array_merge($cookie, $custom);
        
        if (empty($param['rule_id'])) {
            return $this->response->error('40001', '参数错误');
        }

        $rule_id = $param['rule_id'];
        $risk = Risk::unrisk($rule_id, $param);
        return $this->response->success($risk);
    }

}
