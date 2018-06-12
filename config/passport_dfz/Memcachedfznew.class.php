<?php
/**
 * Created by PhpStorm.
 * User: MLS
 * Date: 15/12/4
 * Time: 下午5:44
 */

namespace Inauth\Config\Passport;
class Memcachedfznew extends \Inauth\Libs\Singleton {

    public function configs() {
        return array(
            'unixsock' => array(
                array('host' => '/home/work/nutcracker/twemproxy5' , 'port' => '0'),
                array('host' => '/home/work/nutcracker/twemproxy6' , 'port' => '0'),
            ),
        );
    }
}
