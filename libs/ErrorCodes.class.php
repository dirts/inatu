<?php

/***************************************************************************
 *
 * Copyright (c) 2015 Meilishuo.com, Inc. All Rights Reserved
 *
 **************************************************************************/



/**
 * @file   ErrorCodes.class.php
 * @author CHEN Yijie(yijiechen@meilishuo.com)
 * @date   2015/12/29
 * @brief  错误码及错误信息管理类 
 *
 **/

namespace Inauth\Libs;

class ErrorCodes {
    /* Basic  */
    const PARAM_ERROR                   = 00001;
    const RAL_CALL_ERROR                = 00002;
    const NOT_FIND                      = 00003;
    const TIME_ERROR                    = 00004;
    const DB_ERROR                      = 00005;
    const INVALID_SQL                   = 00006;
    const QUERY_ERROR                   = 00007;
    const MQ_COMMIT_ERROR               = 00008;
    const DB_PARAM_ERROR                = 00009;
    const SERVICE_AUTHORIZE_INVALID     = 00010;

    const LOGIN_PARAM_ERROR             = 1000;
    const API_LOGIN_PASS_ERROR          = 1001;
    const API_MOBLE_EXIST_ERROR         = 1003;
    const NEED_REACTIVE                 = 1007;
    const ACCOUNT_FROZEN                = 1008;
    const PERMISSION_DENIED             = 1009;
    const SERVICE_ERROR                 = 1010;

    /* Session 1 */

    /* User    2 */
    const USER_NOT_EXISTS               = 20001;
    const USER_NICK_NOT_EXISTS          = 20002;
    const USER_MAIL_NOT_EXISTS          = 20003;
    const USER_MOBILE_NOT_EXISTS        = 20004;
    const USER_UID_NOT_EXISTS           = 20005;
     
    const PASSWORD_ERROR                = 20101;
    const NEW_PASSOWRD_ERROR            = 20102;
    const NEW_PASSOWRD_SAME_AS_OLD      = 20103;
    const CONFIRM_PASSOWRD_ERROR        = 20103;

    /* Connect 3 */
    const EMPTY_AUTH_CACHE              = 30001;
    const CONNECT_UID_LOST              = 30002;
    const CONNECT_USER_NOT_FIND         = 30003;

    /* Risk    4 */
    const NO_RECORD_IP                  = 40001;

    /***
     * 记录错误码对应的基础错误信息
     **/
    public static $codes = array(
        00001     => 'Param Error', /* 输入参数错误 */
        00002     => 'Remote Service Call Failed',/* 后端服务调用失败  */
        00003     => 'NOT FINDED',/* 未找到对应记录 */
        00004     => 'time set error',/*时间设置错误*/
        00005     => 'Db Error, Please Retry Later',/*数据库出错请稍后重试*/
        00006     => 'Invalid SQL cannot be Executed',/*非法SQL语句无法被执行*/
        00007     => 'DB Query Error',/*数据库查询出错*/
        00008     => 'MQ commit error',/*提交消息队列失败*/
        00009     => 'DB SQL fields Param Error',/*拼接SQL的参数错误*/
        00010     => 'Invalid service visit',/*未授权的非法访问*/

        1000      => 'Login param error',/* 登录参数错误 */
        1001      => 'User Login Password Failed',/* 数据库中未找到对应记录 */
        1003      => 'User Login Mobile not exists',/* 数据库中未找到对应记录 */
        1007      => 'Need reactive',/* 用户需要短信验证码再激活 */
        1008      => 'User has been frozen',/* 您的账户处于受限状态，请发送邮件到：check@meilishuo.com 申请解封 */
        1009      => 'Permission denied',/* mob端用户受限 */
        1010      => 'Service error',/* 亲出了点小状况，请稍后再试! */
         
        20001     => 'User not exists',       /* 用户不存在(通用)*/
        20002     => 'User nick not exists',  /* 用户昵称不存在*/
        20003     => 'User mail exists',      /* 用户邮箱不存在*/
        20004     => 'User mobile exists',    /* 用户手机号不存在*/
        20005     => 'User uid not exists',   /* 用户uid不存在*/

        20101     => 'Password Error',        /* 密码错误*/
        20102     => 'New Password Error',    /* 新密码错误*/
        20103     => 'New Password can not be same as Old Password', /* 新老密码不可以一致*/
        20104     => 'Confirm Password must be same as New Password',/* 确认的密码必须和新密码一致*/

        30001     => 'Empty auth cache',      /* 互联auth缓存信息为空 */
        30002     => 'Connect user_id lost',  /* 互联auth对应的user_id丢了 */
        30003     => 'Connect user not find', /* 互联auth没有绑定的美丽说帐号 */

        40001     => 'No record ip', /* 地址库没有收录该ip */
    );

    /***
     * 直接根据错误码返回标准的结果
     *
     * @param int      $error_code          错误码 此值只支持传入在本类中定义的错误码常量
     * @param string   $errmsg              详细的错误信息,拼接在基础错误信息之后
     * @return data
     *                 array 始终返回标准格式的错误结果
     **/
    public static function getErrorResult($error_code, $errmsg = '') {
        $message = self::$codes[$error_code] . ' ' . $errmsg;

        $result = array('error_code' => $error_code,'message' => $message,'data' => NULL);

        return $result;
    }

    /***
     * 直接根据错误码返回标准的错误字符串
     *
     * @param int      $error_code          错误码 此值只支持传入在本类中定义的错误码常量
     * @param string   $errmsg              详细的错误信息,拼接在基础错误信息之后
     * @return data
     *                 string 返回标准格式的错误信息
     **/
    public static function getErrorMessage($error_code, $errmsg = '') {
        $message = self::$codes[$error_code] . ' ' . $errmsg;
        return $message;
    }

    /***
     * 返回标准的正确结果
     *
     * @param  array  $data   正确的结果数据
     * @return result array   标准格式的正确结果
     **/
    public static function getSuccessResult($data = array()) {
        $result = array('error_code' => 0,'message' => 'success', 'data' => $data);
        return $result;
    }
}

/* vim: set expandtab ts=4 sw=4 sts=4 tw=100: */
