<?php
/***************************************************************************
 *
 * Copyright (c) 2015 Meilishuo.com, Inc. All Rights Reserved
 *
 **************************************************************************/



/**
 * @file modules/user/Kick_user.class.php
 * @author 李守岩(shouyanli@meilishuo.com)
 * @date 2015/12/02
 * @brief  kick user
 **/

namespace Inauth\Modules\User;

use \Inauth\Package\Session\Helper\SessionRelation;
use \Inauth\Package\Session\Helper\RedisSession;
use \Inauth\Package\Session\Helper\RedisSessionWeb;

class Kick_user extends \Frame\Module {
    
    public function run() {
        $user_ids = (string)$this->request->request('user_ids', ''); 
        
        $app_id     = (int)$this->request->request('app_id', 0);
        $app_key    = (string)$this->request->request('app_key', '');
       
        if (empty($app_id) || empty($app_key)) {
            return $this->response->error(40001, '未授权');
        }
         
        $user_ids = explode(',', $user_ids);

        if (count($user_ids) > 100) {
            return $this->response->error(40001, '参数错误');
        }

        if (empty($user_ids)) {
            return $this->response->error(40001, '参数错误');
        }

        $res = array();
        foreach ($user_ids as $user_id) {
            $res[$user_id] = SessionRelation::kick_user($user_id);
        }

        return $this->response->success($res);
         
    }
}
