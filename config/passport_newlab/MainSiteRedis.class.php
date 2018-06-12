<?php
/***************************************************************************
 *
 * Copyright (c) 2015 Meilishuo.com, Inc. All Rights Reserved
 *
 **************************************************************************/



/**
 * @file   config/passport_newlab/MainSiteRedis.class.php
 * @author 荣小龙(xiaolongrong@meilishuo.com)
 * @date   2015/12/22
 * @brief  主站redis配置
 **/

namespace Inauth\Config\Passport;

class MainSiteRedis extends \Inauth\Libs\Singleton {
    public function configs() {
        return array(
            'writeHost' => 'http://10.8.14.26/write',
            'xwriteHost' => 'http://10.8.14.26/xwrite',
            'readHosts' => array(
                'http://10.8.14.76/read',
                'http://10.8.14.81/read',
                'http://10.8.14.82/read',
                'http://10.8.14.83/read',
                'http://10.8.14.32/read',
                'http://10.8.14.33/read',
                'http://10.8.14.34/read',
                'http://10.8.14.35/read',
            )
        );
    }
}
