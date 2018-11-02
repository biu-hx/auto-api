<?php
// +----------------------------------------------------------------------
// | 登录
// +----------------------------------------------------------------------
// | Author: duanhy <shongyudxmas@163.com> 
// +----------------------------------------------------------------------
// | version: 1.0
// +----------------------------------------------------------------------

namespace app\equip\event\v1;

use think\Loader;
use app\equip\controller\Base;
use app\component\response\Response;
use app\component\server\Server;


class Equip extends Base
{

	protected $validate = '\app\equip\validate\v1\Equip';

	protected $scene = [
		'number',
		'hardware',
		'network',
		'version',
	];

	/**
	 * 获取设备编号
	 *
	 * @access 	public
	 * @return 	void
	 */
	public function number()
	{
		$mac 	= $this->data['mac'];
		$data 	= Loader::model('Equip')->detailByMac($mac);
		if (!$data) Response::message(20001);
		$number = ['number' => $data['code']];
		/*$data 	= Loader::model('Equip')->config();
		isset($data['SERVICE_PHONE']) && $number['SERVICE_PHONE'] = */
		$number['SERVICE_PHONE'] = '18208173320';
		Response::message(10000 , $number);
	}

	public function hardware()
	{
		$mac 	= $this->data['mac'];
		$type 	= $this->data['hardware'];
		$value  = $this->data['value'];
		$data 	= Loader::model('Equip')->detailByMac($mac);
		if (!$data) Response::message(20001);
		$equipId 	= $data['id'];
		$result = Loader::model('Equip')->setHardware($equipId , $type , $value);
		Response::message(10000 , true);
	}

	public function network()
	{
		$mac 	= $this->data['mac'];
		$data 	= Loader::model('Equip')->detailByMac($mac);
		if (!$data) Response::message(20001);
		$equipId 	= $data['id'];
		$result = Loader::model('Equip')->setNetwork($equipId);
		Response::message(10000 , true);
	}
	
	public function version()
	{
		$mac 	= $this->data['mac'];
		$version= $this->data['version'];
		$data 	= Loader::model('Equip')->detailByMac($mac);
		if (!$data) Response::message(20001);
		$equipId 	= $data['id'];
		$result = Loader::model('Equip')->setVersion($equipId , $version);
		Response::message(10000 , true);
	}

}
