<?php
namespace Inauth\Package\User;

use \Inauth\Libs\Util\Util;
use \Inauth\Package\User\Helper\DBDugongHelper;
use Libs\Cache\Memcache as Memcache;

/**
 * 用户信息-模块
 */
class User {

    static $query_user_join_mod = 1;
    static $table = 't_dolphin_user_profile';
    const DefaultHeadImage = 'ap/c/78/32/59c5fb29fb8ea04deddb2ad0ff42_256_256.cf.png';
    /*
     *批量获取用户信息
     */
    static public function get_user_infos($user_ids, $cols = 'nickname', $hash = true, $master = false) {
        if (empty($user_ids)) {
            return false;
        }

        $userinfos = array();
        foreach ($user_ids as $user_id) {
            $user = self::get_user_info($user_id, $cols, $master);
            if ($hash) {
                $user && $userinfos[$user_id] = $user;
            } else {
                $user && $userinfos[] = $user;
            }
        }

        return $userinfos;
    }

    /* 获取单个用户信息*/
    static public function get_user_info($user_id, $cols = '`nickname`', $master = false) {

        //读缓存 
        $cache = self::get_user_info_from_cache($user_id);
        if (!empty($cache)) {
            unset($cache['password']);
            unset($cache['cookie']);
            return $cache;
        }
         
        //通过left join 获取用户信息,DB QPS 会减少，但是DB压力可能回大
        if (self::$query_user_join_mod = 0) {
            $user_info = self::get_join_user_info_from_db($user_id);
        } else {
            $user_info     = self::get_user_info_from_db($user_id, $cols, $master); 
            $user_ext_info = UserExt::get_user_ext_info_from_db($user_id, $cols, $master); 
            $user_info     = array_merge($user_info, $user_ext_info); 
        }

        if (empty($user_info)) {
            return false;
        }
        //构造用户数据结构
        $user_info = self::format_user_info($user_info, true);

        //写缓存
        self::set_user_info_to_cache($user_id, $user_info);
        return $user_info;
    }
    
    /*
     *批量获取用户信息
     */
    static public function get_base_user_infos($user_ids, $cols = 'nickname', $hash = true, $master = false) {
        
        if (empty($user_ids)) {
            return false;
        }
        
        $userinfos = array();
        foreach ($user_ids as $user_id) {
            $user = self::get_user_base_info($user_id, $cols, $master);
            if ($hash) {
                $userinfos[$user_id] = $user;
            } else {
                $userinfos[] = $user;
            }
        }
        return $userinfos;
    }
    
    /* 获取单个用户信息*/
    static public function get_user_base_info($user_id, $params = '`nickname`', $master = false) {

        if (empty($user_id) || empty($params)) {
            return false;
        }

        $redis_key = "User:getUserBaseInfo:{$user_id}";
        
        $mc = Memcache::instance(); 
        $user_info = $mc->get($redis_key);

        if (!empty($user_info)) {
            return $user_info;
        }

        $user_info = self::get_user_info_from_db($user_id, $params = 'nickname', $master); 
        $user_info = self::format_user_info($user_info);
        
        $a = $mc->set($redis_key, $user_info, 300);
        return $user_info;
    }

    static public function update_login_times($uid) {
        $sql = "update " . self::$table . " set login_times = login_times + 1, last_logindate = now() where user_id = :user_id";
        $result = DBDugongHelper::getConn()->write($sql, array('user_id' => $uid));
        return $result;
    }

    /* 从db 获取用户信息 */
    static public function get_user_info_from_db($user_id, $cols = '`nickname`', $master = false) {
        $param  = array('user_id' => $user_id);
        $sql    = "SELECT user_id, nickname, email, password, cookie, ctime, is_actived, status, realname, istested, reg_from, last_email_time, level, isPrompt, isBusiness, login_times , last_logindate FROM t_dolphin_user_profile WHERE user_id = :user_id";
        $data   = DBDugongHelper::getConn()->read($sql, $param, $master, 'user_id');
        if (empty($data)) {
            return $data;
        }
        return $data[$user_id];
    }
    
    /* 从db 获取用户ext信息 */
    static public function get_user_ext_info_from_db($user_id, $cols = '`nickname`', $master = false) {
        return UserExt::get_user_ext_info_from_db($user_id, $cols, $master); 
    }
    
    /* 从db 获取用户ext信息 */
    static public function get_join_user_info_from_db($user_id, $cols = '`nickname`', $master = false) {
        $param  = array('user_id' => $user_id);
        $sql    = "SELECT 
            a.user_id, a.nickname, a.email, a.password, a.cookie, a.ctime, a.is_actived, a.status, a.realname, a.istested, a.reg_from, a.last_email_time, a.level, a.isPrompt, a.isBusiness, a.login_times , a.last_logindate,
            b.user_id, b.gender, b.birthday, b.province_id, b.city_id, b.about_me, b.avatar_c, b.is_taobao_buyer, b.verify_icons, b.verify_msg, b.weibo_url, b.is_taobao_seller, b.school, b.workplace, b.industry, b.hobby, b.shipping_address, b.post_code, b.mobile, b.qq, b.msn
            FROM `t_dolphin_user_profile` AS a 
            LEFT JOIN `t_dolphin_user_profile_extinfo` AS b 
            ON a.user_id = b.user_id 
            WHERE a.user_id = :user_id";
        
        $data   = DBDugongHelper::getConn()->read($sql, $param, $master, 'user_id');
        return $data[$user_id];
    }
    
