<?php
namespace Inauth\Modules\Callback;

use \Inauth\Package\Mon\CallbackMon;

/**
 * 异步处理  Login -> mq(kafka) -> this(async) -> other 
 */
class User_register_callback extends \Frame\Module {

    public function run() {

        $param = array();
        $param['header']   = $this->request->request('header', array()); 
        $param['cookie']   = $this->request->request('cookie', array()); 
        $param['request']  = $this->request->request('request', array()); 
        $param['custom']   = $this->request->request('custom', array()); 
  
        if (empty($param))  {
            return $this->response->error(40001, '参数错误!');    
        }
           
        $datas = CallbackMon::get_callback_apis('user_register_callback');
    
        $res = array(); 
        foreach ($datas as $env => $item) {
            $res[$item['callback']] = $this->request->curl($item['remote'], $item['callback'], $param);
        }

        $this->response->success($res);
        
    }

}
