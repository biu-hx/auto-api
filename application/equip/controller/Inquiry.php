<?php
// +----------------------------------------------------------------------
// | 登录
// +----------------------------------------------------------------------
// | Author: duanhy <shongyudxmas@163.com> 
// +----------------------------------------------------------------------
// | version: 1.0
// +----------------------------------------------------------------------

namespace app\equip\controller;

use think\Loader;
use app\equip\controller\Base;

class Inquiry extends Base
{

	protected $event 		= false;

	/**
	 * 科室列表
	 *
	 * @access 	public
	 * @return 	void
	 */
	public function dept()
	{
		$layer 		= 'event\v'.$this->apiVersion;
		$controller = $this->controller;
		$action 	= $this->action;
		Loader::controller($controller , $layer)->$action();
	}

	/**
	 * 医生列表
	 *
	 * @access 	public
	 * @return 	void
	 */
	public function doctor()
	{
		$layer 		= 'event\v'.$this->apiVersion;
		$controller = $this->controller;
		$action 	= $this->action;
		Loader::controller($controller , $layer)->$action();
	}

	/**
	 * 医生详情
	 *
	 * @access 	public
	 * @return 	void
	 */
	public function doctorDetail()
	{
		$layer 		= 'event\v'.$this->apiVersion;
		$controller = $this->controller;
		$action 	= $this->action;
		Loader::controller($controller , $layer)->$action();
	}

	/**
	 * 问诊下单
	 *
	 * @access 	public
	 * @return 	void
	 */
	public function inquiryOrder()
	{
		$layer 		= 'event\v'.$this->apiVersion;
		$controller = $this->controller;
		$action 	= $this->action;
		Loader::controller($controller , $layer)->$action();
	}

	/**
	 * 问诊查询
	 *
	 * @access 	public
	 * @return 	void
	 */
	public function inquiryQuery()
	{
		$layer 		= 'event\v'.$this->apiVersion;
		$controller = $this->controller;
		$action 	= $this->action;
		Loader::controller($controller , $layer)->$action();
	}

	/**
	 * 问诊连接
	 *
	 * @access 	public
	 * @return 	void
	 */
	public function inquiryConnect()
	{
		$layer 		= 'event\v'.$this->apiVersion;
		$controller = $this->controller;
		$action 	= $this->action;
		Loader::controller($controller , $layer)->$action();
	}
	
	/**
	 * 问诊视频截图
	 *
	 * @access 	public
	 * @return 	void
	 */
	public function inquiryScreen()
	{
		$layer 		= 'event\v'.$this->apiVersion;
		$controller = $this->controller;
		$action 	= $this->action;
		Loader::controller($controller , $layer)->$action();
	}

	/**
	 * 问诊视频报告
	 *
	 * @access 	public
	 * @return 	void
	 */
	public function inquiryReport()
	{
		$layer 		= 'event\v'.$this->apiVersion;
		$controller = $this->controller;
		$action 	= $this->action;
		Loader::controller($controller , $layer)->$action();
	}	

	/**
     * 医院列表
     */
	public function hospitalList(){
        $layer 		= 'event\v'.$this->apiVersion;
        $controller = $this->controller;
        $action 	= $this->action;
        Loader::controller($controller , $layer)->$action();
    }

    /**
     * 是否接通
     */
	public function answer(){
        $layer 		= 'event\v'.$this->apiVersion;
        $controller = $this->controller;
        $action 	= $this->action;
        Loader::controller($controller , $layer)->$action();
    }

    /**
     * 挂断状态
     */
	public function outStatus(){
        $layer 		= 'event\v'.$this->apiVersion;
        $controller = $this->controller;
        $action 	= $this->action;
        Loader::controller($controller , $layer)->$action();
    }

    /**
     * 上报异常
     */
	public function putError(){
        $layer 		= 'event\v'.$this->apiVersion;
        $controller = $this->controller;
        $action 	= $this->action;
        Loader::controller($controller , $layer)->$action();
    }

    /**
     * 获取状态
     */
	public function callStatus(){
        $layer 		= 'event\v'.$this->apiVersion;
        $controller = $this->controller;
        $action 	= $this->action;
        Loader::controller($controller , $layer)->$action();
    }

    /**
     * 是否接通
     */
	public function orderStatus(){
        $layer 		= 'event\v'.$this->apiVersion;
        $controller = $this->controller;
        $action 	= $this->action;
        Loader::controller($controller , $layer)->$action();
    }

}
