<?php

namespace app\equip\event\v2;

use think\Db;
use think\Loader;
use app\equip\controller\Base;
use app\component\response\Response;
use app\component\server\Server;
use app\component\Common;

class Registration extends Base
{
	protected $validate = '\app\equip\validate\v2\Registration'; 	//定义validate文件

	protected $scene = [ 									//定义需要验证的方法
		'date',
		'doctorDetail',
		'schedule',
		'lock',
		'registrationQuery',
		'fetchReg',
		'fetchRegOrder',
		'fetchQuery',
	];

	protected $mustId  = [
		'dept',
		'date',
		'doctorDetail',
		'schedule',
		'lock',
		'fetchReg',
		'fetchRegOrder',
	];

	/**
	 * 获取医院数据
	 *
	 * @access 	public
	 * @return 	void
	 */
	public function hospital()
	{
		$hospitalConf 	= $this->hospitalConf;
		$hid    = $hospitalConf;
		$data 	= Loader::model('Hospital')->listById($hospitalConf);
		$hospital	= [];
		foreach ($data as $v) {
			$hospital[] 	= ['hospitalId' => $v['id'] , 'logo' => $v['logo'] , 'hospitalName' => $v['name']];
		}
		Response::success($hospital);
	}

 	/**
	 * 排班科室
	 *
	 * @access 	public
	 * @return  void
	 */
	public function dept()
	{
        $districtId = $this->getData("districtId",false,0);
		$hid 	= $this->hospitalId;
		//error_log("HID => " . $hid);
        $params = [
            'districtId' 	=> $districtId,
        ];
		$data 	= Server::ability('hospital')->dutyDept($hid,$params);
		if (!$data) { Response::message(10101); }
		$data['code'] != 10000 && Response::message(10102);
		$data 	= $data['data'];
		$dept 	= [];
		$hDept 	= Loader::model('Dept')->deptByHospital($hid);
		$hDept 	= array_column($hDept , 'name' , 'his_id');
		$add 	= $update 	= [];
		foreach ($data as $key => $value) {
			if (!isset($hDept[$value['deptHisId']])) {
				$add[] 	= ['hisId' => $value['deptHisId'] , 'name' => $value['deptName']];
			} else if ($hDept[$value['deptHisId']] != $value['deptName']) {
				$update[] = ['hisId' => $value['deptHisId'] , 'name' => $value['deptName']];
			}
			$dept[] 	= ['deptId' => $value['deptHisId'] , 'name' => $value['deptName']];
		}
		Loader::model('Dept')->sync($hid , $add , $update);
		Response::success($dept);
	}


