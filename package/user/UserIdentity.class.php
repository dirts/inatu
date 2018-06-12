<?php
namespace Inauth\Package\User;

/**
 * 用户信息-模块
 */
class UserIdentity {


    /* 身份认证信息 */ 
    static public function format_identity($user_info) {
        $result = array(
            'blueV'         => '',
            'pinkV'         => '',
            'purpleV'       => '',
            'editorV'       => '',
            'heart_buyer'   => '',
            'diamond_buyer' => '',
        );
        
        if (!empty($user_info['verify_icons']) && is_array($user_info['verify_icons'])) {
            $result['blueV']    = in_array('t', $user_info['verify_icons']) ? '资深爱美丽' : '';
            $result['pinkV']    = in_array('s', $user_info['verify_icons']) ? '美丽说特别认证' : '';
            $result['purpleV']  = in_array('b', $user_info['verify_icons']) ? '美丽说认证品牌' : '';
            $result['editorV']  = in_array('e', $user_info['verify_icons']) ? '美丽说超级主编' : '';
            $result['bloggerV'] = in_array('y', $user_info['verify_icons']) ? '博主' : '';
            $result['expertV'] = in_array('z', $user_info['verify_icons']) ? '达人' : '';
            //if ($user_id == 18185784) {
        } 
        
        if ($user_info['user_id'] == 71330941) {
            $result['blueV'] = '首都网警';
        }
        
        if (!empty($user_info['verify_msg']) && is_array($user_info['verify_msg'])) {
            foreach ($user_info['verify_msg'] as $key => $value) {
                if ($key == 't') {
                    $result['description']['blueV']   = $value;
                } elseif ($key == 's') {
                    $result['description']['pinkV']   = $value;
                } elseif ($key == 'b') {
                    $result['description']['purpleV'] = $value;
                } elseif ($key == 'e') {
                    $result['description']['editorV'] = $value;
                } elseif ($key == 'y') {
                    $result['description']['bloggerV'] = $value;
                } elseif ($key == 'z') {
                    $result['description']['expertV'] = $value;
                }
            }
        }
        
        if (isset($user_info['is_taobao_buyer'])) {
            if ($user_info['is_taobao_buyer'] == 1) {
                $result['heart_buyer']      = '美丽说心级买家认证';
            } elseif ($user_info['is_taobao_buyer'] == 2) {
                $result['diamond_buyer']    = '美丽说黄钻买家认证';
            }
        }
        return $result;
    }

}
