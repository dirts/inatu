<?php
namespace Inauth\Config\Passport;

class Kafka extends \Inauth\Libs\Singleton {
    public function configs() {
        return array(
            'servers' => array (
                    array('host' => '127.0.0.1', 'port' => '9090'),
            ),
            'failover_servers' => array(
                    array('host' => '127.0.0.1', 'port' => '9090'),
            ),
            'timeout_ms' => 500,
            'connect_timeout_ms' => 200,
            'retry_times' => 2,
        );
    }
}

