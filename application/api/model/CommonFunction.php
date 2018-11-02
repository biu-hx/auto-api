<?php
// +----------------------------------------------------------------------
// | 公用数据相关类
// +----------------------------------------------------------------------
// | Author: duanhy <shongyudmxas@163.com> 
// +----------------------------------------------------------------------
// | version: 1.0
// +----------------------------------------------------------------------

namespace app\api\model;

use think\Db;

class CommonFunction
{
    const VERFY_CODE = 'CodeVerify';//验证码key前缀

    /**
     * 设置为此电话号码已验证
     * @param $phone 电话号码
     */
	public static function setCodeVerifyEd($phone){
        $key = self::VERFY_CODE.$phone;
        \app\component\server\Server::cache('redis')->set($key,1,300);
    }

    public static function checkCodeVerifyEd($phone){
        $key = self::VERFY_CODE.$phone;
        return \app\component\server\Server::cache('redis')->get($key);
    }


}