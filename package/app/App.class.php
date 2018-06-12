<?php
namespace Inauth\Package\App;

use \Libs\Util\Utilities;
use \Inauth\Package\User\Helper\DBDugongHelper;

class App {
    
    static $table = 't_dolphin_auth_app_info';
    static $conf = array();

    static public function validate($app_id, $app_key) {
        return true;    
    }
        
    static public function exists($param, $fields = '*', $master = true) {
        $class = get_called_class();
        $data   = DBDugongHelper::getConn()->table($class::$table)->where($param)->query($fields, $master);
        if (empty($data)) {
            return 0;
        }
        return 1;
    }
    
    static public function query($param, $fields = '*', $master = true, $hash = null) {
        $class = get_called_class();
        $data  = DBDugongHelper::getConn()->table($class::$table)->where($param)->query($fields, $master, $hash);
        return $data;
    }

    static public function get_session_config($app_id, $field = 'session_config') {

        $cache = self::get_config_from_cache($app_id);

        if ($cache) {
            return $cache[$field];
        }

        $param = array('app_id' => $app_id);
        $data  = DBDugongHelper::getConn()->table('t_dolphin_passport_config')->where($param)->query('*', true, 'app_id');
        if (empty($data[$app_id])) {
            return false;
        }

        $app_config = $data[$app_id];
        $session_configs = explode(';', $app_config['session_config']);
        $sessions = array();

        foreach ($session_configs as $config) {
            $config = explode('|', $config);
            $a = preg_match("/\d+/", $config[0]);
            $sessions[$config[0]] = array('app_id' => $a, 'expire' => (int)$config[1], 'domain'=> explode(';',$data[$app_id]['domain']));
        }

        $app_config[$field] = $sessions;

        self::set_config_to_cache($app_id, $app_config);

        return $app_config[$field]; 
    }
    
    static public function get_config_from_cache($app_id) {
        $mc     = \Libs\Cache\Memcache::instance(); 
        $key    = "Passport:session:{$app_id}";
        return $mc->get($key);
    }

    static public function set_config_to_cache($app_id, $config, $expire_time = 3600) {
        $mc     = \Libs\Cache\Memcache::instance(); 
        $key    = "Passport:session:{$app_id}";
        return $mc->set($key, $config, $expire_time);
    }


    static public function get_all_app_ids() {
        return array(0,10001, 10002);
    }
    
    static public function session_config($app_id, $user_id, $remember = 0) {
        return self::temp_config($app_id, $user_id, $remember);
        
        $sessions = self::get_session_config($app_id);

        if (is_array($sessions)) {
            foreach ($sessions as $key => &$session) {
                $session['user_id'] = $user_id;
                $session['login'] = time();
            }
            return $sessions; 
        }
        
        return array(); 
    }
    

    static public function temp_config($app_id, $user_id, $remember) {
        $config = array(
            '1' => array(
                'p_o_mls' => array(
                    'user_id' => $user_id,
                    'app_id' => 1,
                    'login'  => time(),
                    'expire' => 7200,
                    'domain' => array('.meilishuo.com'),
                ), 
            ), 
            '2' => array(	//主app
                'p_o_2' => array(
                    'user_id' => $user_id,
                    'app_id' => 2,
                    'login'  => time(),
                    'expire' => 0,
                    'domain' => array('.meilishuo.com'),
                ), 
            ), 
            '10001' => array( //Higo
                'p_o_10001' => array(
                    'user_id'   => $user_id,
                    'app_id'    => $app_id,
                    'login'     => time(),
                    'expire'    => 3600 * 24 * 30,
                    'domain'    => array('.meilishuo.com'),
                ), 
            ), 
            '10002' => array( //商家后台
                /*
		'p_o_mls' => array(
                    'user_id'   => $user_id,
                    'app_id'    => 1,
                    'login'     => time(),
                    'expire'    => 3600 * 2,
                    'domain'    => array('.meilishuo.com'),
                ), 
		*/
                'p_o_10002' => array(
                    'user_id'   => $user_id,
                    'app_id'    => $app_id,
                    'login'     => time(),
                    'expire'    => 3600 * 24,
                    'domain'    => array('.meilishuo.com'),
                ), 
            ), 
            '10004' => array( //HIGO 
                'p_o_mls' => array(
                    'user_id'   => $user_id,
                    'app_id'    => 0,
                    'login'     => time(),
                    'expire'    => 60,
                    'domain'    => array('.meilishuo.com'),
                ), 
                'p_o_10001' => array(
                    'user_id'   => $user_id,
                    'app_id'    => $app_id,
                    'login'     => time(),
                    'expire'    => 60,
                    'domain'    => array('.meilishuo.com'),
                ), 
            ), 
        );

        if (empty($config[$app_id])) {
            $app_id = 10001;
        }

        if ($remember) {
            foreach ($config[$app_id] as $k => &$v) {
                $v['expire'] = 3600 * 24 * 2;
            }
        }
        return $config[$app_id];
    }
    
}
