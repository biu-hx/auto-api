<?php
// +----------------------------------------------------------------------
// | 渠道数据
// +----------------------------------------------------------------------
// | Author: duanhy <shongyudmxas@163.com> 
// +----------------------------------------------------------------------
// | version: 1.0
// +----------------------------------------------------------------------

namespace app\api\model;

use think\Db;

class Littleapp
{

    /**
     * 获取小程序token
     *
     * @access    public
     * @param    int $orderId //订单id
     * @return  array
     */
    public static function littleAppTokenDetail($little_app_token)
    {
        $map = ['little_app_token' => $little_app_token];
        $data = Db::name('littleapp_user')->where($map)->find();
        return $data ? $data : [];
    }

    /**
     * 获取小程序token记录
     * @param $little_app_token
     * @return array|false|\PDOStatement|string|\think\Model
     */
    public static function getLittleTokenLog($little_app_token){
        $map = ['little_app_token' => $little_app_token];
        $data = Db::name('littleapp_token_log')->where($map)->find();
        return $data ? $data : [];
    }

    public static function resetLittleAppExpireTs($openid,$token_expireTs){
        $map = ['openid' => $openid];
        $update = ['token_expireTs' => time()+$token_expireTs];
        return $data = Db::name('littleapp_user')->where($map)->update($update);
    }


    public static function appTokenDetail($app_token){
        $map = ['token' => $app_token];
        $data = Db::name('doctor_auth')->where($map)->find();
        return $data ? $data : [];
    }

    public static function appTokenAddDoctor($openid,$doctorid,$form_id=""){
        $map = ['openid' => $openid];
        $update = ['bind_doctor' => $doctorid];
        return $data = Db::name('littleapp_user')->where($map)->update($update);
    }


    public static function setOtherUnbind($openid,$doctorid){
        $map = ['openid' =>['neq',$openid],'bind_doctor'=>$doctorid];
        $update = ['bind_doctor' => 0];
        return $data = Db::name('littleapp_user')->where($map)->update($update);
    }



}