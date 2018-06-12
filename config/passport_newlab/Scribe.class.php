<?php

/***************************************************************************
 *
 * Copyright (c) 2016 Meilishuo.com, Inc. All Rights Reserved
 *
 **************************************************************************/



/**
 * @file    Scribe.class.php
 * @author  荣小龙(xiaolongrong@meilishuo.com)
 * @date    2016/01/13
 * @brief   passport-Scribe配置：newlab
 *
 **/
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