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

class Inquiry extends Base
{

	/**
	 * 订单列表
	 *
	 * @access 	public
	 * @return 	void
	 */
	public function inquiry()
	{
		$layer 		= 'event\v'.$this->apiVersion;
		$controller = $this->controller;
		$action 	= $this->action;
		Loader::controller($controller , $layer)->$action();
	}

	/**
	 * 订单详情
	 *
	 * @access 	public
	 * @return 	void
	 */
	public function detail()
	{
		$layer 		= 'event\v'.$this->apiVersion;
		$controller = $this->controller;
		$action 	= $this->action;
		Loader::controller($controller , $layer)->$action();
	}

	/**
	 * 标记
	 *
	 * @access 	public
	 * @return 	void
	 */
	public function mark()
	{
		$layer 		= 'event\v'.$this->apiVersion;
		$controller = $this->controller;
		$action 	= $this->action;
		Loader::controller($controller , $layer)->$action();
	}



	/**
	 * 标记咨询单
	 *
	 * @access 	public
	 * @return 	void
	 */
	public function markCall()
	{
		$layer 		= 'event\v'.$this->apiVersion;
		$controller = $this->controller;
		$action 	= $this->action;
		Loader::controller($controller , $layer)->$action();
	}

	/**
	 * 家真号换取订单的相关信息
	 *
	 * @access 	public
	 * @return 	void
	 */
	public function relate()
	{
		$layer 		= 'event\v'.$this->apiVersion;
		$controller = $this->controller;
		$action 	= $this->action;
		Loader::controller($controller , $layer)->$action();
	}

	/**
	 * 获取订单的报告
	 *
	 * @access 	public
	 * @return 	void
	 */
	public function report()
	{
		$layer 		= 'event\v'.$this->apiVersion;
		$controller = $this->controller;
		$action 	= $this->action;
		Loader::controller($controller , $layer)->$action();
	}

    public function _empty()
    {
        $layer 		= 'event\v'.$this->apiVersion;
        $controller = $this->controller;
        $action 	= $this->action;
        Loader::controller($controller , $layer)->$action();
    }

	
}