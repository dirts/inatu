<?php
namespace Inauth\Libs\Util;
/**
 * 用于userId反解
 * Created by PhpStorm.
 * User: xiaolongrong
 * Date: 15/11/3
 * Time: 下午4:52
 */
use \Inauth\Package\Util\Utilities;

class PassportInverseSolution
{
    private static $final_key = '9p57dvr6';    //MCRYPT_DES算法支持的密钥最大长度是8位

    //加密user_id
    public static function inverse_encode ($origin) {
        $td = mcrypt_module_open(MCRYPT_DES,'','ecb','');         //使用MCRYPT_DES算法,ecb模式
        $size=mcrypt_enc_get_iv_size($td);                        //设置初始向量的大小
        $iv = mcrypt_create_iv($size, MCRYPT_RAND);               //创建初始向量
        mcrypt_generic_init($td,self::$final_key,$iv);            //初始处理
        $encode = mcrypt_generic($td, $origin);                   //加密
        mcrypt_generic_deinit($td);                               //结束处理
        mcrypt_module_close($td);
        $finalEncodeResult = utf8_encode(base64_encode($encode)); //base64encode和utf-8encode
        return $finalEncodeResult;
    }

    //反解出user_id
    public static function inverse_decode ($needDecrypt) {
        $td = mcrypt_module_open(MCRYPT_DES,'','ecb','');         //使用MCRYPT_DES算法,ecb模式
        $size=mcrypt_enc_get_iv_size($td);                        //设置初始向量的大小
        $iv = mcrypt_create_iv($size, MCRYPT_RAND);               //创建初始向量
        mcrypt_generic_init($td,self::$final_key,$iv);            //初始处理
        $encode = base64_decode(utf8_decode($needDecrypt));       //utf-8decode和base64decode
        $decrypted = mdecrypt_generic($td,$encode);               //解密
        $finalDecodeResult = rtrim($decrypted,"\0");              //解密后,可能会有后续的\0,需去掉
        mcrypt_generic_deinit($td);                               //结束
        mcrypt_module_close($td);
        return $finalDecodeResult;
    }

    public static function fetchUserId ($needDecrypt, $index = 1) {
        if ( empty($needDecrypt) ) {
            return 0;
        }
        $decrypted = self::inverse_decode($needDecrypt);
        $new_arr = explode("\t",$decrypted);
        //newarr组成:$session_id\t$user_id\t$expireUnixTime
        if (count($new_arr) == 3 && isset($new_arr[$index]) && !empty($new_arr[$index])) {
            $rand = rand(0,1000);
            if(strlen($new_arr['2']) == 10) {   //时间戳是10位的
                if (time() <= $new_arr['2']) {
                    return $new_arr[$index];
                } else {
                    if ($rand == 666) {    //已过期的反解cookie，降低打印日志量
                        Utilities::log('InauthInverseSolutionExpired', print_r(array('needDecrypt'=>$needDecrypt, 'fetchInverseArr'=>$new_arr), TRUE));
                    }
                }
            } else {
                Utilities::log('InauthInverseSolutionWrong', print_r(array('needDecrypt'=>$needDecrypt, 'fetchInverseArr'=>$new_arr), TRUE));
            }
        }
        return 0;
    }
}
