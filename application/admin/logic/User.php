<?php
/**
 * Author: Sky9th
 * Date: 2017/4/2
 * Time: 17:11
 */

namespace app\admin\logic;
use app\admin\model\User as UserModel;

class User {

    /**
     * 登陆
     * @param $username
     * @param $password
     * @param $code
     * @param $auto
     * @param $url
     * @return array
     */
    public function login($username, $password, $code, $auto = false, $url = false){
        if( !captcha_check($code , 'login') ){
            return error(lang('verify error'));
        }
        if( strlen($password) < 6 && strlen($password) > 20 ){
            return error('密码错误');
        }
        $model = new UserModel();
        $user = $model->where('username', $username)->find();
        if( !$user ){
            return error('该账号尚未注册');
        }
        $seed = $user['seed'];
        $password = md5(config('encryption_key').$seed.$password);
        $res = $model->where('username',$username)->where('password',$password)->find();
        if( count($res) == 1 ){ //用户密码正确
            //注册登录状态
            if( $this->do_login($res, $auto) ){
                if( !$url ){
                    $url = url('admin/index/index');
                }
//                app_log(1, 0,'login');
                return success(lang('login success'), $url);
            }
        }
//        app_log(1, 0 , 'login_fail', ['username'=>$username, 'password'=>$password ] );
        return error(lang('login fail'));
    }

    /**
     * 注册登陆状态
     * @param $data
     * @param $auto
     * @return bool
     */
    public function do_login($data, $auto){
        session(md5('user_id'),$data['id']);
        if( $auto ){
            cookie(md5('username'),$data['username'], 3600*24*3 );
            cookie(md5('password'), sha1( $data['username'].$data['password'].$_SERVER['HTTP_USER_AGENT']), 3600*24*3 );
        }
        $this->update( ['last_session_id' => session_id() , 'last_login' => time() ] , [ 'id' => $data['id'] ] );
        if( session(md5('user_id')) ){
            return true;
        }else{
            return false;
        }
    }

    /**
     * 退出登陆
     * @return bool
     */
    static public function logout(){
        session(md5('user_id'), null);
        cookie(md5('username'), null);
        cookie(md5('password'), null);
        if( !session(md5('user_id')) && !cookie(md5('username')) && !cookie(md5('password')) ){
            return true;
        }
        return false;
    }

    /**
     * 获取管理员信息
     * @return array
     */
    public function info(){
        $admin = UserModel::get(is_admin(),'auth');
        $data = $admin->toArray();

        $data['default'] = '';
        if( $data['avatar'] == '0' ){
            $sex = $data['sex']  == '男' ? 0 : 1;
            $data['default'] = '/static/common/images/avatar'. $sex .'.png';
        }

        $rules = [];
        foreach ($data['auth'] as $key => $value){
            if(  $value['rules'] == '0' || !empty($value['rules']) ){
                $_r = $value['rules'];
            }else{
                $_r = $this->getParentRules($value['pid']);
            }
            $data['auth'][$key]['rules'] = $_r;
            $rules = array_merge($rules,explode(',', $_r));
        }
        $data['rules'] = $rules;
        return $data;
    }

    /**
     * 获取父级权限节点
     * @param $pid
     * @return mixed
     */
    protected function getParentRules($pid){
        $role = db('role')->find($pid);
        if( empty($role['rules']) && $role ){
            $role = $this->getParentRules($pid);
        }
        return $role['rules'];
    }

}
