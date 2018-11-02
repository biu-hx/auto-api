<?php
// +----------------------------------------------------------------------
// | 将消息发送版本控制里，通用
// +----------------------------------------------------------------------
// | Author: duanhy <shongyudxmas@163.com> 
// +----------------------------------------------------------------------
// | version: 1.0
// +----------------------------------------------------------------------

namespace app\equip\controller;

use think\Loader;
use app\equip\controller\Base;

class Error extends Base
{
	protected $event 		= false;

	/**
	 * 查询就诊卡-身份证信息
	 *
	 * @access 	public
	 * @return 	void
	 */
	public function _empty()
	{
		$layer 		= 'event\v'.$this->apiVersion;
		$controller = $this->controller;
		$action 	= $this->action;
		Loader::controller($controller , $layer)->$action();
	}


	


}
