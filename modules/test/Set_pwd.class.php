<?php
/***************************************************************************
 *
 * Copyright (c) 2015 Meilishuo.com, Inc. All Rights Reserved
 *
 **************************************************************************/



/**
 * @file    Set_pwd.class.php
 * @author  李守岩(shouyanli@meilishuo.com)
 * @date    2016/02/29
 * @brief   \inauth\modules\test
 *
 **/
 
namespace Inauth\Modules\Test;
 
//use \inauth\modules\test;
use \Inauth\Package\User\User;
use \Inauth\Package\Util\Utilities as U;
 
class Set_pwd  extends \Frame\Module{
 
    public  function run () {
        
        $user_id = (int)$this->request->post('user_id', 0);
        $new_pwd = (string)$this->request->post('new_password', '');
       
        if (empty($user_id) || empty($new_pwd)) {
            return $this->response->error(40001, '参数错误');   
        }

        $where = array('user_id' => $user_id);
        $update = array('password' => md5($new_pwd));
        $res = User::update($where, $update); 

        U::log('passport.reset_pwd', json_encode(array('user_id'=>$user_id, $res)));
        return $this->response->success($res);
    }
}
 
/* vim: set expandtab ts=4 sw=4 sts=4 tw=100: */

