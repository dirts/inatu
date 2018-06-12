<?php
namespace Inauth\Config\Passport;

class Redis extends \Inauth\Libs\Singleton {
    public function configs() {
        return array(
            'nutHosts' => array(
                array('host' => '10.8.8.98','port' => '6001'),
                array('host' => '10.8.8.99','port' => '6001'),
                array('host' => '10.8.8.100','port' => '6001'),
                array('host' => '10.8.8.101','port' => '6001'),
                #array('host' => '10.6.5.11','port' => '6011'),
            ),
		    'writeHost' => 'http://10.6.4.179:8081/write',
		    'xwriteHost' => 'http://10.6.4.179:8081/xwrite',
            'readHosts' => array(
                'http://10.6.4.179:8081/read',
            )
        );
    }
}
