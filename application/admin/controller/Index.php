<?php
/**
 * Created by PhpStorm.
 * User: Sky9th
 * Date: 2018/6/5
 * Time: 13:38
 */

namespace app\admin\controller;

use app\admin\model\Module;
use think\Controller;

class Index extends Controller{

    public function index(){
        $module = new Module();
        $param['sidebar'] = $module->tree();
        return view('', $param);
    }

    public function dashboard(){
        return view();
    }

}