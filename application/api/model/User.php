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

class User
{

	/** 
	 * 根据jwt获取是哪个用户
	 *
	 * @access 	public
	 * @param 	string 	$jwt 		jwt token
	 * @return 	array
	 */
	public function jwt($jwt)
	{
		$map 	= ['jwt' => $jwt];
		$data 	= Db::name('doctor_auth')->where($map)->find();
		return $data ? $data : [];
	}

	/** 
	 * 根据电话获取是哪个用户
	 *
	 * @access 	public
	 * @param 	string 	$phone 		电话号码
	 * @return 	array
	 */
	public function userByPhone($phone)
	{
		$map 	= ['phone' => $phone];
		$data 	= Db::name('doctor_auth')->where($map)->find();
		return $data ? $data : [];
	}

	/** 
	 * 根据token获取是哪个用户
	 *
	 * @access 	public
	 * @param 	string 	$token 		修改token
	 * @return 	array
	 */
	public function userByToken($token)
	{
		$map 	= ['token' => $token];
		$data 	= Db::name('doctor_auth')->where($map)->find();
		return $data ? $data : [];
	}

	/** 
	 * 保存新的jwt
	 *
	 * @access 	public
	 * @param 	int 	$doctorId 	医生id
	 * @param 	string 	$jwt 		jwt
	 * @param 	int 	$expire 	过期时间
	 * @return 	boolen
	 */
	public function saveJwt($doctorId , $jwt , $expire , $equip)
	{
		$map 	= ['doctor_id' => $doctorId];
		$update = ['jwt' => $jwt , 'expire' => $expire , 'login_equip' => $equip];
		return Db::name('doctor_auth')->where($map)->update($update) !== false ? true : false;
	}


    public static function setLoginEquip($doctorId, $equip)
    {
        $map 	= ['doctor_id' => $doctorId];
        $update = ['login_equip' => $equip];
        return Db::name('doctor_auth')->where($map)->update($update) !== false ? true : false;
    }

	/** 
	 * 保存新的短信验证码
	 *
	 * @access 	public
	 * @param 	int 	$doctorId 	医生id
	 * @param 	string 	$code 		短信验证码
	 * @param 	int 	$expire 	过期时间
	 * @return 	boolen
	 */
	public function saveCode($doctorId , $code , $expire)
	{
		$map 	= ['doctor_id' => $doctorId];
		$update = ['code' => $code , 'code_expire' => $expire];
		return Db::name('doctor_auth')->where($map)->update($update) !== false ? true : false;
	}

	/** 
	 * 保存新的密码token
	 *
	 * @access 	public
	 * @param 	int 	$doctorId 	医生id
	 * @param 	string 	$token 		修改密码token
	 * @param 	int 	$expire 	过期时间
	 * @return 	boolen
	 */
	public function saveToken($doctorId , $token , $expire)
	{
		$map 	= ['doctor_id' => $doctorId];
		$update = ['token' => $token , 'token_expire' => $expire , 'code' => '' ];
		return Db::name('doctor_auth')->where($map)->update($update) !== false ? true : false;
	}

    /**
     * 将token设置为过期
     * @param $token
     */
    public static function setTokenTimeout($token){
        Db::name('doctor_auth')->where(['token' => $token])->update(['token_expire'=>time()]);
    }

	/** 
	 * 保存新的密码
	 *
	 * @access 	public
	 * @param 	int 	$doctorId 	医生id
	 * @param 	string 	$password 	修改密码token
	 * @return 	boolen
	 */
	public function savePassword($doctorId , $password)
	{
		$map 	= ['doctor_id' => $doctorId];
		$update = ['password' => $password ];
		return Db::name('doctor_auth')->where($map)->update($update) !== false ? true : false;
	}

	/**
	 * 消除jwt
	 *
	 * @access 	public
	 * @param 	int 	$doctorId 	医生id
	 * @return 	boolen
	 */
	public function unsetJwt($doctorId)
	{
		$map 	= ['doctor_id' => $doctorId];
		$update	= ['jwt' => ''];
		return Db::name('doctor_auth')->where($map)->update($update) !== false ? true : false;
	}



}