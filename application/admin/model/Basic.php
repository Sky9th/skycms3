<?php
/**
 * Created by PhpStorm.
 * User: Sky9th
 * Date: 2018/6/6
 * Time: 10:23
 */
namespace app\admin\model;

use think\Model;

class Basic extends Model{

    public function getStatusAttr($value)
    {
        $status = config('static.status');
        return $status[$value];
    }


}