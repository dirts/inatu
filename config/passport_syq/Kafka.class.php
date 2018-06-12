<?php
namespace Inauth\Config\Passport;

class Kafka extends \Inauth\Libs\Singleton {
    public function configs() {
        return array(
            'servers' => array (
                    array('host' => '10.8.12.79', 'port' => '9200'),
                    array('host' => '10.8.12.80', 'port' => '9200'),
                    array('host' => '10.8.12.80', 'port' => '9200'),
            ),
            'failover_servers' => array(
                    array('host' => '10.8.13.23', 'port' => '9100'),
                    array('host' => '10.8.13.24', 'port' => '9100'),
                    array('host' => '10.8.13.25', 'port' => '9100'),
            ),
            'timeout_ms' => 500,
            'connect_timeout_ms' => 200,
            'retry_times' => 2,
        );
    }
}

