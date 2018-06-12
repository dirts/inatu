<?php
namespace Inauth\Package\Session\Helper;

use \Inauth\Libs\Db\Table;
use \Inauth\Package\Util\Utilities as U;

class DBPassportHelper extends Table {
    const _DATABASE_ = 'passport';

    static $table_prefix = 't_passport_access_token_';

    static function get_table_sharp_name($token) {
        $last = substr(trim($token), -1);
        return  self::$table_prefix . $last;
    }

    //写入新数据
    static function create($param) {
        $token = $param['token'];
        unset($param['id']);
        $table = self::get_table_sharp_name($token);
        $a = self::getConn()->table($table)->where(array('token' => $token))->query('*');
        if (!empty($a)) {
            return false;
        }
        return self::getConn()->table($table)->insert($param);
    }

    //删掉session
    static function del_session ($token) {
        $table = self::get_table_sharp_name($token);
        return $a = self::getConn()->table($table)->where(array('token' => $token))->remove();
    }


    static  function update_token($token, $data = array()) {
        if (empty($token) && empty($data)) {
            return FALSE;
        }

        $table = self::get_table_sharp_name($token);
        $sql = "UPDATE {$table} SET";
        $sql_data = $data;
        foreach($data as $key => &$value) {
            if ($key == 'version') {
                $value = substr($value, 0, 12);
            }
            $sql .= " `$key` = '$value',";
        }
        $update_by = " WHERE";
        $update_by .= " token = '$token'";
        $sql_data['token'] = $token;
        $sql = substr_replace($sql, $update_by, -1);
        try {
            //var_dump($sql);
            //var_dump($sql_data);
            $res = self::getConn()->write($sql, array());
        }catch (\Exception $e ) {
            U::log('passport.db.log', print_r(array($token, $data, $e->getMessage()), true));
        //var_dump($e->getMessage());
        }
        //var_dump($res);
        return $res;
   }
}
