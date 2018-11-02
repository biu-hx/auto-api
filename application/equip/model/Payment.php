<?php
namespace app\equip\model;

use think\Db;

class Payment 
{

	/**
	 * 住院缴费信息
	 *
	 * @access 	public	
	 * @param 	int 	$orderId 		订单号
	 * @return 	array
	 */
	public function inpatientByOrderId($orderId)
	{
		$map 	= ['order_id' => $orderId];
		$data 	= Db::name('order_inpatient')->where($map)->find();
		//error_log("SQL => " . Db::name("order_inpatient")->getLastSql());
		return $data ? $data : [];
	}

	/**
	 * 住院缴费成功
	 *
	 * @access 	public	
	 * @param 	int 	$id 			缴费信息ID
	 * @param 	array 	$successInfo 	成功信息
	 * @return 	boolen
	 */
	public function setInpatientSuccess($id , $successInfo)
	{
		$map 	= ['id' => $id];
		$update = ['status' => 1 , 'success_info' => is_array($successInfo) ? json_encode($successInfo , JSON_UNESCAPED_UNICODE) : $successInfo];
		$result = Db::name('order_inpatient')->where($map)->update($update);
		return $result !== false ? true : false;
	}

	/**
	 * 住院缴费失败
	 *
	 * @access 	public	
	 * @param 	int 	$id 			缴费信息ID
	 * @return 	boolen
	 */
	public function setInpatientFail($id)
	{
		$map 	= ['id' => $id];
		$update = ['status' => 2];
		$result = Db::name('order_inpatient')->where($map)->update($update);
		return $result !== false ? true : false;
	}

	/**
	 * 门诊缴费信息
	 *
	 * @access 	public	
	 * @param 	int 	$orderId 		订单号
	 * @return 	array
	 */
	public function outpatientByOrderId($orderId)
	{
		$map 	= ['order_id' => $orderId];
		$data 	= Db::name('order_outpatient')->where($map)->find();
		return $data ? $data : [];
	}

	/**
	 * 门诊缴费成功
	 *
	 * @access 	public	
	 * @param 	int 	$id 			缴费信息ID
	 * @param 	array 	$successInfo 	成功信息
	 * @return 	boolen
	 */
	public function setOutpatientSuccess($id , $successInfo)
	{
		$map 	= ['id' => $id];
		$update = ['status' => 1 , 'success_info' => is_array($successInfo) ? json_encode($successInfo , JSON_UNESCAPED_UNICODE) : $successInfo];
		$result = Db::name('order_outpatient')->where($map)->update($update);
		return $result !== false ? true : false;
	}

	/**
	 * 门诊缴费失败
	 *
	 * @access 	public	
	 * @param 	int 	$id 			缴费信息ID
	 * @return 	boolen
	 */
	public function setOutpatientFail($id)
	{
		$map 	= ['id' => $id];
		$update = ['status' => 2];
		$result = Db::name('order_outpatient')->where($map)->update($update);
		return $result !== false ? true : false;
	}
}