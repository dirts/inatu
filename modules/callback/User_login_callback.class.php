<?php
/***************************************************************************
 *
 * Copyright (c) 2015 Meilishuo.com, Inc. All Rights Reserved
 *
 **************************************************************************/



/**
 * @file modules/callback/User_login_callback.class.php
 * @author 李守岩(shouyanli@meilishuo.com)
 * @date 2015/12/14
 * @brief  passport sdk session
 *
 **/

namespace Inauth\Modules\Callback;

use \Inauth\Package\Mon\CallbackMon;

/**
 * 异步处理  Login -> mq(kafka) -> this(async) -> other 
 */
class User_login_callback extends \Frame\Module {

    public function run() {

        $param = array();
        $param['header']   = $this->request->request('header', array()); 
        $param['cookie']   = $this->request->request('cookie', array()); 
        $param['request']  = $this->request->request('request', array()); 
        $param['custom']   = $this->request->request('custom', array()); 

        $this->response->success(array());
        
    }

}
