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

class Video extends Base
{

	protected $event 		= false;

	/**
	 * 科教视频列表
	 *
	 * @access 	public
	 * @return 	void
	 */
	public function videoList()
	{
		$layer 		= 'event\v'.$this->apiVersion;
		$controller = $this->controller;
		$action 	= $this->action;
		Loader::controller($controller , $layer)->$action();
	}

	/**
	 * 科教视频详情
	 *
	 * @access 	public
	 * @return 	void
	 */
	public function videoDetail()
	{
		$layer 		= 'event\v'.$this->apiVersion;
		$controller = $this->controller;
		$action 	= $this->action;
		Loader::controller($controller , $layer)->$action();
	}

	/**
	 * 科教视频下单
	 *
	 * @access 	public
	 * @return 	void
	 */
	public function videoOrder()
	{
		$layer 		= 'event\v'.$this->apiVersion;
		$controller = $this->controller;
		$action 	= $this->action;
		Loader::controller($controller , $layer)->$action();
	}

	/**
	 * 科教视频订单查询
	 *
	 * @access 	public
	 * @return 	void
	 */
	public function videoQuery()
	{
		$layer 		= 'event\v'.$this->apiVersion;
		$controller = $this->controller;
		$action 	= $this->action;
		Loader::controller($controller , $layer)->$action();
	}

    /**
     * 科教视频订单查询
     *
     * @access 	public
     * @return 	void
     */
    public function playVideo()
    {
        $layer 		= 'event\v'.$this->apiVersion;
        $controller = $this->controller;
        $action 	= $this->action;
        Loader::controller($controller , $layer)->$action();
    }

    /**
     * 科教视频订单查询
     *
     * @access 	public
     * @return 	void
     */
    public function getUrl()
    {
        $layer 		= 'event\v'.$this->apiVersion;
        $controller = $this->controller;
        $action 	= $this->action;
        Loader::controller($controller , $layer)->$action();
    }


}
