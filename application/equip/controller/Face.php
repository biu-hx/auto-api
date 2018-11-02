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

class Face extends Base
{

	protected $event 		= false;
	
	/**
	 * 添加人脸
	 *
	 * @access 	public
	 * @return 	void
	 */
	public function addFace()
	{
		$layer 		= 'event\v'.$this->apiVersion;
		$controller = $this->controller;
		$action 	= $this->action;
		Loader::controller($controller , $layer)->$action();
	}

	/**
	 * 搜索人脸
	 *
	 * @access 	public
	 * @return 	void
	 */
	public function searchFace()
	{
		$layer 		= 'event\v'.$this->apiVersion;
		$controller = $this->controller;
		$action 	= $this->action;
		Loader::controller($controller , $layer)->$action();
	}
	
	/**
	 * 添加用户
	 *
	 * @access 	public
	 * @return 	void
	 */
	public function addPerson()
	{
		$layer 		= 'event\v'.$this->apiVersion;
		$controller = $this->controller;
		$action 	= $this->action;
		Loader::controller($controller , $layer)->$action();
	}

	/**
	 * 搜索用户
	 *
	 * @access 	public
	 * @return 	void
	 */
	public function searchPerson()
	{
		$layer 		= 'event\v'.$this->apiVersion;
		$controller = $this->controller;
		$action 	= $this->action;
		Loader::controller($controller , $layer)->$action();
	}

    /**
     * 上传图片
     *
     * @access 	public
     * @return 	void
     */
    public function upPic()
    {
        $layer 		= 'event\v'.$this->apiVersion;
        $controller = $this->controller;
        $action 	= $this->action;
        Loader::controller($controller , $layer)->$action();
    }

}
