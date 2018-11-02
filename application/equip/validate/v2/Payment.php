<?php

namespace app\equip\validate\v2;

use think\Validate;

class Payment extends Validate
{
	protected $rule 	= [
		'cardId' 		=> 'require|alphaNum',
		'treatNo' 		=> 'require',
		'fee' 			=> 'require|number|gt:0',
		'orderNum' 		=> 'require|alphaNum',
		'business' 		=> 'require|alphaNum',
        'type' 		=> 'in:0,1',
        'business' 		=> 'require|alphaNum',
	];

	protected $message 	= [
		'orderNum.require' 	=> 90000,
		'cardId.require' 	=> 90005,
		'treatNo' 			=> 90016,
		'fee.require' 		=> 90017,
		'orderNum.alphaNum' => 91000,
		'business.alphaNum' => 90032,
		'cardId.alphaNum' 	=> 91005,
		'fee.number' 		=> 91017,
		'fee.gt' 			=> 91017,
		
	];

	protected $scene 	= [
		'outpatientList' 		=> ['cardId'],
		'outpatient' 			=> ['cardId'],	
		'outpatientOrder' 		=> ['cardId'],	
		'outpatientQuery' 		=> ['orderNum'],
		'inpatient' 			=> ['cardId'],
		'inpatientOrder' 		=> ['cardId' , 'treatNo' , 'fee','type'],
		'inpatientQuery' 		=> ['orderNum'],
		'getPay' 		        => ['business'],
	];

	



}