<?php
namespace Inauth\Libs\Http;


/**
 * http请求通用request
 * chaowang@20150129
 */
use \Inauth\Libs\Http\Curl;
use \Inauth\Libs\Util\Remote;
use \Libs\Http\Util;


final class Request extends \Libs\Http\BasicRequest
{
    public function __construct($app) {
        parent::__construct($app);

        $this->request_data['COOKIE'] = Util::slashes(Util::unmarkAmps($_COOKIE));
        $this->request_data['REQUEST'] = Util::slashes(Util::unmarkAmps($_REQUEST));
        $this->request_data['time'] = $_SERVER['REQUEST_TIME'];
        $this->request_data['ip'] = $this->getIP();
        //$this->app = $app->request;
        $this->app = $app;
    }
    
    function __call ($method, $args) {
        array_unshift($args, $method);
        if (in_array($method, array('get', 'post', 'request')))  {
            return call_user_func_array(array($this, 'input'), $args);
        }
        return null; 
    }
    
    public function input($method, $name) {
        $args    = func_get_args();
        $request = $this->app->request->{strtoupper($method)};

        if (!isset($request[$name])) {
            $length = func_num_args();
            if ($length == 2) {
                return null;
            }
            return $args[2];
        } else {
            return $request[$name];
        }
    }



    public function isMob() {
        return isset($_COOKIE['app_access_token']) || isset($_COOKIE['access_token']);
    }

    private function getIP() {
        static $ip;

        if (isset($ip)) {
            return $ip;
        }

        if (empty($this->request_data['headers']['Clientip'])) {
            $ip = '127.0.0.1';
        } elseif ( ! strpos($this->request_data['headers']['Clientip'], ',')) {
            $ip = $this->request_data['headers']['Clientip'];
        }
        else {
            $hosts = explode(',', $this->request_data['headers']['Clientip']);
            foreach ($hosts as $host) {
                $host = trim($host);
                if ($host != 'unknown') {
                    $ip = $host;
                    break;
                }
            }
        }
        return $ip;
    }
    
    public function curl($env, $api, $params) {
           $url = Remote::instance()->get_url($env) . $api;
           
           $curl = new Curl();
           $res = $curl->post($url, $params);
           
           $res = json_decode($res, true);
           return $res;      
    }

}
