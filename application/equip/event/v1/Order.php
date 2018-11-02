<?php

namespace app\equip\event\v1;

use think\Loader;
use app\equip\controller\Base;
use app\component\response\Response;
use app\component\Common;

class Order extends Base
{

	protected $validate = '\app\equip\validate\v1\Order';

	protected $scene = [
		'wechatQR',
	];

	public function wechatQR()
	{
		$orderNumber = $this->data['orderNum'];
		$data 	= Loader::model('Order')->detailByNumber($orderNumber);
		if (!$data) Response::message(30012);
		$orderId= $data['id'];
		$qr 	= Loader::model('Order')->wechatQR($orderId);
		if ($qr) { Response::message(10000 , ['orderNum' => $orderNumber , 'qr' => $qr , 'price' => $data['price']]); }
		
		$url 	= "http://".($_SERVER['HTTP_HOST'] == 'auto-api.mobimedical.cn' ? 'auto-admin.mobimedical.cn' : '139.199.206.91:8092')."/index.php?s=Home/Pay/index&orderNum=".$orderNumber;
		$qr 	= Common::qr($url , 'wechat.png');
		$qr 	= Common::TencentCloud($qr);
		$result = Loader::model('order')->insertWechatQR($orderId , $qr);
		if (!$result) Response::message(30011);
		Response::message(10000 , ['orderNum' => $orderNumber , 'qr' => $qr , 'price' => $data['price']]);

	}

	public function alipayQR()
	{
		$orderNumber = $this->data['orderNum'];
		$data 	= Loader::model('Order')->detailByNumber($orderNumber);
		if (!$data) Response::message(30012);
		$orderId= $data['id'];
		$qr 	= Loader::model('Order')->alipayQR($orderId);
		if ($qr) { Response::message(10000 , ['orderNum' => $orderNumber , 'qr' => $qr , 'price' => $data['price']]); }
		$url 	= "sadasd";
		$qr 	= Common::qr($url);
		$qr 	= Common::TencentCloud($qr);
		$result = Loader::model('order')->insertAlipayQR($orderId , $qr);
		if (!$result) Response::message(30012);
		Response::message(10000 , ['orderNum' => $orderNumber , 'qr' => $qr , 'price' => $data['price']]);
	}


}