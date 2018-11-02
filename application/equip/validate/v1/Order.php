<?php

namespace app\equip\validate\v1;

use think\Validate;

class Order extends Validate
{
	protected $rule 	= [
		'orderNum' 	=> 'require|alphaNum',	
	];

	protected $message 	= [
		'orderNum.require' 	=> 90011,
		'orderNum.alphaNum' => 91011,
	];

	protected $scene 	= [
		'wechatQR' 	=> ['orderNum'],
		'alipayQR' 	=> ['orderNum'],
	];

	



}