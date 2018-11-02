<?php
// +----------------------------------------------------------------------
// | 登录
// +----------------------------------------------------------------------
// | Author: duanhy <shongyudxmas@163.com> 
// +----------------------------------------------------------------------
// | version: 1.0
// +----------------------------------------------------------------------

namespace app\api\controller;

use think\Loader;
use app\api\controller\Base;
use app\component\response\Response;

class App extends Base
{
	protected $validate = '\app\api\validate\App';

	protected $scene 	= [
		'markCall',
	];

	/**
	 * 医生App用户登录
	 *
	 * @access 	public
	 * @return 	void
	 */
	public function version()
	{
		$data 	= Loader::model('App')->version();
		$response 	= [
			'version' 	=> $data && $data['version'] ? $data['version'] : '',
			'url' 		=> $data && $data['url'] ? $data['url'] : '',
		];
		Response::message(10000 , $response);
	}

	/**
	 * 标记时间（考虑崩溃情况不验证用户）
	 *
	 * @access 	public
	 * @return 	void
	 */
	public function markCall()
	{
		$doctorId 	= $this->doctorId;
		$inquiryId 	= $this->data['inquiryId'];
		$callId 	= $this->data['callId'];
		$eventType  = $this->data['eventType'];
		$time 		= $this->data['time'];
		$data 		= Loader::model('Inquiry')->isCall($inquiryId , $callId);
		$data 		|| Response::message(13003);
		$result 	= $eventType == 'EVENT_BEGIN' ? Loader::model('Inquiry')->markCallBegin($inquiryId  , $callId , $time) : Loader::model('Inquiry')->markCallEnd($inquiryId , $callId , $time);
		$holdingTime= Loader::model('Inquiry')->holdingTime($inquiryId);
		Loader::model('Inquiry')->setHoldingTime($inquiryId , $holdingTime);
		$response 	= [
			'inquiryId' 	=> $inquiryId,
			'callId' 		=> $callId,
			'eventType' 	=> $eventType,
			'time' 			=> $time,
		];
		Response::message(10000 , $response);
	}

}