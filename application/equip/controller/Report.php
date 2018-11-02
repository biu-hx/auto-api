<?php
// +----------------------------------------------------------------------
// | 缴费
// +----------------------------------------------------------------------
// | Author: duanhy <shongyudxmas@163.com> 
// +----------------------------------------------------------------------
// | version: 1.0
// +----------------------------------------------------------------------

namespace app\equip\controller;

use think\Loader;
use app\equip\controller\Base;

class Report extends Base
{

	protected $event 		= false;
	
	/**
	 * 报告列表
	 *
	 * @access 	public
	 * @return 	void
	 */
	public function reportList()
	{
		$layer 		= 'event\v'.$this->apiVersion;
		$controller = $this->controller;
		$action 	= $this->action;
		Loader::controller($controller , $layer)->$action();
	}

	/**
	 * 报告详情
	 *
	 * @access 	public
	 * @return 	void
	 */
	public function reportDetail()
	{
		$layer 		= 'event\v'.$this->apiVersion;
		$controller = $this->controller;
		$action 	= $this->action;
		Loader::controller($controller , $layer)->$action();
	}

	/**
	 * 标记报告打印
	 *
	 * @access 	public
	 * @return 	void
	 */
	public function reportPrint()
	{
		$layer 		= 'event\v'.$this->apiVersion;
		$controller = $this->controller;
		$action 	= $this->action;
		Loader::controller($controller , $layer)->$action();
	}

	/**
	 * 条码查询
	 *
	 * @access 	public
	 * @return 	void
	 */
	public function barCode()
	{
		$layer 		= 'event\v'.$this->apiVersion;
		$controller = $this->controller;
		$action 	= $this->action;
		Loader::controller($controller , $layer)->$action();
	}

	/**
	 * 标记条码已打印
	 *
	 * @access 	public
	 * @return 	void
	 */
	public function barCodePrint()
	{
		$layer 		= 'event\v'.$this->apiVersion;
		$controller = $this->controller;
		$action 	= $this->action;
		Loader::controller($controller , $layer)->$action();
	}




}
