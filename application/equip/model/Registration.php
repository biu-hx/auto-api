<?php
namespace app\equip\model;

use think\Db;

class Registration 
{

	/**
	 * 获取挂号订单详情
	 *
	 * @access 	public
	 * @param 	int 	$orderId 		订单号
	 * @return 	array
	 */
	public function detailByOrderId($orderId)
	{
		$map 	= ['order_id' => $orderId];
		$data 	= Db::name('order_registration')->where($map)->find();
		return $data;
	}

	/**
	 * 查询挂号信息
	 * 
	 * @access 	public
	 * @param 	string 	$cardId 		就诊卡卡号
	 * @param  	int 	$scheduleId 	排班Id
	 * @return 	array
	 */
	public function detailByScheduleId($cardId , $scheduleId)
	{
		$map 	= ['a.card_id' => $cardId , 'a.schedule_id' => $scheduleId , 'b.status' => 0];
		$data 	= Db::name('order_registration')
				->alias('a')
				->join('emt_order b' , 'a.order_id = b.id')
				->where($map)
				->find();
		return $data ? $data : [];	
	}
	
	/**
	 * 设置挂号成功
	 * 
	 * @access 	public
	 * @param 	int 	$id 			挂号记录Id
	 * @param  	array 	$successInfo 	成功信息
	 * @return 	boolen
	 */
	public function setSuccess($id , $successInfo)
	{
		$map 	= ['id' => $id];
		$update = ['status' => 1 , 'success_info' => is_array($successInfo) ? json_encode($successInfo , JSON_UNESCAPED_UNICODE) : $successInfo];
		$result = Db::name('order_registration')->where($map)->update($update);
		return $result !== false ? true : false;
	}

	/**
	 * 设置挂号失败
	 * 
	 * @access 	public
	 * @param 	int 	$id 			挂号记录Id
	 * @return 	boolen
	 */
	public function setFail($id)
	{
		$map 	= ['id' => $id];
		$update = ['status' => 2];
		$result = Db::name('order_registration')->where($map)->update($update);
		return $result !== false ? false : true;
	}

	/**
	 * 获取取号订单详情
	 *
	 * @access 	public
	 * @param 	int 	$orderId 		订单号
	 * @return 	array
	 */
	public function fetchByOrderId($orderId)
	{
		$map 	= ['order_id' => $orderId];
		$data 	= Db::name('order_fetch')->where($map)->find();
		return $data;
	}

	/**
	 * 设置取号成功
	 * 
	 * @access 	public
	 * @param 	int 	$id 			取号记录Id
	 * @param  	array 	$successInfo 	成功信息
	 * @return 	boolen
	 */
	public function setFetchSuccess($id , $successInfo)
	{
		$map 	= ['id' => $id];
		$update = ['status' => 1 , 'success_info' => is_array($successInfo) ? json_encode($successInfo , JSON_UNESCAPED_UNICODE) : $successInfo];
		$result = Db::name('order_fetch')->where($map)->update($update);
		return $result !== false ? true : false;
	}

	/**
	 * 设置取号失败
	 * 
	 * @access 	public
	 * @param 	int 	$id 			取号记录Id
	 * @return 	boolen
	 */
	public function setFetchFail($id)
	{
		$map 	= ['id' => $id];
		$update = ['status' => 2];
		$result = Db::name('order_fetch')->where($map)->update($update);
		return $result !== false ? false : true;
	}

    /**
     *  查看当天是否已经挂号 指定医生
     * @param $cardId
     * @param $docHisId
     * @param $hId
     */
    public function detailByCardIdDocId($cardId , $docHisId , $hId){
        $startTime = strtotime(date('Y-m-d') , time());
        $endTime   = $startTime + 60 * 60 * 24;
        $sql = "SELECT * FROM `emt_order_registration` AS reg 
JOIN `emt_order` AS o ON o.id=reg.`order_id` 
WHERE reg.card_id='{$cardId}' AND reg.hid='{$hId}' AND reg.business_info LIKE '%\"doctorHisId\":\"{$docHisId}\"%'
 AND reg.status=1 AND o.create_time >='{$startTime}' AND o.create_time <='{$endTime}'";
        $data = Db::query($sql);
        return $data;
    }

}