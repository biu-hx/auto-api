<?php
namespace app\timing\model;

use think\Db;

class DiymenSet
{


	/**
	 * 订单详情
	 *
	 * @access 	public
	 * @param 	int 	$orderId 		订单ID
	 * @return 	array
	 */
	public static function getDataList()
	{
		$data 	= Db::name('diymen_set')->select();
		return $data ? $data : [];
	}


    /**
     * 保存数据
     * @return array|false|\PDOStatement|string|\think\Collection
     */
    public static function saveAccessToken($id,$accesstoken)
    {
        $map 	= ['id' => $id];
        $update = ['accesstoken' => $accesstoken];
        $result = Db::name('diymen_set')->where($map)->update($update);
        return $result !== false ? true : false;
    }

    public static function detail($id){
        $map 	= ['id' => $id];
        $data 	= Db::name('diymen_set')->where($map)->find();
        return $data ? $data : [];
    }

    public static function updateAccessTokenByToken($token,$accesstoken){
        $map 	= ['token' => $token];
        $update = ['accesstoken' => $accesstoken];
        $result = Db::name('diymen_set')->where($map)->update($update);

    }

    /**
     * 创建小程序token
     * @param $id
     */
    
    public static function createTokenAndSave($userinfo,$expitreTs){
        $expitreTs = $expitreTs+time();
        $openid = $userinfo['openId'];
        $token = md5($openid.time().rand(10000,99999));
        //查询用户是否已存在
        $laDb = Db::name('littleapp_user');
        $laLogDb = Db::name('littleapp_token_log');
        $where = array("openid"=>$openid);
        $userInfo = $laDb->where($where)->find();
        //存在则更新
        $data = array();
        $data['little_app_token'] = $token;
        $data['token_expireTs'] = $expitreTs;
        $data['update_time'] = date("Y-m-d H:i:s");
        $data['nickname'] = $userinfo['nickName'];
        $data['sex'] = $userinfo['gender'];
        $data['language'] = $userinfo['language'];
        $data['city'] = $userinfo['city'];
        $data['province'] = $userinfo['province'];
        $data['country'] = $userinfo['country'];
        $data['headimgurl'] = $userinfo['avatarUrl'];
        $data['remark'] = $userinfo['watermark'];
        if($userInfo){
            $laDb->where($where)->update($data);
            $bind_doctor = $userInfo['bind_doctor'];
        }else{//不存在则写入
            $data['create_time'] = date("Y-m-d H:i:s");
            $data['openid'] = $openid;
            $laDb->insert($data);
            $bind_doctor = '';
        }
        //写入生成日志表
        $logData = array("little_app_token"=>$token,'token_expireTs'=>$expitreTs,'createTs'=>date("Y-m-d H:i:s"),'openid'=>$openid);
        $laLogDb->insert($logData);
        return ['token'=>$token,'bind_doctor'=>$bind_doctor] ;
    }


}