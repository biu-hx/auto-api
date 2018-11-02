<?php

namespace app\equip\validate\v1;

use think\Validate;

class Registration extends Validate
{
	protected $rule 	= [
		'hospitalId' 	=> 'number|gt:0',
		'deptId' 		=> 'require|alphaNum',
		'doctorId' 		=> 'require|alphaNum',
		'date' 			=> 'require|regex:\d{4}-\d{2}-\d{2}',
		'period' 		=> 'require|in:am,pm,npm,all',
		'scheduleId' 	=> 'require|number|gt:0',
		//'IDCard'  		=> 'require|alphaNum',
		'cardId' 		=> 'require|alphaNum',
	];

	protected $message 	= [
		'hospitalId.require' 	=> 90008,
		'hospitalId.number' 	=> 91008,
		'hospitalId.gt' 		=> 91008,
		'deptId.require' 		=> 90001,
		'deptId.alphaNum' 		=> 91001,
		'doctorId.require' 		=> 90007,
		'doctorId.alphaNum' 	=> 91007,
		'scheduleId.require' 	=> 90004,
		'scheduleId.number' 	=> 91004,
		'scheduleId.gt' 		=> 91004,
		'IDCard.require' 		=> 90006,
		'IDCard.alphaNum' 		=> 91006,
		'cardId.require' 		=> 90005,
		'cardId.alphaNum' 		=> 91005,
		'date.require' 			=> 90002,
		'date.regex' 			=> 91002,
		'period.require' 		=> 90003,
		'period.in' 			=> 91003,
		
	];

	protected $scene 	= [
		'dept' 				=> ['hospitalId'],
		'doctor' 			=> ['hospitalId' , 'deptId'],
		'doctorDetail' 		=> ['hospitalId' , 'deptId' , 'doctorId'],
		'schedule' 			=> ['hospitalId' , 'deptId' , 'doctorId'],
		'lock' 				=> ['hospitalId' , 'deptId' , 'doctorId' , 'date' , 'period' ,  'scheduleId' , 'IDCard' , 'cardId' ],
		'registrationQuery' => ['orderNum'],
	];

	



}