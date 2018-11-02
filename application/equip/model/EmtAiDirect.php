<?php
namespace app\equip\model;

use think\Db;

class EmtAiDirect
{

	/**
	 * 
	 * @access 	public
	 * @param 	int 	$id 	业务处理的订单ID
	 * @return 	int
	 */
	public static function addDirectValue()
	{
		$map = ['service_type' => 'service' ];
        $data 	= Db::name('ai_keyword')->where($map)->select();
        return $data ? $data : [];
	}













	


}