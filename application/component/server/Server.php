<?php

namespace app\component\server;

use think\Config;

class Server
{

	private static $component = [
		'ability' 	=> [],
		'cache' 	=> [],
	];

	/**
	 * 能力层组件
	 *
	 * @access 	public
	 * @static
	 * @return 	object
	 */
	public static function ability($name)
	{
		if (!isset(self::$component['ability'][$name])) {
			$config  	= Config::get('ability_driver');
			if (!isset($config[$name])) {
				throw new \Exception('error ability config '.$name);
			}
			$config 	= $config[$name];
			self::$component['ability'][$name] = new $config['class']($config);
		}
		return self::$component['ability'][$name];
	}

	/**
	 * 缓存组件
	 *
	 * @access 	public
	 * @static
	 * @return 	object
	 */
	public static function cache($name)
	{
		if (!isset(self::$component['cache'][$name])) {
			$config 	= Config::get('cache_driver');
			if (!isset($config[$name])) {
				throw new \Exception('error cache config '.$name);
			}
			$config 	= $config[$name];
			self::$component['cache'][$name] = new $config['class']($config);
		}
		return self::$component['cache'][$name];
	}


}

