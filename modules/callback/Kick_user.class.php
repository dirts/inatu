<?php
/***************************************************************************
 *
 * Copyright (c) 2015 Meilishuo.com, Inc. All Rights Reserved
 *
 **************************************************************************/



/**
 * @file modules/callback/Kick_user.class.php
 * @author 李守岩(shouyanli@meilishuo.com)
 * @date 2015/12/02
 * @brief  passport sdk session
 *
 **/

namespace Inauth\Modules\Callback;

use \Inauth\Package\Session\Helper\SessionRelation;
use \Inauth\Package\Session\Helper\RedisSession;
use \Inauth\Package\Session\Helper\RedisSessionWeb;

class Kick_user extends \Frame\Module {
    
    public function run() {
        $user_id = (int)$this->request->request('user_id', 0); 
        $access_token = (string)$this->request->request('access_token', ''); 
        
        if (empty($user_id)) {
            return $this->response->error(40001, '参数错误');
        }       //$a = SessionRelation::zAdd($user_id, time(), rand(0,1). ":". md5(rand(0,999)));
        $res = SessionRelation::kick_user($user_id, $access_token);
        
   
        return $this->response->success($res);
       
         
    }
}
