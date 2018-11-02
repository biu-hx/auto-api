<?php

namespace app\component;

class Curl
{

	private static $opt  	= [];

	private static $params 	= [];

	private static $type 	= 'get'; 	//定义默认type类型

	private static $error 	= ''; 		//error存储

	private static $httpCode= 200;

	private static $handle 	= null; 	//句柄 		根据项目需求只进行单一句柄操作

	/**
	 * 初始化curl
	 *
	 * @access 	public 
	 * @static 
	 * @return  void
	 */
	public static function init()
	{
		 self::$handle 	= curl_init(); 						//新句柄
         self::$opt  	= [ 				//设置项
            CURLOPT_TIMEOUT 			=> 10,		//默认10秒断开
            CURLOPT_HEADER				=> false, 	//默认头文件的信息不作为数据流输出
            CURLOPT_RETURNTRANSFER 		=> true, 	//默认为字符串返回  false 为直接输出
            CURLOPT_HTTPHEADER			=> ['Content-type: application/x-www-form-urlencoded;charset=utf-8'],
        ];

	}


	/**
	 * 重置curl句柄
	 *
	 * @access 	public 
	 * @static 
	 * @return  void
	 */
	public static function reset()
	{
		if (!self::$handle) { throw new \Exception('no handle');}
		curl_reset(self::$handle);
	}

	/**
	 * 设置httpHeader
	 * 
	 * @access 	public 
	 * @static 
	 * @param 	array 	$httpHeader header数组
	 * @return  void
	 */
	public static function setHttpHeader($httpHeader)
	{
		self::$opt[CURLOPT_HTTPHEADER] 	= $httpHeader;
	}

	/**
	 * 设置超时时间
	 *
	 * @access 	public 
	 * @static 
	 * @param 	int 	$second 	超时时间（秒）
	 * @return  void
	 */
	public static function setTimeout($second)
	{
		self::$opt[CURLOPT_TIMEOUT] 	= $second;
	}

	/**
	 * 设置url连接
	 *
	 * @access 	public 
	 * @static 
	 * @param   string 	$url 	url地址
	 * @return  void
	 */
	public static function setUrl($url)
	{
		self::$opt[CURLOPT_URL] 			= $url;
		if ('https' == strtolower(substr($url , 0 , 5))) {
			self::$opt[CURLOPT_SSL_VERIFYPEER] 	= 0; 		//不验证证书
			self::$opt[CURLOPT_SSL_VERIFYHOST] 	= 0;	
		}
	}

	/**
	 * 设置默认头文件的信息是否作为数据流输出 
	 *
	 * @access 	public 
	 * @static 
	 * @param   boolen 	$header 	true
	 * @return  void
	 */
	public static function setHeader($header)
	{
		self::$opt[CURLOPT_HEADER] 		= $header; 				//控制是否需要返回header
	}

	/**
	 * 设置http请求方式
	 *
	 * @static
	 * @access 	public
	 * @param 	string 	$type 	请求类型
	 * @return 	void
	 */
	public static function setCustomRequest($type)
	{
		self::$type = $type;
	}

	/**
	 * 设置请求参数
	 *
     * @static 
	 * @access 	public 
	 * @param   array 	$params 请求的参数
	 * @return  void
	 */
	public static function setParams($params)
	{
		if (!$params) return ; 	//如果没有数据，则为GET请求
		self::$type || self::$type 	= 'post';
		self::$params 	= $params;
		if (self::$type == 'post') {
			self::$opt[CURLOPT_CUSTOMREQUEST] 	= 'POST'; 		//如果请求为POST
			self::$opt[CURLOPT_POSTFIELDS] 		= $params; 		//设置参数;
		} else if (self::$type == 'put') {
			self::$opt[CURLOPT_CUSTOMREQUEST] 	= 'PUT'; 		//如果请求为PUT
			self::$opt[CURLOPT_POSTFIELDS] 		= $params; 		//设置参数;
		} else if (self::$type == 'patch') {
			self::$opt[CURLOPT_CUSTOMREQUEST] 	= 'PATCH'; 		//如果请求为PATCH;
			self::$opt[CURLOPT_POSTFIELDS] 		= $params; 		//设置参数;
		} else if (self::$type == 'delete') {
			self::$opt[CURLOPT_CUSTOMREQUEST] 	= 'DELETE'; 	//如果请求为DELETE;
			self::$opt[CURLOPT_POSTFIELDS] 		= $params; 		//设置参数;
		} 	
	}

	/**
	 * 设置opt
	 *
	 * @access 	public 
	 * @static 
	 * @return  void
	 */
	public static function setOpt()
	{
		$opt 	= self::$opt;
		curl_setopt_array(self::$handle , $opt); 	 		//设置参数
	}

	/**
 	 * 执行curl
	 *
	 * @access 	public 
	 * @static 
	 * @return  data , boolen
	 */
	public static function execute()
	{
		self::$error 	= ''; 								
		$result 		= curl_exec(self::$handle);

		$curlInfo 		= curl_getinfo(self::$handle);
		self::$httpCode = $httpCode = $curlInfo['http_code'];
		$result 		= $httpCode == 200 ? $result : false;
		if ($httpCode != 200) { //如果httpcode不等于200 获取错误信息
			$error 			= curl_error(self::$handle);
			self::$error 	= $error;
		}
        curl_close(self::$handle);
        self::$handle = false;


		return $result;

	}
	

	/**
	 * 获取httpcode码
	 *
	 * @access 	public
	 * @static
	 * @return 	int
	 */
	public static function getHttpCode()
	{
		return self::$httpCode;
	}

	/**
	 * 获取错误消息 
	 *
	 * @access 	public 
	 * @static 
	 * @return  string
	 */
	public static function getError()
	{
		return self::$error;
	}

    public static function postData($url,$data,$header1=[]){
        $header = [
            'Content-Type:application/x-www-form-urlencoded;charset=utf-8',
        ];
        $header = array_merge($header,$header1);
        self::init();
        self::setUrl($url);
        self::setCustomRequest('POST');
        $data && Curl::setParams($data);
        Curl::setHttpHeader($header);
        Curl::setOpt();
        $response 	= Curl::execute();
        return $response;
    }

    public static function curlPost($url,$data,$headerAdd=[]){
        $ch = curl_init();
        $header[] = 'charset:utf8';
        $header = array_merge($header,$headerAdd);
        curl_setopt($ch, CURLOPT_HTTPHEADER,$header);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 6);
        curl_setopt($ch, CURLOPT_TIMEOUT, 6);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, TRUE);
//        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, TRUE);
        //curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $tmpInfo = curl_exec($ch);
        if (curl_errno($ch)) {
            return false;
        } else {
            return $tmpInfo;
        }
    }






}
