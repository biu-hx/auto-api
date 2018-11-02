<?php

namespace app\equip\validate\v2;

use think\Validate;

class Order extends Validate
{
	protected $rule 	= [
		'orderNum' 	=> 'require|alphaNum',
		'cardId' 	=> 'require|alphaNum',	
		'link' 		=> 'require',
		'treatNo' 	=> 'require',
		'status' 	=> 'require',
		'price' 	=> 'require',
		'cardName' 	=> 'require',
		'cardNo' 	=> 'require',
		'payTime' 	=> 'require',
		'eatingCode' 	=> 'require',
		'regNumber' 	=> 'require',
	];

	protected $message 	= [
		'orderNum.require' 	=> 90000,
		'cardId.require' 	=> 90005,
		'orderNum.alphaNum' => 91000,
		'cardId.alphaNum' 	=> 91005,
		'link' 				=> 90015,
		'treatNo' 			=> 90016,
		'status' 			=> 90034,
		'price' 			=> 90035,
		'cardName' 			=> 90036,
		'cardNo' 			=> 90037,
		'payTime' 			=> 90038,
		'eatingCode' 		=> 90039,
		'regNumber' 		=> 90040,
	];

	protected $scene 	= [
		'searchByOrder' 	=> ['orderNum'],
		'searchByCard' 		=> ['cardId'],
		'searchByLink' 		=> ['link'],
		'searchByTreat' 	=> ['treatNo'],
		'detail' 			=> ['orderNum'],
		'markPrint' 		=> ['orderNum'],
		'wechatQR' 			=> ['orderNum'],
		'alipayQR' 			=> ['orderNum'],
		'setStatus' 		=> ['orderNum' , 'status|in:0,1,2,3,4,5,-2'],
		'addDinner' 		=> ['orderNum' , 'status' , 'price' , 'cardName' , 'cardNo' , 'payTime' , 'eatingCode' , 'regNumber'],
	];

	



}