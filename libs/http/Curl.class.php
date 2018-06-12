<?php
namespace Inauth\Libs\Http;

/**
 * class for curl
 * @author Xuan Zheng
 * @package base
 * @link http://calos-tw.blogspot.com/2008/12/php-curl.html
 */

class Curl{

	var $userAgent = "MLS Passport.Service Curl";
	var $cookie = false;
    var $proxy = "";   
	var $ch = NULL;
	var $url = '';

	/**
	 * 将头文件的信息作为数据流输出
	 * @var boolean
	 */
	var $haveHeader = false;//TRUE;

	/**
	 * 會將服務器服務器返回的「Location:」放在header中遞歸的返回給服務器
	 * @var boolean
	 */
	var $followLocation = TRUE;

	/**
	 * 強制獲取一個新的連接，替代緩存中的連接。
	 * @var boolean
	 */
	var $freshConnect = TRUE;

	/**
	 * header中「Accept-Encoding: 」部分的內容，支持的編碼格式為："identity"，"deflate"，"gzip"。如果設置為空字符串，則表示支持所有的編碼格式
	 * @var string
	 */
	var $encodingMethod = 'gzip';

	/**
	 * time out
	 * @var int
	 */
	var $timeOut = 30;

    var $timeOutMs = 0;
    
    var $addHeader = array();

	function __construct() {
		$this->initialize();	
	}

	/**
	 * 初始化，来开启一个curl
	 * @param NULL
	 * @return TRUE
	 */
	private function initialize() {
		$this->ch = curl_init();	
		curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, TRUE);

		return TRUE;
	}
	
	/**
	 * 一坨opt set
	 * @param NULL
	 * @return TRUE
	 */
	private function setOpt() {
		curl_setopt($this->ch, CURLOPT_HEADER, $this->haveHeader);
		curl_setopt($this->ch, CURLOPT_USERAGENT, $this->userAgent);
		curl_setopt($this->ch, CURLOPT_FOLLOWLOCATION, $this->followLocation);
		curl_setopt($this->ch, CURLOPT_FRESH_CONNECT, $this->freshConnect);
		curl_setopt($this->ch, CURLOPT_ENCODING, $this->encodingMethod);
        
        //兼容老的curl版本
        defined('CURLOPT_TIMEOUT') || define('CURLOPT_TIMEOUT', 13);
        defined('CURLOPT_CONNECTTIMEOUT') || define('CURLOPT_CONNECTTIMEOUT', 78);        
        defined('CURLOPT_TIMEOUT_MS') || define('CURLOPT_TIMEOUT_MS', 155);
        defined('CURLOPT_CONNECTTIMEOUT_MS') || define('CURLOPT_CONNECTTIMEOUT_MS', 156);
        
        if ($this->timeOutMs) {
            curl_setopt($this->ch, CURLOPT_NOSIGNAL, 1);
            curl_setopt($this->ch, CURLOPT_TIMEOUT_MS, $this->timeOutMs);
            curl_setopt($this->ch, CURLOPT_CONNECTTIMEOUT_MS, $this->timeOutMs);            
        } else {     
            curl_setopt($this->ch, CURLOPT_TIMEOUT, $this->timeOut);
        }
       
        //if (!empty($this->addHeader)) {

            $headerArr = array('Meilishuo:' . 'uid:1;ip:127.0.0.1');
            curl_setopt($this->ch, CURLOPT_HTTPHEADER, $headerArr);            
      // }  
        
		return TRUE;
	}

	/**
	 * 超时时间(ms)设置
	 * @param int
	 * @return TRUE
	 */
	public function setTimeOut($timeOut = 1) {
		//return curl_setopt($this->ch, CURLOPT_TIMEOUT, $timeOutMs);
		return $this->timeOut = $timeOut;
	}
    
    public function setTimeOutMs($timeOutMs=200) {
        return $this->timeOutMs = $timeOutMs;
    }  

    /**
	 * 被ban的时候用代理
	 * @param string
	 * @return TRUE
	 */
	public function setProxy($proxy) {
		curl_setopt($this->ch, CURLOPT_PROXY, $this->proxy);
		return TRUE;
	}

    /*
     * 是否输出头信息
     * */
    public function setNeedHeader($need_header = FALSE) {
        $this->haveHeader = (bool)$need_header;
        return TRUE;
    }

	/**
	 * 设置cookie时候用
	 * @param string
	 * @return TRUE
	 * @todo cookie 用file实现
	 */
	public function cookie($cookie) {
		curl_setopt($this->ch, CURLOPT_COOKIE, $cookie);	
	}


	/**
	 * to curl
	 * @param string
	 * @return html
	 */
	private function curl($url = '') {
		$this->setOpt();
		curl_setopt($this->ch, CURLOPT_URL, $url);
		$html = curl_exec($this->ch);
        curl_close($this->ch);
		return $html;
	}

	/**
	 * post method
	 * @param string
	 * @param array
	 * @return array
	 */
	public function post($url = '', $params = array()) {
		$checkPos = strpos ( $url , "#");
		if ( $checkPos !== false ) {
			$url = substr ( $url , 0 , $checkPos );
		}
		if (trim($url) == '') {
			return TRUE;	
		}
		curl_setopt($this->ch, CURLOPT_POST, TRUE);
		curl_setopt($this->ch, CURLOPT_POSTFIELDS, http_build_query($params));
		return $this->curl($url);
	}

	/**
	 * post method
	 * @param string
	 * @param array
	 * @return array
	 */
	public function get($url, $referer = '') {
		$checkPos = strpos ( $url , "#");
		if ( $checkPos !== false ) {
			$url = substr ( $url , 0 , $checkPos );
		}
		if (trim($url) == '') {
			return TRUE;	
		}
		return $this->curl($url);
	}

    /** 
     * post method
     * @param string
     * @param array
     * @return array
     */
    public function setAgent($userAgent) {
        if(!empty($userAgent)) {
            $this->userAgent = $userAgent;
        }
        return TRUE;
    }
    
     
    public function addHeader($headers) {
        if (empty($headers)) {
            return TRUE;
        }
        $this->addHeader = $headers;
    } 

}
