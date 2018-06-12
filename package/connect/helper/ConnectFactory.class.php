<?php
/***************************************************************************
 *
 * Copyright (c) 2015 Meilishuo.com, Inc. All Rights Reserved
 *
 **************************************************************************/



/**
 * @file   ConnectFactory.class.php
 * @author 荣小龙(xiaolongrong@meilishuo.com)
 * @date   2015/12/28
 * @brief  passport-sdk-connect 互联工具集合类
 *
 **/

namespace Inauth\Package\Connect\Helper;

use \Libs\Cache\Memcache;  //passport-memcache
use \Inauth\Libs\Constant;
use \Inauth\Libs\ErrorCodes;
use \Inauth\Package\User\User;
use \Inauth\Package\Util\Utilities;
use \Inauth\Package\User\UserMobile;
use \Inauth\Package\User\Dao\DUserMobile;
use \Inauth\Package\User\Dao\DUserProfile;
use \Inauth\Package\Connect\Dao\DUserProfileConnect;

class ConnectFactory {

    //支持的第三方互联渠道:web后缀为PC网页入口，client后缀为客户端入口，mall后缀为商城
    public static $channel_type_list = array(
        'renren_web'            => 1,   //人人网帐号互联，入口：PC网页
        'weibo_web'             => 3,   //新浪微博帐号互联，入口：PC网页
        'weibo_client'          => 3,   //新浪微博帐号互联，入口：客户端
        'qzone_mall'            => 4,   //QQ互联，入口：手Q商城
        'qzone_web'             => 4,   //QQ互联，入口：PC网页
        'qzone_sdk_client'      => 4,   //QQ互联，入口：客户端，通过sdk授权
        'qzone_client'          => 4,   //QQ互联，入口：客户端，跳转授权authorize_url授权
        'baidu_web'             => 5,   //百度帐号互联，入口：PC网页
        'taobao'                => 6,   //淘宝帐号互联，入口：PC网页，已下线
        'wangyi'                => 7,   //网易帐号互联，已下线
        'txweibo_web'           => 8,   //腾讯微博帐号互联，入口：PC网页
        'txweibo_client'        => 8,   //腾讯微博帐号互联，入口：客户端
        'mtaobao'               => 9,   //淘宝帐号互联，入口：手机wap页，已下线
        'douban_web'            => 10,  //豆瓣帐号互联，入口：PC网页
        'qplus'                 => 11,  //腾讯开放平台，已下线
        'weixinfriend'          => 12,  //待产品确认
        'mtaobao_client'        => 13,  //淘宝帐号互联，入口：客户端，已下线
        'mtaobao_client_ipad'   => 14,  //淘宝帐号互联，入口：ipad客户端，已下线
        'weixin_mall'           => 15,  //微信互联，入口：微信商城
        'weixin_web'            => 15,  //微信互联，入口：PC网页
        'weixin_sdk_client'     => 15,  //微信互联，入口：客户端，通过sdk授权
        'fanli'                 => 3000,//返利网互联，入口：PC网页
    );

