<?php
namespace app\equip\model;

use think\Db;
use app\component\Common;
use app\component\server\ability\TencentVideoLive;

class Order 
{
	
	/**
	 * 挂号下单
	 *
	 * @access 	public
	 * @param  	int 	$equipId	编号id	
	 * @param 	string 	$hospitalId 医院id
	 * @param 	string 	$orderNum 	能力层返回的orderNum
	 * @param 	string 	$price 		价格
	 * @param 	array 	$schedule 	排班信息
	 * @param 	array 	$cardInfo 	排班信息
	 * @param 	array 	$payType 	排班信息
	 * @param 	integer 	$districtId  院区ID
	 * @return 	array
	 */
	public function orderRegistration($equipId , $orderNum , $hospitalId , $doctorName , $projectId , $price , $schedule , $cardInfo , $payType , $districtId = 0)
	{
		$time 	= time();
		$order 	= [
			'equipment_id' 	=> $equipId,
			'hospital_id' 	=> $hospitalId,
			'project_id' 	=> $projectId,
			'type' 			=> 2,	
			'status' 		=> 0,
			'pre_status' 	=> 0,
			'order_number' 	=> $orderNum,
			'price' 		=> $price,
			'create_time' 	=> $time,
			'print' 		=> 0,
			'pay_type' 		=> $payType,
		];
		Db::startTrans();
		$orderId 	= Db::name('order')->insertGetId($order);
		if (!$orderId) {
			Db::rollback();
			return false;
		}
		$patient 	  = [
			'order_id' 		=> $orderId,
			'card_id' 		=> $cardInfo['cardId'],
			'card_name' 	=> $cardInfo['cardName'],
		];
		$result 	= Db::name('order_patient')->insert($patient);
		if (!$result) {
			Db::rollback();
			return false;
		}	 
		$registration = [
			'order_id' 	 	=> $orderId,
			'district_id' 	=> $districtId,
			'card_id' 		=> $cardInfo['cardId'],
 			'card_name' 	=> $cardInfo['cardName'],
			'hid' 			=> $hospitalId,
			'schedule_id' 	=> $schedule['scheduleId'],
			'appt_id' 		=> $schedule['apptId'],
 			'business_info' => json_encode($schedule , JSON_UNESCAPED_UNICODE),
			'status' 	 	=> 0, 
			'doctor_name' 	=> $doctorName,
			'success_info'  => '[]',
		];
		$result 	= Db::name('order_registration')->insert($registration);
		$log 		= [
			'type' 		=> 'create',
			'person' 	=> 'user',
		];
		self::log($orderId , 0 , $order , $log , $time);
		if (!$result) {
			Db::rollback();
			return false;
		}
		Db::commit();
		return [
			'orderNum' 		=> $orderNum,
			'price' 		=> $price,
			'create_time' 	=> $time,
			'orderId' 	    => $orderId,
		];
	}

