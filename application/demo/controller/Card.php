<?php

namespace app\demo\controller;

use app\demo\Common;

class Card
{

	public function patient()
	{
		$data 	= [	
			'userName' 	=> '000000000018',
			'IdCardNo' 	=> '',
			'UserIdKey' => '00024462',
			'sex' 		=> '',
			'birthday' 	=> '',
			'phone' 	=> '',
			'createDate'=> '2008-11-17',
		];
		$response 	= [
			'code' 	=> 10000,
			'msg' 	=> 'ok',
			'data' 	=> $data,
		];
		echo json_encode($response , JSON_UNESCAPED_UNICODE);
		
	}


	

}