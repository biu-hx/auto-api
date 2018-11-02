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

class Order
{

	/**
	 * 获取订单信息
	 * 
	 * @access 	public
	 * @param 	int 	$orderId 	//订单id
	 * @return  array
	 */
	public static function basic($orderId)
	{
		$map 	= ['id' => $orderId];
		$data 	= Db::name('order')->where($map)->find();
		return $data ? $data : [];
	}

	/**
	 * 获取订单支付信息
	 * 
	 * @access 	public
	 * @param 	int 	$orderId 	//订单id
	 * @return  array
	 */
	public function pay($orderId)
	{
		$map 	= ['order_id' => $orderId];
		$data 	= Db::name('order_pay')->where($map)->find();
		return $data ? $data : [];
	}

}