	/**
	 * 取号下单
	 *
	 * @access 	public
	 * @param  	int 	$equipId		编号id	
	 * @param 	string 	$hospitalId 	医院id
	 * @param 	string 	$orderNum 		能力层返回的orderNum
	 * @param 	string 	$price 			价格
	 * @param 	array 	$fetchInfo 		取号信息
	 * @param 	array 	$cardInfo 		就诊人信息
	 * @param 	array 	$payType 		支付方式
	 * @return 	array
	 */
	public function orderFetch($equipId , $orderNum , $hospitalId , $doctorName , $projectId , $price , $fetchInfo , $cardInfo , $payType)
	{
		$time 	= time();
		$order 	= [
			'equipment_id' 	=> $equipId,
			'hospital_id' 	=> $hospitalId,
			'project_id' 	=> $projectId,
			'type' 			=> 3,	
			'status' 		=> 0,
			'pre_status' 	=> 0,
			'order_number' 	=> $orderNum,
			'price' 		=> $price,
			'create_time' 	=> $time,
			'print' 		=> 0,
			'pay_type' 		=> $payType,
		];
		Db::startTrans();
		$orderId 	= Db::name('order')->insertGetId($order);
		if (!$orderId) {
			Db::rollback();
			return false;
		}
		$patient 	  = [
			'order_id' 		=> $orderId,
			'card_id' 		=> $cardInfo['cardId'],
			'card_name' 	=> $cardInfo['cardName'],
		];
		$result 	= Db::name('order_patient')->insert($patient);
		if (!$result) {
			Db::rollback();
			return false;
		}	 
		$fetch = [
			'order_id' 	 	=> $orderId,
			'hid' 			=> $hospitalId,
			'card_id' 		=> $cardInfo['cardId'],
 			'card_name' 	=> $cardInfo['cardName'],
 			'schedule_code' => $fetchInfo['scheduleCode'],
 			'business_info' => json_encode($fetchInfo , JSON_UNESCAPED_UNICODE),
			'status' 	 	=> 0, 
			'doctor_name' 	=> $doctorName,
			'success_info'  => '[]',
		];
		$result 	= Db::name('order_fetch')->insert($fetch);
		$log 		= [
			'type' 		=> 'create',
			'person' 	=> 'user',
		];
		self::log($orderId , 0 , $order , $log , $time);
		if (!$result) {
			Db::rollback();
			return false;
		}
		Db::commit();
		return [
			'orderNum' 		=> $orderNum,
			'price' 		=> $price,
			'create_time' 	=> $time,
            'orderId' 	    => $orderId,
		];
	}

	/**
	 * 门诊缴费下单
	 *
	 * @access 	public
	 * @param  	int 	$equipId		编号id	
	 * @param 	string 	$hospitalId 	医院id
	 * @param 	string 	$orderNum 		能力层返回的orderNum
	 * @param 	string 	$price 			价格
	 * @param 	array 	$outpatientInfo 门诊缴费信息
	 * @param 	array 	$cardInfo 		就诊人信息
	 * @param 	array 	$payType 		支付方式
	 * @return 	array
	 */
	public function orderOutpatient($equipId , $orderNum , $hospitalId , $projectId , $price , $outpatientInfo , $cardInfo , $payType)
	{
		$time 	= time();
		$order 	= [
			'equipment_id' 	=> $equipId,
			'hospital_id' 	=> $hospitalId,
			'project_id' 	=> $projectId,
			'type' 			=> 4,	
			'status' 		=> 0,
			'pre_status' 	=> 0,
			'order_number' 	=> $orderNum,
			'price' 		=> $price,
			'create_time' 	=> $time,
			'print' 		=> 0,
			'pay_type' 		=> $payType,
		];
		Db::startTrans();
		$orderId 	= Db::name('order')->insertGetId($order);
		
		if (!$orderId) {
			Db::rollback();
			return false;
		}

		$patient 	  = [
			'order_id' 		=> $orderId,
			'card_id' 		=> $cardInfo['cardId'],
			'card_name' 	=> $cardInfo['cardName'],
		];
		$result 	= Db::name('order_patient')->insert($patient);
		if (!$result) {
			Db::rollback();
			return false;
		}

		$outpatient = [
			'order_id' 	 	=> $orderId,
			'hid' 			=> $hospitalId,
			'card_id' 		=> $cardInfo['cardId'],
 			'card_name' 	=> $cardInfo['cardName'],
 			'business_info' => json_encode($outpatientInfo , JSON_UNESCAPED_UNICODE),
			'status' 	 	=> 0, 
			'success_info'  => '[]',
		];
		$result 	= Db::name('order_outpatient')->insert($outpatient);
		$log 		= [
			'type' 		=> 'create',
			'person' 	=> 'user',
		];
		self::log($orderId , 0 , $order , $log , $time);
		if (!$result) {
			Db::rollback();
			return false;
		}
		Db::commit();
		return [
			'orderNum' 		=> $orderNum,
			'price' 		=> $price,
			'create_time' 	=> $time,
            'orderId' 	    => $orderId,
		];
	}