    /* 用户信息查询 */
    static public function query($param, $fields = '*', $master = true, $hash = null) {
        $class = get_called_class();
        $data   = DBDugongHelper::getConn()->table($class::$table)->where($param)->query($fields, $master, $hash);
        return $data;
    }
    
    /* 用户信息更新 */
    static public function update($param, $update) {
        $class = get_called_class();
        $data   = DBDugongHelper::getConn()->table($class::$table)->where($param)->update($update);
        return $data;
    }
    
    /* 从db 获取用户ext信息 */
    static public function exists($param, $fields = 'user_id', $master = true) {
        $class = get_called_class();
        $data   = DBDugongHelper::getConn()->table($class::$table)->where($param)->query($fields, $master);
        if (empty($data)) {
            return 0;
        }
        return 1;
    }

    /* 注册用户信息 */
    static public function register($param) {
        if (empty($param)) return false;
       
        $conn    = DBDugongHelper::getConn();
	    return $user_id = $conn->table('t_dolphin_user_profile')->insert($param);
	if ($user_id) {
		$ext = array('user_id' =>$user_id);

		$default_ext_profile = array(
				'user_id' =>$user_id,
				'gender'        => '女',            //性别
				'birthday'      => '0000-00-00',    //生日
				'province_id'   => 0,               //省份
				'city_id'       => 0,               //城市
				'school'        => '',              //学校
				'workplace'     => '',              //工作单位
				'industry'      => 0,               //从事行业
				'hobby'         => '',              //爱好
				'weibo_url'     => '',              //微博地址
				'about_me'      => '',              //美丽宣言
				'alipay'	=> 0,
				'taobao_id'     => 0,
				'verify_icons'  => '',
				'verify_msg'   => '',
				'lastname'     => '',
				);
	
            $conn->table('t_dolphin_user_profile_extinfo')->insert($default_ext_profile);
            //$conn->table('t_dolphin_user_settings')->insert($ext);
            //$conn->table('t_dolphin_user_statistic')->insert($ext);
        }
        return $user_id;
    }

    /* 取catch */
    static public function get_user_info_from_cache($user_id) {
        $mc     = \Libs\Cache\Memcache::instance(); 
        $key    = "User:getUserInfo:{$user_id}";
        return $mc->get($key);
    }

    /* 存用户信息到 cache, 1小时过期 */
    static public function set_user_info_to_cache($user_id, $user_info, $expire_time = 3600) {
        $mc     = \Libs\Cache\Memcache::instance(); 
        $key    = "User:getUserInfo:{$user_id}";
        return $mc->set($key, $user_info, $expire_time);
    }
    
    /* 取catch */
    static public function del_user_info_from_cache($user_id) {
        $mc     = \Libs\Cache\Memcache::instance(); 
        $key    = "User:getUserInfo:{$user_id}";
        return $mc->del($key);
    }

    static public function clear_user_cache($user_id) {
        $mc     = \Libs\Cache\Memcache::instance(); 
        $user_info_key    = "User:getUserInfo:{$user_id}";
        $user_base_info_key    = "User:getUserBaseInfo:{$user_id}";
        $mc->del($user_base_info_key);
        return $mc->del($user_info_key);
    }

    static public function format_user_avatar($key) {
            if (empty($key)) {
                $key = self::DefaultHeadImage;
            } else if (strpos($key, 'css/images') !== FALSE) {
                $key = self::DefaultHeadImage;
            } else if ($key == 'ap/c/ba/1b/bb814d3c8e55e2004c8a62233ce0_128_128.c1.jpeg') {
                $key = self::DefaultHeadImage;
            }
        return $result = Util::convertPicture($key);
    }
    
    /* 处理用户信息 */
    static public function format_user_info($user_info, $identity = false ) {
        if (empty($user_info)) {
            return $user_info;
        }
        $user_info['user_id'] = (int)$user_info['user_id'];
        $user_info =  self::_format_weibo_nickname($user_info);
        
        unset($user_info['password']); 
        unset($user_info['cookie']); 
        
        if (isset($user_info['avatar_c'])) {
            $user_info['avatar_c'] = self::format_user_avatar($user_info['avatar_c']);
        }

        if ($identity) { 
            !empty($user_info['verify_msg']) && $user_info['verify_msg'] = json_decode($user_info['verify_msg'], TRUE);
            !empty($user_info['verify_icons']) && $user_info['verify_icons'] = explode(',', $user_info['verify_icons']);
            $user_info['identity'] =  UserIdentity::format_identity($user_info);
        }
        return $user_info;
    }

    /* 处理新浪微博等互联过来的 昵称带#的问题 */
    static private function _format_weibo_nickname($user_info) {
        if (!is_array($user_info)) {
            return FALSE;
        }
        if (isset($user_info['nickname'])) {
            if (mb_strpos($user_info['nickname'], '#', 0, 'utf-8') > 0) {
                $nick = explode("#", $user_info['nickname']);
                $user_info['nickname'] = $nick[0];
            }
        }
        return $user_info;
    }
   

}
