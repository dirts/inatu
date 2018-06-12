<?php
namespace Inauth\Modules\User;

use \Inauth\Package\User\User;
use \Inauth\Package\User\UserMobile;
use \Inauth\Package\User\UserFormat;
use \Inauth\Libs\Util\Util;
use \Inauth\Package\Util\Utilities;

/**
 * 获取用户信息
 */
class Register extends \Frame\Module {

    public function run() {

        $app_id     = $this->request->post('app_id', 0);
        $app_key    = $this->request->post('app_key', '');


        $this->baseinfo   = $this->request->post('base_info', array());
        $this->router     = $this->request->post('router', '');   

        //非空验证
        if (empty($this->baseinfo['email']) ||
            empty($this->baseinfo['nickname']) ||
            empty($this->baseinfo['password']) ||
            empty($this->baseinfo['gender'])
        ) {
            return $this->response->error(20209, '用户userinfo必须项为空');
        }

        //验证一些基本数据
        if(!$this->validate()) {
            return false;
        }

        $exists = array('nickname' => $this->baseinfo['nickname'], 'or', 'email' => $this->baseinfo['email']);
        if ($is_exists = User::exists($exists)) {
            return $this->response->error(20213, '邮箱或者昵称已经被注册!');
        }

        //构造注册数据
        $params = $this->init_params();        
        
        /*用户注册*/
        if (!$user_id = User::register($params)) {
            return $this->response->error(40004, '亲，出现了点小状况，稍等下再试试');
        }

       
	    $this->response->success(array('user_id' => $user_id));
        return;
        /* 手机注册 */ 
        if (in_array('mobile', $types) && !UserMobile::bind($user_id, $mobile)) {
            return $this->response->error(40004, '亲，出现了点小状况，稍等下再试试');
        }

        /* 互联注册 */
        if (in_array('connect', $types) && !UserConnect::bind($user_id, $auth)) {
            return $this->response->error(40004, '亲，出现了点小状况，稍等下再试试');
        }

        return $this->response->success($user_id);
    }

    private function init_params() {
        $params = array();
        $params['nickname']     = $this->baseinfo['nickname'];
        $params['email']        = $this->baseinfo['email'];
        $params['password']     = $this->baseinfo['password'];
        $params['active_code']  = $this->baseinfo['activateCode'];
        $params['invite_code']  = $this->baseinfo['inviteCode'];
        $params['is_actived']   = 1;//$this->baseinfo['isActived'];
        $params['realname']     = '';//$this->baseinfo['realname'];
        $params['reg_from']    = $this->baseinfo['regFrom'];
        $params['cookie']       = $this->baseinfo['cookie'];
        return $params;
    }

    private function validate() {
 
        if ($this->router == 'Register_actionconnect') {
            return true;
        }
        //正常注册检测数据
        //格式验证

        $this->formatHandler = new UserFormat();
         
        if (
            $this->baseinfo['gender'] != '女' ||
            $this->formatHandler->nicknameFormat($this->baseinfo['nickname']) === FALSE 
        ) {
            $this->response->error(20210, '用户userInfo 数据项不合法');
            return false;
        }

        //不能超过8位数字
        if ($this->formatHandler->nicknameNumberFormat($this->baseinfo['nickname']) === TRUE) {
            $this->response->error(20211, '用户nickname 不得存在超过8位数字');
            return false;
        }

        return true;

    }
    
    function asyncJob() {
        $res = $this->response->get_response();
        $reg_stat['plat']       = $this->request->post('app_id', 0); // 平台(1/2) pc/client_id
        $reg_stat['is_success'] = (int)!$res['error_code']; // 是否注册成功
        $reg_stat['reg_err']    = $res['message']; // 注册失败原因
        $reg_stat['reg_type']   = 'higo'; // 注册方式 mail/tel/connect/fast/onclick
        $reg_stat['open_type']  = ''; // 互联渠道（互联方式））(renren \weixin\douban \….)
        $reg_stat['reg_way']    = 1; // 注册形式（主动1or被动0(互联方式)）
        $reg_stat['path']       = ''; // snake接口
        $reg_stat['user_id']    = empty($res['data']['user_id']) ? 0 : $res['data']['user_id']; //userid
        $reg_stat['session_id'] = ''; // global_key or access_token
        $reg_stat['mobile']     = ''; // tel
        $reg_stat['version']    = ''; // mob客户端版本号
        $reg_stat['ip']         = $this->request->ip; // 用户ip
        $reg_stat['refer']      =''; // 注册前所访问页面
        $reg_stat['reg_from']   = 4;//$this->request->request('base_info')['regFrom']; // 注册平台（pc, iPad, iPhone, Android）
        $reg_stat['captchaTimes'] = ''; //验证码输错记录
        $reg_stat['flash_id']   = ''; //验证码输错记录
        $reg_stat['email']      = $this->baseinfo['email']; //邮箱
        $reg_stat['nickname']   = $this->baseinfo['nickname']; //昵称
        $reg_stat['password']   = ''; //密码
        $reg_stat['open_id']    = substr($this->baseinfo['email'], 1,-9); //第三方id
        $log = '';
        foreach ($reg_stat as $k => $item) {
            $log .= "[$item]\t";
        }
        Utilities::log('Statistics_register', $log);
    }

}
