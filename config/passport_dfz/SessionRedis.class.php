<?php
namespace Inauth\Config\Passport;

class SessionRedis extends \Inauth\Libs\Singleton {
    public function configs() {
        return array(
            'nutHosts' => array(
                	array('host' => '10.5.8.15', 'port' => '6051'),
                	array('host' => '10.5.8.17', 'port' => '6051'),
                	array('host' => '10.5.8.19', 'port' => '6051'),
                	array('host' => '10.5.8.21', 'port' => '6051'),
                	array('host' => '10.5.8.23', 'port' => '6051'),
                	array('host' => '10.5.8.25', 'port' => '6051'),
                	array('host' => '10.5.8.27', 'port' => '6051'),
                	array('host' => '10.5.8.35', 'port' => '6051'),
            ),
        );
    }
}
