<?php
namespace Inauth\Package\Sync;

/**
 * 票据管理类
 */
class TestSync {

    static $user_session_max =  5;

    /*
     * lPush key session_id
     * lRem key 0 session_id
     * lPop  key
     * del
     */
    static public function sync_session_id($op, $key, $session_id) {
        $op = 'lPush';
        $key = '12345';
        $session_id = '6789';

    }


    /*
        sync票据
        add session_id, 200, array()
     *  del session_id
     */
    static public function sync_session($op, $session_id, $expire, $values) {

    }


}
