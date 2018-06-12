<?php
/***************************************************************************
*
* Copyright (c) 2015 Meilishuo.com, Inc. All Rights Reserved
*
**************************************************************************/

/**
* @file   Module.class.php
* @author CHEN Yijie(yijiechen@meilishuo.com)
* @date   2015/12/29
* @brief  Inauth项目统一Module层基类Controller 
*
**/

namespace Inauth\Libs;

use \Inauth\Libs\ErrorCodes;

abstract class Module extends \Frame\Module {
    public function __construct($app) {
        parent::__construct($app);
        $this->init();
    }

    private function init() {
        $this->hook('after',  array($this, 'afterRun'));
        $this->hook('before', array($this, 'beforeRun'));
    }
    
    protected function beforeRun() {
        return TRUE;
        // 授权检测
        $app_id  = intval($this->app->request->post('app_id'));
        $app_key = strval($this->app->request->post('app_key'));
        $passport_app_auth = \Frame\ConfigFilter::instance()->getConfig('passport_app_auth');
        if ($app_key !== $passport_app_auth[$app_id]) {
            $this->setView(ErrorCodes::SERVICE_AUTHORIZE_INVALID, 
                ErrorCodes::getErrorMessage(ErrorCodes::SERVICE_AUTHORIZE_INVALID));
            return FALSE;
        }
        return TRUE;
    }
    
    final public function run() {
        try {
            $this->execute();
        } catch (\Exception $e) {
            $error_code = $e->getCode();
            $message    = $e->getMessage();
            $this->setView($error_code, $message);
        }

        return TRUE;
    }
    
    abstract protected function execute();
    
    protected function afterRun() {
        return TRUE;
    }
    
    /***
     * 输入参数校验 支持 int 和 string 两种类型参数
     *
     *
     * @param int           必要  $input_param_name  输入参数名
     * @param int/string    必要  $input_param_val   输入参数值
     * @param string        必要  $type              参数类型 int string
     * @param bool          必要  $equalZero         是否可以等于0
     * @param min          非必要 $min               int 型参数最小值
     * @param max          非必要 $max               int 型参数最大值
     * 
     * @return bool           TRUE/FALSE
     * 
     **/
    protected function inputVerify($input_param_name, $input_param_val, $type, 
                                   $equalZero=false, $min=false, $max=false) {
        $flag = false;  // 标记是否错误返回
        $errstring_prefix = "Param $input_param_name input error: ";
        if ($type === 'int') {
            if ($equalZero) {
                if ($input_param_val < 0) {
                    $errstring = $errstring_prefix . "input value cannot < 0";
                    $flag = true;
                }
            } else {
                if ($input_param_val <= 0) {
                    $errstring = $errstring_prefix . "input value cannot <= 0";
                    $flag = true;
                }
            }
            if ($min !== false) {
                if ($input_param_val <= $min) {
                    $errstring = $errstring_prefix . "input value cannot <= $min";
                    $flag = true;
                }
            }
            if ($max !== false) {
                if ($input_param_val >= $max) {
                    $errstring = $errstring_prefix . "input value cannot >= $max";
                    $flag = true;
                }
            }
        } else if ($type === 'string') {
            if (empty($input_param_val)) { 
                $errstring = $errstring_prefix . "input value cannot be empty";
                $flag = true;
            }
        }
        if ($flag) {
            $this->setView(ErrorCodes::PARAM_ERROR, $errstring);
            return FALSE;
        }
        return TRUE;
    }

    protected function setView($mlEcode = 0, $mlEmsg = 'success', $result = NULL) {
        $this->app->response->setContent($mlEcode, $mlEmsg, $result);
    }

}

/* vim: set expandtab ts=4 sw=4 sts=4 tw=100: */
