<?php
namespace Inauth\Modules\Risk;

use \Inauth\Package\Risk\Risk;
use \Inauth\Package\Util\Utilities as U;

/**
 * 获取用户信息
 */
class Risk_proxy extends \Frame\Module {

    public function run() {
        
        $custom     = (array)$this->request->request('customParams', array());
        $cookie     = (array)$this->request->request('userCookie', array());

        $param = array_merge($cookie, $custom);

        if (empty($param['rule_id'])) {
            return $this->response->error('40001', '参数错误');
        }

        $rule_id = $param['rule_id'];
        /*
        $rule_param = array(
                'uid'       => 765,
                'ip'        => $this->request->ip,
                'pwd_err'   => 1,
                'santorini_mm' => '5abf8fb45f3156e5dcd4274ff3c0b1a2',
            );
        */
        $risk = Risk::risk($rule_id, $param);
        U::log('inauth.risk', print_r(array($rule_id, $param, $risk), true));
        return $this->response->success($risk);
    }

}
