<?php

namespace app\demo\controller;

use app\demo\Common;

class Payment
{
	public function outpatientList()
	{
		$data 	= [	
			'payList' => [
				['fee' 		=> '102.60',
				'payDate' => date('Y-m-d'),
			]],
		];
		$response 	= [
			'code' 	=> 10000,
			'msg' 	=> 'ok',
			'data' 	=> $data,
		];
		echo json_encode($response , JSON_UNESCAPED_UNICODE);
	}

	public function outpatient()
	{
		$data 	= [
			'payDate' 		=> date('Y-m-d'),
			'categories' 	=> [
				[
					'address' 	=> '急诊药房',
					'cateName' 	=> '西药口服药',
					'cateFee' 	=> '89.10',
					'items' 	=> [
						[
							'itemName' 	=> '[基]氨酚伪麻那敏分散片(1T*12片)[农]' , 
							'quantity' 	=> '12',
							'price' 	=> '0.91',
							'unit' 		=> '12',
							'fee' 		=> '10.92'
						],
						[
							'itemName' 	=> '金世力德(2g*6包)' , 
							'quantity' 	=> '12',
							'price' 	=> '6.52',
							'unit' 		=> '12',
							'fee' 		=> '78.18'
						],
					],
				],
				[
					'address' 	=> '急诊药房',
					'cateName' 	=> '中成药口服药',
					'cateFee' 	=> '13.50',
					'items' 	=> [
						[
							'itemName' 	=> '儿感退热宁(10ml*6支)[农]' , 
							'quantity' 	=> '6',
							'price' 	=> '2.25',
							'unit' 		=> '6',
							'fee' 		=> '13.50'
						],
						
					],
				],
			],
			'orderNumber' 	=> '',
			'deptName' 		=> '',
			'doctorName' 	=> '',
			'out_trade_no'  => Common::orderNumber(4),
			'totalFee' 		=> '102.60',
		];
		$response 	= [
			'code' 	=> 10000,
			'msg' 	=> 'ok',
			'data' 	=> $data,
		];
		echo json_encode($response , JSON_UNESCAPED_UNICODE);
	}


	public function inpatient()
	{
		$data 	= [
			'treat_no' 		=> '18598816',
			'patient_name' 	=> '佘竹君',
			'dept_name' 	=> 'EKICUBQ-儿科ICU病区',
			'inhospital_date' => date('Y-m-d' , strtotime('-1 month')),
			'bed_no' 		=> '14床',
			'total_fee' 	=> '84474.48',
			'prepay_fee' 	=> '85000.00',
			'arrears_fee' 	=> '525.52',
		];
		$response 	= [
			'code' 	=> 10000,
			'msg' 	=> 'ok',
			'data' 	=> $data,
		];
		echo json_encode($response , JSON_UNESCAPED_UNICODE);
	}	

	public function inpatientOrder()
	{
		$data 	= [
			'out_trade_no' 	=> Common::orderNumber(5),
			'weChaId' 		=> '',
		];
		$response 	= [
			'code' 	=> 10000,
			'msg' 	=> 'ok',
			'data' 	=> $data,
		];
		echo json_encode($response , JSON_UNESCAPED_UNICODE);
	} 
}