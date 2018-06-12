<?php
namespace Inauth\Config\Passport;

class Memcache extends \Inauth\Libs\Singleton {

    public function configs() {
        return array(
            'unixsock' => array(
                array('host' => '/home/work/nutcracker/twemproxy1' , 'port' => '0'),
                array('host' => '/home/work/nutcracker/twemproxy1-1' , 'port' => '0'),
            ),
        );
    }
}
