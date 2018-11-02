<?php

namespace app\api\validate\v1;

use think\Validate;

class Personal extends Validate
{
	protected $rule 	= [
		'page' 			=> 'number|gt:0',
		'pagesize' 		=> 'number|gt:0',
		'eventType' 	=> 'require|in:EVENT1,EVENT2,EVENT3,EVENT4',
		'content' 		=> 'require',
		'online' 		=> 'require|in:0,1',
	];

	protected $message 	= [
		'page' 				=> 30102,
		'pagesize' 			=> 30103,
		'eventType.require' => 30001,
		'content.require' 	=> 32001,
		'online.require' 	=> 32002,
		'eventType.in' 		=> 30101,
		'online.in' 		=> 32102,
	];

	protected $scene 	= [
		'modify' 		=> ['eventType' , 'content'],
		'income' 		=> ['page' , 'pagesize'],
		'autoOnline' 	=> ['online'],
	];

	



}