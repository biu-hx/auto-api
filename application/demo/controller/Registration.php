<?php

namespace app\demo\controller;

use app\demo\Common;

class Registration
{

	static $week = [
		0 	=> '日',
		1 	=> '一',
		2 	=> '二',
		3 	=> '三',
		4 	=> '四',
		5 	=> '五',
		6 	=> '六'
	]; 

	static $dept 	= [
		2 	=> '妇科门诊',
		3 	=> '生殖内分泌门诊',
		5 	=> '男性科门诊',
		7 	=> '儿科门诊'
	];
		

	public function hospital()
	{

	}

	public function dept()
	{
		$data 	= [	
			[
				'deptName' 		=> '妇科门诊',
				'deptHisId' 	=> 2,
				'date' 			=> date('Y-m-d'),
				'period' 		=> 'all',
			],
			[
				'deptName' 		=> '生殖内分泌门诊',
				'deptHisId' 	=> 3,
				'date' 			=> date('Y-m-d'),
				'period' 		=> 'all',
			],
			[
				'deptName' 		=> '男性科门诊',
				'deptHisId' 	=> 5,
				'date' 			=> date('Y-m-d'),
				'period' 		=> 'all',
			],
			[
				'deptName' 		=> '儿科',
				'deptHisId' 	=> 7,
				'date' 			=> date('Y-m-d'),
				'period' 		=> 'am',
			],
		];
		$response 	= [
			'code' 	=> 10000,
			'msg' 	=> 'ok',
			'data' 	=> $data,
		];
		echo json_encode($response , JSON_UNESCAPED_UNICODE);
	}

	public function duty()
	{
		$date 	= isset($_GET['date']) ? $_GET['date'] : '1';
		$period = isset($_GET['period']) ? $_GET['period'] : 'all';
		$deptId = $_GET['deptId'];
		$data 	= [
			[
				'doctorName' 	=> '李振英',
				'doctorHisId' 	=> '3268',
				'deptName' 		=> self::$dept[$deptId],
				'deptHisId' 	=> $deptId,
				'scheduleList' 	=> [
					[
						'scheduleId' 	=> '111349',
						'scheduleCode' 	=> '3168||3603',
						'scheduleDate' 	=> $date == 1 ? date('Y-m-d') : $date,
						'weekDay' 		=> '星期'.self::$week[date('w' , strtotime($date))],
						'period' 		=> $period,
						'feeSum' 		=> 58,
						'availableNum' 	=> 10,
						'startTime' 	=> 0,
						'endTime' 		=> 0,
					],
				],
			],
			[
				'doctorName' 	=> '彭芝兰',
				'doctorHisId' 	=> '3324',
				'deptName' 		=> self::$dept[$deptId],
				'deptHisId' 	=> $deptId,
				'scheduleList' 	=> [
					[
						'scheduleId' 	=> '111363',
						'scheduleCode' 	=> '3112||1548',
						'scheduleDate' 	=>  $date == 1 ? date('Y-m-d' , strtotime('+1 days')) : $date,
						'weekDay' 		=> '星期'.self::$week[date('w' , strtotime($date))],
						'period' 		=> $period,
						'feeSum' 		=> 98,
						'availableNum' 	=> 0,
						'startTime' 	=> 0,
						'endTime' 		=> 0,
					],
				],
			],
			[
				'doctorName' 	=> '方芳',
				'doctorHisId' 	=> '3642',
				'deptName' 		=> self::$dept[$deptId],
				'deptHisId' 	=> $deptId,
				'scheduleList' 	=> [
					[
						'scheduleId' 	=> '111351',
						'scheduleCode' 	=> '3281||989',
						'scheduleDate' 	=> $date == 1 ? date('Y-m-d' , strtotime('+2 days')) : $date,
						'weekDay' 		=> '星期'.self::$week[date('w' , strtotime($date))],
						'period' 		=> $period,
						'feeSum' 		=> 58,
						'availableNum' 	=> 30,
						'startTime' 	=> 0,
						'endTime' 		=> 0,
					],
				],
			],
			[
				'doctorName' 	=> '蔡压西',
				'doctorHisId' 	=> '3662',
				'deptName' 		=> self::$dept[$deptId],
				'deptHisId' 	=> $deptId,
				'scheduleList' 	=> [
					[
						'scheduleId' 	=> '111353',
						'scheduleCode' 	=> '3152||2933',
						'scheduleDate' 	=> $date == 1 ? date('Y-m-d' , strtotime('+3 days')) : $date,
						'weekDay' 		=> '星期'.self::$week[date('w' , strtotime($date))],
						'period' 		=> $period,
						'feeSum' 		=> 58,
						'availableNum' 	=> 10,
						'startTime' 	=> 0,
						'endTime' 		=> 0,
					],
				],
			],
			[
				'doctorName' 	=> '陈杰',
				'doctorHisId' 	=> '3942',
				'deptName' 		=> self::$dept[$deptId],
				'deptHisId' 	=> $deptId,
				'scheduleList' 	=> [
					[
						'scheduleId' 	=> '111356',
						'scheduleCode' 	=> '3159||1140',
						'scheduleDate' 	=> $date == 1 ? date('Y-m-d' , strtotime('+4 days')) : $date,
						'weekDay' 		=> '星期'.self::$week[date('w' , strtotime($date))],
						'period' 		=> $period,
						'feeSum' 		=> 38,
						'availableNum' 	=> 10,
						'startTime' 	=> 0,
						'endTime' 		=> 0,
					],
				],
			],
			[
				'doctorName' 	=> '郑莹',
				'doctorHisId' 	=> '4092',
				'deptName' 		=> self::$dept[$deptId],
				'deptHisId' 	=> $deptId,
				'scheduleList' 	=> [
					[
						'scheduleId' 	=> '111358',
						'scheduleCode' 	=> '3287||912',
						'scheduleDate' 	=> $date == 1 ? date('Y-m-d' , strtotime('+5 days')) : $date,
						'weekDay' 		=> '星期'.self::$week[date('w' , strtotime($date))],
						'period' 		=> $period,
						'feeSum' 		=> 38,
						'availableNum' 	=> 25,
						'startTime' 	=> 0,
						'endTime' 		=> 0,
					],
				],
			],
			[
				'doctorName' 	=> '朱联',
				'doctorHisId' 	=> '4100',
				'deptName' 		=> self::$dept[$deptId],
				'deptHisId' 	=> $deptId,
				'scheduleList' 	=> [
					[
						'scheduleId' 	=> '111361',
						'scheduleCode' 	=> '5467||1762',
						'scheduleDate' 	=> $date == 1 ? date('Y-m-d' , strtotime('+5 days')) : $date,
						'weekDay' 		=> '星期'.self::$week[date('w' , strtotime($date))],
						'period' 		=> $period,
						'feeSum' 		=> 58,
						'availableNum' 	=> 30,
						'startTime' 	=> 0,
						'endTime' 		=> 0,
					],
				],
			],
			[
				'doctorName' 	=> '李金科',
				'doctorHisId' 	=> '5964',
				'deptName' 		=> self::$dept[$deptId],
				'deptHisId' 	=> $deptId,
				'scheduleList' 	=> [
					[
						'scheduleId' 	=> '111362',
						'scheduleCode' 	=> '4639||272',
						'scheduleDate' 	=> $date == 1 ? date('Y-m-d' , strtotime('+6 days')) : $date,
						'weekDay' 		=> '星期'.self::$week[date('w' , strtotime($date))],
						'period' 		=> $period,
						'feeSum' 		=> 28,
						'availableNum' 	=> 10,
						'startTime' 	=> 0,
						'endTime' 		=> 0,
					],
				],
			],
		];
		$response 	= [
			'code' 	=> 10000,
			'msg' 	=> 'ok',
			'data' 	=> $data,
		];
		echo json_encode($response , JSON_UNESCAPED_UNICODE);

	}

