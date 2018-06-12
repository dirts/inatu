<?php
namespace Inauth\Scripts;
use \Inauth\Package\Sync\RedisSync;

class Sync extends \Frame\Script {
    static $queue = array('name'=>"redis", 'durable'=>false);

    public function run() {
        $prefix = 'gz.syq';
        RedisSync::consume($prefix);


//        $prefix = 'syq.dfz';
//        RedisSync::consume($prefix);
//
//
//        $prefix = 'syq.gz';
//        RedisSync::consume($prefix);

    }
}