	/**
	 * 排班日期
	 *
	 * @access 	public
	 * @return  void
	 */
	public function date()
	{
        $districtId = $this->getData("districtId",false,0);
		$hid 	= $this->hospitalId;
		$deptId = $this->data['deptId'];
		$params = [
			'deptId' 	=> $deptId,
            'districtId' 	=> $districtId,
		];
		$data 	= Server::ability('hospital')->dutyDoctor($hid , $params);
		if (!$data) { Response::message(10101); }
		$data['code'] != 10000 && Response::message(10102); 	//接口响应失败
		$data 	= $data['data'];
		$date 	= [];
		//Response::success($data);die;
        //获取医院对应的项目配置
        $projectArr =  Loader::model('Project')->detailByHospital($hid);
        if(isset($projectArr['registration'])){
            $registrationArr =  json_decode($projectArr['registration'],true);
            $YuYueTianShu = $registrationArr['YuYueTianShu'] ? $registrationArr['YuYueTianShu'] : 15;
                //$startDay = date('Y-m-d');
                //$endDay = date('Y-m-d',strtotime("+{$YuYueTianShu} day"));
        }else{
            $registrationArr['ZhouMoGuaHao'] = 1;
            $YuYueTianShu = 15;
        }
		// 将每个医生的每个时段剩余挂号数 叠加
		foreach ($data as $value) {
			foreach ($value['scheduleList'] as $v) {
			    $nowDateStr = date("Y-m-d");
			    //限制华二当天不能挂号
			    if( $hid == 10000 & $v['scheduleDate'] == $nowDateStr){
			        continue;
                }
				$key 	= date('Ymd' , strtotime($v['scheduleDate']));
				//周末是否可预约
				if(!$registrationArr['ZhouMoGuaHao']){
                    $weekDay = date('w',strtotime($v['scheduleDate']));
                    $weekDayArr = [6,0];
                    if(in_array($weekDay,$weekDayArr)){
                        continue;
                    }
                }
                // 是否只显示有号的医生
                if($registrationArr['ZhiXianShiYouHaoYiSheng']){
                    if($v['availableNum'] <= 0) {
                        continue;
                    }
                }
                // 预约天数
                $endDay = date('Ymd',strtotime("+{$YuYueTianShu} day"));
                if($key > $endDay){
                    continue;
                }
				!isset($date[$key]) && $date[$key] 	= ['date' => $v['scheduleDate'] , 'period' => []];
				if ($v['period'] == 'am') {
				    !in_array($v['period'] , $date[$key]['period']) && $date[$key]['period'][0] = 'am';
				} else if ($v['period'] == 'pm') {
				    !in_array($v['period'], $date[$key]['period']) && $date[$key]['period'][1] = 'pm';
				} else if ($v['period'] == 'npm') {
				    !in_array($v['period'], $date[$key]['period']) && $date[$key]['period'][2] = 'npm';
				} else if ($v['period'] == 'all') {
				    $date[$key]['period'] = ['am', 'pm', 'npm'];
				}
				//特殊处理南江妇幼时间段
				if($hid == 61760){
                    if ($v['period'] == 'day') {
                        !in_array($v['period'] , $date[$key]['period']) && $date[$key]['period'][3] = 'day';
                    } else if ($v['period'] == 'md') {
                        !in_array($v['period'], $date[$key]['period']) && $date[$key]['period'][4] = 'md';
                    } else if ($v['period'] == 'sam') {
                        !in_array($v['period'], $date[$key]['period']) && $date[$key]['period'][5] = 'sam';
                    }else if ($v['period'] == 'amd') {
                        !in_array($v['period'], $date[$key]['period']) && $date[$key]['period'][6] = 'amd';
                    }
                }
				$date[$key]['period'] 	= array_merge($date[$key]['period']);
			}
		}

		$date = $this->limitRegister($date , $deptId);
		sort($date);
		$date 	= array_merge($date);
		Response::success($date);

	}

	/**
	 * 医生详情
	 *
	 * @access 	public
	 * @return 	void
	 */
	public function doctorDetail()
	{
		$hid 		= $this->hospitalId;
		$doctorId 	= $this->data['doctorId'];
		$deptId 	= $this->data['deptId'];
		//$doctorId 	= 3218;
		$doctor 	= Loader::model('Doctor')->detailByHisId($hid , $doctorId);
		if (!$doctor) {
			Response::message(30013);
		}
		$doctor 	= [
			'doctorId' 	=> $doctorId,
			'hospitalId'=> $hid,
			'deptId' 	=> $deptId,
			'doctorName'=> $doctor['name'],
			'picture' 	=> $doctor['avatar'],
			'title' 	=> $doctor['title'],
			'disease' 	=> $doctor['sections'],
			'profile' 	=> $doctor['content'],
		];
		Response::message(10000 , $doctor);
	}

    /**
     * 医生详情
     *
     * @access 	public
     * @return 	void
     */
    public function doctorTime()
    {
        $hid 		= $this->hospitalId;
        $data['scheduleId'] = $this->data['scheduleId'];
        $data['period'] 	= $this->data['period'];
        $doctor 	= Server::ability('hospital')->doctorScheduleTime($hid , $data);
        if (!$doctor) {
            Response::message(10102);
        }
        $doctorData = '';
        if($doctor['data']){
            $doctorData = $doctor['data'];
        }
        Response::message(10000 , $doctorData);
    }

