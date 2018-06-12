<?php
/***************************************************************************
 *
 * Copyright (c) 2015 Meilishuo.com, Inc. All Rights Reserved
 *
 **************************************************************************/



/**
 * @file   ISQL.class.php
 * @author CHEN Yijie(yijiechen@meilishuo.com)
 * @date   2015/09/01 16:16:03
 * @brief  SQL拼接器接口类 
 *
 **/

namespace Inauth\Libs\Db;

interface ISQL
{
    // return SQL text or false on error
    public function getSQL();
}

/* vim: set expandtab ts=4 sw=4 sts=4 tw=100: */
