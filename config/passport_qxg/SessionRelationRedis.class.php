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
                	array('host' => '10.0.22.20', 'port' => '6101'),
                	array('host' => '10.0.22.21', 'port' => '6101'),
                	array('host' => '10.0.22.22', 'port' => '6101'),
                	array('host' => '10.0.22.23', 'port' => '6101'),
                	array('host' => '10.0.22.24', 'port' => '6101'),
                	array('host' => '10.0.22.25', 'port' => '6101'),
                	array('host' => '10.0.20.35', 'port' => '6101'),
                	array('host' => '10.0.20.36', 'port' => '6101'),
                	array('host' => '10.0.20.37', 'port' => '6101'),
            ),
        );
    }
}
