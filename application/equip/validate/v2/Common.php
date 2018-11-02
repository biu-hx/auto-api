<?php

namespace app\equip\validate\v2;

use think\Validate;

class Common extends Validate
{
	protected $rule 	= [
		'parent_id' => 'require|number|>=:0'
	];

	protected $message 	= [
		'parent_id.require' 		=> 90030
	];

	protected $scene 	= [
		'getAreaList' 	=> ['parent_id'],
	];

	



}