<?php

namespace app\equip\event\v1;

use think\Loader;
use app\equip\controller\Base;
use app\component\response\Response;
use app\component\server\Server;


class Inquiry extends Base
{
	protected $validate = '\app\equip\validate\v1\Inquiry';

	protected $scene = [
		'doctor',
		'doctorDetail',
		'inquiryOrder',
		'inquiryQuery',
		'inquiryConnect',
		'inquiryScreen',
		'inquiryReport'
	];

	public function dept()
	{
		$data 	= Loader::model('Dept')->deptList();
		$dept 	= [];
		foreach ($data as $v) {
			$dept[] 	= ['deptId' => $v['id'] , 'deptName' => $v['title']];
		}
		Response::message(10000 , $dept);
	}

	public function doctor()
	{
		$deptId = $this->data['deptId'];
		$data 	= Loader::model('Doctor')->inquiryDoctor($deptId);
		$doctor = [];
		foreach ($data as $v) {
			if ($v['online'] == 1 && $v['busy'] == 1) {
				$v['online'] = 2;
			}
			$doctor[] 	= [
				'doctorId' 	=> $v['doctor_id'],
				'picture' 	=> $v['picture'], 
				'doctorName'=> $v['name'], 
				'disease' 	=> $v['sections'], 
				'online' 	=> $v['online'],
				'title' 	=> $v['title'],
			];
		}
		Response::message(10000 , $doctor);
	}

	public function doctorDetail()
	{
		$doctorId 	= $this->data['doctorId'];
		$data 	= Loader::model('Doctor')->inquiryDoctorDetail($doctorId);
		if (!$data) {
			Response::message(30013);
		}
		if ($data['online'] == 1 && $data['busy'] == 1) {
			$data['online'] = 2;
		}
		$doctor = [
			'doctorId' 	=> $data['doctor_id'],
			'picture' 	=> $data['picture'],
			'doctorName'=> $data['name'],
			'title' 	=> $data['title'],
			'profile' 	=> $data['content'],
			'uptime' 	=> $data['uptime'],
			'disease' 	=> $data['sections'],
			'price' 	=> $data['price'],
			'online' 	=> $data['online'],
		];
		Response::message(10000 , $doctor);
	}

	/**
	 * @access 	public
	 * @return 	void
	 */
	public function inquiryOrder()
	{
		$doctorId 	= $this->data['doctorId'];
		$equipId 	= $this->equipId;
		$data 	= Loader::model('Doctor')->inquiryDoctorDetail($doctorId);
		if (!$data) {
			Response::message(30013);
		}
		if ($data['online'] != 1 || $data['busy'] == 1) { 	//如果为在线才可以发起订单
			Response::message(30014);
		} 
		$hid 		= $data['hid'];
		$fee 		= $data['price'];	
		$equip 		= Loader::model('Equip')->detail($equipId);
		$order 	 	= Loader::model('Order')->orderInquiry($equipId , $equip , $fee , $hid , $doctorId);
		if (!$order) {
			Response::message(30015); 	//
		}
		$doctor 	= Loader::model('Doctor')->detail($doctorId);
		$hospital 	= Loader::model('Hospital')->detail($hid);
		$deptId 	= $doctor['dept_id'];
		$dept 		= [];
		if ($deptId) {
			$dept 	= Loader::model('Dept')->detail($deptId);
		}
		$order['hospitalName'] 	= $hospital['title'];
		$order['deptName'] 		= $dept ? $dept['title'] : '暂无';
		$order['doctorName'] 	= $doctor['name'];
		Response::message(10000 , $order);
	}

