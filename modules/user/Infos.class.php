<?php
namespace Inauth\Modules\User;

use \Inauth\Package\User\User;
use \Inauth\Package\User\UserMobile;
use \Inauth\Package\User\UserLoginIp;
use \Inauth\Package\Util\Utilities as U;

/**
 * 获取用户信息
 */
class Infos extends \Frame\Module {

    public function run() {
        $uids    = (string)$this->request->post('user_ids', ''); 
        $fields  = (string)$this->request->post('fields', '');
        $size    = (string)$this->request->post('size', 'c');
        $hash    = (int)$this->request->post('hash', 0);
        
        $app_id  = (int)$this->request->post('app_id', 0);
        $app_key = (string)$this->request->post('app_key', '');

        $app_id  = (int)$this->request->post('app_id', 0);
        $app_key = (string)$this->request->post('app_key', '');

        $uids = explode(",", $uids);

        $sizes = range('a', 'h');
        if (!in_array($size, $sizes)) {
           $size = 'c';
        }

        if (empty($uids)) {
            return $this->response->error(40001, '参数错误!');
        }

        if (empty($fields)) {
            $fields = 'nickname, email, password, cookie, ctime, is_actived, status, realname, istested, reg_from, last_email_time, level, isPrompt, isBusiness, login_times , last_logindate';
        }

        $userinfos = User::get_user_infos($uids, $fields, $hash);
        foreach ($userinfos as $key => &$value) {
            if (!empty($value['avatar_c'])) {
                $value['avatar_c'] = preg_replace("/\/ap\/c/i", "/ap/$size", $value['avatar_c']);
            }
        }
        $fields = explode(',', $fields);
        //获取本地location 
        if (in_array('login_location', $fields)) {
            $ips = UserLoginIp::get_last_login_ips($uids);
            foreach ($userinfos as $uid => &$userinfo) {
                if (!empty($ips[$uid])) {
                    $userinfo['login_location'] = $ips[$uid]; 
                } else {
                    $userinfo['login_location'] = array(); 
                }
                $userinfo['identity'] = $userinfo['identity']; 
            }
        }

        //获取本地location 
        if (in_array($app_id, array(10011))) {
            foreach ($userinfos as $uid => &$userinfo) {
                $userinfo['mobile'] = UserMobile::get_mobile_by_uid($uid);    
            }
        }
        return $this->response->success($userinfos);
    }

}

