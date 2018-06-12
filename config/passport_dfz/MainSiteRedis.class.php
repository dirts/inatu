<?php
/***************************************************************************
 *
 * Copyright (c) 2015 Meilishuo.com, Inc. All Rights Reserved
 *
 **************************************************************************/



/**
 * @file   config/passport_dfz/MainSiteRedis.class.php
 * @author 荣小龙(xiaolongrong@meilishuo.com)
 * @date   2015/12/22
 * @brief  主站redis配置--dfz
 **/

namespace Inauth\Config\Passport;

class MainSiteRedis extends \Inauth\Libs\Singleton {
    public function configs() {
        return array(
            $this->writeHost = 'http://10.5.0.63/xwrite',
            $this->xwriteHost = 'http://10.5.0.63/xwrite',
            $this->readHosts = array(
                'http://10.5.0.63/read',
                'http://10.5.0.83/read',
            )
        );
    }
}
