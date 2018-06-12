<?php
/***************************************************************************
 *
 * Copyright (c) 2016 Meilishuo.com, Inc. All Rights Reserved
 *
 **************************************************************************/



/**
 * @file    DUserMobile.class.php
 * @author  荣小龙(xiaolongrong@meilishuo.com)
 * @date    2016/01/14
 * @brief
 *
 **/
namespace Inauth\Package\User\Dao;

use \Inauth\Libs\Dao;
use \Inauth\Libs\Db\Filter;

class DUserMobile extends Dao {
    protected $_dbName    = 'dugong';    // 设置连接的数据库名
    protected $_tableName = 't_dolphin_user_mobile'; // 设置连接表名

    // 设置表字段类型对应关系  Filter::TYPE_INT Filter::STRING
    protected $_checkMapping = array(
        'user_id' => array(
            Filter::PARAM_TYPE => Filter::TYPE_INT,
        ),
        'mobile' => array(
            Filter::PARAM_TYPE => Filter::TYPE_STRING,
        ),
        'ctime' => array(
            Filter::PARAM_TYPE => Filter::TYPE_STRING,
        ),
        'country_code' => array(
            Filter::PARAM_TYPE => Filter::TYPE_STRING,
        ),
    );

}
