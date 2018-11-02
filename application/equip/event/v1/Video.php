<?php

namespace app\equip\event\v1;

use think\Loader;
use app\equip\controller\Base;
use app\component\response\Response;
use app\component\server\Server;


class Video extends Base
{
	protected $validate = '\app\equip\validate\v1\Video';

	protected $scene = [
		'videoDetail',
		'videoOrder',
		'videoQuery',
	];

	public function videoList()
	{
		$data 	= Loader::model('Video')->videoList();
		$video 	= [];
		foreach ($data as $v) {
			$video[] 	= [
				'videoId' 	=> $v['id'],
				'title' 	=> $v['title'],
				'price' 	=> $v['price'],
				'logo' 		=> $v['logo'],
				'url' 		=> $v['url'],
			];
		}
		Response::message(10000 , $video);
	}

	public function videoDetail()
	{
		$videoId 	= $this->data['videoId'];
		$data 		= Loader::model('Video')->detail($videoId);
		if (!$data || $data['status'] != 1) {
			Response::message(30016); 
		}
		$video 		= [
			'videoId' 	=> $data['id'],
			'title' 	=> $data['title'],
			'price' 	=> $data['price'],
			'logo' 		=> $data['logo'],
			'time' 		=> $data['time'],
			'introduce' => $data['introduce'],
			'author' 	=> $data['author'],
		];
		Response::message(10000 , $video);
	}

	public function videoOrder()
	{
		$videoId 	= $this->data['videoId'];
		$equipId 	= $this->equipId;
		$data 		= Loader::model('Video')->detail($videoId);
		if (!$data || $data['status'] != 1) {
			Response::message(30016); 
		}
		$fee 		= $data['price'];
		$order 	 	= Loader::model('Order')->orderVideo($equipId , $fee , $videoId);
		if (!$order) {
			Response::message(30017);//
		}
		$order['title'] 	= $data['title'];
		$order['time'] 		= $data['time'];
		$order['logo'] 		= $data['logo'];
		$order['introduce'] = $data['introduce'];
		Response::message(10000 , $order); 
	}

	public function videoQuery()
	{
		$orderNumber = $this->data['orderNum'];
		$data 	= Loader::model('Order')->detailByNumber($orderNumber);
		if (!$data || $data['type'] != 3) {
			Response::message(30011);
		}
		$orderId 	= $data['id'];
		$orderPay 	= Loader::model('Order')->payDetail($orderId);	
		$orderVideo = Loader::model('Video')->detailByOrderId($orderId);
		$videoId 	= $orderVideo['video_id'];
		$video 		= Loader::model('Video')->detail($videoId);
		$response 	= [
			'orderNum' 	=> $orderNumber,
			'payStatus' => $data['status'],
			'title' 	=> $video['title'],
			'time' 		=> $video['time'],
			'logo' 		=> $video['logo'],
			'introduce' => $video['introduce'],
		];
		if ($data['status'] >= 1) {
			$response['status'] = 1;
			$response['payPrice'] 	= $orderPay['price'];
			$response['payTime'] 	= $orderPay['pay_time'];
			$response['transactionId'] 	= $orderPay['transaction_id'];
			if ($data['status'] > 1) {
				$response['status'] = 0;
			}
		}
		Response::message(10000 , $response);
	}
}