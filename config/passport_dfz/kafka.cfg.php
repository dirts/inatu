<?php
/***************************************************************************
 *
 * Copyright (c) 2015 Meilishuo.com, Inc. All Rights Reserved
 *
 **************************************************************************/



/**
 * @file    kafka.cfg.php
 * @author  李守岩(shouyanli@meilishuo.com)
 * @date    2015/12/04
 * @brief   kafka 配置文件(dfz)
 *
 **/

return array(
        'passport' => array(
            'servers' => array(
                'http://10.5.12.66:9100',
                'http://10.5.12.75:9100',
                'http://10.5.12.76:9100',
             ),
            'timeout_ms'    => 500,
            'connect_timeout_ms'    => 200,
            'retry_times'   => 2,
            ),
        );
