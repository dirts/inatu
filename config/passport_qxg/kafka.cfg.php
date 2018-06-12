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
 * @brief   kafka 配置文件
 *
 **/

return array(
        'passport' => array(
            'servers' => array(
                'http://10.0.18.47:9200',
                'http://10.0.18.48:9200',
                'http://10.0.18.49:9200',
            ),
            'timeout_ms' => 500,
            'connect_timeout_ms' => 200,
            'retry_times' => 2,
        ),
    );
