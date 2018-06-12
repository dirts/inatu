<?php
namespace Inauth\Queue;

use \Inauth\Libs\Util\Daemon;
use \Inauth\Package\Task\Helper\RedisLoginHelper as Queue;
use \Inauth\Package\User\User;

/**
 * 获取用户信息
 */
class Test extends Daemon {
    

    public function run() {
        //$this->task();
        $this->begin(10); 
    }

    public function task() {
        $datas = Queue::pop('login');
        if (empty($datas)) {
            return;
        }
        
            error_log(var_export($datas, true), 3, '/home/work/xxaa.log');
        foreach ($datas as $data) {
            $a = User::update_login_times($data); 
            error_log(var_export($data. "\n" , true), 3, '/home/work/xxaa.log');
        }
        
    }

    public function push($data) {
        Queue::lPush('login', json_encode($data));
    }

}
