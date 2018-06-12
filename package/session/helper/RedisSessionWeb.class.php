<?php


namespace Inauth\Package\Session\Helper;

class RedisSessionWeb extends \Libs\Redis\ReliableRedisClient {
    static $prefix = '';

    private static $read_retry = 0;
    private static $write_retry = 0;

    public static function set_token($session_id, $fields = array()) {

    }


    protected static function loadConfig() {
	    //self::setLogger(\Couponservice\Package\Frame\Utilities::getLogger());

	    $config = \Frame\ConfigFilter::instance()->getConfig('wredis');
	    $nutHosts = $config['nutHosts'];
	    self::setHost($nutHosts);

	    $redis_connect_timeout = 2;
	    $redis_read_timeout	   = 5;
	    $redis_read_retry 	   = 1;
	    $redis_write_retry     = 1;


	    self::setTimeout(intval($redis_connect_timeout)/1000);
	    self::setReadTimeOut(intval($redis_read_timeout)/1000);

	    self::$read_retry = intval($redis_read_retry);
	    self::$write_retry = intval($redis_write_retry);
    }
	
    static public function set_session_data($id, $data, $expire = 0) {		
    	$data = serialize($data);
	    if ($expire) {
		    return self::setex($id, $expire, $data);
	    } else {
		    return self::set($id, $data);
    	}
    }

	static public function get_session_data($token) {
		return unserialize(self::get($token));
	}
}
