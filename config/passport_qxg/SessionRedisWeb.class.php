<?php
namespace Inauth\Config\Passport;

class SessionRedisWeb extends \Inauth\Libs\Singleton {
    public function configs() {
        return array(
            'nutHosts' => array(
                	array('host' => '10.0.22.20', 'port' => '6061'),
                	array('host' => '10.0.22.21', 'port' => '6061'),
                	array('host' => '10.0.22.22', 'port' => '6061'),
                	array('host' => '10.0.22.23', 'port' => '6061'),
                	array('host' => '10.0.22.24', 'port' => '6061'),
                	array('host' => '10.0.22.25', 'port' => '6061'),
                	array('host' => '10.0.20.35', 'port' => '6061'),
                	array('host' => '10.0.20.36', 'port' => '6061'),
                	array('host' => '10.0.20.37', 'port' => '6061'),
            ),
        );
    }
}
