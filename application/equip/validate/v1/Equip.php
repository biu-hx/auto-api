<?php

namespace app\equip\validate\v1;

use think\Validate;

class Equip extends Validate
{
	protected $rule 	= [
		'mac' 		=> 'require|regex:([0-9A-Z]{2}-){5}[0-9A-Z]{2}',
		'hardware' 	=> 'require|number|gt:0',	
		'value' 	=> 'require|number|gt:0',
		'version' 	=> 'require'
	];

	protected $message 	= [
		'mac.require' 		=> 90000,
		'mac.regex' 		=> 91000,
		'hardware.require' 	=> 90013,
		'hardware.number' 	=> 91013,
		'hardware.gt' 		=> 91013,
		'value.require'	 	=> 90014,
		'value.number' 		=> 91014,
		'value.gt' 			=> 91014,
		'version' 			=> 90015
	];

	protected $scene 	= [
		'number' 	=> ['mac'],
		'network' 	=> ['mac'],
		'hardware' 	=> ['mac' , 'hardware' , 'value'],	
		'version' 	=> ['mac' , 'version'],
	];

	



}