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

class App
{

	/**
	 * Android 最新版本
	 *
	 * @access 	public
	 * @return 	array
	 */
	public function version()
	{
		$data 	= Db::name('android_app')->limit(1)->order('id desc')->find();
		return $data ? $data : [];
	}





}