    /**
     * 取消挂号
     *
     * @access 	public
     * @return 	void
     */
    public function cancel()
    {
        $hid 		= $this->hospitalId;
        $orderNum = $this->data['orderNum'];
        $orderArr = Db::name("order")->alias("o")
            ->field("o.id,p.transaction_id,r.success_info,r.business_info,r.card_id,o.type")
            ->join("order_pay p" , "p.order_id=o.id")
            ->join("order_registration r" , "r.order_id=o.id")
            ->where("o.order_number='{$orderNum}'")->find();
        $data['transactionId'] = $orderArr['transaction_id'];
        $successInfo = json_decode($orderArr['success_info'] , true);
        $businessInfo = json_decode($orderArr['business_info'] , true);
        if(!$successInfo){
            Response::errorMessage('订单异常!');
        }
        $admId = isset($successInfo['admId']) ? $successInfo['admId'] : '';
        $dateStr = $successInfo['RegYear'] .'-' . $successInfo['RegMonth']  . '-' .$successInfo['RegDay'];
        $regDay = strtotime($dateStr) - 86400;
        $day = time();
        if($regDay < $day){
            Response::errorMessage('就诊时间24小时之前才可退号退费!');
        }
        $data['patientId'] = isset($this->data['uniqueId']) ? $this->data['uniqueId'] : '';
        //$data['serNo'] = isset($successInfo['SerNo']) ? $successInfo['SerNo'] : '';
        $data['serNo'] = isset($businessInfo['apptId']) ? $businessInfo['apptId'] : '';
        $data['invoiceNo'] = isset($successInfo['invoiceNo']) ? $successInfo['invoiceNo'] : '';
        $data['reason'] = '申请退号';
        $data['cardNo'] = $orderArr['card_id'];
        $data['admId'] = $admId;
        $result	= Server::ability('hospital')->cancel($hid , $data);
        if (!$result or $result['code'] != 10000) {
            Response::errorMessage($result['msg']);
        }
        Db::name("order")->where("order_number='{$orderNum}'")->update(['status' => 2]);
        Common::syncOrder($orderArr['id'] , 2 , $orderArr['type']);
        if($hid == 61757){
            $business = json_decode($orderArr['business_info'] , true);
            $params['periodId'] = isset($business['periodId']) ? $business['periodId'] : '';
            $params['apptId'] = $business['apptId'];
            $params['CardNo'] = $orderArr['card_id'];
            $params['date'] = $business['scheduleDate'];
            $result = Server::ability('hospital')->releaseSource($hid,$params);
        }
        Response::message(10000 , $result['msg']);
    }


	/**
	 * 医生排班
	 *
	 * @access 	public
	 * @return  void
	 */
	public function schedule()
	{
        $districtId = $this->getData("districtId",false,0);
		$hid 	= $this->hospitalId;
		$deptId = $this->data['deptId'];
		$date 	= $this->data['date'];
		$period = $this->data['period'];
		/*$params = [
			'deptId' 	=> $deptId,
			'date' 		=> $date,
			'period' 	=> $period,
		];*/
		$scheduleList 	= [];
		if ($date != date('Y-m-d')) {
			$params = [
				'deptId' 	=> $deptId,
				'date' 		=> $date,
                'districtId' 	=> $districtId,
			];
			$data 	= Server::ability('hospital')->dutyDoctor($hid , $params);
			if ($data && $data['code'] == 10000) {
				$scheduleList 	= $data['data'];
			}
		} else {
			$params = [
				'deptId' 	=> $deptId,
				'date' 		=> $date,
				'period' 	=> $period,
                'districtId' 	=> $districtId,
			];
			$data 	= Server::ability('hospital')->dutyDoctor($hid , $params);
			if ($data && $data['code'] == 10000) {
				$scheduleList 	= $data['data'];
			}
            if(in_array($this->hospitalId,  [10000 , 61759])) {
                if ($period != 'all') {
                    $params = [
                        'deptId' => $deptId,
                        'date' => $date,
                        'period' => 'all',
                        'districtId' => $districtId,
                    ];
                    $data = Server::ability('hospital')->dutyDoctor($hid, $params);
                    if ($data && $data['code'] == 10000) {
                        $scheduleList2 = $data['data'];
                        foreach ($scheduleList2 as $v) {
                            $scheduleList[] = $v;
                        }
                    }
                }
            }
		}

		if (!$scheduleList) { Response::message(10102); }
//		print_r($scheduleList);
        $schedule = [];
		foreach ($scheduleList as $key => $value) {
			foreach ($value['scheduleList'] as $v) {
			    if($period != 'all'){
                    if (($v['period'] != 'all' && $v['period'] != $period) || $v['availableNum'] <= 0) continue;
                }
				$schedule[] = ['scheduleId' => $v['scheduleId'] , 'doctorId' => $value['doctorHisId'] , 'period' => $v['period'] , 'num' => $v['availableNum'] , 'image' => 'http://auto-1253714281.cosgz.myqcloud.com/Upload/e5591c9106b74189933ee815ad593951.png' ,'doctorName' => $value['doctorName'] , 'title' => $value['deptName'] , 'fee' => $v['feeSum']];
			}
		}
		Response::message(10000 , $schedule);
	}

