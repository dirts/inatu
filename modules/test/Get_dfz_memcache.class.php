<?php
/**
 * Created by PhpStorm.
 * User: xiaolongrong
 * Date: 15/12/9
 * Time: 下午12:20
 */

namespace Inauth\Modules\Test;
use \Inauth\Package\Session\Helper\Dfz_new_memcache;

class Get_dfz_memcache extends \Frame\Module {

    public function run() {
        $token  = (string)$this->request->request('token', '');
        if (empty($token)) {
            return $this->response->error('40001', '参数错误');
        }
        $res = '';
        try {
            $memcache_helper = Dfz_new_memcache::instance();
            $res = $memcache_helper->get($token);
        } catch (\Exception $e) {
        }
        return $this->response->success($res);
    }
}

