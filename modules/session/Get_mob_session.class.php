<?php
namespace Inauth\Modules\Session;

use \Inauth\Package\Session\Helper\RedisSession;
use \Inauth\Package\Session\Helper\DBPassportHelper;


/**
 * 老 session 鉴权服务化
 */
class Get_mob_session extends \Frame\Module {

    public function run() {
	    
        $token      = (string)$this->request->request('access_token', '');
	    
        $app_id     = (int)$this->request->request('app_id', 0);
	    $app_key    = (string)$this->request->request('app_key', '');
		
        if (empty($token)) {
		    return $this->error('40001', '参数错误');    
        }
        
        $redis   = RedisSession::get_token_data($token);
       
        if (!empty($redis)) {
            return $this->response->success($redis);
        }

        $table  = DBPassportHelper::get_table_sharp_name($token);
        $res    = DBPassportHelper::getConn()->table($table)->where(array('token' => $token))->query('*', false, 'token');
        if (!empty($res[$token])) {
            $token_db = $res[$token];
            RedisSession::set_token_data($token, $token_db); 
        }
	    return $this->response->success($token_db);
    
    }

}
