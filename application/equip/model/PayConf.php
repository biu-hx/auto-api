<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/30 0030
 * Time: 17:10
 */

namespace app\equip\model;


use think\Db;

class PayConf
{
    //兼容数据用
    public function getPayConf(){
        $pay_type_list = Db::name('pay_type')->select();
        $pay = [];
        foreach ($pay_type_list as $val) {
            $pay['type'.$val['id']] = false;
        }
        return $pay;
    }

    //兼容数据用
    public function payTypeListByIds(array $payTypeIds){
        if(!$payTypeIds) return [];
        $data = Db::name('pay_type')->where(['id'=>['in',$payTypeIds]])->select();
        return $data ? $data : [];
    }
}