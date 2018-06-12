<?php
/***************************************************************************
 *
 * Copyright (c) 2015 Meilishuo.com, Inc. All Rights Reserved
 *
 **************************************************************************/



/**
 * @file modules/callback/Session_relation_limit.class.php
 * @author 李守岩(shouyanli@meilishuo.com)
 * @date 2015/12/03
 * @brief  passport sdk session
 *
 **/

namespace Inauth\Modules\Callback;

use \Inauth\Package\Session\Helper\SessionRelation;
use \Inauth\Package\Session\Helper\RedisSession;
use \Inauth\Package\Session\Helper\RedisSessionWeb;

class Session_relation_limit extends \Frame\Module {
    
    public function run() {
        $user_id    = (int)$this->request->request('user_id', 0); 
        $session_id = (string)$this->request->request('session_id', ''); 
        
        if (empty($user_id) || empty($session_id)) {
            //return $this->response->error(40001, '参数错误');
        } 
        
        $res = SessionRelation::relation_limit($user_id, $session_id);
        return $this->response->success($res);
         
    }
}
