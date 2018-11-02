<?php

namespace app\equip\event\v1;

use think\Loader;
use app\equip\controller\Base;
use app\component\response\Response;
use app\component\server\Server;

class Registration extends Base
{
	protected $validate = '\app\equip\validate\v1\Registration';

	protected $scene = [
		'dept',
		'doctor',
		'doctorDetail',
		'schedule',
		'lock',
		'registrationQuery',
	];

	public function hospital()
	{
		$hid    = [10000 , 61743];
		$data 	= Loader::model('Hospital')->listById($hid);
		$hospital	= [];
		foreach ($data as $v) {
			$hospital[] 	= ['hospitalId' => $v['id'] , 'logo' => $v['logo'] , 'hospitalName' => $v['name']];
		}
		Response::message(10000 , $hospital);
	}

 	/**
 	 * 驴脝脢脪脕脨卤铆
 	 *
 	 *
 	 *
 	 */
	public function dept()
	{
		$hid 	= $this->hospitalId;
		$data 	= Server::ability('hospital')->dutyDept($hid);
		if (!$data) { Response::message(10101); }
		$data['code'] != 10000 && Response::message(10102); 	
		$data 	= $data['data'];
		$dept 	= [];
		foreach ($data as $key => $value) {
			$dept[] 	= ['deptId' => $value['deptHisId'] , 'deptName' => $value['deptName']];
		}
		Response::message(10000 , $dept);
	}

	public function doctor()
	{
		$hid 	= $this->hospitalId;
		$deptId = $this->data['deptId'];
		$data 	= Server::ability('hospital')->dutyDoctor($hid , $deptId);
		if (!$data) { Response::message(10101); }
		$data['code'] != 10000 && Response::message(10102); 	
		$data 	= $data['data'];
		$hisId 	= $data ? array_column($data , 'doctorHisId') : [];
		$hisDoctor 	= [];
		if ($hisId) {
			$result 	= Loader::model('Doctor')->listByHisId($hid , $hisId);
			foreach ($result as $v) {
				$hisDoctor[$v['his_id']] = $v;
			}
		}
		$doctor = [];
		foreach ($data as $key => $value) {
			$doctor[] 	= [
				'doctorId' 		=> $value['doctorHisId'] , 
				'doctorName' 	=> $value['doctorName'] , 
				'disease' 		=> isset($hisDoctor[$value['doctorHisId']]) ? $hisDoctor[$value['doctorHisId']]['sections'] : '', 
				'picture' 		=> isset($hisDoctor[$value['doctorHisId']]) ? $hisDoctor[$value['doctorHisId']]['picture'] : 'http://auto-1253714281.cosgz.myqcloud.com/Upload/e5591c9106b74189933ee815ad593951.png', 
			];
		}
		Response::message(10000 , $doctor);
	}

	public function doctorDetail()
	{
		$hid 		= $this->hospitalId;
		$doctorId 	= $this->data['doctorId'];
		$deptId 	= $this->data['deptId'];
		$doctor 	= Loader::model('Doctor')->detailByHisId($hid , $doctorId);
		if (!$doctor) {
			Response::message(30013);
		}
		$doctor 	= [
			'doctorId' 	=> $doctorId,
			'hospitalId'=> $hid,
			'deptId' 	=> $deptId,
			'doctorName'=> $doctor['name'],
			'picture' 	=> $doctor['picture'],
			'title' 	=> $doctor['title'],
			'disease' 	=> $doctor['sections'],
			'profile' 	=> $doctor['content'],
		];
		Response::message(10000 , $doctor);
	}

	/**
	 * 脪陆脡煤脜脜掳脿
	 *
	 *
	 *
	 *
	 *
	 */
	public function schedule()
	{
		$hid 	= $this->hospitalId;
		$deptId = $this->data['deptId'];
		$doctorId 	= $this->data['doctorId'];
		$next 	= isset($this->data['next']) ? intval($this->data['next']) : 0;
		$data 	= Server::ability('hospital')->doctorSchedule($hid , $deptId , $doctorId);
		if (!$data) { Response::message(10101); }
		$data['code'] != 10000 && Response::message(10102); 
		$schedule  = ['am' => [] , 'pm' => []];
		$week 	= date('w');
		$begin 	= strtotime(date('Y-m-d' , strtotime( '+'. $next* 7 + 1-$week .' days')));
		$end 	= strtotime(date('Y-m-d' ,strtotime( '+'. $next* 7 + 7-$week .' days'))) + 86399;
		foreach ($data['data'] as $v) {
			$time 	= strtotime($v['scheduleDate']);
			if ($begin > $time || $end < $time) {
				continue;
			}
			if ($v['period'] == 'am') {
				$schedule['am'][] 	= [
					'date' 	=> $v['scheduleDate'] , 'period' => $v['period'] , 'scheduleId' => $v['scheduleId'] , 'num' => $v['availableNum'] , 'fee' => $v['feeSum']
				];
			} else if ($v['period'] == 'pm') {
				$schedule['pm'][] 	= [
					'date' 	=> $v['scheduleDate'] , 'period' => $v['period'] , 'scheduleId' => $v['scheduleId'] , 'num' => $v['availableNum'] , 'fee' => $v['feeSum']
				];
			} else if ($v['period'] == 'all') {
				$schedule['am'][] 	= [
					'date' 	=> $v['scheduleDate'] , 'period' => $v['period'] , 'scheduleId' => $v['scheduleId'] , 'num' => $v['availableNum'] , 'fee' => $v['feeSum']
				];
				$schedule['pm'][] 	= [
					'date' 	=> $v['scheduleDate'] , 'period' => $v['period'] , 'scheduleId' => $v['scheduleId'] , 'num' => $v['availableNum'] , 'fee' => $v['feeSum']
				];
			}
		}
		Response::message(10000 , $schedule);
	}

