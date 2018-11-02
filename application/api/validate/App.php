<?php

namespace app\api\validate;

use think\Validate;

class App extends Validate
{
	protected $rule 	= [
		'eventType' 	=> 'require|in:EVENT_BEGIN,EVENT_END',
		'inquiryId' 	=> 'require|number|gt:0',
		'callId' 		=> 'require|number|gt:0',
		'time' 			=> 'require|number|gt:0',
	];

	protected $message 	= [
		'eventType.require' => 30001,
		'inquiryId.require' => 33001,
		'callId.require' 	=> 33002,	
		'time.require' 		=> 33003,
		'eventType.in' 		=> 30101,
		'inquiryId.gt' 		=> 33101,
		'callId.gt' 		=> 33102,
		'time.gt' 			=> 33103,
		'inquiryId.number' 	=> 33101,
		'callId.number' 	=> 33102,
		'time.number' 		=> 33103,
		'time.number' 		=> 33104,
	
	];

	protected $scene 	= [
		'markCall' 		=> ['inquiryId' , 'callId' , 'eventType' , 'time'], 
	];

	



}