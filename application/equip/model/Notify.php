<?php
namespace app\equip\model;

use think\Db;

class Notify 
{

	/**
	 * 
	 * @access 	public
	 * @param 	int 	$id 	业务处理的订单ID
	 * @return 	int
	 */
	public function setNotifyBusiness($id)
	{
		$insert = ['order_id' => $id , 'create_time' => time()];
		$id 	= Db::name('notify_business')->insertGetId($insert);
		return $id ? $id : 0;  
	}

	


}