	/**
	 * 锁号
	 *
	 * @access 	public
	 * @return  void
	 */
	public function lock()
	{
		$hid 		= $this->hospitalId;
		$equipId 	= $this->equipId;
		$IDCard 	= isset($this->data['IDCard']) ? $this->data['IDCard'] : '';
		$scheduleId = $this->data['scheduleId'];
		$cardId 	= $this->data['cardId'];
		$period 	= $this->data['period'];
		$date 		= $this->data['date'];
		$deptId 	= $this->data['deptId'];
		$doctorId 	= $this->data['doctorId'];
		$uniqueId 	= isset($this->data['uniqueId']) ? $this->data['uniqueId'] : '';
		$periodId 	= isset($this->data['periodId']) ? $this->data['periodId'] : '';
        $userName 	= isset($this->data['userName']) ? $this->data['userName'] : '';
		$phone 	= isset($this->data['phone']) ? $this->data['phone'] : '';
		$beginTime 	= isset($this->data['beginTime']) ? $this->data['beginTime'] : '';
		$endTime 	= isset($this->data['endTime']) ? $this->data['endTime'] : '';
		$districtId = isset($this->data['districtId']) ? $this->data['districtId'] : 0;
        $payType 	= isset($this->data['payType']) ? $this->data['payType'] : 1;
		$lock 		= Loader::model('Registration')->detailByScheduleId($cardId , $scheduleId);
        $projectArr =  Loader::model('Project')->detailByHospital($hid);
        if(isset($projectArr['registration'])){
            $registrationArr =  json_decode($projectArr['registration'],true);
            $YiShengYiTianYiCi = isset($registrationArr['YiShengYiTianYiCi']) ? $registrationArr['YiShengYiTianYiCi'] : 0 ;
            $KeZhiFuShiChang = isset($registrationArr['KeZhiFuShiChang']) ? $registrationArr['KeZhiFuShiChang'] : 150 ;
        }else{
            $YiShengYiTianYiCi = 0;
            $KeZhiFuShiChang = 150;
        }
		if ($lock && $lock['status'] == 0) {
            $needTime = $lock['create_time'] + $KeZhiFuShiChang;
			if ( $needTime < time()) {
				Response::message(30005); 	//返回锁号频繁
			}
			$registration 	= json_decode($lock['business_info'] , true);
			$update['pay_type'] = $payType;
			Db::name("order")->where("id={$lock['order_id']}")->update($update);
			$response 		= [
				'cardId' 		=> $lock['card_id'],
				'cardName' 		=> $lock['card_name'],
				'doctorName' 	=> $registration['doctorName'],
				'deptName' 		=> $registration['deptName'],
				'date' 		 	=> $registration['scheduleDate'],
				'period' 		=> $registration['period'],
				'periodId' 		=> isset($registration['periodId']) ? $registration['periodId'] : '',
				'create_time' 	=> $lock['create_time'],
				'orderNum' 		=> $lock['order_number'],
				'price' 		=> $lock['price'],
                'now_time' 		=> time(),
			];
			Response::success($response);
		}

		$params = [
			'IdCardNo' 	=> $IDCard,
			'cardId' 	=> $cardId,
		];
        //青海医院的就诊卡就是身份证号
        if($hid == 61756){
            $params['IdCardNo'] = $cardId;
        }
		$card 	= Server::ability('hospital')->patientCard($hid , $params);
		if (!$card || $card['code'] != 10000) Response::message(30000);
		$cardName 	= $card['data']['userName'];
		$params = [
			'deptId' 	=> $deptId,
			'doctorId' 	=> $doctorId,
			'date' 		=> $date,
			'period' 	=> $period,
			'scheduleId'=> $scheduleId,
            'districtId' => $districtId,
		];

        $data 		= Server::ability('hospital')->doctorSchedule($hid , $params);
		if (!$data) { Response::message(10101); }
		$data['code'] != 10000 && Response::message(10102);
		$schedule 	= [];
		foreach ($data['data'] as $v) {
			if ($scheduleId == $v['scheduleId']) {
				$schedule = $v;
			}
			$doctorHisId = $v['doctorHisId'];
		}
        $schedule['districtName'] = isset($this->data['districtName']) ? $this->data['districtName'] : '';
		if (!$schedule) Response::message(30001);
        //限制当天同一医生挂号 YiShengYiTianYiCi
        if($YiShengYiTianYiCi){
            $searchOrder = Loader::model('Registration')->detailByCardIdDocId($cardId , $doctorHisId ,$hid );
            if($searchOrder){
                Response::message(30053); //今天已经挂了这个医生的号了
            }
        }
		if ($schedule['availableNum'] <= 0) {
			Response::message(30002); //可挂号数量不足
		}

        $params = [
			'scheduleId' 	=> $scheduleId,
			'period' 		=> $period,
			'IdCardNo' 		=> $IDCard,
			'cardId' 		=> $cardId,
			'uniqueId' 		=> $uniqueId,
			'beginTime' 	=> $beginTime,
			'endTime' 		=> $endTime,
            'districtId'    => $districtId,
		];
        //青海医院的就诊卡就是身份证号
        if($hid == 61756){
            $params['IdCardNo'] = $cardId;
            $params['userName'] = $userName;
            $params['phone'] = $phone;
        }
        if($hid == 61757){
            $params['periodCode'] = $periodId;
        }
        if($periodId){
            $schedule['periodId'] = $periodId;
        }
		$lock 	 	= Server::ability('hospital')->lockSchedule($hid , $params);
		if (!$lock || $lock['code'] != 10000) {
			Response::message(30003); 	//返回锁号失败
		}
		$orderNum 	= $lock['data']['out_trade_no'];
		$fee 		= $schedule['feeSum'];
		$deptName 	= $schedule['deptName'];
		$doctorName = $schedule['doctorName'];

		$schedule['apptId'] = $lock['data']['apptId'];
		$cardInfo 			= [
			'cardId' 		=> $cardId,
			'cardName' 		=> $cardName,
		];

		$order 	 	= Loader::model('Order')->orderRegistration($equipId , $orderNum , $hid , $doctorName , $this->projectId , $fee , $schedule , $cardInfo , $payType , $districtId);
		if (!$order) {
			Response::message(30004);//
		}
		$response 	= [
			'doctorName' 	=> $doctorName,
			'deptName' 		=> $deptName,
			'date' 		 	=> $date,
			'period' 		=> $period,
			'create_time' 	=> $order['create_time'],
			'orderNum' 		=> $order['orderNum'],
			'price' 		=> $order['price'],
            'now_time' 		=> time(),
            'orderId' 		=> $order['orderId'],
		];
		Response::message(10000 , $response);

	}

