<?php
// +----------------------------------------------------------------------
// | 基础继承控制
// +----------------------------------------------------------------------
// | Author: duanhy <shongyudxmas@163.com> 
// +----------------------------------------------------------------------
// | version: 1.0
// +----------------------------------------------------------------------

namespace app\equip\controller;

use think\Controller;
use think\Request;
use think\Loader;
use app\component\response\Response;
use app\component\Log;

class Base extends Controller
{
	protected $action; 						//定义当前请求类型

	protected $controller; 					//定义当前执行文件

	protected $validate; 					//设置验证器

	protected $scene 		= []; 			//控制需要验证的关系

	protected $data; 						//数据接收参数

	protected $projectId; 					//定义项目

	protected $hospitalId 	= 0; 			//定义是哪个医院的数据

    protected $hospitalName 	= ""; 			//定义是哪个医院的名称

	protected $hospitalConf = []; 			//定义项目拥有的医院权限

	protected $noAuthModule = ['Equip']; 	//定义不参加验证的模块

	protected $apiVersion 	= 2; 			//定义api版本

	protected $mustId 		= []; 			//定义需要验证医院Id的方法 		

	protected $event 		= true; 		//定义是否为event版本内容

    protected $eqit_type 		= 0; 		//机器类型

    protected $have_branch 		= 0; 		//是否有新院区

    protected $equipId ; 		//设备ID

    protected $number ; 		//设备ID


	function __construct()
	{
		if (Request::instance()->isOptions()) { 	//如果为options让其通过
			header('HTTP/1.1 202 Accepted');die;
		}
		$this->action 		= Request::instance()->action(); 		//获取当前方法名称
		$this->controller 	= Request::instance()->controller(); 	//获取当前执行文件
		$this->apiVersion 	= Request::instance()->header('api-version' , $this->apiVersion);
		if (!$this->event) { return ; }
		parent::__construct();
		$this->data = self::params();
		error_log("PARAMS => " . implode(',' , $this->data));
        Log::storageRequest('equip'.$this->action.'Response' , $this->data);
        Log::storageRoute('equip'.$this->action.'Response' , Request::instance()->url());
		if (!in_array($this->controller , $this->noAuthModule)) {
			$this->setEquip();
		}
		if (in_array($this->action , $this->scene) && $this->validate) {
			$result = $this->validate($this->data , $this->validate.'.'.$this->action);
			$result !== true && Response::message($result);
		}
	}

	/**
	 * 设备设置
	 *
	 * @access 	public
	 * @return 	void
	 */
	protected function setEquip()
	{
		$number 	= Request::instance()->header('number' , '');
		$this->number = $number;
		$signature 	= Request::instance()->header('signature' , '');
		$timestamp 	= Request::instance()->header('timestamp' , 0);
		$nonce 		= Request::instance()->header('nonce' , '');
		/*
		$signature 	|| Response::message(20101);
		$number 	|| Response::message(20102);
		$nonce 		|| Response::message(20103);
		$timestamp 	|| Response::message(20104);
		if (abs($timestamp - time()) > 120) {
			Response::message(20011);
		}
		$secret = \think\config::get('secret');
		$params = [
			'number' 	=> $number,
			'timestamp' => $timestamp,
			'nonce' 	=> $nonce,
		];
		$sign 	= Sign::signature($secret , $params);
		if ($sign != $signature) {
			Response::message(20021);
		}
		*/
		$data 	= Loader::model('Equip')->detailByNumber($number);
		if (!$data) {
			Response::message(20002);
		}
		$this->projectId  	= $projectId 	= $data['project_id'];
		$project 			= Loader::model('Project')->detail($projectId);
		if ($project['type'] == 1) {
			$this->hospitalId 	= $project['hospital_config'];
		}
		$this->hospitalConf 	= explode(',' , $project['hospital_config']);
		unset($this->mustId[5]);
		if (!$this->hospitalId && in_array($this->action , $this->mustId)) {
			//说明此处传入医院ID或者没有这个医院的权限
			if (!isset($this->data['hospitalId'])) {
				Response::message(29001);
			}
			$this->hospitalId 	= $this->data['hospitalId'];
			if (!in_array($this->hospitalId , $this->hospitalConf)) {
				Response::message(29002); 	//没有权限
			}
		}
        //获取医院相关信息
        if($this->hospitalId){
            $hospitalInfo = \app\equip\model\Hospital::detail($this->hospitalId);
            $this->have_branch 	  	= $hospitalInfo['have_branch'];
            $this->hospitalName       = $hospitalInfo['name'];
        }
		$this->equipId 	  	= $data['id'];
        $this->eqit_type       = $data['type'];
	}
	

	/**
	 * 转化参数
	 * 
	 * @access 	protected
	 * @return 	array
	 */
	protected function params()
	{
		if (Request::instance()->isGet()) { // 是否为 GET 请求
			$data 	= Request::instance()->get();
		} else if (Request::instance()->isPost()) { // 是否为 POST 请求
			$data 	= Request::instance()->post();
		} else if (Request::instance()->isPut()) {// 是否为 PUT 请求
			$data 	= Request::instance()->put();
		} else if (Request::instance()->isDelete()) { // 是否为 DELETE 请求
			$data 	= Request::instance()->delete();
		} else if (Request::instance()->isPatch()) { // 是否为 Patch 请求
			$data 	= Request::instance()->patch();
		} 
		return $data ? $data : [];
	}

	protected function getData($key,$must=true,$default=""){
	    if($must){
	        if(!isset($this->data[$key])||!$this->data[$key]){
	            Response::errorMessage("需少参数".$key);
            }
        }
        return isset($this->data[$key])?$this->data[$key]:$default;
    }


}