	/**
	 * 门诊缴费下单
	 *
	 * @access 	public
	 * @param  	int 	$equipId		编号id	
	 * @param 	string 	$hospitalId 	医院id
	 * @param 	string 	$orderNum 		能力层返回的orderNum
	 * @param 	string 	$price 			价格
	 * @param 	array 	$inpatientInfo 	住院缴费信息
	 * @param 	array 	$cardInfo 		就诊人信息
	 * @param 	array 	$payType 		支付方式
	 * @return 	array
	 */
	public function orderInpatient($equipId , $orderNum , $hospitalId , $projectId , $price , $inpatientInfo , $cardInfo , $payType)
	{
		$time 	= time();
		$order 	= [
			'equipment_id' 	=> $equipId,
			'hospital_id' 	=> $hospitalId,
			'project_id' 	=> $projectId,
			'type' 			=> 5,	
			'status' 		=> 0,
			'pre_status' 	=> 0,
			'order_number' 	=> $orderNum,
			'price' 		=> $price,
			'create_time' 	=> $time,
			'print' 		=> 0,
			'pay_type' 		=> $payType,
		];
		Db::startTrans();
		$orderId 	= Db::name('order')->insertGetId($order);
		if (!$orderId) {
			Db::rollback();
			return false;
		}
		$patient 	  = [
			'order_id' 		=> $orderId,
			'card_id' 		=> $cardInfo['cardId'],
			'card_name' 	=> $cardInfo['cardName'],
		];
		$result 	= Db::name('order_patient')->insert($patient);
		if (!$result) {
			Db::rollback();
			return false;
		}	 
		$inpatient = [
			'order_id' 	 	=> $orderId,
			'hid' 			=> $hospitalId,
			'treat_no' 		=> $inpatientInfo['treat_no'],
			'card_id' 		=> $cardInfo['cardId'],
 			'card_name' 	=> $cardInfo['cardName'],
 			'business_info' => json_encode($inpatientInfo , JSON_UNESCAPED_UNICODE),
			'status' 	 	=> 0, 
			'success_info'  => '[]',
		];
		$result 	= Db::name('order_inpatient')->insert($inpatient);
		$log 		= [
			'type' 		=> 'create',
			'person' 	=> 'user',
		];
		self::log($orderId , 0 , $order , $log , $time);
		if (!$result) {
			Db::rollback();
			return false;
		}
		Db::commit();
		return [
			'orderNum' 		=> $orderNum,
			'price' 		=> $price,
			'create_time' 	=> $time,
            'orderId' 	    => $orderId,
		];
	}


	/**
	 * 视频订单
	 *
	 * @access 	public
	 * @param  	int 	$equipId		编号ID
	 * @param 	int 	$projectId 		项目ID
	 * @param 	string 	$price 			价格
	 * @param 	int 	$videoId 		视频ID
	 * @param 	int 	$payType 		支付方式
	 * @return 	array
	 */
	public function orderVideo($equipId , $projectId , $price , $videoId , $payType)
	{
		$time 		= time();
		$orderNum 	= self::orderNumber(99);
		$order 		= [
			'equipment_id' 	=> $equipId,
			'hospital_id' 	=> 0,
			'project_id' 	=> $projectId,
			'type' 			=> 99,	
			'status' 		=> 0,
			'pre_status' 	=> 0,
			'order_number' 	=> $orderNum,
			'price' 		=> $price,
			'create_time' 	=> $time,
			'print' 		=> 0,
			'pay_type' 		=> $payType,
		];
		Db::startTrans();
		$orderId 	= Db::name('order')->insertGetId($order);
		if (!$orderId) {
			Db::rollback();
			return false;
		}
		$video = [
			'order_id' 	 	=> $orderId,
			'video_id' 		=> $videoId,
			'status' 	 	=> 0, 
		];
		$result 	= Db::name('order_video')->insert($video);
		$log 		= [
			'type' 		=> 'create',
			'person' 	=> 'user',
		];
		self::log($orderId , 0 , $order , $log , $time);
		if (!$result) {
			Db::rollback();
			return false;
		}
		Db::commit();
		return [
			'orderNum' 		=> $orderNum,
			'orderId' 		=> $orderId,
			'price' 		=> $price,
			'create_time' 	=> $time,
		];
	}

