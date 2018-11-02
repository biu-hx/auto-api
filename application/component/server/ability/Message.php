<?php

namespace app\component\server\ability;

use app\component\Curl;
use app\component\Log;

class Message
{	

	private $url;

	private $appid;

	private $appsecret;

	private static $route 	= [
		'sendMessage' 		=> ['/index.php?s=/Home/MessageInterface/sendMessageToUser' , 'post'],
	
	];


	function __construct($config = [])
	{
		$config = (array) $config; 		//强制config为数组
		if (!isset($config['url']) || !preg_match('/^((https|http|ftp|rtsp|mms)?:\/\/)[^\s]+/' , $config['url'])) {
			throw new \Exception('error url');	
		}
		if (!isset($config['appid'])) {
			throw new \Exception('error appid');	
		}
		if (!isset($config['appsecret'])) {
			throw new \Exception('error appsecret');	
		}
		$this->url 		= $config['url'];
		$this->appid 	= $config['appid'];
		$this->appsecret= $config['appsecret'];
	}

	/**
	 * 发送腾讯短信
	 * 
	 * @param 	string 	$phone 		电话号码
	 * @param 	string 	$tempId 	短信模板ID
	 * @param 	array 	$temp 		短信内容 （参考消息中心）
	 * @param 	string 	$signname 	短信签名
	 * @return 	array
	 */
	public function sendMessage($phone , $tempId , $temp , $signname)
	{
		$data 	= json_encode([
			'phone' 	=> $phone,
			'signName' 	=> $signname,
			'tempId' 	=> $tempId,
			'temp' 		=> $temp,
		] , JSON_UNESCAPED_UNICODE);
		$time 	= time();
		$params = [
			'interface_id' 	=> $this->appid,
			'type' 			=> 2,
		];
 		$params['sign'] 	= $this->signature($params , $time);
 		$params['time'] 	= $time;
 		$params['data'] 	= $data;
		$data 	= $this->request(__FUNCTION__ , $params);
		return $data; 
	}

	/**
	 * 获取签名
	 *
	 * @access 	public
	 * @param 	array 	$data 		签名数据 	
	 * @param 	int 	$time 		签名时间
	 * @return 	string
	 */
	private function signature($data , $time)
	{
		ksort($data);
	    $sign     = [];
	    foreach ($data as $key => $v) {
	        $sign[]     = $key.'='.$v;
	    }
	    $string = implode('' , $sign);
	    return md5($string.$this->appsecret.$time);
	}
	

	/**
	 * 请求
	 *
	 * @access 	private
	 * @param 	string 	$method 请求调用的方法
	 * @param   array 	$params 请求的参数
	 * @return 	array
	 */
	private function request($method , $params)
	{
		Log::storageRequest('messageRequest_'.$method , $params);
		$data 	= http_build_query($params);
		$header = [
			'Content-Type:application/x-www-form-urlencoded;charset=utf-8',
		];
		$route 		= self::$route[$method][0];
		$type 		= isset(self::$route[$method][1]) ? strtolower(self::$route[$method][1]) : 'post';
		switch ($type) {
			case 'get':			
				$url 	= $this->url.$route.'?'.implode('&' , $params);
				break;
			case 'post':		
				$url 	= $this->url.$route;
				$data 	= http_build_query($params);
				break;
			case 'put': 	
				$url 	= $this->url.$route;
				$data 	= http_build_query($params);
			break;
			case 'patch':	
				$url 	= $this->url.$route;
				$data 	= http_build_query($params);
			break;
			case 'delete':	
				$url 	= $this->url.$route;
				$data 	= http_build_query($params);
			break;
			default:		break;
		}
		Log::storageRoute('messageRequest_'.$method , $url);
		Curl::init();
		Curl::setUrl($url);
		Curl::setCustomRequest($type);
		$data && Curl::setParams($data);
		Curl::setHttpHeader($header);
		Curl::setOpt();
		$response 	= Curl::execute();	
		$result 	= json_decode($response , true);
		Log::storageResponse('messageRequest_'.$method , $result);
		Log::writeLog('messageRequest_'.$method);
		return $result ? $result : [];
	}





}