<?php
namespace Inauth\Modules\Callback;

use \Inauth\Package\Session\Helper\RedisSessionWeb;
use Libs\Cache\Memcache as Memcache;

/**
 * 获取用户信息
 */
class Web_sync_auto_login_access_token extends \Frame\Module {

    public function run() {

    $op     = (string)$this->request->request('op', 'set'); 
    $token  = (string)$this->request->request('access_token', ''); 
    $data   = $this->request->request('data', array()); 
    $expire = $this->request->request('expire', 0); 

    if (empty($token)) {
        return $this->response->error('40001', '参数错误');
    }

    switch ($op) {
        case 'delete':
            $res   = RedisSessionWeb::del($token);
        break;
        case 'set':
        default:
            $res['redis']    = (int)RedisSessionWeb::set_session_data($token, $data, $expire);
            //$res['mc']       = (int)Memcache::instance()->set($token, $data, $expire);
    }

    return $this->response->success($res);
    
    }

}
