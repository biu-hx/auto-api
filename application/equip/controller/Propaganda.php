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

class Propaganda extends Base
{
	protected $event 		= false;

// 空操作时
    public function _empty()
    {
        $layer 		= 'event\v'.$this->apiVersion;
        $controller = $this->controller;
        $action 	= $this->action;
        Loader::controller($controller , $layer)->$action();
    }

}
