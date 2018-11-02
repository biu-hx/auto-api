<?php

namespace app\component\response\message;

class MessageApi
{
	
	private static $prompt 	= [
		400 		=> 'Error 400 Bad Request', 				// 	- 400
		401 		=> 'Error 401 Unauthorized', 				// 	- 401
		402 		=> 'Error 402 Payment Required', 			//  - 402
		403 		=> 'Error 403 Forbidden', 					// 	- 403
		404 		=> 'Error 404 Not Found', 					//  - 404
		405 		=> 'Error 405 Method Not Allowed', 			// 	- 405
		502 		=> 'Error 502 Bad Gateway', 				//  - 502 
		10000 		=> 'ok',									//	- 成功返回状态，对应，GET,PUT,PATCH,DELETE等
		10001 		=> 'created', 								//	- 成功创建。
		10002 		=> 'not modified', 							//	- HTTP缓存有效。
		10003 		=> 'bad request', 							//	- 请求格式错误。
		10004 		=> 'unauthorized', 							//	- 未授权。
		10005 		=> 'forbidden', 							//	- 鉴权成功，但是该用户没有权限。
		10006 		=> 'not found', 							//	- 请求的资源不存在
		10007 		=> 'method not allowed', 	  				//	- 该http方法不被允许。
		10008 		=> 'gone', 									//	- 这个url对应的资源现在不可用。
		10009 		=> 'unsupported media type', 				//	- 请求类型错误
		10010 		=> 'unprocessable entity', 					//	- 校验错误时用
		10011 		=> 'too many request', 						//	- 请求过多
		/******************************调用服务失败从11001至11999*********************************/
		11001 		=> '验证码发送失败', 						// 	- 短信服务不可用
		/******************************用户服务失败从12001至12999*********************************/
		12001 		=> '问诊服务未开通，请联系后台管理员18086957701', 					// 	- 自助机问诊服务未开通
		12002 		=> '问诊服务已被禁用，请联系后台管理员18086957701', 				// 	- 自助机问诊服务未开通
		13001 		=> '此手机号未注册，请重新输入', 			// 	- 医生不存在
		13002 		=> '此咨询单不存在', 						// 	- 咨询单不存在
		13003 		=> '此咨询呼叫单不存在', 					// 	- 咨询呼叫单不存在
		14001 		=> '没有权限标记此咨询单', 					// 	- 没有权限标记此咨询单
		19000 		=> '您的账户在其他设备登录', 				// 	- 已在其他设备登录
		20000 		=> '执行异常，请重试', 						// 	- 执行失败
		20001 		=> '缺少参数emt-key', 						// 	- 缺少参数emt-key
		20002 		=> '缺少参数emt-jwt', 						// 	- 缺少参数emt-jwt
		20003 		=> '缺少参数user-equip', 		 			//  - 缺少参数user-equip
		20004 		=> '缺少参数api-version', 					// 	- 缺少参数api-version
		20101 		=> 'emt-key参数不匹配', 					// 	- emt-key参数不匹配
		20102 		=> 'emt-jwt参数不匹配', 					// 	- emt-jwt参数不匹配
		20103 		=> '验证码错误', 							// 	- code参数不匹配
		20104 		=> 'token参数不匹配', 						// 	- token参数不匹配
		20111 		=> '密码错误', 								// 	- token参数不匹配
		20201 		=> 'emt-jwt参数已过期', 					// 	- emt-jwt参数已过期
		20202 		=> '验证码已过期', 							// 	- code参数已过期
		20203 		=> '修改密码已过期', 						// 	- token参数已过期
		30001 		=> '缺少参数eventType', 					// 	- 缺少参数eventType
		30101  		=> 'eventType参数错误', 					// 	- eventType参数错误 具体参数请查看相关接口
		30102 		=> 'page参数错误', 							// 	- page参数错误
		30103 		=> 'pagesize参数错误', 						// 	- pagesize参数错误
		31001 		=> '缺少参数phone',  			 			// 	- 缺少参数phone
		31002 		=> '缺少参数password', 						// 	- 缺少参数password
		31003 		=> '缺少参数code',  			 			// 	- 缺少参数code
		31004 		=> '缺少参数token', 						// 	- 缺少参数token
		31101 		=> 'phone参数格式错误', 					// 	- phone参数格式错误
		31102 		=> 'password参数格式错误', 					// 	- password参数格式错误
		31103 		=> 'code参数格式错误', 						// 	- code参数格式错误
		31104 		=> 'token参数格式错误', 					// 	- token参数格式错误
		32001 		=> '缺少参数content', 						// 	- 缺少参数content
		32002 		=> '缺少参数online', 						// 	- 缺少参数online
		32101 		=> 'content参数格式错误', 				 	// 	- content参数格式错误
		32102 		=> 'online参数格式错误', 					// 	- online参数格式错误
		33001 		=> '缺少参数inquiryId',  					// 	- 缺少参数inquiryId
		33002 		=> '缺少参数callId', 						// 	- 缺少参数callId
		33003 		=> '缺少参数time',  			 			// 	- 缺少参数time
		33004 		=> '缺少参数number', 						// 	- 缺少参数number
		33005 		=> '缺少参数orderNum', 						// 	- 缺少参数orderNum
		33006 		=> '缺少参数orderStatus', 					// 	- 缺少参数orderStatus
		33007 		=> '缺少参数inquiryStatus', 				// 	- 缺少参数inquiryStatus
		33101 		=> 'inquiryId参数格式错误', 				// 	- inquiryId参数格式错误
		33102 		=> 'ecallId参数格式错误', 					// 	- callId参数格式错误
		33103 		=> 'time参数格式错误', 						// 	- time参数格式错误
		33104 		=> 'number参数格式错误', 					// 	- number参数格式错误
        33105 		=> '缺少参数js_code', 					// 	- 缺少参数
        33106 		=> '缺少参数iv', 					// 	- 缺少参数
        33107 		=> '你还没有登录', 					// 	- 小程序你还没有登录
        33108 		=> '其它设备上已登录', 					// 	- 小程序其它设备上已登录
        33109 		=> '登录已过期，请重新登陆', 					// 	- 小程序登录已过期，请重新登陆
	];





	/**
	 * 获取解释语
	 *
	 * @access 	public
	 * @param 	int 	$code  	返回的状态码
	 * @return 	string
	 */
	public function prompt($code)
	{
		return self::$prompt[$code];
	} 

}