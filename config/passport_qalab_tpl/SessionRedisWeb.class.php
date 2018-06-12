<?php
namespace Inauth\Config\Passport;

class SessionRedisWeb extends \Inauth\Libs\Singleton {
    public function configs() {
        return array(
            'nutHosts' => array(
                	array('host' => '10.8.8.98', 'port' => '6001'),
            ),
        );
    }
}
