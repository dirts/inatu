<?php
namespace Inauth\Config\Passport;
class Scribe extends \Inauth\Libs\Singleton
{
    public function configs() {
        return array(
            'nodes' => array(
                array('host' => '127.0.0.1' , 'port' => '1463'),
            )
        );
    }
}
