<?php

namespace app\component\response;

use think\Request;
use think\Config;
use app\component\Log;

class Response
{

	private static $prompt = [];

	/**
	 * 成功提示
	 *  
	 * @access 	public
	 * @param 	array 	$data 		数据 		
	 * @return 	void
	 */
	public static function success($data = true)
	{
		self::message(10000 , $data);
	}

	/**
	 * 错误提示
	 * 
	 * @access 	public
	 * @param 	int 	$code 		错误码 		
	 * @return 	void
	 */
	public static function error($code)
	{
		self::message($code);
	}



	/**
	 * 输出
	 * 
	 * @static
	 * @access 	public
	 * @param 	array 	$data 	返回数据
	 * @return 	void
	 */
	public static function jsonPut($data)
	{  
		echo json_encode($data , JSON_UNESCAPED_UNICODE);die; 
	}

	/**
	 * 相应的code编码
	 *
	 * @static
	 * @access	public
	 * @param 	int 	$code 	状态码
	 * @param 	array 	$data 	数据
	 * @return  void
	 */
	public static function message($code , $data = true)
	{	
		$module 	= Request::instance()->module();
        $action 	= Request::instance()->action();
		if (!isset(self::$prompt[$module])) {
			$config 	= Config::get('prompt');
			if (!isset($config[$module])) {
				throw new \Exception("error prompt message");
			}
			self::$prompt[$module] 	= new $config[$module];
		}
		$Prompt 	= self::$prompt[$module];
		$message 	= $Prompt->prompt($code);
		$output 	= ['code' => $code , 'msg' => $message];
		$code == 10000 && $data !== true && $output['data'] = $data;
        $code == 10012 && !is_array($data) && $output['msg'] = $data;//自定义提示信息
		Log::storageResponse($module.$action.'Response' , $output);
		Log::writeLog($module.$action.'Response');
		self::jsonPut($output);
		
	}

    /**
     * 直接输出错误信息
     * @param $message
     * @param int $code
     * @param bool $data
     */
    public static function errorMessage($message,$code=20000 , $data = true){
        $module 	= Request::instance()->module();
	    if($message){
            $output 	= ['code' => $code , 'msg' => $message];
            if($data!==true){
                $output['data'] = $data;
            }
            Log::storageResponse($module.'Response' , $output);
            Log::writeLog($module.'Response');
            self::jsonPut($output);
        }else{
	        self::message($code,$data);
        }

    }



    /**
     * 输出xml字符
     * @throws WxPayException
     **/
    public static function  ToXml($data,$k='')
    {
        $xml='';
        foreach ($data as $key=>$val)
        {
            if($k){
                $key=$k;
            }

            if (is_numeric($val)){
                $xml.="<".$key.">".$val."</".$key.">";
            }else{
                if(empty($val)){
                    $val='';
                }
                if(is_array($val)){
                    if(is_numeric(key($val))){
                        $xml.=self::ToXml($val,$key);
                    }else{
                        $xml.="<".$key.">".self::ToXml($val)."</".$key.">";
                    }
                }else{
                    $xml.="<".$key.">".htmlspecialchars($val)."</".$key.">";
                }
            }
        }
        return $xml;
    }



}