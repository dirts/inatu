<?php
namespace Inauth\Modules\Session;

use \Inauth\Package\Session\Helper\RedisSessionWeb;
use \Libs\Cache\Memcache as Memcache;
use \Passport\Libs\Util\PassportInverseSolution;
/**
 * 老 session 鉴权服务化
 */
class Get_web_session extends \Frame\Module {
    public static $needInverse = 1; //反解开关

    public function run() {

        $token      = (string)$this->request->request('access_token', '');
        $inverseCookie = (string)$this->request->request('inverse_cookie', '');
        $app_id     = (int)$this->request->request('app_id', 0);
        $app_key    = (string)$this->request->request('app_key', '');

        if (empty($token)) {
            return $this->response->error('40001', '参数错误');
        }

        $use_new_cache = true;
        if ($use_new_cache == true) {
            $sessionData   = RedisSessionWeb::get_session_data($token);
        } else {
            $sessionData = Memcache::instance()->get($token);
        }
        if (self::$needInverse) {
            if (empty($sessionData['keyid']) || empty($sessionData['session_data']['user_id'])) {
                $inverse_uid = 0;
                if (!empty($inverseCookie)) {
                    $inverse_uid = PassportInverseSolution::fetchUserId($inverseCookie,1);
                }
                if (!empty($inverse_uid)) {
                    $sessionData['keyid'] = $inverse_uid;
                    $sessionData['session_data']['user_id'] = $inverse_uid;
                }
            }
        }
        return $this->response->success($sessionData);
    }

}
