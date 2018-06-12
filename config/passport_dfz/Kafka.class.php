<?php
namespace Inauth\Config\Passport;

class Kafka extends \Inauth\Libs\Singleton {
    public function configs() {
        return array(
            'servers' => array (
                    array('host' => '10.5.12.66', 'port' => '9100'),
                    array('host' => '10.5.12.75', 'port' => '9100'),
                    array('host' => '10.5.12.76', 'port' => '9100'),
            ),
            'failover_servers' => array(
                    array('host' => '10.5.13.52', 'port' => '9100'),
                    array('host' => '10.5.13.53', 'port' => '9100'),
                    array('host' => '10.5.13.54', 'port' => '9100'),
            ),
            'timeout_ms' => 500,
            'connect_timeout_ms' => 200,
            'retry_times' => 2,
        );
    }
}

