<?php

namespace app\equip\event\v2;

use think\Loader;
use think\Db;
use app\equip\controller\Base;
use app\component\response\Response;


class Video extends Base
{
	protected $validate = '\app\equip\validate\v2\Video';

	protected $scene = [
		'videoDetail',
		'videoOrder',
		'videoQuery',
		'getUrl',
	];

	/**
	 * 视频列表
	 *
	 * @access 	public
	 * @return 	void
	 */
	public function videoList()
	{
		$projectId 	= $this->projectId;
		//error_log("projectId => " . $projectId);
		$data 	= Loader::model('Video')->videoList($projectId);
		$video 	= [];
		foreach ($data as $v) {
			$video[] 	= [
				'videoId' 	=> $v['id'],
				'title' 	=> $v['title'],
				'price' 	=> $v['price'],
				'logo' 		=> $v['logo'],
				'url' 		=> $v['url'],
				'time' 		=> $v['time'],
			];
		}
		Response::success($video);
	}

	/**
	 * 视频详情
	 *
	 * @access 	public
	 * @return 	void
	 */
    public function videoDetail()
    {
        $projectId 	= $this->projectId;
        $videoId 	= $this->data['videoId'];
        $data 		= Loader::model('Video')->detail($projectId , $videoId);
        $serviceConf = Db::name("service_config")->where("project_id='{$projectId}'")->find();
        if($serviceConf){
            $jsonStr = json_decode($serviceConf['video'] , true)['YouXiaoShiChang'];
            $day  = $jsonStr['day'] ;
            $hour  = $jsonStr['hour'] ;
            $branch  = $jsonStr['branch'] ;
            $YouXiaoShiChang = sprintf('%.2f' , $branch/60) + $hour + $day*24;
        }else{
            $YouXiaoShiChang = 0.5;
        }
        if (!$data || $data['status'] != 1) {
            Response::message(30016);
        }
        $videoStatus = 0;
        if($data['price'] > 0){
            $nowDate = time();
            $needDate = $nowDate - ( $YouXiaoShiChang * 60 *60 );
            //查询有没有购买
            $videoPay = Db::name("order")->alias("a")
                ->join('order_video b' ,'b.`order_id`=a.`id`')
                ->join('order_pay c' , 'c.order_id=a.id')
                ->where(" a.project_id='{$this->projectId}' and b.video_id='{$videoId}' and a.equipment_id='{$this->equipId}' and c.pay_time>'{$needDate}'")
                ->find();
            if($videoPay){
                $YouXiaoShiChang = sprintf('%.2f' , ($videoPay['pay_time'] +  ($YouXiaoShiChang * 3600) - $nowDate)/3600);
                $videoStatus = 1;
                $orderNumber = $videoPay['order_number'];
            }
        }
        $video 		= [
            'videoId' 	=> $data['id'],
            'title' 	=> $data['title'],
            'price' 	=> $data['price'],
            'logo' 		=> $data['logo'],
            'time' 		=> $data['time'],
            'introduce' => $data['introduce'],
            'author' 	=> $data['author'],
            'url' 	    => $data['url'],
            'orderNumber' 	=> isset($orderNumber) ? $orderNumber : 0,
            'status' 	=> $videoStatus,
            'YouXiaoShiChang' 	=> $YouXiaoShiChang,
        ];
        //error_log("SUCC => " . json_encode($video));
        Response::success($video);
    }

    public function videoOrder()
	{
		$projectId 	= $this->projectId;
		$equipId 	= $this->equipId;
		$videoId 	= $this->data['videoId'];
        $payType 	= $this->data['payType'];
		$data 		= Loader::model('Video')->detail($projectId , $videoId);
		if (!$data || $data['status'] != 1) {
			Response::message(30016); 
		}
		$fee 		= $data['price'];
		$projectId  = $this->projectId;
		$order 	 	= Loader::model('Order')->orderVideo($equipId , $projectId , $fee , $videoId , $payType);
		if (!$order) {
			Response::message(30017);//
		}
		$order['title'] 	= $data['title'];
		$order['time'] 		= $data['time'];
		$order['logo'] 		= $data['logo'];
		$order['introduce'] = $data['introduce'];
		Response::success($order); 
	}

