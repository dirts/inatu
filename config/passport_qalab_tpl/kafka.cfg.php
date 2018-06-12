<?php
/***************************************************************************
 *
 * Copyright (c) 2015 Meilishuo.com, Inc. All Rights Reserved
 *
 **************************************************************************/



/**
 * @file config/passport/kafka.cfg.php
 * @author 李守岩(shouyanli@meilishuo.com)
 * @date 2015/12/04
 * @brief  passport sdk session
 *
 **/

return array(
        'passport' => array(
            'servers' => array(
                'http://127.0.0.1:9090/',
                ),
            'failoverServers' => array(
                'http://127.0.0.1:9090/',
                ),
            ),
            'timeout_ms' => 500,
            'connect_timeout_ms' => 200,
            'retry_times' => 2,
        );
