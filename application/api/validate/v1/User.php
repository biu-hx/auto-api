<?php

namespace app\api\validate\v1;

use think\Validate;

class User extends Validate
{
	protected $rule 	= [
		'phone' 		=> ['require' , 'regex' => '/^1(3|4|5|7|8)[0-9]{9}$/'],
		'password' 		=> 'require|min:6',
		'code' 			=> ['require' , 'regex' => '/^[0-9]{4}$/'],
		'token' 		=> 'require|length:32',
	];

	protected $message 	= [
		'phone.require' 	=> 31001,
		'password.require' 	=> 31002,
		'code.require' 		=> 31003,
		'token.require' 	=> 31004,
		'phone.regex' 		=> 31101,
		'password.min' 		=> 31102,
		'code.regex' 		=> 31103,
		'token.length' 		=> 31104,
	];

	protected $scene 	= [
		'login' 		=> ['phone' , 'password'],
		'code' 			=> ['phone'],
		'verify' 		=> ['phone' , 'code'],
		'modify' 		=> ['token' , 'password'],
	];

	



}