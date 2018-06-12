<?php
/**
 * InsertAccessToken
 * Created by PhpStorm.
 * User: xiaolongrong
 * Date: 15/11/5
 * Time: 上午11:19
 */

namespace Inauth\Modules\Callback;
use \Inauth\Package\User\Helper\DBAccessTokenHelper;
use \Inauth\Package\Util\Utilities as U;
use \Inauth\Package\User\Helper\DBPassportHelper;

class Insert_access_token extends \Frame\Module {
    private static $openPassportInsert = 1;

    public function run() {
        $data = $this->request->request('data', array());
        if (empty($data)) {
            return $this->request->error('40001', '参数错误');
        }
        U::log('inauth.insert_access_token', print_r($data,true));
        $this->process($data);
        return true;
    }

    public function process($data) {
        $this->insertTokenIntoSwanDB($data);
        if (self::$openPassportInsert) {
            $this->insertTokenIntoPassportDB($data);
        }
    }

    private function insertTokenIntoSwanDB ($data) {
        try{
            //bak_sql
            $bak_sql = "replace into t_swan_oauth_access_token_new (token, auth_code, client_id, user_id, expiration, type) values ";
            $bak_sql .= "('{$data['token']}', '{$data['auth_code']}', {$data['client_id']},{$data['user_id']},{$data['expiration']},{$data['type']}),";

            //sqlComm
            $sqlComm = "insert into t_swan_oauth_access_token_new (token, auth_code, client_id, user_id, expiration, type) values ";
            $ext = "";
            if(isset($data['token']) && isset($data['user_id']) && isset($data['auth_code']) && !empty($data['token'])) {
                $sqlComm .= "('{$data['token']}', '{$data['auth_code']}', {$data['client_id']},{$data['user_id']},{$data['expiration']},{$data['type']}),";
                $ext .= "('{$data['token']}', '{$data['auth_code']}', {$data['client_id']},{$data['user_id']},{$data['expiration']},{$data['type']}),";
            }
            $sqlComm = trim($sqlComm,",");
            U::log('inauth.insert_access_token', "swan_bak_sql:".$bak_sql);
            U::log('inauth.insert_access_token', "swan_sqlComm".$sqlComm);
            if(!empty($ext)) {
                $result = DBAccessTokenHelper::getConn()->write($sqlComm, array());
                if(empty($result)) {
                    U::log('inauth.insert_access_token_err', "db写失败:".$sqlComm);
                    U::log('inauth.insert_access_token_err', $bak_sql);
                }
            }
        }
        catch(\Exception $e) {
            if(!empty($bak_sql)) {
                U::log('inauth.insert_access_token_err', "异常操作:".$e->getMessage());
                U::log('inauth.insert_access_token_err', $bak_sql);
            }
        }
    }

    private function insertTokenIntoPassportDB ($data) {
        try{
            $passport_token_table = DBPassportHelper::switchPassportTokenTable($data['token']);

            //bak_sql
            $bak_sql = "replace into ". $passport_token_table ." (token, auth_code, client_id, user_id, expiration, type) values ";
            $bak_sql .= "('{$data['token']}', '{$data['auth_code']}', {$data['client_id']},{$data['user_id']},{$data['expiration']},{$data['type']}),";

            //sqlComm
            $sqlComm = "insert into " .$passport_token_table. " (token, auth_code, client_id, user_id, expiration, type) values ";
            $ext = "";
            if(isset($data['token']) && isset($data['user_id']) && isset($data['auth_code']) && !empty($data['token'])) {
                $sqlComm .= "('{$data['token']}', '{$data['auth_code']}', {$data['client_id']},{$data['user_id']},{$data['expiration']},{$data['type']}),";
                $ext .= "('{$data['token']}', '{$data['auth_code']}', {$data['client_id']},{$data['user_id']},{$data['expiration']},{$data['type']}),";
            }
            $sqlComm = trim($sqlComm,",");
            U::log('inauth.insert_access_token', "passport_bak_sql:".$bak_sql);
            U::log('inauth.insert_access_token', "passport_sqlComm".$sqlComm);
            if(!empty($ext)) {
                $result = DBPassportHelper::getConn()->write($sqlComm, array());
                if(empty($result)) {
                    U::log('inauth.insert_access_token_err', "db写失败:".$sqlComm);
                    U::log('inauth.insert_access_token_err', $bak_sql);
                }
            }
        }
        catch(\Exception $e) {
            if(!empty($bak_sql)) {
                U::log('inauth.insert_access_token_err', "异常操作:".$e->getMessage());
                U::log('inauth.insert_access_token_err', $bak_sql);
            }
        }
    }
}