	/**
	 * 问诊订单
	 *
	 * @access 	public
	 * @param  	int 	$equipId		编号ID
	 * @param  	string 	$equip			购买的设备编号
	 * @param 	int 	$projectId 		项目ID
	 * @param 	string 	$price 			价格
	 * @param 	int 	$videoId 		视频ID
	 * @param 	int 	$payType 		支付方式
	 * @return 	array
	 */
	public static function orderInquiry($equipId , $equip , $hospitalId , $projectId , $doctorId , $price , $payType , $fenRun)
	{
		$time 	= time();
		$orderNum 	= self::orderNumber(98);
		$order 	= [
			'equipment_id' 	=> $equipId,
			'hospital_id' 	=> $hospitalId,
			'project_id' 	=> $projectId,
			'type' 			=> 98,	
			'status' 		=> 0,
			'pre_status' 	=> 0,
			'order_number' 	=> $orderNum,
			'price' 		=> $price,
			'create_time' 	=> $time,
			'print' 		=> 0,
			'pay_type' 		=> $payType,
		];
		Db::startTrans();
		$orderId 	= Db::name('order')->insertGetId($order);
		if (!$orderId) {
			Db::rollback();
			return false;
		}
        //生成腾讯生成视频推送地址
        $liveV = new TencentVideoLive($orderId);
        $videoUrl = $liveV->getAllUrl();
		$inquriy = [
			'order_id' 	 	    => $orderId,
			'hospital_id' 	    => $hospitalId,
			'doctor_id' 	    => $doctorId,
			'holding_time'  	=> 0,
			'status' 	 	    => 0,
            'docPushUrl' 	 	=> $videoUrl['docPushUrl'],
            'docPlayUrl' 	 	=> $videoUrl['docPlayUrl'],
            'patPushUrl' 	 	=> $videoUrl['patPushUrl'],
            'patPlayUrl' 	 	=> $videoUrl['patPlayUrl'],
            'YiSheng' 	 	    => sprintf('%.2f' , $price*($fenRun['YiSheng']/$fenRun['All'])),
            'YiYuan' 	 	    => sprintf('%.2f' , $price*($fenRun['YiYuan']/$fenRun['All'])),
            'PingTai' 	 	    => sprintf('%.2f' , $price*($fenRun['PingTai']/$fenRun['All'])),
        ];
		$inquiryId 	= Db::name('order_inquiry')->insertGetId($inquriy);
		if (!$inquiryId) {
			Db::rollback();
			return false;
		}
		$inquriyEquip 	= [
			'inquiry_id' 	=> $inquiryId,
			'equipment' 	=> $equip['code'],
			'address' 		=> $equip['address'],
		];
		$result 	= Db::name('inquiry_equipment')->insert($inquriyEquip);
		$log 		= [
			'type' 		=> 'create',
			'person' 	=> 'user',
		];
		self::log($orderId , 0 , $order , $log , $time);
		if (!$result) {
			Db::rollback();
			return false;
		}
		Db::commit();
		return [
			'orderNum' 		=> $orderNum,
			'price' 		=> $price,
			'create_time' 	=> $time,
		];
	}

	/**
	 * 订单日志处理
	 *
	 * @access 	public
	 * @param 	int 	$orderId 	订单ID
	 * @param 	int 	$status 	订单状态
	 * @param 	array 	$orderInfo 	订单信息
	 * @param   array 	$log 		日志信息
	 * @param 	int 	$time 		时间
	 * @return  boolen
	 */
	public function orderLog($orderId , $status , $orderInfo , $log , $time )
	{
		$time || $time = time();
		$insert 	= [
			'order_id' 		=> $orderId,
			'status' 		=> $status,
			'time' 			=> $time,
			'order_info' 	=> is_array($orderInfo) ? json_encode($orderInfo , JSON_UNESCAPED_UNICODE) : $orderInfo,
			'log' 			=> is_array($log) ? json_encode($log , JSON_UNESCAPED_UNICODE) : $log,
		];
		$result  	= Db::name('order_log')->insert($insert);
		return $result ? true : false;
	}