    //互联渠道对应的类名称
    public static $channel_class_list = array(
        'renren_web'            => 'RenrenWeb',          //人人网帐号互联，入口：PC网页
        'weibo_web'             => 'WeiboWeb',           //新浪微博帐号互联，入口：PC网页
        'weibo_client'          => 'WeiboClient',        //新浪微博帐号互联，入口：客户端
        'qzone_mall'            => 'QzoneMall',          //QQ互联，入口：手Q商城
        'qzone_web'             => 'QzoneWeb',           //QQ互联，入口：PC网页
        'qzone_sdk_client'      => 'QzoneSdkClient',     //QQ互联，入口：客户端，通过sdk授权
        'qzone_client'          => 'QzoneClient',        //QQ互联，入口：客户端，跳转授权authorize_url授权
        'baidu_web'             => 'BaiduWeb',           //百度帐号互联，入口：PC网页
        'taobao'                => 'Taobao',             //淘宝帐号互联，入口：PC网页，已下线
        'wangyi'                => 'Wangyi',             //网易帐号互联，已下线
        'txweibo_web'           => 'TxweiboWeb',         //腾讯微博帐号互联，入口：PC网页
        'txweibo_client'        => 'TxweiboClient',      //腾讯微博帐号互联，入口：客户端
        'mtaobao'               => 'Mtaobao',            //淘宝帐号互联，入口：手机wap页，已下线
        'douban_web'            => 'DoubanWeb',          //豆瓣帐号互联，入口：PC网页
        'qplus'                 => 'Qplus',              //腾讯开放平台，已下线
        'weixinfriend'          => 'WeixinFriend',       //待产品确认
        'mtaobao_client'        => 'MtaobaoClient',      //淘宝帐号互联，入口：客户端，已下线
        'mtaobao_client_ipad'   => 'MtaobaoClientIpad',  //淘宝帐号互联，入口：ipad客户端，已下线
        'weixin_mall'           => 'WeixinMall',         //微信互联，入口：微信商城
        'weixin_web'            => 'WeixinWeb',          //微信互联，入口：PC网页
        'weixin_sdk_client'     => 'WeixinSdkClient',    //微信互联，入口：客户端，通过sdk授权
        'fanli'                 => 'FanliWeb',           //返利网互联，入口：PC网页
    );

    public static $connect_table = 't_dolphin_user_profile_connect';  //互联信息表，dugong库
    public static $auth_cache_time = 300;                             //授权auth信息缓存过期时间
    private static $connect_class = '';                               //互联子类

    function __construct ($channel) {
        self::$connect_class = self::get_connect_class($channel);
    }

    public static function get_connect_class($channel){
        $class = self::$channel_class_list[$channel];
        if (empty($channel) || empty($class)) {
            return ErrorCodes::getErrorResult(ErrorCodes::PARAM_ERROR);
        }
        $point = strpos($channel,'_');
        if ($point) {
            $package = substr($channel, 0, $point);
        } else {
            $package = $channel;
        }
        return __NAMESPACE__ . "\\$package\\$class";
    }

    public static function check_channel ($channel) {
        if( empty($channel) || !isset(self::$channel_type_list[$channel])) {
            return FALSE;
        }
        return TRUE;
    }

    public function connect_login($type, $user_id, $params = array()) {
        $connect_obj = new $this->connect_class;
        $method = 'connect_login';
        $result = $connect_obj->$method($user_id, $params);
        return $result;
    }

    /**
     * @param $type String for example, 'weibo', 'qzone'; args[0] <br/>
     * @param $params array
     * 包括httpRequest信息，包括oauth_code,santorini_mm等信息<br/>
     */
    /***
     * 构造第三方授权url
     *
     * @param  string      $channel     互联渠道
     *         array       $ext_params  拓展参数
     * @return data
     *                     FALSE        参数错误
     *                     array        成功时返回第三方授权url
     **/
    public function getAuthorizeUrl($params = array()) {
        $connect_obj = new $this->connect_class;
        $method = 'get_authorize_url';
        $result = $connect_obj->$method($params);
        return $result;
    }

    public function get_access_token($params = array()) {
        $connect_obj = new $this->connect_class;
        $method = 'get_access_token';
        $result = $connect_obj->$method($params);
        return $result;
    }

    public function login_fail($params = array()) {
        $connect_obj = new $this->connect_class;
        $method = 'login_fail';
        $result = $connect_obj->$method($params);
        return $result;
    }

    /**
     * 更新token
     */
    public function update_token($user_id, $token, $auth, $ttl) {
        $connect_obj = new $this->connect_class;
        $method = 'update_token';
        $result = $connect_obj->$method($user_id, $token, $auth, $ttl);
        return $result;
    }

    public function sync_bind($user_id, $token, $auth, $ttl) {
        $connect_obj = new $this->connect_class;
        $method = 'sync_bind';
        $result = $connect_obj->$method($user_id, $token, $auth, $ttl);
        return $result;
    }

