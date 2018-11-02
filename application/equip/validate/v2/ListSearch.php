<?php

namespace app\equip\validate\v2;

use think\Validate;

class ListSearch extends Validate
{
	protected $rule 	= [
		'search' 		=> 'require',	
	];

	protected $message 	= [
		'search.require' 	=> 90014,
	];

	protected $scene 	= [
		'drug' 				=> ['search'],
		'diagnosis' 		=> ['search'], 
		
	];

}