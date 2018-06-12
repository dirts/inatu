<?php
namespace Inauth\Modules\User;

use \Inauth\Package\User\User;
use \Inauth\Package\User\UserExt;

/**
 * 获取用户信息
 */
class Update extends \Frame\Module {
    
    public function run() {
        $user_id     = (string)$this->request->post('user_id', ''); 
        $userinfo    = (array)$this->request->post('userinfo');

        if (empty($user_id) || empty($userinfo['nickname']) || empty($userinfo)) {
            return $this->response->error(40001, '参数错误!');
        }
        
        $default_profile = array(
            'realname'  => '', //真实姓名
            'nickname'  => '', //昵称
        );
    
        $default_ext_profile = array(
            'gender'        => '女',            //性别
            'birthday'      => '0000-00-00',    //生日
            'province_id'   => 0,               //省份
            'city_id'       => 0,               //城市
            'school'        => '',              //学校
            'workplace'     => '',              //工作单位
            'industry'      => 0,               //从事行业
            'hobby'         => '',              //爱好
            'weibo_url'     => '',              //微博地址
            'about_me'      => '',              //美丽宣言
        );
        
        $update     = array_intersect_key($userinfo, $default_profile);
        $ext_update = array_intersect_key($userinfo, $default_ext_profile);
        
        //检查用户名和email是否已注册 
        //$update['nickname'] = $update['nickname'] . rand(0,100000);
        $exists = array('nickname' => $update['nickname']);
        if ($is_exists = User::exists($exists)) {
            return $this->response->error(40003, '不好意思,该昵称已经被占用啦!');
        }
        
        $where  = array('user_id' => $user_id);

        if (!empty($update)) {
            $userinfos = User::update($where, $update);
        }
        
        if (!empty($ext_update)) {
            $ext_userinfos = UserExt::update($where, $ext_update);
        }
        
        $return = (int)($userinfos || $ext_userinfos);
        if ($return) {
            return $this->response->success($return);
        } else {
            return $this->response->error(40003, '貌似什么都没有修改~');
        }
    }

}
