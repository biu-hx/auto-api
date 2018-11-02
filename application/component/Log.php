<?php

namespace app\component;

class Log
{
	public static $log = [
		'request' 	=> [],
		'response' 	=> [],
		'route' 	=> [],
	];

	/**
	 * 设置存储请求
	 *
	 * @access 	public
	 * @static 	
	 * @param 	array 	$request
	 * @return 	void
	 */
	public static function storageRequest($logName , $request)
	{
		self::$log['request'][$logName] 	= $request;
	}
	
	/**
	 * 设置存储响应
	 *
	 * @access 	public
	 * @static 	
	 * @param 	array 	$request
	 * @return 	void
	 */
	public static function storageResponse($logName , $response)
	{
		self::$log['response'][$logName] 	= $response;
	}

	/**
	 * 设置存储响应
	 *
	 * @access 	public
	 * @static 	
	 * @param 	array 	$request
	 * @return 	void
	 */
	public static function storageRoute($logName , $route)
	{
		self::$log['route'][$logName] 	= $route;
	}


	/**
	 * 设置写入log
	 *
	 * @access 	public
	 * @param 	string 	$logName 	日志存储名称
	 * @return 	void
	 */
	public static function writeLog($logName)
	{
		$log 	= [
			'logName' 	=> $logName,
			'route' 	=> isset(self::$log['route'][$logName]) ? self::$log['route'][$logName] : '',
			'request' 	=> isset(self::$log['request'][$logName]) ? self::$log['request'][$logName] : '',
			'response' 	=> isset(self::$log['response'][$logName]) ? self::$log['response'][$logName] : '',
			'time' 		=> date('Y-m-d H:i:s'),
		];
		\think\Log::write($log , 'info');
	}

}

