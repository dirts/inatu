<?php
/***************************************************************************
*
* Copyright (c) 2015 Meilishuo.com, Inc. All Rights Reserved
*
**************************************************************************/

/**
* @file   Login.class.php
* @author 李守岩(shouyanli@meilishuo.com)
* @date   2015/12/29
* @brief  用户登陆 
*
**/
namespace Inauth\Modules\User;

use \Inauth\Package\User\User;
use \Inauth\Package\User\UserMobile;
use \Inauth\Package\User\UserFormat;
use \Inauth\Package\User\StaticsLog;

use \Inauth\Package\App\App;
use \Inauth\Package\Session\Session;
use \Inauth\Queue\Test as Queue;
use \Inauth\Package\Util\Utilities;
/**
 * 获取用户信息
 */
class Login extends \Frame\Module {
    

    public function run() {
        
        $username   = (string)$this->request->post('username', ''); 
	    $password   = (string)$this->request->post('password', ''); 
	    $app_id     = (int)$this->request->post('app_id', 0); 
        
        $remember   = (int)$this->request->post('remember', 0); 
        $ticket     = (int)$this->request->post('ticket', 0);
        

        if (empty($username)) {
            return $this->response->error(40001, '参数错误!');
        }

        if (empty($password)) {
            return $this->response->error(40001, '参数错误!');
        }

        //验证用户账号密码
        $is_mobile = UserFormat::mobileFormat($username);
        if ($is_mobile) {
            $user = UserMobile::query($param = array('mobile' => $username), 'user_id,mobile', true, 'mobile');
            if (empty($user)) {
                return $this->response->error(1003, '手机号不存在!', array('user_id' => 0, 'nickname' => ''));
            }
            $user_id    = $user[$username]['user_id'];
            $userinfo   = User::query($param = array('user_id' => $user_id), 'user_id,nickname,password,cookie,level');
        
        } else {
            $param = array('nickname' => $username, 'or', 'email' => $username); 
            $userinfo   = User::query($param, 'user_id,nickname,password,cookie,level');
        }
       
        if (empty($userinfo)) {
            return $this->response->error(1001, '账户名或密码错误!',array('user_id' => 0, 'nickname' => ''));
        }
        
        $userinfo   = $userinfo[0];
        $user_id    = (int)$userinfo['user_id'];
        $nickname   = $userinfo['nickname'];

        if ($userinfo['password'] != md5($password)) {
            return $this->response->error(1001, '账户名或密码错误!', array('user_id' => $user_id, 'nickname' => $nickname));
        }

        //获取用户基本信息,验证用户是否激活
        //$userinfo  = $user->get_user_info($user_id);
        if (!empty($userinfo['level']) && $userinfo['level'] == 50) {
            return $this->response->error(1008, '您的账户处于受限状态，请发送邮件到：check@meilishuo.com 申请解封');
        }
        
        //cookie,需要前端也种
        if ($app_id  != 10002 && !empty($userinfo['level']) && $userinfo['level'] == 20) {
            return $this->response->error(1007, '需要激活用户', $data['data']);
        }
        
        if ($ticket !== 1) {
            unset($userinfo['password']);
            return $this->response->success($userinfo);   
        } else {

            $hash = Session::create_ticket($user_id, $app_id, $remember);
            if (empty($hash)) {
                return $this->response->error(1100, '亲出了点小状况，请稍后再试!', array('user_id' => 0, 'nickname' => ''));
            }

            //User::update_login_times($user_id);
            //Queue::push($user_id);
            return $this->response->success(array('user_id' => $user_id, 'nickname' => $nickname, 'session' => $hash));
        }
    }
    
    public function asyncJob() {
        //$this->response->get_error_code
        $_REQUEST['password'] = 'xx';
        $res = $this->response->get_response(); 

        $code 	 = $res['error_code'];
        $message = $res['message'];
        $body    = json_encode($res['data']);
        $request = json_encode($_REQUEST); 
        
        /*登陆日志*/
        StaticsLog::StatForlogin(array());
        //Utilities::log('inauth.login', "[$code]\t[$message]\t[$body]\t[$request]");
    }

}
