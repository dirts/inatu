<?php
namespace Inauth\Package\User;

use \Libs\Util\Utilities;
use \Inauth\Package\User\Helper\DBDugongHelper;

/**
 * 用户信息扩展-模块
 */
class UserExt extends User {

    //帐号拓展信息表
    static $table = 't_dolphin_user_profile_extinfo';
    //帐号拓展信息表字段
    static $col_ext = 'user_id, gender, birthday, province_id, city_id, about_me, avatar_c, is_taobao_buyer, verify_icons,
    verify_msg, weibo_url, is_taobao_seller, school, workplace, industry, hobby, shipping_address, post_code, mobile, qq, msn';
    static $avatar_list = array('avatar_a', 'avatar_b', 'avatar_c', 'avatar_d', 'avatar_e', 'avatar_o');
    const DefaultHeadImage = 'ap/c/78/32/59c5fb29fb8ea04deddb2ad0ff42_256_256.cf.png';

    /* 从db 获取用户ext信息 */
    static public function get_user_ext_info_from_db($user_id, $cols = array(), $master = false) {
        $param = array('user_id' => $user_id);
        $sql   = 'SELECT ' . self::$col_ext . ' FROM t_dolphin_user_profile_extinfo WHERE user_id = :user_id';
        $user_ext_info   = DBDugongHelper::getConn()->read($sql, $param, $master, 'user_id');
        if ( empty($user_ext_info) ) {
            return $user_ext_info;
        }
        $user_ext_info = $user_ext_info[$user_id];
        return $user_ext_info;
        empty($user_ext_info['user_id']) && $user_ext_info['user_id'] = $user_id;

        //转换avatar_c头像为URL形式
        $user_ext_info['avatar_c'] = self::convert_avatar_to_url($user_ext_info['avatar_c']);
        //将avatar_c头像转换为其它格式的头像
        $user_ext_info = self::convert_avatar_to_other($user_ext_info);

        //verify_msg在数据库中是json数据，需要json_decode
        if ( !empty($user_ext_info['verify_msg']) ) {
            $user_ext_info['verify_msg'] = json_decode($user_ext_info['verify_msg'], TRUE);
        }
        //verify_icons在数据库中是字符串，需要打散为数组
        if ( !empty($user_ext_info['verify_icons']) ) {
            $user_ext_info['verify_icons'] = explode(',', $user_ext_info['verify_icons']);
        }

        //根据用户verify_icons数据设置认证标识
        $user_ext_info = self::set_identity($user_ext_info);

        //$user_ext_info = self::format_user_ext_info($user_ext_info, $cols);
        return $user_ext_info;
    }

    /**
     * 从db 获取用户ext信息
     */
    /*
    static public function query($param, $fields = 'user_id, nickname, password', $master = true) {
        $where  = "nickname = :nickname and password = :password";
        $sql    = "SELECT $fields FROM `t_dolphin_user_profile_extinfo` WHERE $where";
        $data   = DBDugongHelper::getConn()->read($sql, $param, $master);
        return $data;
    }
    */
    

    /**
     * 注册帐号：插入用户拓展信息表
     */
    static public function register($param) {
        $conn = DBDugongHelper::getConn();
        $data = $conn->table('t_dolphin_user_profile_extinfo')->insert($param);
        return $data;
    }

    /**
     * 转换用户的头像地址为URL
     */
    static public function convert_avatar_to_url($avatar){
        $avatar = trim($avatar);
        if ( strpos($avatar, 'http://') === FALSE ) {
            return $avatar;
        }
        //如果帐号头像字段为空或等于默认头像，则使用新版的默认头像
        if ( empty($avatar) || strpos($avatar, 'css/images') !== FALSE || $avatar == 'ap/c/ba/1b/bb814d3c8e55e2004c8a62233ce0_128_128.c1.jpeg') {
            $avatar = self::DefaultHeadImage;
        }
        $avatar = \Inauth\Libs\Util\Util::convertPicture($avatar);
        return $avatar;
    }

    /**
     * 将avatar_c头像转换为其它格式的头像
     */
    static public function convert_avatar_to_other($user_ext_info){
        foreach(self::$avatar_list as $avatar_key) {
            $user_ext_info[$avatar_key] = str_replace('/c/', '/' . substr($avatar_key, strlen('avatar_')) . '/', $user_ext_info['avatar_c']);
        }
        return $user_ext_info;
    }

    /**
     * 根据用户verify_icons数据设置认证标识
     */
    static public function set_identity($user_ext_info) {
        if ( !empty($user_ext_info['verify_icons']) && is_array($user_ext_info['verify_icons']) ) {
            $user_ext_info['identity']['blueV']    = in_array('t', $user_ext_info['verify_icons']) ? '资深爱美丽' : '';
            $user_ext_info['identity']['pinkV']    = in_array('s', $user_ext_info['verify_icons']) ? '美丽说特别认证' : '';
            $user_ext_info['identity']['purpleV']  = in_array('b', $user_ext_info['verify_icons']) ? '美丽说认证品牌' : '';
            $user_ext_info['identity']['editorV']  = in_array('e', $user_ext_info['verify_icons']) ? '美丽说超级主编' : '';
            $user_ext_info['identity']['editorV']  = in_array('e', $user_ext_info['verify_icons']) ? '美丽说超级主编' : '';
            $user_ext_info['identity']['bloggerV'] = in_array('y', $user_ext_info['verify_icons']) ? '博主' : '';
            $user_ext_info['identity']['expertV']  = in_array('z', $user_ext_info['verify_icons']) ? '达人' : '';
            if ( $user_ext_info['user_id'] == 18185784 ) {
                $user_ext_info['identity']['blueV'] = '首都网警';
            }
        } else {
            $user_ext_info['identity']['blueV']   = '';
            $user_ext_info['identity']['pinkV']   = '';
            $user_ext_info['identity']['purpleV'] = '';
            $user_ext_info['identity']['editorV'] = '';
        }

        if ( !empty($user_ext_info['verify_msg']) && is_array($user_ext_info['verify_msg']) ) {
            foreach ($user_ext_info['verify_msg'] as $key => $value) {
                if ( $key == 't' ) {
                    $user_ext_info['identity']['description']['blueV']    = $value;
                } else if ( $key == 's' ) {
                    $user_ext_info['identity']['description']['pinkV']    = $value;
                } else if ( $key == 'b' ) {
                    $user_ext_info['identity']['description']['purpleV']  = $value;
                } else if ( $key == 'e' ) {
                    $user_ext_info['identity']['description']['editorV']  = $value;
                } else if ( $key == 'y' ) {
                    $user_ext_info['identity']['description']['bloggerV'] = $value;
                } else if ( $key == 'z' ) {
                    $user_ext_info['identity']['description']['expertV']  = $value;
                }
            }
        }
        if ( isset($user_ext_info['is_taobao_buyer']) && $user_ext_info['is_taobao_buyer'] == 1 ) {
            $user_ext_info['identity']['heart_buyer']   = '美丽说心级买家认证';
            $user_ext_info['identity']['diamond_buyer'] = '';
        } else if ( isset($user_ext_info['is_taobao_buyer']) && $user_ext_info['is_taobao_buyer'] == 2 ) {
            $user_ext_info['identity']['heart_buyer']   = '';
            $user_ext_info['identity']['diamond_buyer'] = '美丽说黄钻买家认证';
        } else {
            $user_ext_info['identity']['heart_buyer']   = '';
            $user_ext_info['identity']['diamond_buyer'] = '';
        }
        return $user_ext_info;
    }

    /**
     * 格式化用户拓展信息
     */
    static public function format_user_ext_info(){

    }
}