	public function videoQuery()
	{
		$orderNumber = $this->data['orderNum'];
		$data 	= Loader::model('Order')->detailByNumber($orderNumber);
		if (!$data || $data['type'] != 99 || $data['project_id'] != $this->projectId) {
			Response::message(30011);
		}
		$orderId 	= $data['id'];
		$orderPay 	= Loader::model('Order')->payDetail($orderId);	
		$orderVideo = Loader::model('Video')->detailByOrderId($orderId);
		$videoId 	= $orderVideo['video_id'];
		$video 		= Loader::model('Video')->detail($this->projectId , $videoId);
		$response 	= [
			'orderNum' 	=> $orderNumber,
			'payStatus' => $data['status'],
			'title' 	=> $video['title'],
			'time' 		=> $video['time'],
			'logo' 		=> $video['logo'],
			'introduce' => $video['introduce'],
            'videoId'   => $videoId,
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
		Response::success($response);
	}
    public function playVideo222() {
        $orderId = $this->data['orderId'];
        $videoPath = Db::name("order_video")->alias("a")->join("resource_video as b on b.id=a.video_id" , true)->where("order_id={$orderId}")->find();
        !$videoPath && Response::error(30011);
        $file = VIDEO_PATH . $videoPath['url'];
        header("Content-type: video/mp4");
        header("Accept-Ranges: bytes");
        $size = (filesize($file));
        if(isset($_SERVER['HTTP_RANGE'])){
            header("HTTP/1.1 206 Partial Content");
            list($name, $range) = explode("=", $_SERVER['HTTP_RANGE']);
            list($begin, $end) =explode("-", $range);
            if($end == 0) $end = $size - 100;
        }
        else {
            $begin = 0; $end = $size - 100;
        }
        header("Content-Length: " . ($end - $begin + 1));
        header("Content-Disposition: filename=".basename($file));
        header("Content-Range: bytes ".$begin."-".$end."/".$size);
        $fp = fopen($file, 'rb');
        fseek($fp, $begin);
        while(!feof($fp)) {
            $p = min(1024, $end - $begin + 1);
            $begin += $p;
            echo fread($fp, $p);
        }
        fclose($fp);exit;
    }

    public function playVideo(){
        $file = VIDEO_PATH . "test.mp4";
        header("Content-type: video/mp4");
        header("Accept-Ranges: bytes");
        $size = (filesize($file));
        if(isset($_SERVER['HTTP_RANGE'])){
            header("HTTP/1.1 206 Partial Content");
            list($name, $range) = explode("=", $_SERVER['HTTP_RANGE']);
            list($begin, $end) =explode("-", $range);
            if($end == 0) $end = $size - 100;
        }
        else {
            $begin = 0; $end = $size - 100;
        }
        header("Content-Length: " . ($end - $begin + 1));
        header("Content-Disposition: filename=".basename($file));
        header("Content-Range: bytes ".$begin."-".$end."/".$size);
        $fp = fopen($file, 'rb');
        fseek($fp, $begin);
        while(!feof($fp)) {
            $p = min(1024, $end - $begin + 1);
            $begin += $p;
            echo fread($fp, $p);
        }
        fclose($fp);exit;
    }


    public function getUrl(){
        $videoId = $this->data['videoId'];
        $join[] = [ 'resource_video b' , 'b.id=a.video_id'];
        $videoUrl = Db::name("project_video")->field("a.*,b.*,a.price as price")->alias("a")->join($join)->where("a.video_id={$videoId} and a.project_id={$this->projectId}")->find();
        if($videoUrl['price']>0){
            if (!isset($this->data['orderNum'])){
                Response::error(91026);
            }
            $orderNum = $this->data['orderNum'];
            $joinStr[] = ['order_video b' , 'b.order_id=a.id'];
            $videoPath = Db::name("order")->alias("a")->field("a.*,b.*,a.status as status")->join($joinStr)->where("a.order_number='{$orderNum}'")->find();
            !$videoPath && Response::error(30011);
            if(!in_array($videoPath['status'] , [1 ,3])){
                Response::error(91027);
            }
            $response['url'] = $videoUrl['url'];
            $response['title'] = $videoUrl['title'];
            $response['logo'] = $videoUrl['logo'];
            Response::success($response);
        }else{
            $response['title'] = $videoUrl['title'];
            $response['url'] = $videoUrl['url'];
            $response['logo'] = $videoUrl['logo'];
            Response::success($response);
        }

    }

}