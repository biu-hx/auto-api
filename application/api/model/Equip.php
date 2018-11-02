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

class Equip
{

	/**
	 * 获取设备编码
	 *
	 * @access 	public 
	 * @param 	int 	$equipId 	设备id
	 * @return  array
	 */
	public function equip($equipId)
	{
		$map 	= ['id' => $equipId];
		$data 	= Db::name('equipment')->where($map)->find();
		return $data ? $data : [];
	}



}