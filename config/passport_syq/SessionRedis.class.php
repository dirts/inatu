<?php
namespace Inauth\Config\Passport;

class SessionRedis extends \Inauth\Libs\Singleton {
    public function configs() {
        return array(
            'nutHosts' => array(
		    array('host' => '10.8.0.94', 'port' => '6051'),
		    array('host' => '10.8.0.95', 'port' => '6051'),
		    array('host' => '10.8.0.96', 'port' => '6051'),
		    array('host' => '10.8.0.97', 'port' => '6051'),
		    array('host' => '10.8.0.30', 'port' => '6051'),
		    array('host' => '10.8.0.31', 'port' => '6051'),
		    array('host' => '10.8.5.36', 'port' => '6051'),
		    array('host' => '10.8.5.52', 'port' => '6051'),
		    array('host' => '10.8.5.51', 'port' => '6051'),
            ),
        );
    }
}
