<?php
namespace Inauth\Libs\Util;

/***************************************************************************
 *
 * Copyright (c) 2015 Meilishuo.com, Inc. All Rights Reserved
 *
 **************************************************************************/



/**
 * @file config/passport/kafka.cfg.php
 * @author 李守岩(shouyanli@meilishuo.com)
 * @date 2015/12/04
 * @brief  passport sdk session
 *
 **/
//use Libs\Mq\MqProxyClient;
use \Inauth\Libs\Util\Conf;


class Kafka {
    
    static public function push($topic, $message, $pid) {
        $conf  = Conf::get('kafka','kafka', 'passport');
        $kafka = MqProxyClient::getClient($conf);
        $res   = $kafka->publish_message($topic, $message, $pid); 
        if (empty($res)) {
        }
        return $res;
    }

}

class MqProxyClient {
    protected $servers;
    protected $timeout_ms = 1000;
    protected $connect_timeout_ms = 1000;
    protected $retry_times = 3;
    /**
     *
     * @param array $config         
     *
     * @return MqProxyClient
     */
    public static function getClient($config) {
        return new self ( $config );
    }
    private function __construct($config) {
        
        if(!is_array($config) || !array_key_exists('servers',$config) || !is_array($config['servers']) || count($config['servers'])<=0){
            throw new \Exception('no valid servers assign');
        }
        
        $this->servers = $config ['servers'];
        if (isset ( $config ['timeout_ms'] )) {
            $this->timeout_ms = $config ['timeout_ms'];
        }
        if (isset ( $config ['connect_timeout_ms'] )) {
            $this->connect_timeout_ms = $config ['connect_timeout_ms'];
        }
        if (isset ( $config ['retry_times'] )) {
            $this->retry_times = $config ['retry_times'];
        }
    }
    public function publish_message($topic, $message, $partition_key = 0) {
        $current_retry_time = 0;
        $offset = false;
        
        while ( $current_retry_time < $this->retry_times ) {
            try {
                $offset = $this->do_publish_message ( $topic, $message, $partition_key, $current_retry_time );
                break;
            } catch ( MqClientRetryException $ex ) {
                $current_retry_time ++;
                continue;
            } catch ( \Exception $ex ) {
                break;
            }
        }
        
        return $offset;
    }
    protected function do_publish_message($topic, $message, $partition_key = 0, $current_retry_time = 0) {
        $stime = microtime ( true );
        
        $rtn = $offset = $partition = false;
        
        $post_body = http_build_query ( $message );
        
        $headers = array ();
        $headers [] = 'X-Kmq-Topic: ' . $topic;
        $headers [] = 'X-Kmq-Partition-Key: ' . $partition_key;
        $headers [] = 'X-Kmq-Logid: 0';
        
        $endpoint = $this->getEndpoint ();
        $ch = curl_init ( $endpoint );
        curl_setopt($ch, CURLOPT_NOSIGNAL, 1);
        curl_setopt ( $ch, CURLOPT_TIMEOUT_MS, $this->timeout_ms );
        curl_setopt ( $ch, CURLOPT_CONNECTTIMEOUT_MS, $this->connect_timeout_ms );
        //curl_setopt ( $ch, CURLOPT_TIMEOUT, $this->timeout_ms / 1000 );
        //curl_setopt ( $ch, CURLOPT_CONNECTTIMEOUT, $this->connect_timeout_ms / 1000 );
        curl_setopt ( $ch, CURLOPT_HTTPHEADER, $headers );
        curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt ( $ch, CURLOPT_BINARYTRANSFER, true );
        curl_setopt ( $ch, CURLOPT_POSTFIELDS, $post_body );
        
        $proxy_result = curl_exec ( $ch );
        $this->rpclog ( $ch );
        
        if (! $proxy_result) {
            $rtn = false;
            $catched_exception = "request error : curl_errno : " . curl_errno($ch) . ' , curl_error : ' . curl_error ( $ch );
        } else {
            $res = json_decode ( $proxy_result, true );
            if (! is_array ( $res )) {
                $rtn = false;
                $catched_exception = "json_decode failed : #$proxy_result#";
            } else {
                if ($res ['errno'] != 0) {
                    $rtn = false;
                    $catched_exception = "proxy processed failed , errno:" . $res ['errno'] . " , errmsg: " . $res ['errmsg'];
                } else {
                    $rtn = $offset = $res ['data'] ['Offset'];
                    $partition = $res ['data'] ['Partition'];
                }
            }
        }
        
        $logData = array ();
        $logData ['endpoint'] = $endpoint;
        $logData ['topic'] = $topic;
        $logData ['partition_key'] = $partition_key;
        $logData ['message'] = $message;
        $logData ['partition'] = $partition;
        $logData ['offset'] = $offset;
        $logData ['exception'] = isset ( $catched_exception ) ? json_encode ( $catched_exception ) : '';
        $logData ['timecost'] = number_format ( (microtime ( true ) - $stime) * 1000, 2 );
        $logData ['current_retry_time'] = $current_retry_time;
        if(!$logData ['exception']){
            $loglevel = 'INFO';
        }else{
            if( $logData ['current_retry_time'] >= $this->retry_times - 1 ){
                $loglevel = 'FATAL';
            }else{
                $loglevel = 'WARNING';
            }
        }
        
        $logStr = "[publish_message]\t$loglevel\t" . json_encode ( $logData );
        
        //\Phplib\Tools\Liblog::log ( $logStr, "phplibmqproxy", "INFO" );
        //\Couponservice\Package\Frame\Utilities::log_local('Couponservice_Kafka_proxy', $logStr);
        //\Couponservice\Package\Frame\Utilities::log('Couponservice_Kafka_proxy', $logStr);
        
        $retry_curl_errno = array (
                CURLE_COULDNT_RESOLVE_HOST,
                CURLE_COULDNT_CONNECT 
        );
        if (in_array ( curl_errno ( $ch ), $retry_curl_errno )) {
            throw new MqClientRetryException ( curl_error ( $ch ) );
        }
        
        return $rtn;
    }
    private function getEndpoint() {
        $server = $this->servers [array_rand ( $this->servers )];
        
        $server = ltrim ( $server, 'http://' );
        $url = sprintf ( "http://%s/produce?format=json", $server );
        return $url;
    }
    private function rpclog($ch) {
        $curlErrno = curl_errno ( $ch );
        $curlError = curl_error ( $ch );
        $info = curl_getinfo ( $ch );
        
        $logInfo = array ();
        $logInfo ['curl_errno'] = $curlErrno;
        $logInfo ['curl_error'] = $curlError;
        $logInfo ['url'] = $info ['url'];
        $logInfo ['http_code'] = $info ['http_code'];
        $logInfo ['total_time'] = number_format ( $info ['total_time'] * 1000, 0 );
        $logInfo ['time_detail'] = number_format ( $info ['namelookup_time'] * 1000, 0 ) . "," . number_format ( $info ['connect_time'] * 1000, 0 ) . "," . number_format ( $info ['pretransfer_time'] * 1000, 0 ) . "," . number_format ( $info ['starttransfer_time'] * 1000, 0 );
        
        $logStr = '[mqrpc] ' . json_encode ( $logInfo );
        
        //\Couponservice\Package\Frame\Utilities::log('Couponservice_Kafka_sender', $logStr);
    }
}

class MqClientRetryException extends \Exception {
}
