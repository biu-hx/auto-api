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

class Card extends Base
{
	protected $event 		= false;

	/**
	 * 查询就诊卡-身份证信息
	 *
	 * @access 	public
	 * @return 	void
	 */
	public function card()
	{
		$layer 		= 'event\v'.$this->apiVersion;
		$controller = $this->controller;
		$action 	= $this->action;
		Loader::controller($controller , $layer)->$action();
	}

	/**
	 * 查询就诊卡-就诊卡卡号
	 *
	 * @access 	public
	 * @return 	void
	 */
	public function cardByPatient()
	{
		$layer 		= 'event\v'.$this->apiVersion;
		$controller = $this->controller;
		$action 	= $this->action;
		Loader::controller($controller , $layer)->$action();
	}
	

	/**
	 * 查询电子居民健康卡
	 *
	 * @access 	public
	 * @return 	void
	 */
	public function cardByEleHealthCard()
	{
		$layer 		= 'event\v'.$this->apiVersion;
		$controller = $this->controller;
		$action 	= $this->action;
		Loader::controller($controller , $layer)->$action();
	}


    /**
     * 查询登记号
     *
     * @access 	public
     * @return 	void
     */
    public function getCardInfo()
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
