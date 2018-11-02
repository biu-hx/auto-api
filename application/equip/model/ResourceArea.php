<?php
namespace app\equip\model;

use think\Db;

class ResourceArea
{
	
	/**
	 * ID 换设备信息
	 *
	 * @access 	public	
	 * @param 	int 	$id 	设备主键
	 * @return 	array
	 */
	public function getAreaList($parent_id=0)
	{
		$map 	= ['parent_id' => $parent_id];
		$data 	= Db::name('resource_area')->where($map)->select();
		return $data ? $data : [];
	}




}