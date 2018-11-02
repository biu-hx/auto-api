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

class Payment extends Base
{

	protected $event 		= false;

	/**
	 * 门诊缴费列表查询
	 *
	 * @access 	public
	 * @return 	void
	 */
	public function outpatientList()
	{
		$layer 		= 'event\v'.$this->apiVersion;
		$controller = $this->controller;
		$action 	= $this->action;
		Loader::controller($controller , $layer)->$action();
	}

	/**
	 * 门诊缴费详情
	 *
	 * @access 	public
	 * @return 	void
	 */
	public function outpatient()
	{
		$layer 		= 'event\v'.$this->apiVersion;
		$controller = $this->controller;
		$action 	= $this->action;
		Loader::controller($controller , $layer)->$action();
	}

	/**
	 * 门诊缴费订单
	 *
	 * @access 	public
	 * @return 	void
	 */
	public function outpatientOrder()
	{
		$layer 		= 'event\v'.$this->apiVersion;
		$controller = $this->controller;
		$action 	= $this->action;
		Loader::controller($controller , $layer)->$action();
	}

	/**
	 * 门诊缴费订单查询 
	 *
	 * @access 	public
	 * @return 	void
	 */
	public function outpatientQuery()
	{
		$layer 		= 'event\v'.$this->apiVersion;
		$controller = $this->controller;
		$action 	= $this->action;
		Loader::controller($controller , $layer)->$action();
	}

	/**
	 * 住院缴费查询
	 *
	 * @access 	public
	 * @return 	void
	 */
	public function inpatient()
	{
		$layer 		= 'event\v'.$this->apiVersion;
		$controller = $this->controller;
		$action 	= $this->action;
		Loader::controller($controller , $layer)->$action();
	}

	/**
	 * 住院缴费订单处理
	 *
	 * @access 	public
	 * @return 	void
	 */
	public function inpatientOrder()
	{
		$layer 		= 'event\v'.$this->apiVersion;
		$controller = $this->controller;
		$action 	= $this->action;
		Loader::controller($controller , $layer)->$action();
	}

	/**
	 * 住院缴费订单查询
	 *
	 * @access 	public
	 * @return 	void
	 */
	public function inpatientQuery()
	{
		$layer 		= 'event\v'.$this->apiVersion;
		$controller = $this->controller;
		$action 	= $this->action;
		Loader::controller($controller , $layer)->$action();
	}


    /**
     * 查询设备业务支付方式
     *
     * @access 	public
     * @return 	void
     */
    public function getPay()
    {
        $layer 		= 'event\v'.$this->apiVersion;
        $controller = $this->controller;
        $action 	= $this->action;
        Loader::controller($controller , $layer)->$action();
    }




}
