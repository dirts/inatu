<?php
namespace Inauth\Libs\Util;

class Remote  {

    static $single = null;
    const CFG_FILE_PATH = "/home/work/inauth/config/passport/remote.ini";

    private $cfg = null;
    private $index = 'idx';

    function __construct() {
        $this->load();
    }
    
    static public function instance() {
        if (is_null(self::$single)) {
            self::$single = new self();    
        }
        return self::$single;
    }

    public function set_index($idx) {
        $this->index = $idx;    
    }

    //加载配置
    private function load() {
        $file   = file_get_contents(self::CFG_FILE_PATH);
        $datas  = explode("\n", $file);
        $pattern = "/[\s]+/";
        $cfg = array();
        foreach ($datas as $line) {
             
             if (empty($line)) continue;

             $items = preg_split($pattern, $line);

             if (empty($items)) continue;

             $arr = array();     
             foreach($items as $item) {
                $kv = explode("=", $item);
                list($k, $v) = $kv;
                if (empty($k) || empty($v)) continue;
                $arr[$k] = $v;
             }

             if ($this->index) {
                $api = $arr[$this->index];
                $cfg[$api][] = $arr;
             } else {
                $cfg[] = $arr;
             }
        
        }
        $this->cfg = $cfg;
    }

    public function get_url($remote) {
        $cfg = $this->cfg[$remote]; 
        $r = array_rand($cfg); 
        $cfg = $cfg[$r];
        return $cfg['url'];
    }


}