	/**
	 * 挂号查询
	 *
	 * @access 	public
	 * @return  void
	 */
	public function registrationQuery()
	{
		$orderNumber = $this->data['orderNum'];
		$data 	= Loader::model('Order')->detailByNumber($orderNumber);
		if (!$data || $data['type'] != 2 || $data['project_id'] != $this->projectId) {
			Response::message(30011);
		}
		if ($data['status'] <= 0) {
			Response::message(10000 , [
				'orderNum' 	=> $orderNumber,
				'payStatus' => $data['status'],
			]);
		}
		$orderId 	= $data['id'];
		$orderPay 	= Loader::model('Order')->payDetail($orderId);
		$registration 		= Loader::model('Registration')->detailByOrderId($orderId);
		$successInfo 		= json_decode($registration['success_info'] , true);
		$registrationInfo  	= json_decode($registration['business_info'] , true);
		$response 	= [
			'orderNum' 	=> $orderNumber,
			'payStatus' => $data['status'],
			'registrationStatus' => $data['status'],
			'cardId' 	=> $registration['card_id'],
			'cardName' 	=> $registration['card_name'],
			'doctorName'=> $registrationInfo['doctorName'],
			'deptName' 	=> $registrationInfo['deptName'],
			'date' 		=> $registrationInfo['scheduleDate'],
			'period' 	=> $registrationInfo['period'],
			'districtName' 	=> isset($registrationInfo['districtName']) ? $registrationInfo['districtName'] : '',
			'weekDay' 	=> $registrationInfo['weekDay'],
			'fee' 		=> sprintf('%.2f' , $registrationInfo['feeSum']),
			'payType' 	=> $orderPay['pay_type'],
			'payPrice' 	=> $orderPay['price'],
			'transactionId' => $orderPay['transaction_id'],
		];
		if ($data['status'] == 1) {
			$response['status'] = $registration['status'];
//			if($this->hospitalId == 61754){
//                $response['status'] = 2;
//            }
			if ($registration['status'] == 1) {
				$response['queueNo'] = isset($successInfo['queueNo']) ? $successInfo['queueNo'] : '';
				$response['LocInfo'] = isset($successInfo['LocInfo']) ? $successInfo['LocInfo'] : '';
			}
		}

		Response::message(10000 , $response);
	}