    /**
     * 订单日志处理
     *
     * @access 	public
     * @param 	int 	$orderId 	订单ID
     * @param 	int 	$status 	订单状态
     * @param 	array 	$orderInfo 	订单信息
     * @param   array 	$log 		日志信息
     * @param 	int 	$time 		时间
     * @return  boolen
     */
    protected static function log($orderId , $status , $orderInfo , $log , $time )
    {
        $time || $time = time();
        $insert 	= [
            'order_id' 		=> $orderId,
            'status' 		=> $status,
            'time' 			=> $time,
            'order_info' 	=> is_array($orderInfo) ? json_encode($orderInfo , JSON_UNESCAPED_UNICODE) : $orderInfo,
            'log' 			=> is_array($log) ? json_encode($log , JSON_UNESCAPED_UNICODE) : $log,
        ];
        $result  	= Db::name('order_log')->insert($insert);
        return $result ? true : false;
    }

	/** 
	 * 生成订单编号
	 *
	 * @access 	public
	 * @param 	int 	$type 		订单类型
	 * @return 	string
	 */
	private static function orderNumber($type)
	{
		$array 	= ['A' , 'B' , 'C' , 'D' , 'E' , 'F' , 'G'];
		$key 	= rand(0 , 6);
		return date('Ymd').str_pad($type , 2 , '0').$array[$key].str_pad(rand(0 , 999999) , 6 , '0');
	}
	
	/**
	 * 获取订单详情
	 *
	 * @access 	public
	 * @param 	int 	$orderId 	订单ID
	 * @return 	array
	 */
	public function detail($orderId)
	{
		$map 	= ['id' => $orderId];
		$data 	= Db::name('order')->where($map)->find();
		return $data ? $data : [];
	}

	/**
	 * 获取订单详情
	 *
	 * @access 	public
	 * @param 	string 	   $orderNumber 	订单号
	 * @return 	array
	 */
	public function detailByNumber($orderNumber)
	{
		$map 	= ['order_number' => $orderNumber];
		$data 	= Db::name('order')->where($map)->find();
		return $data ? $data : [];
	}

	/**
	 * 获取订单记录
	 *
	 * @access 	public
	 * @param 	sting 		$orderNumber 	订单号
	 * @param 	array 		$orderType 	订单类型
	 * @return 	array
	 */
	public function listByNumber($orderNumber , $orderType = [])
    {
    	$map 	= [
			'a.order_number' => $orderNumber,
		];
    	if($orderType){
            $map['a.type'] 	= ['IN' , implode(',' , $orderType)];
        }
		$data 	= Db::name('order')
				->field('d.code,a.*,b.*,c.*')
				->alias('a')
				->join('emt_order_pay c' , 'a.id = c.order_id')
				->join('emt_equipment d' , 'd.id = a.equipment_id')
                ->join('emt_order_patient b' , 'a.id = b.order_id' , 'LEFT')
				->where($map)
				->select();
    	//error_log("SQL => " . Db::name('order')->getLastSql());
		return $data ? $data : [];
    }
	/**
	 * 查询订单记录
	 *
	 * @access 	public
	 * @param 	string  	$cardId 		卡号
	 * @param 	array  	$orderType 		订单类型
	 * @return 	array
	 */
	public function listByCardId($cardId , $orderType = [])
	{
		$map 	= [
			'b.card_id' => $cardId,
		];
        if($orderType){
            $map['a.type'] 	= ['IN' , implode(',' , $orderType)];
        }
		$data 	= Db::name('order')
				->alias('a')
				->join('emt_order_patient b' , 'a.id = b.order_id')
				->join('emt_order_pay c' , 'a.id = c.order_id')
				->where($map)
				->order('a.id desc')
				->select();
		return $data ? $data : [];
	}

