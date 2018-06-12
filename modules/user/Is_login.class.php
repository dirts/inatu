<?php
namespace Inauth\Modules\User;

use \Inauth\Package\User\User;
use \Inauth\Package\Session\Session;
use \Inauth\Package\Util\Utilities as U;

//web session
use \Inauth\Package\Session\Helper\RedisSessionWeb;
use \Libs\Cache\Memcache as Memcache;

//mob session
use \Inauth\Package\Session\Helper\RedisSession;
use \Inauth\Package\Session\Helper\DBPassportHelper;

use \Inauth\Libs\Util\PassportInverseSolution;

/**
 * 获取用户信息
 */
class Is_login extends \Frame\Module {


    public function run() {


        $session_id  = (string)$this->request->request('session_id', ''); 
        $inverse_key = (string)$this->request->request('inverse_key', ''); 
     
        $user_id    = (int)$this->request->post('user_id', 0); 
        $source     = (string)$this->request->request('source', '');

        $app_id     = (int)$this->request->request('app_id', 0);
        $app_key    = (string)$this->request->request('app_key', '');
        $type       = (string)$this->request->post('type', 'session'); 

        if (empty($session_id) && empty($user_id)) {
            return $this->response->error(40001, '参数错误!');
        }

        if ($type == 'session') {
            $id = trim($session_id);
        } else {
            $id = $user_id;
        }

        switch($source) {
            case 'web':

                $use_new_cache = true; 
                if ($use_new_cache == true) {
                    $sessionData = RedisSessionWeb::get_session_data($session_id);
                } else {
                    $sessionData = Memcache::instance()->get($session_id);
                }

//                if (empty($sessionData['keyid']) && !empty($inverse_key)) {
//                    if ($user_id = PassportInverseSolution::fetchUserId($inverse_key)){
//                        $sessionData['session_id']   = $session_id;
//                        $sessionData['keyid']        = $user_id;
//                        $sessionData['session_data'] = User::get_user_info($user_id);
//                        $sessionData['aes'] = 1;//User::get_user_info($user_id);
//
//                        return $this->response->success($sessionData);
//                    }
//                }
                if (empty($sessionData)) {
                    
                    return $this->response->error(40004, '获取失败');
                }
                return $this->response->success($sessionData);

                break;
            case 'mob':
                $redis   = RedisSession::get_token_data($session_id);
                if (!empty($redis)) {
                    return $this->response->success($redis);
                }

                $table  = DBPassportHelper::get_table_sharp_name($session_id);
                $res    = DBPassportHelper::getConn()->table($table)->where(array('token' => $session_id))->query('*', false, 'token');

                if (empty($res[$session_id])) {
                    return $this->response->error(40004, '获取失败');
                }
                
                $token_db = $res[$session_id];
                RedisSession::set_token_data($session_id, $token_db); 
                return $this->response->success($token_db);

                break;
            default :

                $user_id = Session::check_login($id, $app_id, $type);
                return $this->response->success($user_id);
        }

    }

}
