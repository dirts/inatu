<?php
namespace Inauth\Config\Passport;

class Redis extends \Inauth\Libs\Singleton {
    public function configs() {
        return array(
            'nutHosts' => array(
                array('host' => '10.5.8.15','port' => '6001'),
                array('host' => '10.5.8.17','port' => '6001'),
                array('host' => '10.5.8.19','port' => '6001'),
                array('host' => '10.5.8.21','port' => '6001'),
                array('host' => '10.5.8.23','port' => '6001'),
                array('host' => '10.5.8.25','port' => '6001'),
                array('host' => '10.5.8.27','port' => '6001'),
                array('host' => '10.5.8.35','port' => '6001'),
            ),
//		    'writeHost' => 'http://10.6.4.179:8081/write',
//		    'xwriteHost' => 'http://10.6.4.179:8081/xwrite',
//            'readHosts' => array(
//                'http://10.6.4.179:8081/read',
//            )
        );
    }
}
