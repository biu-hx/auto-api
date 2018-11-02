<?php

namespace app\api\event\v1;

use think\Loader;
use app\api\controller\Base;
use app\component\response\Response;

class Personal extends Base
{

	protected $validate = '\app\api\validate\v1\Personal';

	protected $scene 	= [
		'modify',
		'income',
		'autoOnline',
	];

	/**
	 * 获取医生的基本信息
	 *
	 * @access 	public
	 * @return 	void
	 */
	public function basic()
	{
		$doctorId 	= $this->doctorId;
		$basic 		= Loader::model('Personal')->basic($doctorId);
		$auto 		= Loader::model('Personal')->isAutoOnline($doctorId);
		$response 	= [
			'name' 		=> $basic['name'],
			'phone' 	=> $this->doctorPhone,
			'picture' 	=> $basic['avatar'],
			'auto' 		=> [
				'open' 		=> $auto ? $auto['open'] : 0,
				'online' 	=> $auto && $auto['open'] == 1 ? $auto['online'] : 0,
			],
		];
		Response::message(10000 , $response);
	}

	/**
	 * 医生的信息
	 *
	 * @access 	public
	 * @return 	void
	 */
	public function info()
	{
		$doctorId 	= $this->doctorId;
		$data 		= \app\api\model\Personal::info($doctorId);
        $data || Response::errorMessage("没有找到医生信息");
//		print_r($data);exit;
		$response 	= [
			'uptime' 	=> ['EVENT1' , $data['uptime']],
			'title' 	=> ['EVENT2' , $data['title']],
			'sections' 	=> ['EVENT3' , $data['sections']],
			'content' 	=> ['EVENT4' , $data['content']],
		];
		Response::message(10000 , $response);
	}

	/**
	 * 获取账号
	 *  
	 * @access 	public
	 * @return 	void
	 */
	public function account()
	{
		$doctorId 	= $this->doctorId;
		$response 	= [
			'auto' => ['open' => 0 , 'status' => 0]
		];
		$open 		= Loader::model('Personal')->isAutoOpen($doctorId);
		$response['auto']['open'] 	= $open;
		if ($open == 1) {
			$huawei 	= Loader::model('Personal')->huawei($doctorId);
			if ($huawei) {
				$response['auto']['status'] = $huawei['status'];
				$huawei['status'] == 1 && $response['auto']['number'] 	= $huawei['number'] ;
				$huawei['status'] == 1 && $response['auto']['password'] = $huawei['password'];
			}
		}
		Response::message(10000 , $response);
	}

	/**
	 * 修改个人信息
	 *
	 * @access 	public
	 * @return 	void
	 */
	public function modify()
	{
		$doctorId 	= $this->doctorId;
		$eventType 	= $this->data['eventType']; 		//操作类型
		$content 	= $this->data['content']; 			//内容
//        echo $eventType;exit;
        $field = "";
		switch ($eventType) {
			case 'EVENT1':$field = 'uptime'; break;
			case 'EVENT2':$field = 'title';break;
			case 'EVENT3':$field = 'sections';break;
			case 'EVENT4':$field = 'content';break;
			default:break;
		}
        $field || Response::errorMessage("没找到对应参数");
		Loader::model('Personal')->modifyField($doctorId , $field , $content);
		$response 	= [
			$field 	=> [$eventType , $content],
		];
		Response::message(10000 , $response);
	}

	/**
	 * 设置上下线
	 *
	 * @access 	public
	 * @return 	void
	 */
	public function autoOnline()
	{
		$doctorId 	= $this->doctorId;
//		echo $doctorId;exit;
		$online 	= $this->data['online'];
		$open 		= \app\api\model\Personal::isAutoOpen($doctorId);
		$open == 0 && Response::message(12001);
		$open == 2 && Response::message(12002);
		$data 		= $online == 1 ? Loader::model('Personal')->autoOnline($doctorId) : Loader::model('Personal')->autoOffline($doctorId);
		$response 	= [
			'online' => $online,
		];
		Response::message(10000 , $response);
	}

	
	/**
	 * 获取收入
	 * 
	 * @access 	public
	 * @return 	void
	 */
	public function income()
	{
//        $wechat = new \app\component\server\WechatAPI();
//        $wechat->sendAppTemplateMsg("");
//        exit;
		$doctorId 	= $this->doctorId;
		$page 		= isset($this->data['page']) ? $this->data['page'] : 1;
		$pagesize 	= isset($this->data['pagesize']) ? $this->data['pagesize'] : \think\Config::get('pagesize');
		$data 		= Loader::model('Personal')->income($doctorId , $page , $pagesize);
		$response 	= [
			'price' 	=> $data['price'],
			'count' 	=> $data['count'],
			'totalPage' => $data['totalPage'],
			'page' 		=> $page,
			'pagesize' 	=> $pagesize,
			'list' 		=> $data['list'],
		];
		Response::message(10000 , $response);
	}




}