	/**
	 * 脣酶潞脜
	 *
	 *
	 *
	 *
	 */
	public function lock()
	{
		$hid 		= $this->hospitalId;
		$equipId 	= $this->equipId;
		$IDCard 	= isset($this->data['IDCard']) ? $this->data['IDCard'] : '';
		$scheduleId = $this->data['scheduleId'];
		$cardId 	= $this->data['cardId'];
		$period 	= $this->data['period'];
		$date 		= $this->data['date'];
		$deptId 	= $this->data['deptId'];
		$doctorId 	= $this->data['doctorId'];
		$lock 		= Loader::model('Registration')->detailByScheduleId($cardId , $scheduleId);
		if ($lock && $lock['status'] == 0) {
			$registration 	= json_decode($lock['business_info'] , true);
			$response 		= [
				'cardId' 		=> $lock['card_id'],
				'cardName' 		=> $lock['card_name'],
				'doctorName' 	=> $registration['doctorName'],
				'deptName' 		=> $registration['deptName'],
				'date' 		 	=> $registration['scheduleDate'],
				'period' 		=> $registration['period'],
				'create_time' 	=> $lock['create_time'],
				'orderNum' 		=> $lock['order_number'],
				'price' 		=> $lock['price'],
			];
			Response::message(10000 , $response);
		}
		$card 	= Server::ability('hospital')->patientCard($hid , $IDCard , $cardId);
		if (!$card || $card['code'] != 10000) Response::message(30000);
		$cardName 	= $card['data']['userName'];
		$data 		= Server::ability('hospital')->doctorSchedule($hid , $deptId , $doctorId , $date , $period);
		if (!$data) { Response::message(10101); }
		$data['code'] != 10000 && Response::message(10102); 
		$schedule 	= [];
		foreach ($data['data'] as $v) {
			if ($scheduleId == $v['scheduleId']) {
				$schedule = $v;
			}
		}
		if (!$schedule) Response::message(30001);
		if ($schedule['availableNum'] <= 0) {
			Response::message(30002); //驴脡鹿脪潞脜脢媒脕驴虏禄脳茫
		}
		$lock 	 	= Server::ability('hospital')->lockSchedule($hid , $scheduleId , $period , $IDCard , $cardId);
		if (!$lock || $lock['code'] != 10000) {
			Response::message(30003); 	//路碌禄脴脣酶潞脜脢搂掳脺
		}
		$orderNum 	= $lock['data']['out_trade_no'];
		$fee 		= $schedule['feeSum'];
		$deptName 	= $schedule['deptName'];
		$doctorName = $schedule['doctorName'];
		$schedule['apptId'] = $lock['data']['apptId'];
		$cardInfo 			= [
			'cardId' 		=> $cardId,
			'cardName' 		=> $cardName,
		];
		$order 	 	= Loader::model('Order')->orderRegistration($equipId , $orderNum , $hid , $this->projectId , $fee , $schedule , $cardInfo);
		if (!$order) {
			Response::message(30004);//
		}
		$response 	= [
			'doctorName' 	=> $doctorName,
			'deptName' 		=> $deptName,
			'date' 		 	=> $date,
			'period' 		=> $period,
			'create_time' 	=> $order['create_time'],
			'orderNum' 		=> $order['orderNum'],
			'price' 		=> $order['price'],
		];
		Response::message(10000 , $response);

	} 
	
	public function registrationQuery()
	{
		$orderNumber = $this->data['orderNum'];
		$data 	= Loader::model('Order')->detailByNumber($orderNumber);
		if (!$data || $data['type'] != 2) {
			Response::message(30011);
		}
		if ($data['status'] <= 0) {
			Response::message(10000 , [
				'orderNum' 	=> $orderNumber,
				'payStatus' => $data['status'],
			]);
		}
		$orderId 	= $data['id'];
		$orderPay 	= Loader::model('Order')->payDetail($orderId);	
		$registration 		= Loader::model('Registration')->detailByOrderId($orderId);
		$successInfo 		= json_decode($registration['success_info'] , true);
		$registrationInfo  	= json_decode($registration['registration'] , true);
		$response 	= [
			'orderNum' 	=> $orderNumber,
			'payStatus' => $data['status'],
			'cardId' 	=> $registration['card_id'],
			'cardName' 	=> $registration['card_name'],
			'doctorName'=> $registrationInfo['doctorName'],
			'deptName' 	=> $registrationInfo['deptName'],
			'date' 		=> $registrationInfo['scheduleDate'],
			'period' 	=> $registrationInfo['period'],
			'weekDay' 	=> $registrationInfo['weekDay'],
			'fee' 		=> sprintf('%.2f' , $registrationInfo['feeSum']),
			'payType' 	=> $orderPay['pay_type'],
			'payPrice' 	=> $orderPay['price'],
			'transactionId' => $orderPay['transaction_id'],
		];
		if ($data['status'] == 1) {
			$response['registrationStatus'] = $registration['status'];
			if ($registration['status'] == 1) {
				$response['queueNo'] = $successInfo['queueNo'];
				$response['LocInfo'] = $successInfo['LocInfo'];
			}
		}
		
		Response::message(10000 , $response);
	}

}