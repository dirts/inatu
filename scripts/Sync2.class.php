<?php
namespace Inauth\Scripts;
use \Inauth\Package\Sync\RedisSync;

class Sync2 extends \Frame\Script {
    static $queue = array('name'=>"redis", 'durable'=>false);

    public function run() {
        $prefix = 'dfz.syq';
        RedisSync::consume($prefix);


//        $prefix = 'gz.dfz';
//        RedisSync::consume($prefix);
//
//
//        $prefix = 'dfz.gz';
//        RedisSync::consume($prefix);
    }
}
