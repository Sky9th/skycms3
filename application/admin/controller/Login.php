<?php
/**
 * Author: Sky9th
 * Date: 2017/4/2
 * Time: 17:11
 */

namespace app\admin\controller;
use app\admin\logic\User;
use think\Controller;

class Login extends Controller{

    public function index(){
        return view();
    }

    public function login(){

        //获取用户表单提交信息
        $username = input('post.username');
        $password = input('post.password');
        $code = input('post.code');
        $auto = input('post.auto');

        //实例化后台管理员表模型
        $admin = new User();
        $res = $admin->login($username, $password, $code, $auto);
        return $res;
    }

    public function logout(){
        if( User::logout() ){
            $this->success(lang('logout success'));
        }else{
            $this->success(lang('logout error'));
        }
    }

    public function account(){
        $data = input('post.');
        $data['id'] = is_admin();
        return O('Admin/User', $data, 'self', '', 'account', 'admin_account_edit');
    }

    public function password(){
        $data = input('post.');
        $data['id'] = is_admin();
        $data['seed'] = rand(1000,9999);
        return O('Admin/User', $data, 'self', '', 'password', 'admin_password_reset');
    }

}
