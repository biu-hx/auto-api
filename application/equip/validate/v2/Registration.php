<?php

namespace app\equip\validate\v2;

use think\Validate;

class Registration extends Validate
{
	protected $rule 	= [
		'orderNum' 		=> 'require|alphaNum',
		'deptId' 		=> 'require',
		'doctorId' 		=> 'require|alphaNum',
		'date' 			=> 'require|regex:\d{4}-\d{2}-\d{2}',
		'period' 		=> 'require|in:am,pm,npm,all,day,md,sam,amd',
		'scheduleId' 	=> 'require|number|gt:0',
		'cardId' 		=> 'require|alphaNum',
		//'payType' 		=> 'require|alphaNum',
	];

	protected $message 	= [
		'orderNum.require' 		=> 90000,
		'cardId.require' 		=> 90005,
		'deptId.require' 		=> 90010,
		'doctorId.require' 		=> 90011,
		'date.require' 			=> 90018,
		'period.require' 		=> 90019,
		'scheduleId.require' 	=> 90020,
		'orderNum.alphaNum' 	=> 91000,
		'cardId.alphaNum' 		=> 91005,
		'deptId.alphaNum' 		=> 91010,
		'doctorId.alphaNum' 	=> 91011,
		'date.regex' 			=> 91018,
		'period.in' 			=> 91009,
		'scheduleId.number' 	=> 91020,
		'scheduleId.gt' 		=> 91020,
		//'payType.require' 		=> 91031,

	];

	protected $scene 	= [
		'doctor' 			=> ['deptId'],
		'doctorDetail' 		=> ['deptId' , 'doctorId'],
		'schedule' 			=> ['deptId' , 'period' , 'date'],
		'lock' 				=> ['deptId' , 'doctorId' , 'date' , 'period' ,  'scheduleId' , 'cardId' , 'payType'],
		'registrationQuery' => ['orderNum'],
		'date' 				=> ['deptId'],
		'fetchReg' 			=> ['cardId'],
		'fetchRegOrder' 	=> ['cardId' ],
		'fetchQuery' 		=> ['orderNum'],
	];

	



}