	public function listByTreat($treatNo , $orderType = [])
	{
		$map 	= [
			'b.treat_no' => $treatNo,
		];
        if($orderType){
            $map['a.type'] 	= ['IN' , implode(',' , $orderType)];
        }
		$data 	= Db::name('order')
				->alias('a')
				->join('emt_order_inpatient b' , 'a.id = b.order_id')
				->join('emt_order_pay c' , 'a.id = c.order_id')
				->where($map)
				->order('a.id desc')
				->select();
		return $data ? $data : [];
	}

	/**
	 * 标记打印
	 *
	 * @access 	public
	 * @param 	int 	$orderId 	订单ID
	 * @return  boolen
	 */
	public function markPrint($orderId)
	{
		$map 	= ['id' => $orderId];
		$result = Db::name('order')->where($map)->setInc('print');
		return $result !== false ? true : false;
	}


	/**
	 * 微信二维码
	 *
	 * @access 	public
	 * @param 	int 	$orderId 	订单号
	 * @return 	string
	 */
	public function wechatQR($orderId)
	{
		$map 	= ['order_id' => $orderId , 'type' => 1];
		$data 	= Db::name('order_qr')->where($map)->find();
		return $data ? $data['qr'] : false;
	}

	/**
	 * 插入微信二维码
	 *
	 * @access 	public 
	 * @param 	int 	$orderId 	订单号
	 * @param 	string 	$qr 	 	二维码
	 * @return 	boolen
	 */
	public function insertWechatQR($orderId , $qr)
	{
		$map 	= ['order_id' => $orderId , 'type' => 1 , 'qr' => $qr];
		return Db::name('order_qr')->insert($map) ? true : false; 
	}

	/**
	 * 支付宝二维码
	 *
	 * @access 	public
	 * @param 	int 	$orderId 	订单号
	 * @return 	string
	 */
	public function alipayQR($orderId)
	{
		$map 	= ['order_id' => $orderId , 'type' => 2];
		$data 	= Db::name('order_qr')->where($map)->find();
		return $data ? $data['qr'] : false;
	}

	/**
	 * 插入支付宝二维码
	 *
	 * @access 	public 
	 * @param 	int 	$orderId 	订单号
	 * @param 	string 	$qr 	 	二维码
	 * @return 	boolen
	 */
	public function insertAlipayQR($orderId , $qr)
	{
		$map 	= ['order_id' => $orderId , 'type' => 2 , 'qr' => $qr];
		return Db::name('order_qr')->insert($map) ? true : false; 
	}


	public function payDetailByTransactionId($transactionId)
	{
		$map 	= ['transaction_id' => $transactionId];
		$data 	= Db::name('order_pay')->where($map)->find();
		return $data ? $data : [];
	}

	public function payDetail($orderId)
	{
		$map 	= ['order_id' => $orderId];
		$data 	= Db::name('order_pay')->where($map)->find();
		return $data ? $data : [];
	}

	public function orderPay($orderId , $outTradeNo , $transactionId , $price , $payType , $payTime , $mchId,$openid)
	{
		Db::startTrans();
		$map 	= ['id' => $orderId];
		$update = ['status' => 1 , 'pre_status' => 1];
		$result = Db::name('order')->where($map)->update($update);
		if ($result === false) {
			Db::rollback();
			return false;
		}
		$insert = [
			'order_id' 		=> $orderId,
			'out_trade_no' 	=> $outTradeNo,
			'transaction_id'=> $transactionId,
			'price' 		=> $price,
			'pay_type' 		=> $payType,
			'pay_time' 		=> $payTime,
			'mch_id' 		=> $mchId,
            'openid' 		=> $openid,
			'create_time' 	=> time(),
		];
		$result = Db::name('order_pay')->insert($insert);
		if (!$result) {
			Db::rollback();
			return false;
		}
		Db::commit();
		return true;
	}

    /**
     * 设置订单待退款
     * @param $orderId
     * @param string $type
     * @return bool
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
	public static function setOrderRefund($orderId , $type = '')
	{
		$map 	= ['id' => $orderId];
		$update = ['status' => 2];
		$result = Db::name('order')->where($map)->update($update);
        Common::syncOrder($orderId , 2 , $type);
		return $result !== false ? true : false;
	}
	
}	