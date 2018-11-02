<?php

namespace app\equip\validate\v1;

use think\Validate;

class Inquiry extends Validate
{
	protected $rule 	= [
		'deptId' 		=> 'require|gt:0|number',	
		'doctorId' 		=> 'require|gt:0|number',	
		'orderNum' 		=> 'require|alphaNum',
		'callId' 		=> 'require|gt:0|number',
		'picture' 		=> 'require|array',
	];

	protected $message 	= [
		'deptId.require' 	=> 90001,
		'deptId.gt' 		=> 91001,
		'deptId.number' 	=> 91001,
		'doctorId.require' 	=> 90007,
		'doctorId.gt' 		=> 91007,
		'doctorId.number' 	=> 91007,
		'orderNum.require' 	=> 90011,
		'orderNum.alphaNum' => 91011,
		'callId.require' 	=> 90012,
		'callId.gt' 		=> 91012,
		'callId.number' 	=> 91012,
		'picture.require' 	=> 90010,
		'picture.array'  	=> 91010,
	];

	protected $scene 	= [
		'doctor' 			=> ['deptId'],
		'doctorDetail' 		=> ['doctorId'], 
		'inquiryOrder' 		=> ['doctorId'],
		'inquiryQuery' 		=> ['orderNum'],
		'inquiryConnect' 	=> ['orderNum'],
		'inquiryScreen' 	=> ['orderNum' , 'callId' , 'picture'],
		'inquiryReport' 	=> ['orderNum' , 'picture'],
	];

}