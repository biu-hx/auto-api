<?php

namespace app\equip\validate\v1;

use think\Validate;

class Video extends Validate
{
	protected $rule 	= [
		'videoId' 		=> 'require|gt:0|number',	
		'orderNum' 		=> 'require|alphaNum',
	];

	protected $message 	= [
		'videoId.require' 	=> 90009,
		'videoId.gt' 		=> 91009,
		'videoId.number' 	=> 91009,
		'orderNum.require' 	=> 90011,
		'orderNum.alphaNum' => 91011,
	];

	protected $scene 	= [
		'videoDetail' 		=> ['videoId'],	
		'videoOrder' 		=> ['videoId'],
		'videoQuery' 		=> ['orderNum'],
	];

}