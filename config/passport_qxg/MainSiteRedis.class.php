<?php
/***************************************************************************
 *
 * Copyright (c) 2015 Meilishuo.com, Inc. All Rights Reserved
 *
 **************************************************************************/



/**
 * @file   config/passport_qxg/MainSiteRedis.class.php
 * @author 荣小龙(xiaolongrong@meilishuo.com)
 * @date   2015/12/22
 * @brief  主站redis配置--qxg
 **/

namespace Inauth\Config\Passport;

class MainSiteRedis extends \Inauth\Libs\Singleton {
    public function configs() {
        return array(
            $this->writeHost = 'http://10.0.0.41/write',
            $this->xwriteHost = 'http://10.0.0.41/xwrite',
            $this->readHosts = array(
                'http://10.0.0.42/read',
                'http://10.0.0.43/read',
                'http://10.0.0.135/read',
                'http://10.0.0.45/read',
                'http://10.0.0.46/read',
                'http://10.0.0.47/read',
                'http://10.0.0.48/read',
                'http://10.0.0.49/read',
            )
        );
    }
}
