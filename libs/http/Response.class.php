<?php
namespace Inauth\Libs\Http;

/**
 * http请求通用response
 */

final class Response extends \Libs\Http\BasicResponse
{
    /**
     * json render to json 
     * resource render to resource
     */
    private $renderType = 'json';
    private $errCode = 0;
    private $message = 'ok';


    public function setRenderType($type) {
        $this->renderType = $type;
    }

    public function setContent($code = 0, $msg = 'ok', $body = NULL) {
        $this->errCode = $code;
        $this->message = $msg;
        $method = 'build' . ucfirst($this->renderType) . 'Body';
        if (method_exists($this, $method)) {
            $body = $this->$method($body);
        }
        $this->setBody($body);
    }

    protected function buildJsonBody(&$body) {
        $body = array('error_code' => $this->errCode, 'message' => $this->message, 'data' => $body);
        return $body;
    }

    //返回成功
    public function success($data = '', $code = 0, $msg = 'success!') {
        $this->setContent($code, $msg, $data);
    }

    //返回失败
    public function error($code = 40001, $msg = 'error!', $data = '') {
        $this->setContent($code, $msg, $data);
    }

    public function get_response() {
        return $this->body;    
    } 

}
