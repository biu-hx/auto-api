<?php
// +----------------------------------------------------------------------
// | EMT--Redis支持
// +----------------------------------------------------------------------
// | Copyright (c) 2015-2017 http://www.mobimedical.cn All rights reserved.
// +----------------------------------------------------------------------
// | author: duanhy <shongyudxmas@163.com>
// +----------------------------------------------------------------------
namespace app\component\server\cache;

class Redis
{
	private $component; //组件

	private $prefix; 	//定义前缀

	function __construct($config = [])
	{
		if (!isset($config['host'])) {
			throw new \Exception("error host");
		}
		$host 	= $config['host'];
		$port 	= isset($config['port']) ? $config['port'] : 6379;
		$type 	= isset($config['type']) && $config['type'] ? $config['type'] : 'connect';
		$auth 	= isset($config['auth']) && $config['auth'] ? $config['auth'] : false;
		$this->component = new \Redis;
		$method = $type == 'pconnect' ? $type : 'connect';
		if (!$this->component->$method($host , $port)) {
			throw new \Exception("can not connect host:{$host} port:{$port}");
		}
		if ($auth && !$this->component->auth($auth)) {
			throw new \Exception("can not auth host:{$host} auth:{$auth}");
		}
		isset($config['prefix']) && $this->prefix = $config['prefix'];
		if (!isset($config['db'])) return ;
		$db 	= intval($config['db']);
		if (!$this->component->select($db ? $db : 0)) {
			throw new \Exception("can not select db host:{$host} db:{$db}");
		}
		
	}

	/**
	 * 设置缓存
	 *
	 * @access 	public 	
	 * @param 	string 	$key 	键值
	 * @param 	string 	$value 	数据
	 * @param 	int 	$expire 过期时间
	 * @return 	boolen
	 */
	public function set($key , $value , $expire = 0)
	{
		$expire = (int) $expire;
		return $expire > 0 ? $this->component->setex($this->prefix.$key , $expire , $value) : $this->component->set($this->prefix.$key , $value);
	}

	/**
	 * 获取缓存
	 *
	 * @access 	public 	
	 * @param 	string 	$key 	键值
	 * @return 	string
	 */
	public function get($key)
	{
		return $this->component->get($this->prefix.$key);
	}
    public function test()
    {

        exit;
    }

	/**
	 * 入队列
	 *
	 * @access 	public 	
	 * @param 	string 	$key 	键值
	 * @param 	string 	$value 	数据
	 * @param 	boolen 	$bottom 是否队尾
	 * @return 	boolen
	 */
	public function push($key , $value , $bottom = true)
	{
		return (bool) $bottom === true ? $this->component->rpush($this->prefix.$key , $value) : $this->component->lpush($this->prefix.$key , $value);
	}

	/**
	 * 出队列
	 *
	 * @access 	public 	
	 * @param 	string 	$key 	键值
	 * @param 	boolen 	$top 	是否队首
	 * @return 	string
	 */
	public function pop($key , $top = true)
	{
		return (bool) $top === true ? $this->component->lpop($this->prefix.$key) : $this->component->rpop($this->prefix.$key);
	}

	/**
	 * 删除数据
	 *
	 * @access 	public 	
	 * @param 	string 	$key 	键值
	 * @return 	string
	 */
	public function delete($key)
	{
		return $this->component->delete($this->prefix.$key);
	}




}