<?php
namespace Inauth\Config\Passport;

class Redis extends \Inauth\Libs\Singleton {
    public function configs() {
        return array(
            'nutHosts' => array(
                array('host' => '10.8.0.94', 'port' => '6001'),
                array('host' => '10.8.0.95', 'port' => '6001'),
                array('host' => '10.8.0.96', 'port' => '6001'),
                array('host' => '10.8.0.97', 'port' => '6001'),
                array('host' => '10.8.0.30', 'port' => '6001'),
                array('host' => '10.8.0.31', 'port' => '6001'),
            ),
//		    'writeHost' => 'http://10.6.4.179:8081/write',
//		    'xwriteHost' => 'http://10.6.4.179:8081/xwrite',
//            'readHosts' => array(
//                'http://10.6.4.179:8081/read',
//            )
        );
    }
}
