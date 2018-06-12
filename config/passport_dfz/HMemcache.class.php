<?php

namespace Inauth\Config\Passport;


class HMemcache extends \Inauth\Libs\Singleton {

    public function configs() {
        return array(
          'unixsock' => array(
            array('host' => '/home/work/nutcracker/twemproxy3', 'port' => '0'),
            array('host' => '/home/work/nutcracker/twemproxy4', 'port' => '0'),
          ),
        );
    }

}
