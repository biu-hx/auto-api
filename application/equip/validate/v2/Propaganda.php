<?php

namespace app\equip\validate\v2;

use think\Validate;

class Propaganda extends Validate
{
	protected $rule 	= [
		'orderId' 		=> 'require|alphaNum',
	];

	protected $message 	= [
		'orderId.require' 	=> 90041,
		
	];

	protected $scene 	= [
		'evaluate' 		=> ['orderId'],
	];

	



}