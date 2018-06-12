<?php
namespace Inauth\Modules\Test;

use Libs\Cache\Memcache as Memcache;
use \Inauth\Package\Session\Helper\RedisSessionWeb;


/**
 * 获取用户信息
 */
class Get_web_session_detail extends \Frame\Module {


    public function run() {
	    $token  = (string)$this->request->request('access_token', '');
		
        if (empty($token)) {
		    return $this->error('40001', '参数错误');    
        }

        $res['redis']   = unserialize(RedisSessionWeb::get("$token"));
        $res['mc']      = Memcache::instance()->get("$token");
	    return $this->response->success($res);
        
    }


}

