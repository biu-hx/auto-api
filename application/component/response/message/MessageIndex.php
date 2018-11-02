<?php

namespace app\component\response\message;

class MessageIndex
{
	
	private static $prompt 	= [
		404 		=> 'Error 404 Not Found', 					//  - 404
	];





	/**
	 * 获取解释语
	 *
	 * @static 	
	 * @access 	public
	 * @param 	int 	$code  	返回的状态码
	 * @return 	string
	 */
	public function prompt($code)
	{
		return self::$prompt[$code];
	} 







}