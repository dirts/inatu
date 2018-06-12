<?php
/***************************************************************************
 *
 * Copyright (c) 2015 Meilishuo.com, Inc. All Rights Reserved
 *
 **************************************************************************/



/**
 * @file    DUserProfileConnect.class.php
 * @author  荣小龙(xiaolongrong@meilishuo.com)
 * @date    2015/12/29
 * @brief   dugong库t_dolphin_user_profile_connect表
 *
 **/
namespace Inauth\Package\Connect\Dao;

use \Inauth\Libs\Dao;
use \Inauth\Libs\Db\Filter;

class DUserProfileConnect extends Dao {
    protected $_dbName    = 'dugong';                         // 设置连接的数据库名
    protected $_tableName = 't_dolphin_user_profile_connect'; // 设置连接的表名

    //设置表字段类型对应关系  Filter::TYPE_INT Filter::STRING
    protected $_checkMapping = array(
        'user_id' => array(
            Filter::PARAM_TYPE => Filter::TYPE_INT,
        ),
        'user_type' => array(
            Filter::PARAM_TYPE => Filter::TYPE_INT,
        ),
        'status' => array(
            Filter::PARAM_TYPE => Filter::TYPE_INT,
        ),
        'access' => array(
            Filter::PARAM_TYPE => Filter::TYPE_STRING,
        ),
        'auth' => array(
            Filter::PARAM_TYPE => Filter::TYPE_STRING,
        ),
        'ctime' => array(
            Filter::PARAM_TYPE => Filter::TYPE_STRING,
        ),
        'sync_goods' => array(
            Filter::PARAM_TYPE => Filter::TYPE_INT,
        ),
        'sync_collect' => array(
            Filter::PARAM_TYPE => Filter::TYPE_INT,
        ),
        'sync_like' => array(
            Filter::PARAM_TYPE => Filter::TYPE_INT,
        ),
        'sync_ask' => array(
            Filter::PARAM_TYPE => Filter::TYPE_INT,
        ),
        'sync_answer' => array(
            Filter::PARAM_TYPE => Filter::TYPE_INT,
        ),
        'sync_medal' => array(
            Filter::PARAM_TYPE => Filter::TYPE_INT,
        ),
    );

}
