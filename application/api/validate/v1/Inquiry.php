<?php

namespace app\api\validate\v1;

use think\Validate;

class Inquiry extends Validate
{
	protected $rule 	= [
		'page' 			=> 'number|gt:0',
		'pagesize' 		=> 'number|gt:0',
		'eventType' 	=> 'require|in:EVENT_DOING,EVENT_OVER,EVENT_BREAK',
		'inquiryId' 	=> 'require|number|gt:0',
		'number' 		=> 'require|number',
		'orderNum' 		=> 'require',
		'orderStatus' 	=> 'require',
		'inquiryStatus' => 'require',
	];

	protected $message 	= [
		'page' 				=> 30102,
		'pagesize' 			=> 30103,
		'eventType.require' => 30001,
		'inquiryId.require' => 33001,
		'number.require' 	=> 33004,
		'eventType.in' 		=> 30101,
		'inquiryId.gt' 		=> 33101,
		'inquiryId.number' 	=> 33101,
		'orderNum.require' 	=> 33005,
		'orderStatus.require' 	=> 33006,
		'inquiryStatus.require' 	=> 33007,

	];

	protected $scene 	= [
		'inquiry' 		=> ['page' , 'pagesize' , 'eventType'],
		'detail' 		=> ['inquiryId'],			
		'report' 		=> ['inquiryId'],
		'mark' 			=> ['inquiryId'],
		'relate' 		=> ['number'],
		'setStatus' 	=> ['orderNum' , 'orderStatus' , 'inquiryStatus'],
	];

	



}