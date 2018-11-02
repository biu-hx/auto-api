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

class ListSearch extends Base
{

	protected $event 		= false;

	/**
	 * 药品查询
	 *
	 * @access 	public
	 * @return 	void
	 */
	public function drug()
	{
		$layer 		= 'event\v'.$this->apiVersion;
		$controller = $this->controller;
		$action 	= $this->action;
		Loader::controller($controller , $layer)->$action();
	}	

	/**
	 * 诊疗项目查询
	 *
	 * @access 	public
	 * @return 	void
	 */
	public function diagnosis()
	{
		$layer 		= 'event\v'.$this->apiVersion;
		$controller = $this->controller;
		$action 	= $this->action;
		Loader::controller($controller , $layer)->$action();
	}	
	

}