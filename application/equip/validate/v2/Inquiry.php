<?php

namespace app\equip\validate\v2;

use think\Validate;

class Inquiry extends Validate
{
	protected $rule 	= [
		'deptId' 		=> 'require|gt:0|number',	
		'doctorId' 		=> 'require|gt:0|number',	
		'status' 		=> 'require|gt:0|number',
		'orderNum' 		=> 'require|alphaNum',
		'payType' 		=> 'require|alphaNum',
		'callId' 		=> 'require|gt:0|number',
		'picture' 		=> 'require|array',
	];

	protected $message 	= [
		'orderNum.require' 	=> 90000,
		'deptId.require' 	=> 90010,
		'doctorId.require' 	=> 90011,
		'callId.require' 	=> 90012,
		'picture.require' 	=> 90013,
		'status.require' 	=> 90034,
		'orderNum.alphaNum' => 91000,
		'deptId.gt' 		=> 91010,
		'status.gt' 		=> 91010,
		'status.number' 	=> 91010,
		'deptId.number' 	=> 91010,
		'doctorId.gt' 		=> 91011,
		'doctorId.number' 	=> 91011,
		'callId.gt' 		=> 91012,
		'callId.number' 	=> 91012,
		'picture.array'  	=> 91013,
		'payType.alphaNum'  => 90031,
	];

	protected $scene 	= [
		//'doctor' 			=> [],
		'doctorDetail' 		=> ['doctorId'], 
		'inquiryOrder' 		=> ['doctorId' , 'payType'],
		'inquiryQuery' 		=> ['orderNum'],
		'inquiryConnect' 	=> ['orderNum'],
		'inquiryScreen' 	=> ['orderNum' , 'callId' , 'picture'],
		'inquiryReport' 	=> ['orderNum' , 'picture'],
		'outStatus' 	    => ['callId' , 'status'],
		'putError' 	        => ['callId' , 'status'],
		'callStatus' 	    => ['callId'],
		'orderStatus' 	    => ['orderNum'],
		'answer' 	        => ['callId'],
		//'hospitalList'  	=> ['deptId'],
	];

}