	/**
	 * @access 	public
	 * @return 	void
	 */
	public function inquiryQuery()
	{
		$orderNumber = $this->data['orderNum'];
		$data 		= Loader::model('Order')->detailByNumber($orderNumber);
		if (!$data || $data['type'] != 1) {
			Response::message(30011);
		}
		if ($data['status'] <= 0) {
			Response::message(10000 , [
				'orderNum' 	=> $orderNumber,
				'payStatus' => $data['status'],
			]);
		}
		$orderId 	= $data['id'];
		$inquiry 	= Loader::model('Inquiry')->detailByOrderId($orderId);
		$hid 		= $inquiry['hid'];
		$doctorId 	= $inquiry['doctor_id'];
		$inquiryId  = $inquiry['id'];
	
		$doctor 	= Loader::model('Doctor')->detail($doctorId);
		$hospital 	= Loader::model('Hospital')->detail($hid);
		$deptId 	= $doctor['dept_id'];
		$dept 		= [];
		if ($deptId) {
			$dept 	= Loader::model('Dept')->detail($deptId);
		}
		$response 	= [
			'orderNum' 		=> $orderNumber,
			'payStatus' 	=> $data['status'],
			'hospitalName' 	=> $hospital['title'],
			'deptName' 		=> $dept ? $dept['title'] : '暂无',
			'doctorName' 	=> $doctor['name'],
		];
		if ($data['status'] > 0) {
			$orderPay 	= Loader::model('Order')->payDetail($orderId);	
			$response['payPrice'] 	= $orderPay['price'];
			$response['payTime'] 	= $orderPay['pay_time'];
			$response['transactionId'] 	= $orderPay['transaction_id'];
			if ($data['status'] == 1) {
				$response['status'] = $inquiry['status'];
				$response['report']	= Loader::model('Inquiry')->report($inquiryId);
			}
		}
		Response::message(10000 , $response);
	}

	/**
	 * @access 	public
	 * @return 	void
	 */
	public function inquiryConnect()
	{
		$orderNum 	= $this->data['orderNum'];
		$equipId 	= $this->equipId;
		$order 	    = Loader::model('Order')->detailByNumber($orderNum);
		if ($order['type'] != 1 || $order['status'] != 1) {
			Response::message(30011);
		}
		$orderId 	= $order['id'];
		$inquiry 	= Loader::model('Inquiry')->detailByOrderId($orderId);
		$doctorId 	= $inquiry['doctor_id'];
		if (!in_array($inquiry['status'] , [0 , 1 , 11])) {
			Response::message(30020);
		}
		$data 	= Loader::model('Doctor')->inquiryDoctorDetail($doctorId);
		if ($data['online'] != 1) { 	//如果为在线才可以发起订单
			Response::message(30014);
		} 
		$doing 		= Loader::model('Inquiry')->doctorDoing($doctorId);
		if ($doing && $doing['order_id'] != $orderId) {
			Response::message(30018);
		} 
		$result 	= Loader::model('Inquiry')->setDoctorDoing($equipId , $doctorId , $inquiry['id']);
		if (!$result) {
			Response::message(30019);
		}
		$dAcount 	= Loader::model('Doctor')->accountHuawei($doctorId);
		Response::message(10000 , [
			'doctorAccount' 	=> '0'.substr($dAcount['number'] , 3),
			'userAccount' 		=> substr($result['account'] , 3),
			'userPassword' 		=> $result['password'],
			'callId' 			=> $result['callId'],
		]);
	}
	
	/**
	 * @access 	public
	 * @return 	void
	 */
	public function inquiryScreen()
	{
		$orderNum 	= $this->data['orderNum'];
		$callId 	= $this->data['callId'];
		$picture 	= $this->data['picture'];
		$order 	= Loader::model('Order')->detailByNumber($orderNum);
		if ($order['type'] != 1 || $order['status'] != 1) {
			Response::message(30011);
		}
		$orderId 	= $order['id'];
		$inquiry 	= Loader::model('Inquiry')->detailByOrderId($orderId);
		$inquiryId 	= $inquiry['id'];
		$result 	= Loader::model('Inquiry')->addScreen($inquiryId , $callId , $picture);
		Response::message(10000 , [
			'orderNum' 	=> $orderNum,
			'callId' 	=> $callId,
			'picture' 	=> $picture,
		]);
	
	}

	/**
	 * @access 	public
	 * @return 	void
	 */
	public function inquiryReport()
	{
		$orderNum 	= $this->data['orderNum'];
		$picture 	= $this->data['picture'];
		$order 	= Loader::model('Order')->detailByNumber($orderNum);
		if ($order['type'] != 1 || $order['status'] != 1) {
			Response::message(30011);
		}
		$orderId 	= $order['id'];
		$inquiry 	= Loader::model('Inquiry')->detailByOrderId($orderId);
		$inquiryId 	= $inquiry['id'];
		$result 	= Loader::model('Inquiry')->addReport($inquiryId , $picture);
		Response::message(10000 , [
			'orderNum' 	=> $orderNum,
			'picture' 	=> $picture,
		]);
	}	



	
}