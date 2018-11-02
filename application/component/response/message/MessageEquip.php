<?php

namespace app\component\response\message;

class MessageEquip
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
        10012 		=> '提示信息', 						//	- 提示信息
		/******************************调用服务失败从11001至11999*********************************/
		/******************************用户服务失败从12001至12999*********************************/
		10101 	 	=> '网络异常，请重试', 						// 	- 接口响应失败
		10102 	 	=> '暂无数据',//'request data fail', 		// 	- 接口响应数据失败
		20001 		=> '不存在的mac地址', 						// 	- 不存在的mac地址
		20002 		=> '不存在的自助机编号', 					// 	- 不存在的自助机编号
		20011 		=> '时间过期', 								// 	- 时间过期
		20021 		=> '签名错误', 								// 	- 签名错误
		20101 		=> '缺少参数signatrue', 					// 	- 缺少参数 	signatrue
		20102 		=> '缺少参数number', 						// 	- 缺少参数 	number
		20103 		=> '缺少参数nonce', 						// 	- 缺少参数	nonce
		20104 		=> '缺少参数timestamp', 					// 	- 缺少参数 	timestamp
		29001 		=> '缺少参数hospitalId', 					// 	- 缺少参数hospitalId
		29002 		=> '没有权限', 								// 	- 没有权限
		30000 		=> '没有此患者', 							// 	- 没有患者
		30001 		=> '此医生没有排班信息', 					// 	- 医生无排班
		30002 		=> '可挂号数量不足', 						// 	- 可挂号数量不足
		30003 		=> '锁号失败',								// 	- 锁号失败
		30004 		=> '订单执行失败',							// 	- 锁号成功 订单失败
		30005 		=> '锁号频繁',								// 	- 锁号成功 订单失败
		30011 		=> '不存在的订单', 							// 	- 不存在的订单
		30013 		=> '此医生没有开通视频问诊', 				// 	- 此医生没有开通
		30014 		=> '此医生未在线', 							// 	- 此医生不处在在线状态
		30015 		=> '问诊下单失败', 							// 	- 问诊下单失败
		30016 		=> '视频不存在', 							// 	- 视频不存在
		30017 		=> '视频下单失败', 							// 	- 视频下单失败
		30018 		=> '医生忙碌中', 							// 	- 医生忙碌中
		30019 		=> '建立连接失败', 							// 	- 建立连接失败
		30020		=> '此问诊不能连接', 						// 	- 此问诊不能连接
		30021 		=> '不存在的医生', 							// 	- 不存在的医生
		30031 		=> '取号失败', 								// 	- 取号失败
		30032 		=> '取号成功 订单失败', 					// 	- 取号成功 订单失败
		30033 		=> '门诊失败', 								//  - 门诊失败
		30034 		=> '门诊成功 订单失败', 					// 	- 门诊成功 订单失败
		30035 		=> '住院失败', 								//  - 住院失败
		30036 		=> '住院成功 订单失败', 					// 	- 住院成功 订单失败
		30037 		=> '生成微信二维码错误', 					// 	- 生成微信二维码错误
		30038 		=> '生成支付宝二维码错误', 					// 	- 生成支付宝二维码错误
		30051 		=> '报告已打印', 							// 	- 报告已打印
		30052 		=> '检验条码已打印', 						// 	- 检验条码已打印
        30053 		=> '今天已挂当前医生的号', 					// 	- 今天已挂当前医生的号
        30054 		=> '缴费金额超出范围', 					    // 	- 缴费金额超出范围
        30055 		=> '操作失败', 					            // 	- 操作失败

		/******************************下面是缺少参数和参数错误的问题*********************************/
		90000 		=> '缺少参数    orderNum', 					// 	- 缺少参数 	orderNum
		90001		=> '缺少参数 	mac', 						// 	- 缺少参数 	mac
		90002		=> '缺少参数 	hardware', 					// 	- 缺少参数 	hardware
		90003		=> '缺少参数 	value', 					// 	- 缺少参数 	value
		90004		=> '缺少参数 	version', 					// 	- 缺少参数 	version
		90005		=> '缺少参数 	cardId', 					// 	- 缺少参数 	cardId
		90006		=> '缺少参数 	IDcard', 					// 	- 缺少参数 	IDcard
		90007		=> '缺少参数 	healthCard', 				// 	- 缺少参数 	healthCard
		90008		=> '缺少参数 	admId', 					// 	- 缺少参数 	admId
		90009		=> '缺少参数 	arpbl', 					// 	- 缺少参数 	arpbl	
		90010		=> '缺少参数 	deptId', 					// 	- 缺少参数 	deptId
		90011		=> '缺少参数 	doctorId', 					// 	- 缺少参数 	doctorId
		90012		=> '缺少参数 	callId', 					// 	- 缺少参数 	callId
		90013		=> '缺少参数 	picture', 					// 	- 缺少参数 	picture	
		90014		=> '缺少参数 	search', 					// 	- 缺少参数 	search	
		90015		=> '缺少参数 	link', 						// 	- 缺少参数 	link	
		90016		=> '缺少参数 	treatNo', 					// 	- 缺少参数 	treatNo	
		90017		=> '缺少参数 	fee', 						// 	- 缺少参数 	fee	
		90018		=> '缺少参数 	date', 						// 	- 缺少参数 	date	
		90019		=> '缺少参数 	period', 					// 	- 缺少参数 	period	
		90020		=> '缺少参数 	scheduleId', 				// 	- 缺少参数 	scheduleId	
		90021		=> '缺少参数 	inspecNo', 					// 	- 缺少参数 	inspecNo	
		90022		=> '缺少参数 	reportType', 				// 	- 缺少参数 	reportType	
		90023		=> '缺少参数 	printCode', 				// 	- 缺少参数 	printCode	
		90024		=> '缺少参数 	videoId', 					// 	- 缺少参数 	videoId	
		90025		=> '缺少参数 	personId', 					// 	- 缺少参数 	personId
		90026		=> '缺少参数 	image', 					// 	- 缺少参数 	image
		90027		=> '缺少参数 	name', 					    // 	- 缺少参数 	name
		90028		=> '缺少参数 	content', 					// 	- 缺少参数 	content
		90029		=> '缺少参数或格式错误 	phone', 			// 	- 缺少参数 	phone
        90030		=> '缺少参数   parent_id', 			        // 	- 缺少参数 	parent_id
        90031		=> '缺少参数   payType', 			        // 	- 缺少参数 	payType
        90032		=> '缺少参数   business', 			        // 	- 缺少参数 	payType
        90033		=> '缺少参数   orderId', 			        // 	- 缺少参数 	payType
        90034		=> '缺少参数   status', 			        // 	- 缺少参数 	payType
        90035		=> '缺少参数   price', 			            // 	- 缺少参数 	price
        90036		=> '缺少参数   cardName', 			        // 	- 缺少参数 	cardName
        90037		=> '缺少参数   cardNo', 			        // 	- 缺少参数 	cardNo
        90038		=> '缺少参数   payTime', 			        // 	- 缺少参数 	payTime
        90039		=> '缺少参数   eatingCode', 			    // 	- 缺少参数 	eatingCode
        90040		=> '缺少参数   regNumber', 			    // 	- 缺少参数 	regNumber
        90041		=> '缺少参数   orderId', 			    // 	- 缺少参数 	regNumber

		91000 		=> 'orderNum参数格式错误', 					// 	- 格式错误 	orderNum
		91001		=> 'mac参数格式错误', 						// 	- 格式错误 	mac
		91002		=> 'hardware参数格式错误', 					// 	- 格式错误 	hardware
		91003		=> 'value参数格式错误', 					// 	- 格式错误 	value
		91004		=> 'version参数格式错误', 					// 	- 格式错误 	version
		91005		=> 'cardId参数格式错误', 					// 	- 格式错误 	cardId
		91006		=> 'IDcard参数格式错误', 					// 	- 格式错误 	IDcard
		91007		=> 'healthCard参数格式错误', 				// 	- 格式错误 	healthCard
		91008		=> 'admId参数格式错误', 					// 	- 格式错误 	admId
		91009		=> 'arpbl参数格式错误', 					// 	- 格式错误 	arpbl
		91010		=> 'deptId参数格式错误', 					// 	- 格式错误 	deptId
		91011		=> 'doctorId参数格式错误', 					// 	- 格式错误 	doctorId
		91012		=> 'callId参数格式错误', 					// 	- 格式错误 	callId
		91013		=> 'picture参数格式错误', 					// 	- 格式错误 	picture	
		91014		=> 'search参数格式错误', 					// 	- 格式错误 	search	
		91015		=> 'link参数格式错误', 						// 	- 格式错误 	link	
		91016		=> 'treatNo参数格式错误', 					// 	- 格式错误 	treatNo	
		91017		=> 'fee参数格式错误', 						// 	- 格式错误 	fee	
		91018		=> 'date参数格式错误', 						// 	- 格式错误 	date	
		91019		=> 'period参数格式错误', 					// 	- 格式错误 	period	
		91020		=> 'scheduleId参数格式错误', 				// 	- 格式错误 	scheduleId	
		91021		=> 'inspecNo参数格式错误', 					// 	- 格式错误 	inspecNo	
		91022		=> 'reportType参数格式错误', 				// 	- 格式错误 	reportType	
		91023		=> 'printCode参数格式错误', 				// 	- 格式错误 	printCode	
		91024		=> 'videoId参数格式错误', 					// 	- 格式错误 	videoId
		91025		=> '尚未接听', 					            // 	- 格式错误 	videoId
		91026		=> '付费视频,缺少orderNumber', 					// 	- 格式错误 	videoId
		91027		=> '订单未支付,或订单异常', 					// 	- 格式错误 	videoId
		91028		=> '订单已经存在', 					        // 	- 订单已经存在
        91029		=> '您选择的项没有查到数据，请重新选择', 					        // 	- 订单已经存在
        91030		=> '评价失败!', 					        // 	- 评价失败
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