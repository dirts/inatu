<?php
/***************************************************************************
 *
 * Copyright (c) 2015 Meilishuo.com, Inc. All Rights Reserved
 *
 **************************************************************************/



/**
 * @file    Delay_for_im.class.php
 * @author  李守岩(shouyanli@meilishuo.com)
 * @date    2016/03/25
 * @brief   \inauth\modules\session
 *
 **/

namespace Inauth\Modules\Session;

use \Inauth\Package\Session\Helper\RedisSessionWeb;
use \Inauth\Package\User\User;

class Delay_for_im extends \Frame\Module {

    public function run () {

        $session_id    = $this->request->request('session_id', '');
        $meilishuo_mm  = $this->request->request('meilishuo_mm', '');
        $ogrin_sign   = (string)$this->request->request('signature', '');

        $param        = $this->request->REQUEST;

        $sign         = $this->sign_comparison($ogrin_sign, $param);
        
        if (abs($this->request->time - $this->request->request('time')) > 300 ) {
            return $this->response->error('40003', '时间戳异常');
        } 

        if (empty($sign)) {
            return $this->response->error('40003', '拒绝服务');
        }
        
        $sessionData  = RedisSessionWeb::get_session_data($session_id);
        $sessionData['last_active_time'] = time();

        if (empty($sessionData['keyid'])) {
            $user_infos = User::query(array('cookie'=> $meilishuo_mm), '*', false, 'user_id');
            
            $count = count($user_infos);
            if ($count != 1) {
                return $this->response->error('40006', "$meilishuo_mm : $count");
            }
            
            foreach ($user_infos as $user_id => $userinfo) {
                $sessionData['keyid']                  = $user_id;
                $sessionData['session_data']['user_id'] = $user_id;
            }
        }
             
        $res  = (int)RedisSessionWeb::set_session_data($session_id, $sessionData, 3600 * 4);

        if (empty($res)) {
            return $this->response->error(40005, '更新失败');
        }
        return $this->response->success($res);
    }

    public function sign_comparison($sign, $params){
        unset($params['signature']);

        $sign_str = $this->sign_encode($params, 'MLS#PASS#iminfo');
        return $sign == $sign_str;
    }

    public function sign_encode($params, $secret) {
        $str = '';  //待签名字符串
        if(empty($params) && !is_array($params)) return $str;
        //先将参数以其参数名的字典序升序进行排序
        ksort($params);
        //遍历排序后的参数数组中的每一个key/value对
        foreach ($params as $k => $v) {
            //为key/value对生成一个key=value格式的字符串，并拼接到待签名字符串后面
            $str .= "$k=$v";
        }
        //将签名密钥拼接到签名字符串最后面
        $str .= $secret;
        //通过md5算法为签名字符串生成一个md5签名，该签名就是我们要追加的sign参数值
        return md5($str);

    }


}

/* vim: set expandtab ts=4 sw=4 sts=4 tw=100: */
