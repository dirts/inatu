<?php
namespace Inauth\Config\Passport;

class Redis extends \Inauth\Libs\Singleton {
    public function configs() {
        return array(
            'nutHosts' => array(
                array('host' => '10.0.22.20', 'port' => '6001'),
                array('host' => '10.0.22.21', 'port' => '6001'),
                array('host' => '10.0.22.22', 'port' => '6001'),
                array('host' => '10.0.22.23', 'port' => '6001'),
                array('host' => '10.0.22.24', 'port' => '6001'),
                array('host' => '10.0.22.25', 'port' => '6001'),
            ),
//		    'writeHost' => 'http://10.6.4.179:8081/write',
//		    'xwriteHost' => 'http://10.6.4.179:8081/xwrite',
//            'readHosts' => array(
//                'http://10.6.4.179:8081/read',
//            )
        );
    }
}