    /**
     * 使用授权获得的access|auth调用第三方api获取外站用户信息
     *
     * @author         xiaolongrong@meilishuo.com
     *
     * @param string $channel  互联渠道：区分各种授权形式，如：weixin_wap，weixin_client，weixin_mall
     *        string $auth 互联auth（外站用于区分用户，如腾讯的open_id，微信的unionid）
     *        string $access_token 外站操作授权access（调取外站相关api的授权access，如腾讯的access，微信的access_token）
     *        string $ext_params 拓展参数
     *
     * @return data    array    成功时返回第三方帐号绑定美丽说帐号状态信息，没有查询到记录则为NULL
     **/
    public function fetchOutsiteUserInfo($channel, $auth, $access_token, $ext_params = array()) {
        $connect_class = self::get_connect_class($channel);
        $connect_obj = new $connect_class;
        $method = 'fetchOutsiteUserInfo';
        $outsite_user_info = $connect_obj->$method($auth, $access_token, $ext_params);
        return $outsite_user_info;
    }

    /**
     * 第三方帐号绑定美丽说帐号状态
     *
     * @author         xiaolongrong@meilishuo.com
     *
     * @param  string  $channel 互联渠道
     *         string  $auth    第三方帐号互联auth
     *         int     $status  绑定状态筛选值
     *         boolean $master  是否读取主库记录
     *
     * @return data    array    成功时返回第三方帐号绑定美丽说帐号状态信息，没有查询到记录则为NULL
     **/
    public static function bindMeilishuoUserStatus($channel, $auth, $status = 0, $master = FALSE) {
        $conds = array();
        $fields = "*";

        if ( !empty($auth) ) {
            $conds['auth'] = $auth;
        } else {
            return ErrorCodes::getErrorResult(ErrorCodes::PARAM_ERROR);
        }

        if ( !empty($channel) ) {
            if ( !isset(self::$channel_type_list[$channel]) ) {
                return ErrorCodes::getErrorResult(ErrorCodes::PARAM_ERROR);
            } else {
                $conds['user_type'] = self::$channel_type_list[$channel];
            }
        } else {
            return ErrorCodes::getErrorResult(ErrorCodes::PARAM_ERROR);
        }

        if ( !empty($status) ) {
            $conds['status'] = $status;
        }

        $dao_user_profile_connect = new DUserProfileConnect();
        $connect_info = $dao_user_profile_connect->getByConds($fields,$conds,NULL,$master);

        if ( !empty($connect_info) ) {
            foreach ( $connect_info as $val ) {
                //获取user_id对应的用户基本信息:user_id=>fields
                $user_info = self::getUserInfo($val['user_id'], '*', $master);
                if( !empty($user_info) ) {
                    $dao_user_mobile = new DUserMobile();
                    $conds = array('user_id' => $val['user_id']);
                    $mobile_info = $dao_user_mobile->getByConds('*', $conds);
                    if ( !empty($mobile_info) ) {
                        $user_info['mobile'] = $mobile_info[0]['mobile'];
                    }

                    $meilishuo_user_info = array_merge($val, $user_info);
                    return array('error_code' => 0, 'message' => '', 'data' => $meilishuo_user_info);
                } else {    //user_id在t_dolphin_user_profile表里丢了
                    Utilities::log('passport.connect.bindMeilishuoUserStatus', print_r(array('error_code' => ErrorCodes::CONNECT_UID_LOST, 'channel' => $channel,
                        'auth' => $auth, 'lost_user_id' => $val['user_id']), TRUE));
                }
            }
            return ErrorCodes::getErrorResult(ErrorCodes::CONNECT_UID_LOST);
        } else {
            return ErrorCodes::getErrorResult(ErrorCodes::NOT_FIND);
        }
    }

