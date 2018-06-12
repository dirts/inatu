<?php
namespace Inauth\Config\Passport;

class Kafka extends \Inauth\Libs\Singleton {
    public function configs() {
        return array(
            'servers' => array (
                    array('host' => '10.0.18.47', 'port' => '9200'),
                    array('host' => '10.0.18.48', 'port' => '9200'),
                    array('host' => '10.0.18.49', 'port' => '9200'),
            ),
            'failover_servers' => array(
                    array('host' => '10.0.28.48', 'port' => '9100'),
                    array('host' => '10.0.6.68', 'port' => '9100'),
                    array('host' => '10.0.6.69', 'port' => '9100'),
            ),
            'timeout_ms' => 500,
            'connect_timeout_ms' => 200,
            'retry_times' => 2,
        );
    }
}

