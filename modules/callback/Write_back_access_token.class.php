<?php
namespace Inauth\Modules\Callback;

use \Inauth\Package\Session\Helper\DBPassportHelper;
use \Inauth\Package\Session\AccessToken;


/**
 * 回写到access_token 到passport
 */
class Write_back_access_token extends \Frame\Module {

    public function run() {

	    $token  = (string)$this->request->request('access_token', ''); 

	    if (empty($token)) {
		    return $this->error('40001', '参数错误');
    	}

        $res = AccessToken::get_token_data($token);

        if (empty(!$res)) {
            $r = (int)DBPassportHelper::create($res[0]);
            return $this->response->success($r);
        } else {
	        return $this->response->error(40002, '写入失败!');
        }

	    return $this->response->success($res);
    }

}
