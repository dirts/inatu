<?php
/***************************************************************************
 *
 * Copyright (c) 2016 Meilishuo.com, Inc. All Rights Reserved
 *
 **************************************************************************/



/**
 * @file   IDCCheck.class.php
 * @author 荣小龙(xiaolongrong@meilishuo.com)
 * @date   2016/12/02
 * @brief  passport-sdk 标识当前机房：qxg
 **/

namespace Inauth\Config\Passport;

class IDCCheck extends \Inauth\Libs\Singleton {
    public function configs() {
        return array(
            'idc' => 'qxg',
        );
    }
}