    /**
     * 美丽说帐号绑定第三方帐号状态
     *
     * @author         xiaolongrong@meilishuo.com
     *
     * @param  string  $channel 互联渠道
     *         int     $user_id 美丽说用户id
     *         int     $status  绑定状态筛选值
     *         boolean $master  是否读取主库记录
     *
     * @return data    array    成功时返回美丽说帐号绑定第三方帐号状态信息，没有查询到记录则为NULL
     **/
    public static function bindOutsiteUserStatus($channel = '', $user_id = 0, $status = 0, $master = FALSE) {
        $conds = array();
        $fields = "*";

        if ( !empty($user_id) ) {
            $conds['user_id'] = $user_id;
        } else {
            return ErrorCodes::getErrorResult(ErrorCodes::PARAM_ERROR);
        }

        if ( !empty($channel) ) {
            if ( !isset(self::$channel_type_list[$channel]) ) {
                return ErrorCodes::getErrorResult(ErrorCodes::PARAM_ERROR);
            } else {
                $conds['user_type'] = self::$channel_type_list[$channel];
            }
        }

        if ( !empty($status) ) {
            $conds['status'] = $status;
        }

        $dao_user_profile_connect = new DUserProfileConnect();
        $result = $dao_user_profile_connect->getByConds($fields, $conds, NULL, $master);

        if ( !empty($result) ) {
            return array('error_code' => 0, 'message' => '', 'data' => $result[0]);
        } else {
            return ErrorCodes::getErrorResult(ErrorCodes::NOT_FIND);
        }
    }

    /**
     * 设置授权auth缓存信息
     *
     * @author        xiaolongrong@meilishuo.com
     *
     * @param  string $channel   互联渠道
     *         string $code      第三方授权code
     *         array  $auth_data 第三方授权auth信息
     * @return boolean
     *                TRUE       设置缓存成功
     *                FALSE      设置缓存失败
     **/
    public static function setAuthCache ($channel, $code, $auth_data) {
        $cache_obj = \Snake\libs\Cache\Memcache::instance();
        $res = $cache_obj->set($channel.":".$code, $auth_data, self::$auth_cache_time);
        return $res;
    }

    /**
     * 获取授权auth缓存信息
     *
     * @author        xiaolongrong@meilishuo.com
     *
     * @param  string $channel 互联渠道
     *         string $code    第三方授权code
     * @return data
     *                array    授权auth缓存信息（若无缓存记录或已过期则为FALSE）
     **/
    public static function getAuthCache($channel, $code) {
        $cache_obj = Memcache::instance();
        $auth_data = $cache_obj->get($channel.":".$code);
        return $auth_data;
    }

    /**
     * 生成got_auth
     *
     * @author        xiaolongrong@meilishuo.com
     *
     * @param  string $channel      互联渠道
     *         string $got_auth_url got_auth链接
     *         string $code         第三方授权code
     * @return data
     *                string        参数错误时，返回NULL
     *                              成功时返回got_auth_url
     **/
    public static function generateGotAuth($channel, $got_auth_url, $code) {
        if ( empty($channel) || !isset(self::$channel_type_list[$channel]) ) {
            return ErrorCodes::getErrorResult(ErrorCodes::PARAM_ERROR);
        }

        $auth_data = self::getAuthCache($channel, $code);

        if (empty($auth_data)) {
            Utilities::log('passport.connect.generateGotAuth', print_r(array('channel' => $channel, 'got_auth_url' =>
                $got_auth_url, 'code' => $code), TRUE));
            return ErrorCodes::getErrorResult(ErrorCodes::EMPTY_AUTH_CACHE);
        }

        $connect_class = self::get_connect_class($channel);
        $connect_obj = new $connect_class;
        $method = 'generateGotAuth';
        $got_auth_url = $connect_obj->$method($got_auth_url, $code);
        return $got_auth_url;
    }

