<?php
/***************************************************************************
 *
 * Copyright (c) 2015 Meilishuo.com, Inc. All Rights Reserved
 *
 **************************************************************************/



/**
 * @file    DUserProfile.class.php
 * @author  李守岩(shouyanli@meilishuo.com)
 * @date    2015/12/30
 * @brief   
 *
 **/
namespace Inauth\Package\User\Dao;

use \Inauth\Libs\Dao;
use \Inauth\Libs\Db\Filter;

class DUserProfile extends Dao {
    protected $_dbName    = 'dugong';    // 设置连接的数据库名
    protected $_tableName = 't_dolphin_user_profile'; // 设置连接表名

    // 设置表字段类型对应关系  Filter::TYPE_INT Filter::STRING
    protected $_checkMapping = array(
        'user_id' => array(
            Filter::PARAM_TYPE => Filter::TYPE_INT,
            ),
        'nickname' => array(
            Filter::PARAM_TYPE => Filter::TYPE_STRING,
            ),
        'email' => array(
            Filter::PARAM_TYPE => Filter::TYPE_STRING,
            ),
        'ctime' => array(
            Filter::PARAM_TYPE => Filter::TYPE_STRING,
            ),
        'password' => array(
            Filter::PARAM_TYPE => Filter::TYPE_STRING,
            ),
        'cookie' => array(
            Filter::PARAM_TYPE => Filter::TYPE_STRING,
            ),
        'is_actived' => array(
            Filter::PARAM_TYPE => Filter::TYPE_INT,
            ),
        'invite_code' => array(
            Filter::PARAM_TYPE => Filter::TYPE_STRING,
            ),
        'last_logindate' => array(
            Filter::PARAM_TYPE => Filter::TYPE_STRING,
            ),
        'status' => array(
            Filter::PARAM_TYPE => Filter::TYPE_INT,
            ),
        'realname' => array(
            Filter::PARAM_TYPE => Filter::TYPE_STRING,
            ),
        'istested' => array(
            Filter::PARAM_TYPE => Filter::TYPE_INT,
            ),
        'reg_from' => array(
            Filter::PARAM_TYPE => Filter::TYPE_STRING,
            ),
        'last_email_time' => array(
            Filter::PARAM_TYPE => Filter::TYPE_STRING,
            ),
        'level' => array(
            Filter::PARAM_TYPE => Filter::TYPE_INT,
            ),
        'isPrompt' => array(
            Filter::PARAM_TYPE => Filter::TYPE_INT,
            ),
        'isBusiness' => array(
            Filter::PARAM_TYPE => Filter::TYPE_INT,
            ),
        'login_times' => array(
            Filter::PARAM_TYPE => Filter::TYPE_STRING,
            ),
    );

}
