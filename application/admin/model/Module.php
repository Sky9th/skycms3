<?php
/**
 * Created by PhpStorm.
 * User: Sky9th
 * Date: 2018/6/6
 * Time: 10:41
 */
namespace app\admin\model;

class Module extends Basic{

    /**
     * 获取后台菜单
     * @return array
     */
    public function tree(){

        /*$admin = new User();
        $info = $admin->info();
        $auth = $info['auth'];
        $roles = $info['rules'];
        $super = false;
        foreach ($auth as $key => $value) {
            if( $value['id'] == '1' ){
                $super = true;
                break;
            }
        }*/
        $roles = [];
        $super = true;

        $list = db('module')->where('status',1)->where('visible',1)->order('sort desc')->select();
        foreach ($list as $key => $value) {
            if( in_array($value['id'], $roles)){
                $list[$key]['access'] = true;
            }else{
                $list[$key]['access'] = false;
            }
            if( empty($value['icon']) ){
                if( is_chinese($value['title']) ){
                    $front = msubstr($value['title'],0,1,'utf-8',false);
                }else{
                    $front = strtoupper(msubstr($value['title'],0,1,'utf-8',false));
                    $front .= strtolower(msubstr($value['title'],1,1,'utf-8',false));
                }
                $list[$key]['thumbnail'] = "{$front}";
            }else{
                $list[$key]['thumbnail'] = "<i class='{$value['icon']}'></i>";
            }
            $ext = [];
            if( $value['param'] ) {
                $param = explode(PHP_EOL,$value['param']);
                foreach ($param as $k => $v) {
                    list($name, $data) = explode(':', $v);
                    $ext[$name] = $data;
                }
            }
            $list[$key]['param'] = $ext;
        }
        $tree = list_to_tree($list);
        if (!$super) {
            foreach ($tree as $key => $value) {
                if ( !isset($value['_child']) && $value['access'] == false ) {
                    unset($tree[$key]);
                } else if( isset($value['_child'])  ){
                    foreach ($value['_child'] as $k => $v) {
                        if (!$v['access']) {
                            unset($tree[$key]['_child'][$k]);
                        }
                    }
                    if( count($tree[$key]['_child']) == 0 ){
                        unset($tree[$key]);
                    }
                }
            }
        }
        return $tree;
    }

}