    /**
     * 使用互联auth查询到已绑定的美丽说帐号user_id，（调用user/login）登录美丽说帐号
     *
     * @author         xiaolongrong@meilishuo.com
     *
     * @param  string  $channel 互联渠道
     *         string  $auth    第三方互联授权auth
     * @return data
     *                 array    互联帐号相关信息
     **/
    public static function connectLogin($channel, $auth, $master = FALSE) {
        if ( empty($channel) || !isset(self::$channel_type_list[$channel]) || empty($auth) ) {
            return ErrorCodes::getErrorResult(ErrorCodes::PARAM_ERROR);
        }

        //根据auth和user_type在t_dolphin_user_profile_connect中查询到绑定的美丽说user_id，取有效的第一条记录
        $conds = array();
        $fields = "*";
        $conds['user_type'] = self::$channel_type_list[$channel];
        $conds['auth'] = $auth;
        $dao_user_profile_connect = new DUserProfileConnect();
        $connect_info = $dao_user_profile_connect->getByConds($fields, $conds, NULL, $master);

        $connect_login_error_codes = 0;
        $user_info = array();
        if ( !empty($connect_info) ) {
            foreach ( $connect_info as $val ) {
                //获取user_id对应的用户基本信息:user_id=>fields
                $user_info = self::getUserInfo($val['user_id'], '*', $master);
                if( !empty($user_info) ) {    //调用user/login进行登录
                    /*
                     *
                     */
                    $connect_login_error_codes = Constant::RET_SUCCESS;
                    break;
                } else {    //user_id在t_dolphin_user_profile表里丢了
                    Utilities::log('passport.connect.connectLogin', print_r(array('error_code' => ErrorCodes::CONNECT_UID_LOST, 'channel' => $channel,
                        'auth' => $auth, 'lost_user_id' => $val['user_id']), TRUE));
                    $connect_login_error_codes = ErrorCodes::CONNECT_UID_LOST;
                }
            }
        } else {    //互联auth没有绑定的美丽说帐号
            Utilities::log('passport.connect.connectLogin', print_r(array('error_code' => ErrorCodes::CONNECT_USER_NOT_FIND, 'channel' => $channel,
                'auth' => $auth), TRUE));
            $connect_login_error_codes = ErrorCodes::CONNECT_USER_NOT_FIND;
        }

        if ( $connect_login_error_codes == Constant::RET_SUCCESS ) {
            //互联帐号相关信息
            $connect_login_result = array(
                'welcome' => $user_info['nickname'] . ', 欢迎回到美丽说!',
                'user_info' => array('user_id' => $user_info['user_id'], 'nickname' => $user_info['nickname'],
                    'avatar' => $user_info['avatar_c'])
            );
            return array('error_code' => $connect_login_error_codes, 'message' => 'success', 'data' => $connect_login_result);
        } else {
            return ErrorCodes::getErrorResult($connect_login_error_codes);
        }
    }

    /**
     * 解除互联帐号绑定
     *
     * @author         xiaolongrong@meilishuo.com
     *
     * @param  int     $user_id 美丽说用户id
     *         string  $channel 互联渠道
     *
     * @return data    array    成功时返回美丽说帐号绑定的互联帐号信息
     **/
    public static function unbindConnect($user_id = 0, $channel = '') {
        $conds = array();
        if ( empty($user_id) ) {
            return ErrorCodes::getErrorResult(ErrorCodes::PARAM_ERROR);
        }
        $conds['user_id'] = $user_id;
        if ( !empty($channel) && isset(self::$channel_type_list[$channel]) ) {
            $conds['user_type'] = self::$channel_type_list[$channel];
        }
        $dao_user_profile_connect = new DUserProfileConnect();
        $connect_info = $dao_user_profile_connect->getByConds('*', $conds, NULL);
        if ( empty($connect_info) ) {
            return ErrorCodes::getErrorResult(ErrorCodes::NOT_FIND);
        }
        $res = $dao_user_profile_connect->delByConds($conds);
        if ( !$res ) {
            return ErrorCodes::getErrorResult(ErrorCodes::DB_ERROR);
        }
        return array('error_code' => 0, 'message' => 'success', 'data' => $connect_info);
    }

    /**
     * 获取用户信息
     *
     * @author         xiaolongrong@meilishuo.com
     *
     * @param  int     $user_id 美丽说用户id
     *         string  $fields  选取的数据字段
     *
     * @return data    array    成功时返回美丽说帐号信息
     **/
    public static function getUserInfo($user_id, $fields = '*', $from_master = FALSE) {
        $conds['user_id'] = $user_id;
        $dao_user_profile = new DUserProfile();
        $user_info = $dao_user_profile->getByConds($fields, $conds, NULL, $from_master);
        if ( !empty($user_info) ) {
            return $user_info[0];
        } else {
            return array();
        }
    }
}

/* vim: set expandtab ts=4 sw=4 sts=4 tw=100: */