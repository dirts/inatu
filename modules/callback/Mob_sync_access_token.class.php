<?php
namespace Inauth\Modules\Callback;

use \Inauth\Package\Session\Helper\RedisSession;
use Libs\Cache\Memcache as Memcache;
use \Inauth\Package\Session\Helper\SessionRelation;

/**
 * 获取用户信息
 */
class Mob_sync_access_token extends \Frame\Module {

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
            $res = RedisSession::del($token);
        break;
        case 'set':
        default:
            $res['redis']    = (int)RedisSession::set_token_data($token, $data, $expire);
            $res['mc']       = (int)Memcache::instance()->set("Mob:Session:AccessToken:$token", $data, $expire);
            //增加关系
            $user_id = $data['user_id'];
            SessionRelation::add_mob_relation($user_id, $expire, $token);
    }

    return $this->response->success($res);
    
    }

}