	/**
	 * 加号、取号列表
	 *
	 * @access 	public
	 * @return  void
	 */
	public function fetchReg()
	{
		$hid 	= $this->hospitalId;
		$cardId = $this->data['cardId'];
		$params = [
			'cardNo' 	=> $cardId,
		];
        //青海医院的就诊卡就是身份证号
        if($hid == 61756){
            $params['IdCardNo'] = $cardId;
        }
		$data 	= Server::ability('hospital')->addRegQuery($hid , $params);
		if (!$data) { Response::message(10101); }
		$data['code'] != 10000 && Response::message(10102);
		$reg 	= [];
		$data 	= $data['data'];
		foreach ($data as $v) {
			$reg[] 	= [
				'doctorName' 	=> isset($v['doctorName']) ? $v['doctorName'] : '',
				'period' 		=> isset($v['period']) ? $v['period'] : '',
				'deptName' 		=> isset($v['deptName']) ? $v['deptName'] : '',
				'roomName' 		=> isset($v['roomName']) ? $v['roomName'] : '',
				'queueNo' 		=> isset($v['queueNo']) ? $v['queueNo'] : '',
				'fee' 			=> isset($v['fee']) ? $v['fee'] : '' ,
				'fetchType' 	=> isset($v['fetchType']) ? $v['fetchType'] : '',
				'date' 			=> isset($v['date']) ? $v['date'] : '',
				'scheduleCode' 	=> isset($v['scheduleCode']) ? $v['scheduleCode'] : '',
			];
		}
		Response::message(10000 , $reg);

	}