	public function schedule()
	{
		$deptId = $_GET['deptId'];
		$date 	= $_GET['date'];
		$scheduleId = $_GET['scheduleId'];
		$data 		= [
			[
				'scheduleId' 	=> $scheduleId,
				'deptName' 		=> self::$dept[$deptId],
				'deptHisId' 	=> $deptId,
				'doctorName' 	=> '罗国林',
				'doctorHisId' 	=> '3576',
				'scheduleCode' 	=> '3148||1148',
				'scheduleDate' 	=> $date,
				'weekDay' 		=> '星期'.self::$week[date('w' , strtotime($date))],
				'period' 		=> 'pm',
				'feeSum' 		=> '38',
				'availableNum' 	=> '38',
				'startTime' 	=> '0',
				'endTime' 		=> '0',
			]
		];
		$response 	= [
			'code' 	=> 10000,
			'msg' 	=> 'ok',
			'data' 	=> $data,
		];
		echo json_encode($response , JSON_UNESCAPED_UNICODE);
	}

	public function lock()
	{
		$data 	= [	
			'apptId' 	 	=> '5270||1061||1',
			'out_trade_no' 	=> Common::orderNumber(2),
		];
		$response 	= [
			'code' 	=> 10000,
			'msg' 	=> 'ok',
			'data' 	=> $data,
		];
		echo json_encode($response , JSON_UNESCAPED_UNICODE);
	}


	public function fetch()
	{
		$data 	= [	
			[
				'doctorName' 	=> '谭世桥',
				'period' 		=> 'am',
				'deptName' 		=> '生殖内分泌门诊',
				'roomName' 		=> '生殖内分泌10诊断室',
				'queueNo' 		=> '35',
				'fee' 			=> '38',
				'scheduleCode' 	=> '3187||1837||41',
				'fetchType' 	=> '2',
				'date' 			=> date('Y-m-d'),
			],
		];
		$response 	= [
			'code' 	=> 10000,
			'msg' 	=> 'ok',
			'data' 	=> $data,
		];
		echo json_encode($response , JSON_UNESCAPED_UNICODE);
	}

	public function fetchOrder()
	{
		$data 	= [	
			'out_trade_no' => Common::orderNumber(3),
		];
		$response 	= [
			'code' 	=> 10000,
			'msg' 	=> 'ok',
			'data' 	=> $data,
		];
		echo json_encode($response , JSON_UNESCAPED_UNICODE);
	}

	public function getWaitDetail(){
        echo '{"code": 10000,"msg": "ok","data": {"myNum": 7,"beingNum": 3,"totalNum": 49,"username": "王净","CardNo": "020000004584","dept": "妇科","doctor": "王军"}}';
    }
}