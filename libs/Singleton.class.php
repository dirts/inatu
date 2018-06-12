<?php
namespace Inauth\Libs;
abstract class Singleton
{
    protected static $instance = array();
    public static function instance() {
        $clz = get_called_class();
        if ( ! isset(self::$instance[$clz])) {
            self::$instance[$clz] = new $clz();
        }
        return self::$instance[$clz];
    }


    protected function __construct() {
    }

    protected function __clone() {
    }
}
