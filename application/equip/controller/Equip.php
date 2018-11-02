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

class Equip extends Base
{

	protected $event 		= false;
	
	/**
	 * 获取设备编号
	 *
	 * @access 	public
	 * @return 	void
	 */
	public function number()
	{
		$layer 		= 'event\v'.$this->apiVersion;
		$controller = $this->controller;
		$action 	= $this->action;
		Loader::controller($controller , $layer)->$action();
	}

	/**
	 * 设置当前设备硬件情况
	 *
	 * @access 	public
	 * @return 	void
	 */
	public function hardware()
	{
		$layer 		= 'event\v'.$this->apiVersion;
		$controller = $this->controller;
		$action 	= $this->action;
		Loader::controller($controller , $layer)->$action();
	}
	
	/**
	 * 设备当前设备网络状态
	 *
	 * @access 	public
	 * @return 	void
	 */
	public function network()
	{
		$layer 		= 'event\v'.$this->apiVersion;
		$controller = $this->controller;
		$action 	= $this->action;
		Loader::controller($controller , $layer)->$action();
	}

	/**
	 * 设置当前设备版本号
	 *
	 * @access 	public
	 * @return 	void
	 */
	public function version()
	{
		$layer 		= 'event\v'.$this->apiVersion;
		$controller = $this->controller;
		$action 	= $this->action;
		Loader::controller($controller , $layer)->$action();
	}

	/**
	 * 设置打印纸张
	 *
	 * @access 	public
	 * @return 	void
	 */
	public function paper()
	{
		$layer 		= 'event\v'.$this->apiVersion;
		$controller = $this->controller;
		$action 	= $this->action;
		Loader::controller($controller , $layer)->$action();
	}

	/**
	 * 设备状态列表
	 *
	 * @access 	public
	 * @return 	void
	 */
	public function equiplist()
	{
		$layer 		= 'event\v'.$this->apiVersion;
		$controller = $this->controller;
		$action 	= $this->action;
		Loader::controller($controller , $layer)->$action();
	}

    /**
     * 重启页面
     *
     * @access 	public
     * @return 	void
     */
    public function restart()
    {
        $layer 		= 'event\v'.$this->apiVersion;
        $controller = $this->controller;
        $action 	= $this->action;
        Loader::controller($controller , $layer)->$action();
    }

    /**
     * 重启页面
     *
     * @access 	public
     * @return 	void
     */
    public function reset()
    {
        $layer 		= 'event\v'.$this->apiVersion;
        $controller = $this->controller;
        $action 	= $this->action;
        Loader::controller($controller , $layer)->$action();
    }

    // 空操作时
    public function _empty()
    {
        $layer 		= 'event\v'.$this->apiVersion;
        $controller = $this->controller;
        $action 	= $this->action;
        Loader::controller($controller , $layer)->$action();
    }

}
