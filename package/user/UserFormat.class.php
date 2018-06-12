<?php
namespace Inauth\Package\User;

use \Libs\Util\Utilities;
use \Inauth\Package\User\Helper\DBDugongHelper;

/**
 * 用户信息-模块
 */
class UserFormat {
	
	const EMAIL_EXP = "/^[0-9a-zA-Z]+([_a-z0-9\-\.]+)*@[a-zA-Z0-9]{2,}(?:[-.][a-zA-Z0-9]+)*\.[a-zA-Z]{2,}$/";
	const NICKNAME_EXP = "/^[\x{4e00}-\x{9fa5}a-zA-Z0-9\_]+$/u";
	const URL_EXP = "|^http://([\w-]+\.)+[\w-]+(/[\w-./?%&=]*)?$|";
	const NICKNAME_NUMBER_EXP = "/\d{1}/";
    const MOB_EXP = "/^1\d{10}$/";

	const FORBIDDEN_WORDS = '美丽说';

	static $black_list = array(
		'mailinator.com',
		'mailcatch.com',
		'027168.com',
		'chacuo.net',
		'tempinbox.com',
		'qiangui888.com',
		'armyspy.com',
		'sharklasers.com',
		'dayrep.com',
		'cuvox.de', 
		'einrot.com',
		'jourrapide.com',
		'gustr.com',
		'rhyta.com',
		'superrito.com',
		'teleworm.us',
		'fakeinbox.com',
		'fleckens.hu',
		'hourmail.net',
		'xfkk.com',
		'yake5.com'
    );

    static $reg_from_list = array(
        //1, //人人
        2, //pc    
        3, //新浪微博互联注册
        4,//QQ互联注册
        //5, //百度互联注册
        //6,//淘宝互联注册
        //7,//网易互联注册
        8,//腾讯微博互联注册
        10,//豆瓣
        15,//微信
        30,//iphone 客户端
        31,//android客户端
        32,//ipad客户端
        33,//iphone sub
        34,//windows phone
    );

    public static function regfrom($from) {
        
    }
	

	/**
	 * 邮箱验证
	 * @param $email string 
	 *
	 */
	public static function emailFormat($email) {
		if (empty($email)) {
			return FALSE;
		}
		if (preg_match(self::EMAIL_EXP, $email)) {
			$dnsV = explode("@", $email);
			$dns = array_pop($dnsV);

            //判断该dns是否在黑名单内
            foreach ($this->black_list as $item) {
                if (stripos($dns, $item, 0) !== FALSE) {
                    return FALSE;
                }
            }

			$memKey = 'USERFORMAT:EMAIL';
			$dnsS = Memcache::instance()->get($memKey);
			$dnsArray = array();
			!empty($dnsS) && $dnsArray = explode(",", $dnsS);
			if (!in_array($dns, $dnsArray)) {
				if (checkdnsrr($dns, "MX") == FALSE) {
					return FALSE;
				}
				$dnsArray[] = $dns;
				$dnsString = implode(',', $dnsArray);
				!empty($dnsString) && Memcache::instance()->set($memKey, $dnsString, 36000);
            }
			return TRUE;
		}
		return FALSE;
	}

	/**
	 * 昵称连接8位数字
	 * @param $nickname string
	 * @return 含8位及以上数字 true
	 */
	public static function nicknameNumberFormat($nickname) {
		$number = preg_match_all(self::NICKNAME_NUMBER_EXP, $nickname, $matches);
		if ($number < 8) 
			return FALSE;
		return TRUE;
	}

	/**
	 * 呢称验证
	 * @param $nickname string
	 */
	public static function nicknameFormat($nickname) {
		$length = (strlen($nickname) + mb_strlen($nickname, 'utf-8' )) / 2;
		if ($length < 1 || $length > 20 || !preg_match(self::NICKNAME_EXP, $nickname)) {
			return FALSE;
		}
		return TRUE;
	}

	public static function urlFormat($url) {
		if (empty($url)) {
			return FALSE;
		}
		if (!preg_match(self::URL_EXP, $url)) {
			return FALSE;	
		}
		return TRUE;
	}

	public static function maskwordFormat($word) {
		if (empty($word)) {
			return FALSE;
		}	
        $mask = \Virus\Package\Spam\NameMaskWords::getParse()->fill($word)->compare();	
		if (!empty($mask['maskWords'])) {
			//含有屏蔽词
			return TRUE;
		}
		return FALSE;
	}

    private static function isExist($param) {
        if (empty($param) || !is_array($param)) {
            return FALSE;
        }
        $user = new User();
        $result = $user->getUserProfile($param, "count(*) AS num", TRUE);
        if ($result[0]['num'] > 0) {
            return FALSE;
        }
        return TRUE;
    }

	/**
     * nickname验证集合类
     */
	public static function nickFilter($nickname, $exist = TRUE) {
		// 昵称验证
		if ($this->nicknameFormat($nickname) === FALSE) {
            throw new \Virus\Frame\VException('用户nickname不合法', 20210);
		}

        //nickname是否包含屏蔽词
        if ($this->maskwordFormat($nickname) === TRUE) {
            throw new \Virus\Frame\VException('用户nickname 无效', 20212);
        }

        //数据验证
		if ($exist == TRUE) {
			$nickArr = array('nickname' => $nickname);
			if ($this->isExist($nickArr) === FALSE) {
				throw new \Virus\Frame\VException('用户nickname存在', 20213);
			}
		}

		// 含有美丽说屏蔽词
        if (mb_stripos($nickname, self::FORBIDDEN_WORDS, 0, 'utf-8') !== FALSE) {
            throw new \Virus\Frame\VException('用户nickname无效', 20212);
        }
		return TRUE;
	}

    public static function mobileFormat($mobile) {
        if (empty($mobile)) {
            return FALSE;
        }
        if (!preg_match(self::MOB_EXP, $mobile)) {
            return FALSE;
        }
        return TRUE;
    }
}