	/**
	 * 取号订单
	 *
	 * @access 	public
	 * @return  void
	 */
	public function fetchRegOrder()
	{
		$hid 	= $this->hospitalId;
		$cardId = $this->data['cardId'];
		$payType = isset($this->data['payType']) ? $this->data['payType'] : 4;
		$scheduleCode = $this->data['scheduleCode'];
		$params = [
			'cardId' 	=> $cardId,
		];
        //青海医院的就诊卡就是身份证号
        if($hid == 61756){
            $params['IdCardNo'] = $cardId;
        }
		$card 	= Server::ability('hospital')->patientCard($hid , $params);
		if (!$card || $card['code'] != 10000) Response::message(30000);
		$cardName = $card['data']['userName'];
		$params = [
			'cardNo' 	=> $cardId,
		];
		$data 	= Server::ability('hospital')->addRegQuery($hid , $params);
		if (!$data) { Response::message(10101); }
		$data['code'] != 10000 && Response::message(10102);
		$reg 	= [];
		$data 	= $data['data'];
		foreach ($data as $v) {
			if ($v['scheduleCode'] == $scheduleCode) {
				$reg = $v; break;
			}
		}
		$fee 	   = $reg['fee'];
		$fetchType = $reg['fetchType'];
		$date 	   = $reg['date'];
		$scheduleCode  = $reg['scheduleCode'];
		$cardInfo 		= [
			'cardId' 		=> $cardId,
			'cardName' 		=> $cardName,
		];
		$params 	= [
			'cardNo' 		=> $cardId,
			'scheduleCode' 	=> $scheduleCode,
			'fetchType'  	=> $fetchType,
			'date' 			=> $date,
			'fee' 			=> $fee,
		];
		$data      = Server::ability('hospital')->addRegOrder($hid , $params);
		if (!$data || $data['code'] != 10000) Response::message(30031);
		$orderNum  = $data['data']['out_trade_no'];
		$equipId   = $this->equipId;
		$order 	   = Loader::model('Order')->orderFetch($equipId , $orderNum , $hid , $reg['doctorName'] , $this->projectId , $fee , $reg , $cardInfo , $payType);
		if (!$order) {
			Response::message(30032);
		}
		$response 	= [
			'doctorName' 	=> $reg['doctorName'],
			'deptName' 		=> $reg['deptName'],
			'cardId' 		=> $cardId,
			'cardName' 		=> $cardName,
			'date' 		 	=> $reg['date'],
			'period' 		=> $reg['period'],
			'create_time' 	=> $order['create_time'],
			'orderNum' 		=> $order['orderNum'],
			'price' 		=> $order['price'],
			'orderId' 		=> $order['orderId'],
		];
		Response::message(10000 , $response);

	}

    public function getWaitDetail(){
        $cardId= $this->getData('cardId');
        $params = [
            'cardId' 	=> $cardId,
        ];
        $hid = $this->hospitalId;
        //青海医院的就诊卡就是身份证号
        if($hid == 61756){
            $params['IdCardNo'] = $cardId;
        }
        $data 	= Server::ability('hospital')->getWaitDetail($hid , $params);
        if (!$data) { Response::message(10101); }
        if($data['code'] != 10000 && isset($data['msg'])){
            Response::message(10012,mb_substr($data['msg'],9,null,'utf8'));
        }
        if($data['data']){
            $waitJson = Db::name("service_config")->field("waiting")->where("project_id={$this->projectId}")->find();
            if($waitJson){
                $houzhen = json_decode($waitJson['waiting'] , true)['HouZhen'];
                $data['data']['HouZhen'] = $houzhen;
            }
        }
//        print_r($data);exit;
        Response::success($data['data']);
    }

    /**
     * 获取医院院区
     *
     * @access 	public
     * @return 	void
     */
    public function getHospitalDistrict()
    {
        $district = \app\equip\model\Hospital::getHospitalDistrict($this->hospitalId);
        $resonse = [];
        foreach ($district as $v){
            $tem = ['name'=>$v['name'],'districtId'=>$v['id'],'address'=>$v['address']];
            $resonse[] = $tem;
        }
        Response::success($resonse);
    }



