<?php
namespace Inauth\Modules\Test;

use \Inauth\Package\Sync\RedisSync;


/**
 * 获取用户信息
 */
class Test extends \Frame\Module {


    public function run() {
        $op = 'kkkkkkkkk';
        $key = '6666666666666';
        $session_id = 'fasdfasdfasdfads';
        $a = '1b';
        $b = '4ddd';
        $hello = RedisSync::sync_session($op, $key, $session_id, $a, $b);
        var_dump($hello);
        echo '2';
    }

}

