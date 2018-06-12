<?php
namespace Inauth\Modules\Callback;

use \Inauth\Package\Session\Helper\DBSwanHelper;
use \Inauth\Package\Session\Helper\DBPassportHelper;
use \Inauth\Package\Session\AccessToken;
use \Inauth\Package\Util\Utilities as U;


/**
 * 更新access_token数据
 */
class Update_access_token extends \Frame\Module {

    public function run() {

        $token      = (string)$this->request->request('access_token', '');
        $data       = $this->request->request('data', array());
        $database   = $this->request->request('database', array());

        if (empty($token)) {
            return $this->error('40001', '参数错误');
        }

        if (empty($data)) {
            return $this->error('40001', '参数错误');
        }

        if ($database == 'passport') {
            //$table  = DBPassportHelper::get_table_sharp_name($token);
        //$res    = DBPassportHelper::getConn()->table($table)->where(array('token' => $token))->update($data);
            $res    = DBPassportHelper::update_token($token, $data);
    } else {
            $res    = DBSwanHelper::getConn()->table('t_swan_oauth_access_token_new')->where(array('token' => $token))->update($data);
        }
        if (empty($res)) {
            U::log('passport.kafka.callback', print_r(array($token, $data, $database, $res), true));
        }

        return $this->response->success($res);
    }



}
