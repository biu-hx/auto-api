<?php

namespace app\equip\validate\v2;

use think\Validate;

class Inpatient extends Validate
{
	protected $rule 	= [
		'cardId' 		=> 'require|alphaNum',
		//'admId' 		=> 'require',
		//'arpbl' 		=> 'require',
	];

	protected $message 	= [
		'cardId.require' 	=> 90005,
		'cardId.alphaNum' 	=> 91005,
		//'admId' 			=> 90008,
		//'arpbl' 			=> 90009,
	];

	protected $scene 	= [
		'inpatientList' 	=> ['cardId'],
		'inpatientType' 	=> [],
		'inpatientDetail' 	=> [],
	];

}