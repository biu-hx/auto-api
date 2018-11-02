<?php

namespace app\equip\validate\v2;

use think\Validate;

class Card extends Validate
{
	protected $rule 	= [
		'IDCard' 		=> 'require|alphaNum',	
		'cardId' 		=> 'require|alphaNum',
		'healthCard' 	=> 'require',
		'hospitalId' 	=> 'require',
		'uniqueId' 	=> 'require',
	];

	protected $message 	= [
		'cardId.require' 	=> 90005,
		'IDCard.require' 	=> 90006,
		'healthCard' 		=> 90007,
		'hospitalId' 		=> 90007,
		'uniqueId' 		    => 90007,
		'cardId.alphaNum' 	=> 91005,
		'IDCard.alphaNum' 	=> 91006,
		
	];

	protected $scene 	= [
		'card' 					=> ['IDCard'],	
		'cardByPatient' 		=> ['cardId'],
		'cardByEleHealthCard' 	=> ['healthCard'],
        'getCardInfo' 	=> ['hospitalId' , 'uniqueId'],
	];

	



}