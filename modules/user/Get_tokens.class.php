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

class Get_tokens extends \Frame\Module {
    
    public function run() {
        $user_id = (int)$this->request->request('user_id', '');
		 
        $res = SessionRelation::zRange($user_id, 0, -1);
      
        return $this->response->success($res);
         
    }
}
