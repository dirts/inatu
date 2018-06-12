<?php
/***************************************************************************
 *
 * Copyright (c) 2015 Meilishuo.com, Inc. All Rights Reserved
 *
 **************************************************************************/



/**
 * @file package/session/helper/SessionRelation.class.php
 * @author 李守岩(shouyanli@meilishuo.com)
 * @date 2015/12/02
 * @brief  passport sdk session
 *
 **/

namespace Inauth\Package\Session\Helper;

use \Inauth\Package\Session\Helper\RedisSession;
use \Inauth\Package\Session\Helper\RedisSessionWeb;
use Libs\Cache\Memcache as Memcache;
use \Inauth\Package\User\User;
use \Inauth\Package\Util\Utilities as U;

use \Inauth\Package\Session\Helper\DBPassportHelper;
use \Inauth\Package\Session\Helper\DBSwanHelper;

class SessionRelation extends \Libs\Redis\ReliableRedisClient {
	static $prefix = 'SessionRelation';

	private static $read_retry = 0;
	private static $write_retry = 0;


	protected static function loadConfig() {

		$config = \Frame\ConfigFilter::instance()->getConfig('session_relation');
		$nutHosts = $config['nutHosts'];
		self::setHost($nutHosts);

		$redis_connect_timeout = 2;
		$redis_read_timeout    = 5;
		$redis_read_retry      = 1;
		$redis_write_retry     = 1;


		self::setTimeout(intval($redis_connect_timeout)/1000);
		self::setReadTimeOut(intval($redis_read_timeout)/1000);

		self::$read_retry = intval($redis_read_retry);
		self::$write_retry = intval($redis_write_retry);
	}


	static public function add_mob_relation($user_id, $s, $session_id) {
		return self::add_relation($user_id, time() + $s, "1:".$session_id);
	}

	static public function add_web_relation($user_id, $s, $session_id) {
		return self::add_relation($user_id, time() + $s, "0:".$session_id);
	}

	static public function add_relation($user_id, $s, $session_id) {
		self::loadConfig();
		return self::zAdd($user_id, $s, $session_id);
	}

	static public function kick_user($user_id, $access_token = '') {
		//$data = User::update(array('user_id' => $user_id), array('level' => 0, 'is_actived' => 0));
		$sessions = self::zRange($user_id, 0, -1);
		//$sessions = array('1:48468b0ef9ae71c32797e50c90e5bd1a');
		if (empty($sessions))  {
			return false;
		}
		$hash = array();
		foreach ($sessions as $session) {
			$s = explode(":",$session);
			if ($s[1] == $access_token) {
				continue;   
			}
			if (count($s) == 2 ) {
				if ($s[0] == 0) {
                    RedisSessionWeb::loadConfig();
					$hash[$session] = RedisSessionWeb::del($s[1]);
					Memcache::instance()->delete($s[1]);
				} else {
					$a = DBPassportHelper::del_session($s[1]);

                    $config = \Frame\ConfigFilter::instance()->getConfig('idc_check');
                    if ( !empty($config['idc']) && $config['idc'] == 'yz' ) {
                        $b = DBSwanHelper::del_session($s[1]);
                    }

					RedisSession::loadConfig();
					$hash[$session] = RedisSession::del($s[1]);

					$b= Memcache::instance()->delete("Mob:Session:AccessToken:".$s[1]);
				}

				if (!empty($hash[$session])) {
					SessionRelation::loadConfig();
					SessionRelation::zRem($user_id, $session);
				}
			}
		}

		//禁止自动登陆
		//$data = User::update(array('user_id' => $user_id), array('level' => 50));
		$res = array('user_id' => $user_id, 'hash' => $hash);
		U::log('passport.kick', json_encode(array($res)));
		return $res;

	}

	static public function relation_limit($user_id, $session_id, $limit = 50 ) {
		return $res = SessionRelation::zRem($user_id, $session_id);
	}


}
