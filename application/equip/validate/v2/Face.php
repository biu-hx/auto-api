<?php

namespace app\equip\validate\v2;

use think\Validate;

class Face extends Validate
{
	protected $rule = [
		'personId' 		=> 'require|length:0,200',
		'image' 	        => 'require|length:0,200',
		'IDCard' 	    => 'require|length:0,200',
	 	'name'          => 'require|length:0,200'
	];

	protected $message 	= [
		'personId.require' 		=> 90025,
		'image.require' 	=> 90026,
		'IDCard.require'	 	=> 90006,
		'name.require' 			=> 90027
	];

	protected $scene 	= [
		'addFace' 	=> ['image','personId'],
		'searchPerson' 	=> ['IDCard'],
		'addPerson' 	=> ['IDCard' , 'name' , 'image'],
		'searchFace' 	=> ['image']
	];

	



}