	/**
	 * 取号结果查询
	 *
	 * @access 	public
	 * @return  void
	 */
	public function fetchQuery()
	{
		$orderNumber = $this->data['orderNum'];
		$hid 	= $this->hospitalId;
		$data 	= Loader::model('Order')->detailByNumber($orderNumber);
		if (!$data || $data['type'] != 3 || $data['project_id'] != $this->projectId) {
			Response::message(30011);
		}
		if ($data['status'] <= 0) {
			Response::message(10000 , [
				'orderNum' 	=> $orderNumber,
				'payStatus' => $data['status'],
			]);
		}
		$orderId 	= $data['id'];
		$orderPay 	= Loader::model('Order')->payDetail($orderId);
		$fetch 		= Loader::model('Registration')->fetchByOrderId($orderId);
		$successInfo 	= json_decode($fetch['success_info'] , true);
		$fetchInfo  	= json_decode($fetch['business_info'] , true);
		$response 		= [
			'orderNum' 	=> $orderNumber,
			'payStatus' => $data['status'],
			'cardId' 	=> $fetch['card_id'],
			'cardName' 	=> $fetch['card_name'],
			'doctorName'=> $fetchInfo['doctorName'],
			'deptName' 	=> $fetchInfo['deptName'],
			'date' 		=> $fetchInfo['date'],
			'period' 	=> $fetchInfo['period'],
			//'weekDay' 	=> $fetchInfo['weekDay'],
			'fee' 		=> $fetchInfo['fee'],
			'payType' 	=> $orderPay['pay_type'],
			'payPrice' 	=> $orderPay['price'],
			'transactionId' => $orderPay['transaction_id'],
		];
		if ($data['status'] == 1) {
			$response['status'] = $fetch['status'];
			if ($fetch['status'] == 1) {
				$response['queueNo'] = isset($successInfo['queueNo']) ? $successInfo['queueNo'] : '';
				$response['LocInfo'] = isset($successInfo['LocInfo']) ? $successInfo['LocInfo'] : '';
			}
		}
		Response::message(10000 , $response);
		
	}

    /**
     * 限制华二挂号时间
     * @param $date
     * @param $deptId
     */
	protected function limitRegister($date , $deptId){
        //针对华二做挂号限制
        if($this->hospitalId == 10000){
            $today = date('Ymd');
            $toTime = date('His');
            if(!isset($date[$today])) return $date;
            if($deptId == 15){
                if($toTime >= 173000){
                    if( $date[$today]['period']){
                        $date[$today]['period'] = array_flip($date[$today]['period']);
                        unset($date[$today]['period']['pm']);
                        unset($date[$today]['period']['am']);
                        $date[$today]['period'] = array_flip($date[$today]['period']);
                    }
                }
            }else{
                if($toTime < 120000){
                    $timeInterval = 'am';
                    if($toTime >= 93000){
                        if($date[$today]['period']) {
                            if (in_array($timeInterval, $date[$today]['period'])) unset($date[$today]['period'][0]);
                        }
                    }
                }else{
                    $timeInterval = 'pm';
                    if($toTime >= 140000){
                        if($date[$today]['period']) {
                            if (in_array($timeInterval, $date[$today]['period'])) {
                                $date[$today]['period'] = array_flip($date[$today]['period']);
                                unset($date[$today]['period']['pm']);
                                unset($date[$today]['period']['am']);
                                $date[$today]['period'] = array_flip($date[$today]['period']);
                            }
                        }
                    }
                }
            }
        }
        return $date;
    }


    protected function getWeekDays($start_date,$end_date){
	    $startDate   =   strtotime($start_date);
	    $endDate     =   strtotime($end_date);
        if ( $startDate> $endDate) list($startDate, $endDate) = array($endDate, $startDate);
        $time = $startDate;
        $weekDays = [];
        for($time ; $time <= $endDate ;$time += 86400){
            $weekDay = date('w',$time);
            $weekDayArr = [6,0];
            if(in_array($weekDay,$weekDayArr)){
                $weekDays[] = date('Ymd',$time);
            }
        }
        return $weekDays;
    }

}