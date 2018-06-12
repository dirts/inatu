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
 * @brief   kafka 配置文件(syq)
 *
 **/
return array(
        'passport' => array(
            'servers' => array(
                'http://10.8.12.79:9200',
                'http://10.8.12.80:9200',
                'http://10.8.12.81:9200',
             ),
            'timeout_ms'    => 500,
            'connect_timeout_ms'    => 200,
            'retry_times'   => 2,
            ),
        );
