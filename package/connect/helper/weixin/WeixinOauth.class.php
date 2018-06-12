<?php
/**
 * Created by PhpStorm.
 * User: MLS
 * Date: 15/12/28
 * Time: 下午3:03
 */

namespace Inauth\package\connect\helper\weixin;

class WeixinOauth {

    private $rediHost = "http://m.meilishuo.com/";
    private $callback = "";
    private $connectType = "snsapi_base";

    private $connectTypeMap = array(
        0=>"snsapi_base",
        1=>"snsapi_userinfo"
    );

    private $authorize_url_mall = "https://open.weixin.qq.com/connect/oauth2/authorize";
    private $authorize_url_web = "https://open.weixin.qq.com/connect/qrconnect";
    private $appid = "";
    private $redirect_uri = "";
    private $response_type = "code";
    private $scope = "";
    private $state = "";

    public function __construct($redirect_uri, $state = "mls", $scope = "snsapi_base") {
        $this->redirect_uri = $redirect_uri;
        $this->state = $state;
        $this->scope = $scope;
        $appkeys = \Snake\Package\Oauth2\Weixin\Client::Clients()->getAppKeys();
        $this->appid = $appkeys[0];
        $this->setAuthorizeUrl();
    }

    private function set_authorize_url() {
        $this->authorize_url_mall .= "?" . implode("&", array(
                'appid=' . $this->appid,
                'redirect_uri=' . urlencode($this->redirect_uri),
                'response_type=code',
                'scope=' . $this->scope,
                'state=' . $this->state
            )) . "#wechat_redirect";
    }

    public function getAuthorizeUrl() {
        return $this->authorize_url;
    }
}
