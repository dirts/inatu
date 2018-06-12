<?php
/***************************************************************************
 *
 * Copyright (c) 2015 Meilishuo.com, Inc. All Rights Reserved
 *
 **************************************************************************/



/**
 * @file   config/passport_qalab_tpl/MainSiteRedis.class.php
 * @author 荣小龙(xiaolongrong@meilishuo.com)
 * @date   2015/12/22
 * @brief  主站redis配置
 **/

namespace Inauth\Config\Passport;

class MainSiteRedis extends \Inauth\Libs\Singleton {
    public function configs() {
        return array(
            'writeHost' => 'http://10.8.8.95:8081/write',
            'xwriteHost' => 'http://10.8.8.95:8081/xwrite',
            'readHosts' => array(
                'http://10.8.8.95:8081/read',
            )
        );
    }
}
