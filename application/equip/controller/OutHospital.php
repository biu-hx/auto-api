<?php
/**
 * 出院
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/22 0022
 * Time: 14:42
 */

namespace app\equip\controller;


use think\Loader;

class OutHospital extends Base
{
    protected $event = false;
    /**
     * 获取设备编号
     *
     * @access 	public
     * @return 	void
     */
    public function search()
    {
        $layer 		= 'event\v'.$this->apiVersion;
        $controller = $this->controller;
        $action 	= $this->action;
        Loader::controller($controller , $layer)->$action();
    }

    public function detail()
    {
        $layer 		= 'event\v'.$this->apiVersion;
        $controller = $this->controller;
        $action 	= $this->action;
        Loader::controller($controller , $layer)->$action();
    }

    public function submit()
    {
        $layer 		= 'event\v'.$this->apiVersion;
        $controller = $this->controller;
        $action 	= $this->action;
        Loader::controller($controller , $layer)->$action();
    }
}