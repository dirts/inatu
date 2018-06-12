<?php
/***************************************************************************
 *
 * Copyright (c) 2015 Meilishuo.com, Inc. All Rights Reserved
 *
 **************************************************************************/



/**
 * @file config/passport_qa_lab/SessionRelationRedis.class.php
 * @author 李守岩(shouyanli@meilishuo.com)
 * @date 2015/12/02
 * @brief  passport sdk session
 **/

namespace Inauth\Config\Passport;

class SessionRelationRedis extends \Inauth\Libs\Singleton {
    public function configs() {
        return array(
            'nutHosts' => array(
                	array('host' => '10.5.8.15', 'port' => '6101'),
                	array('host' => '10.5.8.17', 'port' => '6101'),
                	array('host' => '10.5.8.19', 'port' => '6101'),
                	array('host' => '10.5.8.21', 'port' => '6101'),
                	array('host' => '10.5.8.23', 'port' => '6101'),
                	array('host' => '10.5.8.25', 'port' => '6101'),
                	array('host' => '10.5.8.27', 'port' => '6101'),
                	array('host' => '10.5.8.35', 'port' => '6101'),
            ),
        );
    }
}
