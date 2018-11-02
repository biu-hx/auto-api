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

class Order extends Base
{

	protected $event 		= false;
	



	/**
	 * 订单号 	- 查询订单详情
	 *
	 * @access 	public
	 * @return 	void
	 */
	public function searchByOrder()
	{
		$layer 		= 'event\v'.$this->apiVersion;
		$controller = $this->controller;
		$action 	= $this->action;
		Loader::controller($controller , $layer)->$action();
	}

	/**
	 * 就诊卡	- 查询订单列表
	 *
	 * @access 	public
	 * @return 	void
	 */
	public function searchByCard()
	{
		$layer 		= 'event\v'.$this->apiVersion;
		$controller = $this->controller;
		$action 	= $this->action;
		Loader::controller($controller , $layer)->$action();
	}

	/**
	 * 就诊卡	- 查询订单列表
	 *
	 * @access 	public
	 * @return 	void
	 */
	public function searchByLink()
	{
		$layer 		= 'event\v'.$this->apiVersion;
		$controller = $this->controller;
		$action 	= $this->action;
		Loader::controller($controller , $layer)->$action();
	}

	/**
	 * 就诊卡	- 查询订单列表
	 *
	 * @access 	public
	 * @return 	void
	 */
	public function searchByTreat()
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
	 * 标记打印
	 *
	 * @access 	public
	 * @return 	void
	 */
	public function markPrint()
	{
		$layer 		= 'event\v'.$this->apiVersion;
		$controller = $this->controller;
		$action 	= $this->action;
		Loader::controller($controller , $layer)->$action();
	}

	/**
	 * 微信二维码
	 *
	 * @access 	public
	 * @return 	void
	 */
	public function wechatQR()
	{
		$layer 		= 'event\v'.$this->apiVersion;
		$controller = $this->controller;
		$action 	= $this->action;
		Loader::controller($controller , $layer)->$action();
	}

	/**
	 * 支付宝二维码
	 *
	 * @access 	public
	 * @return 	void
	 */
	public function alipayQR()
	{
		$layer 		= 'event\v'.$this->apiVersion;
		$controller = $this->controller;
		$action 	= $this->action;
		Loader::controller($controller , $layer)->$action();
	}

    /**
     * 订餐订单添加
     *
     * @access 	public
     * @return 	void
     */
    public function addDinner()
    {
        $layer 		= 'event\v'.$this->apiVersion;
        $controller = $this->controller;
        $action 	= $this->action;
        Loader::controller($controller , $layer)->$action();
    }

    /**
     * 订餐订单更新状态
     *
     * @access 	public
     * @return 	void
     */
    public function setStatus()
    {
        $layer 		= 'event\v'.$this->apiVersion;
        $controller = $this->controller;
        $action 	= $this->action;
        Loader::controller($controller , $layer)->$action();
    }

	


}
