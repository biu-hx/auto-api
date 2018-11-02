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

class Personal extends Base
{

	/**
	 * 获取医生的显示资料
	 *
	 * @access 	public
	 * @return 	void
	 */
	public function basic()
	{
		$layer 		= 'event\v'.$this->apiVersion;
		$controller = $this->controller;
		$action 	= $this->action;
		Loader::controller($controller , $layer)->$action();
	}

	/**
	 * 获取医生的基本资料
	 *
	 * @access 	public
	 * @return 	void
	 */
	public function info()
	{
		$layer 		= 'event\v'.$this->apiVersion;
		$controller = $this->controller;
		$action 	= $this->action;
		Loader::controller($controller , $layer)->$action();
	}

	/**
	 * 获取SDK账号信息
	 *
	 * @access 	public
	 * @return 	void
	 */
	public function account()
	{
		$layer 		= 'event\v'.$this->apiVersion;
		$controller = $this->controller;
		$action 	= $this->action;
		Loader::controller($controller , $layer)->$action();
	}

	/**
	 * 获取验证码
	 *
	 * @access 	public
	 * @return 	void
	 */
	public function modify()
	{
		$layer 		= 'event\v'.$this->apiVersion;
		$controller = $this->controller;
		$action 	= $this->action;
		Loader::controller($controller , $layer)->$action();
	}


	/**
	 * 设置上下线
	 *
	 * @access 	public
	 * @return 	void
	 */
	public function autoOnline()
	{
		$layer 		= 'event\v'.$this->apiVersion;
		$controller = $this->controller;
		$action 	= $this->action;
		Loader::controller($controller , $layer)->$action();
	}

	/**
	 * 收入
	 *
	 * @access 	public
	 * @return 	void
	 */
	public function income()
	{
		$layer 		= 'event\v'.$this->apiVersion;
		$controller = $this->controller;
		$action 	= $this->action;
		Loader::controller($controller , $layer)->$action();
	}

	/**
	 * 注销登录
	 *
	 * @access 	public
	 * @return 	void
	 */
	public function unlogin()
	{
		$layer 		= 'event\v'.$this->apiVersion;
		$controller = $this->controller;
		$action 	= $this->action;
		Loader::controller($controller , $layer)->$action();
	}
}