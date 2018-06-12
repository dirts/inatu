<?php
namespace Inauth\Modules\Test;

use Libs\Cache\Memcache as Memcache;
use \Inauth\Package\Session\Helper\RedisSession;
use \Inauth\Package\Session\Helper\DBSwanHelper;
use \Inauth\Package\Session\Helper\DBSwanOldHelper;


/**
 * 获取用户信息
 */
class Get_mob_session_detail extends \Frame\Module {


    public function run() {
	    $token  = (string)$this->request->request('access_token', '');
		
        if (empty($token)) {
		    return $this->error('40001', '参数错误');    
        }

        $res['redis']   = unserialize(RedisSession::get("$token"));
        $res['mc']      = Memcache::instance()->get("Mob:Session:AccessToken:$token");
        $res['t_swan_oauth_access_token_new'] = DBSwanHelper::getConn()->table('t_swan_oauth_access_token_new')->where(array('token' => $token))->query('*');
        $res['t_swan_oauth_access_token'] = DBSwanHelper::getConn()->table('t_swan_oauth_access_token')->where(array('token' => $token))->query('*');
        $res['t_swan_oauth_access_token_old'] = DBSwanOldHelper::getConn()->table('t_swan_oauth_access_token')->where(array('token' => $token))->query('*');

	    return $this->response->success($res);
        
    
    }


}

