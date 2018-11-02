<?php
// +----------------------------------------------------------------------
// | 登录
// +----------------------------------------------------------------------
// | Author: duanhy <shongyudxmas@163.com> 
// +----------------------------------------------------------------------
// | version: 1.0
// +----------------------------------------------------------------------

namespace app\api\controller;

use think\Loader;
use app\api\controller\Base;

class User extends Base
{

	/**
	 * 医生App用户登录
	 *
	 * @access 	public
	 * @return 	void
	 */
	public function login()
	{
		$layer 		= 'event\v'.$this->apiVersion;
		$controller = $this->controller;
		$action 	= $this->action;
		Loader::controller($controller , $layer)->$action();
	}

	/**
	 * 获取验证码
	 *
	 * @access 	public
	 * @return 	void
	 */
	public function code()
	{
		$layer 		= 'event\v'.$this->apiVersion;
		$controller = $this->controller;
		$action 	= $this->action;
		Loader::controller($controller , $layer)->$action();
	}

	/**
	 * 验证验证码
	 *
	 * @access 	public
	 * @return 	void
	 */
	public function verify()
	{
		$layer 		= 'event\v'.$this->apiVersion;
		$controller = $this->controller;
		$action 	= $this->action;
		Loader::controller($controller , $layer)->$action();
	}

	/**
	 * 修改密码
	 *
	 * @access 	public
	 * @return 	void
	 */
	public function modify()
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