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

class Inpatient extends Base
{

	protected $event 		= false;


	/**
	 * 住院清单列表
	 *
	 * @access 	public
	 * @return 	void
	 */
	public function inpatientList()
	{
		$layer 		= 'event\v'.$this->apiVersion;
		$controller = $this->controller;
		$action 	= $this->action;
		Loader::controller($controller , $layer)->$action();
	}	

	/**
	 * 住院清单类型
	 *
	 * @access 	public
	 * @return 	void
	 */
	public function inpatientType()
	{
		$layer 		= 'event\v'.$this->apiVersion;
		$controller = $this->controller;
		$action 	= $this->action;
		Loader::controller($controller , $layer)->$action();
	}

	/**
	 * 住院清单详情
	 *
	 * @access 	public
	 * @return 	void
	 */
	public function inpatientDetail()
	{
		$layer 		= 'event\v'.$this->apiVersion;
		$controller = $this->controller;
		$action 	= $this->action;
		Loader::controller($controller , $layer)->$action();
	}	
	

}