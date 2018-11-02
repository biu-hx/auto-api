<?php
namespace app\timing\model;

use think\Db;

class Equipment
{


	/**
	 * 获取设备详情
	 *
	 * @access 	public
	 * @param 	int 	id 		设备ID
	 * @return 	array
	 */
	public function detail($id)
	{
		$map 	= ['id' => $id];
		$data 	= Db::name('equipment')->where($map)->find();
		return $data ? $data : [];
	}


}