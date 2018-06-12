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
                	array('host' => '10.8.8.98', 'port' => '6001'),
            ),
        );
    }
}
