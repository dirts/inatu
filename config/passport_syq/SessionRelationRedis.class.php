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
                	array('host' => '10.8.0.94', 'port' => '6101'),
                	array('host' => '10.8.0.95', 'port' => '6101'),
                	array('host' => '10.8.0.96', 'port' => '6101'),
                	array('host' => '10.8.0.97', 'port' => '6101'),
                	array('host' => '10.8.0.30', 'port' => '6101'),
                	array('host' => '10.8.0.31', 'port' => '6101'),
                	array('host' => '10.8.5.36', 'port' => '6101'),
                	array('host' => '10.8.5.52', 'port' => '6101'),
                	array('host' => '10.8.5.51', 'port' => '6101'),
            ),
        );
    }
}
