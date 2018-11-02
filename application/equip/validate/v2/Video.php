<?php

namespace app\equip\validate\v2;

use think\Validate;

class Video extends Validate
{
	protected $rule 	= [
		'videoId' 		=> 'require|gt:0|number',	
		'orderNum' 		=> 'require|alphaNum',
		'orderId' 		=> 'require|gt:0|number',
	];

	protected $message 	= [
		'orderNum.require' 	=> 90000,
		'videoId.require' 	=> 90024,
		'orderNum.alphaNum' => 91000,
		'videoId.gt' 		=> 91024,
		'videoId.number' 	=> 91024,
		'orderId.require' 	=> 90033,
	];

	protected $scene 	= [
		'videoDetail' 		=> ['videoId'],	
		'videoOrder' 		=> ['videoId'],
		'videoQuery' 		=> ['orderNum'],
		'getUrl'     		=> ['orderId' => '','videoId'],
	];

}