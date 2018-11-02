<?php

namespace app\equip\validate\v2;

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
		'mac.require' 		=> 90001,
		'hardware.require' 	=> 90002,
		'value.require'	 	=> 90003,
		'version' 			=> 90004,
		'mac.regex' 		=> 91001,
		'hardware.number' 	=> 91002,
		'hardware.gt' 		=> 91002,
		'value.number' 		=> 91003,
		'value.gt' 			=> 91003,
	];

	protected $scene 	= [
		'number' 	=> ['mac'],
		'network' 	=> ['mac'],
		'hardware' 	=> ['mac' , 'hardware' , 'value'],	
		'version' 	=> ['mac' , 'version'],
		'paper' 	=> ['mac' , 'hardware' , 'value'],
	];

	



}