<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件



/**
 * 判断是否为中文
 * @param $str
 * @return bool
 */
function is_chinese($str){
    if(!preg_match("[^\x80-\xff]","$str")){
        return true;
    }else{
        return false;
    }
}


/**
 * 把返回的数据集转换成Tree
 * @param array $list 要转换的数据集
 * @param string $pid parent标记字段
 * @param string $level level标记字段
 * @return array
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function list_to_tree($list, $pk='id', $pid = 'pid', $child = '_child', $root = '0') {
    // 创建Tree
    $tree = array();
    if(is_array($list)) {
        // 创建基于主键的数组引用
        $refer = array();
        foreach ($list as $key => $data) {
            $refer[$data[$pk]] =& $list[$key];
        }
        foreach ($list as $key => $data) {
            // 判断是否存在parent
            $parentId =  $data[$pid];
            if ($root == $parentId) {
                $tree[] =& $list[$key];
            }else{
                if (isset($refer[$parentId])) {
                    $parent =& $refer[$parentId];
                    $parent[$child][] =& $list[$key];
                }
            }
        }
    }
    return $tree;
}


/**
 * 字符串截取，支持中文和其他编码
 * @static
 * @access public
 * @param string $str 需要转换的字符串
 * @param string $start 开始位置
 * @param string $length 截取长度
 * @param string $charset 编码格式
 * @param string $suffix 截断显示字符
 * @return string
 */
function msubstr($str, $start=0, $length, $charset="utf-8", $suffix=true) {
    $str = str_replace('&nbsp;', '', $str);
    if(function_exists("mb_substr"))
        $slice = mb_substr($str, $start, $length, $charset);
    elseif(function_exists('iconv_substr')) {
        $slice = iconv_substr($str,$start,$length,$charset);
        if(false === $slice) {
            $slice = '';
        }
    }else{
        $re['utf-8']   = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
        $re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
        $re['gbk']    = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
        $re['big5']   = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
        preg_match_all($re[$charset], $str, $match);
        $slice = join("",array_slice($match[0], $start, $length));
    }
    return $suffix && mb_strlen($str , $charset) > $length ? $slice.'...' : $slice;
}




/**
 * 验证码<img>快捷生成
 * @param string $id
 * @param int $w 宽
 * @param int $h 高
 * @param int $fs 字体大小
 * @param int $lt 验证码位数
 */
function captcha_make($name = '', $w = 240, $h = 60, $fs = 30, $lt = 5){
    $src = url('/Verify',array(
        'name' => $name,
        'w' => $w,
        'h' => $h,
        'fs' => $fs,
        'lt' => $lt,
    ));
    $alt = config('WEB_TITLE').'验证码';
    $img = '<img src="'.$src.'" _src="'.$src.'" class="captcha_verify" onclick="captcha_refresh(this)" alt="'.$alt.'" />';
    return $img;
}

/**
 * 返回错误状态的数组
 * @param $msg
 * @return array
 */
function error($msg = false){
    if( !$msg ){
        $msg = '系统繁忙，请稍后再试';
    }
    return [ 'status' => 0 , 'info' => $msg ];
}

/**
 * 返回正常状态的数组
 * @param $msg
 * @param $url
 * @param $id
 * @return array
 */
function success($msg = '', $url = '' , $id =''){
    return [ 'status' => 1 , 'info' => $msg, 'url' => $url, 'id' => $id ];
}

/**
 * 跳转专用返回
 * @param $msg
 * @param $url
 * @return array
 */
function direct($msg, $url){
    return [ 'status' => 1 , 'info' => $msg, 'url' => $url];
}


/**
 * 检测用户是否登陆并返回其用户ID
 * @return int|mixed
 */
function is_login(){
    $user_id = session(md5('user_id')) ;

    if( empty($user_id) ){
        $username = cookie(md5('username'));
        if( empty($username) ){
            return 0;
        }
        $password = cookie( md5('password') );
        $exist = \think\Db::table('User')->where(array('username'=>$username))->find();
        if( $password == sha1( $username.$exist['password'].$_SERVER['HTTP_USER_AGENT']) ){
            $admin = new \app\admin\logic\User();
            $admin->do_login($exist,false);
            return $exist['id'];
        }else{
            return 0;
        }
    }

    $last_session_id = \think\Db::table('User')->where((array('id'=>$user_id)))->value('last_session_id');
    if( $last_session_id != session_id() ){
        //TODO:待测试
        if( session( md5('username') ) ){
            return -2;
        }else{
            return 0;
        }
    }
    if (empty($user_id)) {
        return 0;
    } else {
        return $user_id;
    }

}

/**
 * 检测用户是否登陆并返回其用户ID
 * @return int|mixed
 */
function is_admin(){
    return is_login();
}


/**
 * 数据库操作，传入数组自动判断更新或者插入数据，并根据影响的表将对应Module进行日志记录
 * @param $model_name string|object 模型名称 [ 模块/模型 ]
 * @param $data array 传入数据
 * @param string $validate_name string 验证器名称 [ 模块/验证器 ]
 * @param string $scene string 验证场景
 * @param string $pk string 主键字段名
 */
if( !function_exists('O') ) {
    function O($model_name, $data, $url = '',$validate_name = '', $scene = true, $action = '',$pk = 'id')
    {
        function self_class_exist($class)
        {
            if (class_exists($class)) {
                return new $class;
            } else {
                return false;
            }
        }
        if( !$validate_name ){
            $validate_name = $model_name;
        }
        $app = config('app_namespace');
        $models = explode('/', $model_name);
        $validates = explode('/', $validate_name);
        switch (count($models)) {
            case 1:
                $model = self_class_exist('\\'.$app . '\admin\model\\' . $model_name);
                $validate = self_class_exist('\\'.$app . '\admin\validate\\' . $validate_name);
                break;
            case 2:
                $model = self_class_exist('\\'.$app . '\\' . strtolower($models[0]) . '\model\\' . $models[1]);
                $validate = self_class_exist('\\'.$app . '\\' . strtolower($validates[0]) . '\validate\\' . $validates[1]);
                break;
            default:
                $model = [];
                $validate = [];
        }
        if( $validate ) {
            $check = $validate->scene($scene)->check($data);
            if (!$check) {
                return error($validate->getError());
            }
        }
        $map = [];
        if( isset($data[$pk]) ){
            $map[$pk] = $data[$pk];
        }
        $res = $model->allowField(true)->save($data, $map);
        if( $res ){
            $type = 1;
            if( !$action ){
                $type = 0;
                $action_data = $model;
            }else{
                $action_data = $data;
            }
            if( isset($data[$pk]) ){
                $id = $data[$pk];
                if( $action !== false ) {
                    app_log($type, $id, $action ?: 'update', $action_data);
                }
                return success('修改成功',$url);
            }else{
                $id = $model[$pk];
                if( $action !== false ) {
                    app_log($type, $id, $action ?: 'save', $action_data);
                }
                return success('上传成功',$url, $id);
            }
        }
        return error('操作失败');
    }
}