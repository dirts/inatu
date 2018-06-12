<?php
namespace Inauth\Package\Mon;

use \Inauth\Package\Mon\Helper\DBCallbackHelper;
use Libs\Cache\Memcache as Memcache;

class CallbackMon {

    static $table = "t_dugong_callback";
    
    static function add_callback($param) {
        $conn    = DBCallbackHelper::getConn();
	    $id = $conn->table(self::$table)->insert($param);
    }

    static function get_callback_apis($topic) {
        
        $redis_key = "Kafka:" . $topic;
        $mc = Memcache::instance(); 
        $mc_datas = $mc->get($redis_key);

        if (empty($mc_datas)) {
            $conn    = DBCallbackHelper::getConn();
            $datas   = $conn->table(self::$table)->where(array('topic'=> $topic, 'is_del' => 0 , 'status' => 0))->query('*');    
       
            $mc_datas = $mc->set($redis_key, $datas);
            return $datas; 
        }

        return $mc_datas;

    }
}
