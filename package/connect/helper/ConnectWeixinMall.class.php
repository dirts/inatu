<?php
/**
 * Created by PhpStorm.
 * User: MLS
 * Date: 15/12/28
 * Time: 下午2:41
 */

namespace Inauth\package\connect\helper;
use  Inauth\package\connect\helper\weixin\WeixinOauth;

class ConnectWeixinMall {

    private static $redirect_host = 'http://m.meilishuo.com/';
    private static $default_jump_url = '/wx/mall/daily';

    public static function get_authorize_url ($params) {
        if (empty($params['jumpUrl'])) {
            $params['jumpUrl'] = self::$default_jump_url;
        }
        $params['redirect_uri'] = self::$redirect_host . "wx/wxcall?jumpUrl=" . urlencode($params['jumpUrl']);

        $weixin_oauth = new WeixinOauth($params['redirect_uri'], 'mls', $params['connect_type']);
        return $weixin_oauth->getAuthorizeUrl();
    }
}