<?php

namespace app\equip\validate\v1;

use think\Validate;

class Card extends Validate
{
	protected $rule 	= [
		'IDCard' 		=> 'require|alphaNum',	
		'hospitalId' 	=> 'require|number|gt:0',
	];

	protected $message 	= [
		'IDCard.require' 	=> 90006,
		'IDCard.alphaNum' 	=> 91006,
		'hospitalId.require'=> 90008,
		'hospitalId.number' => 91008,
		'hospitalId.gt' 	=> 91008,
	];

	protected $scene 	= [
		'card' 		=> ['IDCard' , 'hospitalId'],	
	];

	



}