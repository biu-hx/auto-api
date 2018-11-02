<?php

namespace app\component\server\ability;

use app\component\Curl;
use app\component\Log;
use app\component\response\Response;
use app\component\server\ability\Route;

class Hospital
{	

	private static $hospital;

	private $url;

	private $token;

	private $version 	= '1';

	private $config 	= [];

	private static $route 	= [
		'patientCard' 			=> ['/hospital/card' , 'get'],
		'dutyDept' 				=> ['/hospital/reg/duty_dept' , 'get'], ///hospital/registration/dutyDept ///hospital/reg/duty_dept
		'dutyDoctor' 			=> ['/hospital/reg/duty_doctor' , 'get'],
		'doctorSchedule' 		=> ['/hospital/reg/doctor_schedule' , 'get'],
		'doctorSchedulePeriod' 	=> ['/hospital/reg/schedule_period' , 'get'],
		'doctorScheduleTime' 	=> ['/hospital/reg/schedule_period' , 'get'],
		'lockSchedule' 			=> ['/hospital/reg/lock_schedule' , 'post'],
		'addRegQuery' 			=> ['/hospital/reg/add_reg_query' , 'get'],
		'getWaitDetail' 		=> ['/hospital/reg/get_reg_wait' , 'get'],
		'addRegOrder' 			=> ['/hospital/reg/add_reg_order' , 'post'],
		'outpatientInfo' 		=> ['/hospital/out_pat_pay/doctor_advice' , 'get'],
		'outpatientPrescription'=> ['/hospital/out_pat_pay/prescription' , 'get'],
		'inpatientInfo' 		=> ['/hospital/in_pat_pay/patient_info' , 'get'],
		'inpatientOrder' 		=> ['/hospital/in_pat_pay/order' , 'post'],
		'inspect' 				=> ['/hospital/report/inspect' , 'get'],
		'examine' 				=> ['/hospital/report/examine' , 'get'],
		'cancel' 				=> ['/hospital/reg/cancel_reg' , 'post'],
		'inspectDetail' 		=> ['/hospital/report/inspect_detail' , 'get'],
		'examineDetail' 		=> ['/hospital/report/examine_detail' , 'get'],
		'searchInpatientCloud' 	=> ['/hospital/in_pat_pay/patient_info' , 'get'],
		'searchInpatientDetailCloud' 	=> ['/hospital/in_pat_pay/fee_list' , 'get'],
        'searchPhysicalCard'    => ['/hospital/transfer/index' , 'get'],
        'addPhysicalCard'       => ['/hospital/transfer/index' , 'get'],
        'getCareer'             => ['/hospital/transfer/index' , 'get'],
        'searchReport'          => ['/hospital/transfer/index' , 'get'],
        'releaseSource'         => ['/hospital/transfer/index' , 'get'],
	];

	function __construct($config)
	{
		$config = (array) $config; 		//强制config为数组
		$this->config = $config;
		if (!isset($config['url']) || !preg_match('/^((https|http|ftp|rtsp|mms)?:\/\/)[^\s]+/' , $config['url'])) {
			throw new \Exception('error url');	
		}
		if (!isset($config['token'])) {
			throw new \Exception('error token');	
		}
		$this->url 		= $config['url'];
		$this->token 	= $config['token'];
		isset($config['version']) && $this->version = $config['version'];
	}


	/**
	 * 路由规则问题
	 *
	 * @access 	private
	 * @param 	string 	$method 		请求方法
	 * @param 	string 	$hosId 			请求的医院
	 * @return 	string|boolen
	 */
	private function route($method , $hosId)
	{
		return Route::rule($hosId , $method);	
	}

	/**
	 * 调用其他文件的方法
	 *
	 * @access 	private
	 * @param 	string 	$method 		请求方法
	 * @param 	int 	$hosId 			医院ID
	 * @param 	array 	$data 			请求的参数
	 * @return 	array|boolen
	 */

	private function calling($method , $hosId , $data)
	{
//		$route 	= self::route($method , $hosId);
//		if ($route === false) return false;
        if(!method_exists($this,$method)){
//		if (!self::$hospital[$hosId]) {
			$class 	= '\app\component\server\ability\hospital\Hospital'.$hosId;
			self::$hospital[$hosId] = new $class($this->config);
            if(!method_exists(self::$hospital[$hosId],$method)){
                Response::errorMessage($method.'方法不存在');
            }
		}else{
            return false;
        }
		$data 	= self::$hospital[$hosId]->$method($data);
		return $data; 
	}

	/**
	 * 魔术方法，调用不存在的类
	 *
	 * @access 	private
	 * @param 	string 	$method 		请求方法
	 * @param 	array 	$params 		请求的参数
	 * @return 	array|boolen
	 */
	public function __call($method , $params)
	{
		$hosId 	= $params[0];
		$data 	= $params[1];
		$result = self::calling($method , $hosId , $data);
		return $result;
	}

	/**
	 * 查询就诊卡
	 *
	 * @access 	public
	 * @param 	int 	$hosId 			医院ID 
	 * @param 	array  	$data 			数据组
	 * ---------------------------------------
	 *  		string 	$IdCardNo 		身份证号（与就诊卡号至少传其一）
	 * 			string 	$cardId 		就诊卡号（与身份证号至少传其一）
	 * 			string 	$userName 		患者姓名（可传空值）
	 * 			string 	$phone 			手机号码（可传空值）
	 * 			string 	$type 			就诊卡类型（可传空值）
	 * ---------------------------------------
	 * @return 	array
	 */
	public function patientCard($hosId , $data)
	{
		$result = self::calling(__FUNCTION__ , $hosId , $data);
		if ($result !== false) { return $result; } 	 	//检测是否有单独配置的访问情况 
		$params 	= [
			'hosId' 	=> $hosId,
		];
		isset($data['IdCardNo']) && $data['IdCardNo'] && $params['IdCardNo'] = $data['IdCardNo'];
		isset($data['cardId']) && $data['cardId'] && $params['cardId'] = $data['cardId'];
		isset($data['userName']) && $data['userName'] && $params['userName'] = $data['userName'];
		isset($data['phone']) && $data['phone'] && $params['phone'] = $data['phone'];
		isset($data['type']) && $data['type'] && $params['type'] = $data['type'];
		$data 	= $this->request(__FUNCTION__ , $params);
		return $data;
	}

	/** 
	 * 查询排班科室
	 *
	 * @access 	public
	 * @param 	int 	$hosId 			医院ID 
	 * @param 	array  	$data 			数据组
	 * ---------------------------------------
	 * 	 		string 	$date 			日期（可传空值）
	 * 			srting 	$period 		时段（可传空值）
	 * ---------------------------------------
	 * @return  array
	 */
	public function dutyDept($hosId , $data = [])
	{
		$result = self::calling(__FUNCTION__ , $hosId , $data);
		if ($result !== false) { return $result; } 	 	//检测是否有单独配置的访问情况 
		$params 	= [
			'hosId' 	=> $hosId,
            'districtId' 	=> isset($data['districtId'])?$data['districtId']:0,

		];
		isset($data['date']) && $data['date'] && $params['date'] = $data['date'];
		isset($data['period']) && $data['period'] && $params['period'] = $data['period'];
		$data 	= $this->request(__FUNCTION__ , $params);
		return $data;
	}

    /**
     * 取消挂号
     *
     * @access 	public
     * @param 	int 	$hosId 			医院ID
     * @param 	array  	$data 			数据组
     * ---------------------------------------
     * 	 		string 	$date 			日期（可传空值）
     * 			srting 	$period 		时段（可传空值）
     * ---------------------------------------
     * @return  array
     */
    public function cancel($hosId , $data = [])
    {
        $result = self::calling(__FUNCTION__ , $hosId , $data);
        if ($result !== false) { return $result; } 	 	//检测是否有单独配置的访问情况
        $params 	= [
            'hosId' 	=> $hosId,
        ];
        isset($data['transactionId']) && $data['transactionId'] && $params['transactionId'] = $data['transactionId'];
        isset($data['reason']) && $data['reason'] && $params['reason'] = $data['reason'];
        isset($data['invoiceNo']) && $data['invoiceNo'] && $params['invoiceNo'] = $data['invoiceNo'];
        isset($data['cardNo']) && $data['cardNo'] && $params['cardNo'] = $data['cardNo'];
        isset($data['IdCardNo']) && $data['IdCardNo'] && $params['IdCardNo'] = $data['IdCardNo'];
        isset($data['phone']) && $data['phone'] && $params['phone'] = $data['phone'];
        isset($data['patientId']) && $data['patientId'] && $params['patientId'] = $data['patientId'];
        isset($data['admId']) && $data['admId'] && $params['admId'] = $data['admId'];
        isset($data['serNo']) && $data['serNo'] && $params['serNo'] = $data['serNo'];
        isset($data['dutyDocId']) && $data['dutyDocId'] && $params['dutyDocId'] = $data['dutyDocId'];
        isset($data['cancelType']) && $data['cancelType'] && $params['cancelType'] = $data['cancelType'];
        isset($data['socialSecurNo']) && $data['socialSecurNo'] && $params['socialSecurNo'] = $data['socialSecurNo'];
        isset($data['sumFee']) && $data['sumFee'] && $params['sumFee'] = $data['sumFee'];
        $data 	= $this->request(__FUNCTION__ , $params);
        return $data;
    }

	/**
	 * 查询排班医生
	 * 
	 * @access 	public
	 * @param 	int 	$hosId 			医院ID 
	 * @param 	array  	$data 			数据组
	 * ---------------------------------------
	 *  		string 	$dateId 		科室ID
	 * 	 		string 	$date 			日期（可传空值）
	 * 			srting 	$period 		时段（可传空值）
	 * ---------------------------------------
	 * @return  array
	 */
	public function dutyDoctor($hosId , $data)
	{
		$result = self::calling(__FUNCTION__ , $hosId , $data);
		if ($result !== false) { return $result; } 	 	//检测是否有单独配置的访问情况 
		$params 	= [
			'hosId' 	=> $hosId,
            'districtId' 	=> isset($data['districtId'])?$data['districtId']:0,
		];
		if(isset($data['deptId'])){
            $params['deptId'] = $data['deptId'];
        }
		isset($data['date']) && $data['date'] && $params['date'] = $data['date'];
		isset($data['period']) && $data['period'] && $params['period'] = $data['period'];
		$data 	= $this->request(__FUNCTION__ , $params);
		return $data;
	}

	/**
	 * 查询医生号源
	 * 
	 * @access 	public
	 * @param 	int 	$hosId 			医院ID 
	 * @param 	array  	$data 			数据组
	 * ---------------------------------------
	 *  		string 	$dateId 		科室ID
	 * 			string 	$doctorId 		医生ID
	 * 	 		string 	$date 			日期（可传空值）
	 * 			srting 	$period 		时段（可传空值）
	 * ---------------------------------------
	 * @return  array
	 */
	public function doctorSchedule($hosId , $data)
	{
		$result = self::calling(__FUNCTION__ , $hosId , $data);
		if ($result !== false) { return $result; } 	 	//检测是否有单独配置的访问情况 
		$params 	= [
			'hosId' 	=> $hosId,
			'deptId' 	=> $data['deptId'],
			'doctorId' 	=> $data['doctorId'],
            'districtId' 	=> isset($data['districtId'])?$data['districtId']:0,
		];
		isset($data['date']) && $data['date'] && $params['date'] = $data['date'];
		isset($data['period']) && $data['period'] && $params['period'] = $data['period'];
		isset($data['scheduleId']) && $data['scheduleId'] && $params['scheduleId'] = $data['scheduleId'];
		$data 	= $this->request(__FUNCTION__ , $params);
		return $data;
	}

	/**
	 * 查询号源
	 * 
	 * @access 	public
	 * @param 	int 	$hosId 			医院ID 
	 * @param 	string 	$date 			日期
	 * @param 	string  $period 		时段 
	 * @return  array
	 */
	public function doctorSchedulePeriod($hosId , $data)
	{
		$result = self::calling(__FUNCTION__ , $hosId , $data);
		if ($result !== false) { return $result; } 	 	//检测是否有单独配置的访问情况 
		$params 	= [
			'hosId' 		=> $hosId,
			'scheduleId' 	=> $data['scheduleId'],
			'period' 		=> $data['period'],
		];
		$data 	= $this->request(__FUNCTION__ , $params);
		return $data;
	}

    /**
     * 查询时段
     *
     * @access 	public
     * @param 	int 	$hosId 			医院ID
     * @param 	string 	$date 			日期
     * @param 	string  $period 		时段
     * @return  array
     */
    public function doctorScheduleTime($hosId , $data)
    {
        $result = self::calling(__FUNCTION__ , $hosId , $data);
        if ($result !== false) { return $result; } 	 	//检测是否有单独配置的访问情况
        $params 	= [
            'hosId' 		=> $hosId,
            'scheduleId' 	=> $data['scheduleId'],
            'period' 		=> $data['period'],
        ];
        $data 	= $this->request(__FUNCTION__ , $params);
        return $data;
    }



	/**
	 * 锁号执行
	 * 
	 * @access 	public
	 * @param 	int 	$hosId 			医院ID
	 * @param 	array  	$data 			数据组
	 * ---------------------------------------
	 *  		string 	$scheduleId 	排班ID
	 *  		string 	$period 		时段
	 * 			string 	$cardId 		就诊卡号  
	 *  		string 	$IdCardNo 		身份证号（可传空值）
	 * 			string 	$queueNo 		分时序号（可传空值）	
	 * 			string 	$userName 		患者姓名（可传空值）	
	 * 			string 	$phone 			患者电话（可传空值）	
	 * 			string 	$uniqueId 		病人id（可传空值）	
	 * 			string 	$shiftName 		挂号时段名称（可传空值）	
	 * 			string 	$periodCode 	挂号时段id（可传空值）	
	 * ---------------------------------------
	 * @return 	array
	 */
	public function lockSchedule($hosId , $data)
	{
		$result = self::calling(__FUNCTION__ , $hosId , $data);
		if ($result !== false) { return $result; } 	 	//检测是否有单独配置的访问情况 
		$params 	= [
			'hosId' 		=> $hosId,
			'scheduleId' 	=> $data['scheduleId'],
			'period' 		=> $data['period'],
			'IdCardNo' 		=> (isset($data['IdCardNo']) && $data['IdCardNo']) ? $data['IdCardNo'] : '',
			'cardId' 		=> $data['cardId'],
            'districtId' 	=> isset($data['districtId'])?$data['districtId']:0,
		];
		isset($data['queueNo']) && $data['queueNo'] && $params['queueNo'] = $data['queueNo'];
		isset($data['userName']) && $data['userName'] && $params['userName'] = $data['userName'];
		isset($data['phone']) && $data['phone'] && $params['phone'] = $data['phone'];
		isset($data['uniqueId']) && $data['uniqueId'] && $params['uniqueId'] = $data['uniqueId'];
		isset($data['shiftName']) && $data['shiftName'] && $params['shiftName'] = $data['shiftName'];
		isset($data['periodCode']) && $data['periodCode'] && $params['periodCode'] = $data['periodCode'];
		isset($data['beginTime']) && $data['beginTime'] && $params['periodStart'] = $data['beginTime'];
		isset($data['endTime']) && $data['endTime'] && $params['periodEnd'] = $data['endTime'];
		$data 	= $this->request(__FUNCTION__ , $params);
		return $data;
	}


	/**
	 * 取号列表
	 *
	 * @access 	public
	 * @param 	int 	$hosId 			医院ID
	 * @param 	array  	$data 			数据组
	 * ---------------------------------------
	 *  		string 	$cardNo 		就诊卡号
	 * ---------------------------------------
	 * @return 	array	
	 */
	public function addRegQuery($hosId , $data)
	{
		$result = self::calling(__FUNCTION__ , $hosId , $data);
		if ($result !== false) { return $result; } 	 	//检测是否有单独配置的访问情况 
		$params 	= [
			'hosId' 	=> $hosId,
			'cardNo' 	=> $data['cardNo'],
		];
		$data 	= $this->request(__FUNCTION__ , $params);
		return $data;
	}

    /**
     * 候诊数据
     *
     * @access 	public
     * @param 	int 	$hosId 			医院ID
     * @param 	array  	$data 			数据组
     * ---------------------------------------
     *  		string 	$cardNo 		就诊卡号
     * ---------------------------------------
     * @return 	array
     */
    public function getWaitDetail($hosId , $data)
    {
        $params 	= [
            'hosId' 	=> $hosId,
            'flag' 		=> 'waitDetail',
            'content' 	=> '<Request><CardNo>'.$data['cardId'].'</CardNo></Request>',
        ];
        $data 	= $this->request(__FUNCTION__ , $params);
        return $data;
    }

	/**
	 * 取号下单
	 *
	 * @access 	public
	 * @param 	int 	$hosId 			医院ID
	 * @param 	array  	$data 			数据组
	 * ---------------------------------------
	 *  		string 	$cardNo 		就诊卡号
	 *  		string 	$scheduleCode 	排班编号
	 *  		string 	$fetchType 		取号类型（1：加号，2：114）
	 *  		string 	$date 			就诊日期
	 *  		string 	$fee 			挂号费
	 *  		string 	$period 		就诊时段（可传空值）	
	 *  		string 	$queueNo 		就诊序号（可传空值）	
	 *  		string 	$deptHisId 		科室id（可传空值）	
	 *  		string 	$deptName 		科室名称（可传空值）	
	 *  		string 	$doctorHisId 	医生id（可传空值）	
	 *  		string 	$doctorName 	医生姓名（可传空值）	
	 *  		string 	$patName 		病人姓名（可传空值）	
	 * ---------------------------------------
	 * @return 	array	
	 */
	public function addRegOrder($hosId , $data)
	{
		$result = self::calling(__FUNCTION__ , $hosId , $data);
		if ($result !== false) { return $result; } 	 	//检测是否有单独配置的访问情况 
		$params 	= [
			'hosId' 		=> $hosId,
			'cardNo' 		=> $data['cardNo'],
			'scheduleCode'	=> $data['scheduleCode'],
			'fetchType' 	=> $data['fetchType'],
			'date' 			=> $data['date'],
			'fee' 			=> $data['fee'],
		];
		isset($data['period']) && $data['period'] && $params['period'] = $data['period'];
		isset($data['queueNo']) && $data['queueNo'] && $params['queueNo'] = $data['queueNo'];
		isset($data['deptHisId']) && $data['deptHisId'] && $params['deptHisId'] = $data['deptHisId'];
		isset($data['deptName']) && $data['deptName'] && $params['deptName'] = $data['deptName'];
		isset($data['doctorHisId']) && $data['doctorHisId'] && $params['doctorHisId'] = $data['doctorHisId'];
		isset($data['doctorName']) && $data['doctorName'] && $params['doctorName'] = $data['doctorName'];
		isset($data['patName']) && $data['patName'] && $params['patName'] = $data['patName'];
		$data 	= $this->request(__FUNCTION__ , $params);
		return $data;
	}


	/**
	 * 门诊缴费列表
	 *
	 * @access 	public
	 * @param 	int 	$hosId 			医院ID
	 * @param 	array  	$data 			数据组
	 * ---------------------------------------
	 *  		string 	$cardNo 		就诊卡号
	 *  		string 	$IdCardNo 		身份证号（可传空值）
	 *  		string 	$date  			日期（可传空值）
	 * 			string 	$userName 		患者姓名（可传空值）
	 * 			string 	$phone 			患者电话（可传空值）
	 * 			string 	$uniqueId 		病人id（可传空值）
	 * ---------------------------------------
	 * @return 	array	
	 */
	public function outpatientInfo($hosId , $data)
	{
		$result = self::calling(__FUNCTION__ , $hosId , $data);
		if ($result !== false) { return $result; } 	 	//检测是否有单独配置的访问情况 
		$params 	= [
			'hosId' 	=> $hosId,
			'cardNo' 	=> $data['cardNo'],
			'IdCardNo' 	=> (isset($data['IdCardNo']) && $data['IdCardNo']) ? $data['IdCardNo'] : '',
			'date' 		=> (isset($data['date']) && $data['date']) ? $data['date'] : '',
			'userName' 	=> (isset($data['userName']) && $data['userName']) ? $data['userName'] : '',
			'phone' 	=> (isset($data['phone']) && $data['phone']) ? $data['phone'] : '',
			'uniqueId' 	=> (isset($data['uniqueId']) && $data['uniqueId']) ? $data['uniqueId'] : '',
		];
		$data 	= $this->request(__FUNCTION__ , $params);
		return $data;
	}
	
	/**
	 * 门诊缴费详情
	 *
	 * @access 	public
	 * @param 	int 	$hosId 			医院ID
	 * @param 	array  	$data 			数据组
	 * ---------------------------------------
	 *  		string 	$cardNo 		就诊卡号
	 *  		string 	$IdCardNo 		身份证号（可传空值）
	 *  		string 	$orderNum  		his订单号（可传空值）
	 *  		string 	$recipeId  		医嘱编号(可传空值，如华西)
	 *  		string 	$date  			日期（可传空值）
	 * 			string 	$userName 		患者姓名（可传空值）
	 * 			string 	$phone 			患者电话（可传空值）
	 * 			string 	$uniqueId 		病人id（可传空值）
	 * ---------------------------------------
	 * @return 	array	
	 */
	public function outpatientPrescription($hosId , $data)
	{
		$result = self::calling(__FUNCTION__ , $hosId , $data);
		if ($result !== false) { return $result; } 	 	//检测是否有单独配置的访问情况 
		$params 	= [
			'hosId' 	=> $hosId,
			'cardNo' 	=> $data['cardNo'],
			'IdCardNo' 	=> (isset($data['IdCardNo']) && $data['IdCardNo']) ? $data['IdCardNo'] : '',
			'orderNum' 	=> (isset($data['orderNum']) && $data['orderNum']) ? $data['orderNum'] : '',
			'recipeId' 	=> (isset($data['recipeId']) && $data['recipeId']) ? $data['recipeId'] : '',
			'date' 		=> (isset($data['date']) && $data['date']) ? $data['date'] : '',
			'userName' 	=> (isset($data['userName']) && $data['userName']) ? $data['userName'] : '',
			'phone' 	=> (isset($data['phone']) && $data['phone']) ? $data['phone'] : '',
			'uniqueId' 	=> (isset($data['uniqueId']) && $data['uniqueId']) ? $data['uniqueId'] : '',
		];
		$data 	= $this->request(__FUNCTION__ , $params);
		return $data;
	}

	/**
	 * 住院信息
	 *
	 * @access 	public
	 * @param 	int 	$hosId 			医院ID
	 * @param 	array  	$data 			数据组
	 * ---------------------------------------
	 *  		string 	$cardNo 		就诊卡号
	 *  		string 	$IdCardNo 		身份证号（可传空值）
	 *  		string 	$inpatientId  	住院号（可传空值）
	 * 			string 	$userName 		患者姓名（可传空值）
	 * 			string 	$uniqueId 		病人id（可传空值）
	 * ---------------------------------------
	 * @return 	array	
	 */
	public function inpatientInfo($hosId , $data)
	{
		$result = self::calling(__FUNCTION__ , $hosId , $data);
		if ($result !== false) { return $result; } 	 	//检测是否有单独配置的访问情况 
		$params 	= [
			'hosId' 		=> $hosId,
			'cardNo' 		=> isset($data['cardNo']) ? $data['cardNo'] : '',
			'IdCardNo' 		=> (isset($data['IdCardNo']) && $data['IdCardNo']) ? $data['IdCardNo'] : '',
			'inpatientId' 	=> (isset($data['inpatientId']) && $data['inpatientId']) ? $data['inpatientId'] : '',
			'userName' 		=> (isset($data['userName']) && $data['userName']) ? $data['userName'] : '',
			'uniqueId' 		=> (isset($data['uniqueId']) && $data['uniqueId']) ? $data['uniqueId'] : '',
		];
		$data 	= $this->request(__FUNCTION__ , $params);
		return $data;
	}

	/**
	 * 住院缴费下单
	 *
	 * @access 	public
	 * @param 	int 	$hosId 			医院ID
	 * @param 	array  	$data 			数据组
	 * ---------------------------------------
	 *  		string 	$cardNo 		就诊卡号
	 *  		string 	$fee 			预缴金额
	 *  		string 	$treatNo  		住院号
	 *  		string 	$patientName  	患者姓名（可传空值）
	 *  		string 	$uniqueId  		病人id（可传空值）
	 * 			string 	$identyId 		身份证号（可传空值）
	 * 			string 	$inpatientId 	住院流水号（可传空值）
	 * 			string 	$orderNo 		his订单号（可传空值）
	 * ---------------------------------------
	 * @return 	array	
	 */
	public function inpatientOrder($hosId , $data)
	{
		$result = self::calling(__FUNCTION__ , $hosId , $data);
		if ($result !== false) { return $result; } 	 	//检测是否有单独配置的访问情况 
		$params 	= [
			'hosId' 	=> $hosId,
			'cardNo' 	=> isset($data['cardNo']) ? $data['cardNo'] : '',
			'fee' 		=> $data['fee'],
			'treatNo' 	=> $data['treatNo'],
			'patientName' 	=> (isset($data['patientName']) && $data['patientName']) ? $data['patientName'] : '',
			'uniqueId' 		=> (isset($data['uniqueId']) && $data['uniqueId']) ? $data['uniqueId'] : '',
			'identyId' 		=> (isset($data['identyId']) && $data['identyId']) ? $data['identyId'] : '',
			'inpatientId' 	=> (isset($data['inpatientId']) && $data['inpatientId']) ? $data['inpatientId'] : '',
			'orderNo' 		=> (isset($data['orderNo']) && $data['orderNo']) ? $data['orderNo'] : '',
		];
		$data 	= $this->request(__FUNCTION__ , $params);
		return $data;
	}

	/**
	 * 检查列表
	 *
	 * @access 	public
	 * @param 	int 	$hosId 			医院ID
	 * @param 	array  	$data 			数据组
	 * ---------------------------------------
	 *  		string 	$cardNo 		就诊卡号
	 *  		string 	$userName 		患者姓名（可传空值）
	 *  		string 	$idCardNo 		身份证号（可传空值）
	 *  		string 	$barCode 		条码号（可传空值）
	 *  		string 	$uniqueId 		病人id（可传空值）
	 *  		string 	$beginTime 		开始时间（可传空值）
	 *  		string 	$endTime 		结束时间（可传空值）
	 * ---------------------------------------
	 * @return 	array	
	 */
	public function inspect($hosId , $data)
	{
		$result = self::calling(__FUNCTION__ , $hosId , $data);
		if ($result !== false) { return $result; } 	 	//检测是否有单独配置的访问情况 
		$params 	= [
			'hosId' 	=> $hosId,
			'cardNo' 	=> $data['cardNo'],
		];
		isset($data['userName']) && $data['userName'] && $params['userName'] = $data['userName'];
		isset($data['idCardNo']) && $data['idCardNo'] && $params['idCardNo'] = $data['idCardNo'];
		isset($data['barCode']) && $data['barCode'] && $params['barCode'] = $data['barCode'];
		isset($data['uniqueId']) && $data['uniqueId'] && $params['uniqueId'] = $data['uniqueId'];
		isset($data['beginTime']) && $data['beginTime'] && $params['beginTime'] = $data['beginTime'];
		isset($data['endTime']) && $data['endTime'] && $params['endTime'] = $data['endTime'];
		$data 	= $this->request(__FUNCTION__ , $params);
		return $data;
	}

	/**
	 * 检查详情
	 *
	 * @access 	public
	 * @param 	int 	$hosId 			医院ID
	 * @param 	array  	$data 			数据组
	 * ---------------------------------------
	 *  		string 	$cardNo 		就诊卡号
	 *  		string 	$inspecNo 		检验结果编号（可传空值）
	 *  		string 	$idCardNo 		身份证号（可传空值）
	 *  		string 	$barCode 		条码号（可传空值）
	 *  		string 	$uniqueId 		病人id（可传空值）
	 *  		string 	$beginTime 		开始时间（可传空值）
	 *  		string 	$endTime 		结束时间（可传空值）
	 *  		string 	$archiveDate 	归库时间（可传空值）
	 * ---------------------------------------
	 * @return 	array	
	 */
	public function inspectDetail($hosId , $data)
	{
		$result = self::calling(__FUNCTION__ , $hosId , $data);
		if ($result !== false) { return $result; } 	 	//检测是否有单独配置的访问情况 
		$params 	= [
			'hosId' 	=> $hosId,
			'cardNo' 	=> $data['cardNo'],
		];
		isset($data['inspecNo']) && $data['inspecNo'] && $params['inspecNo'] = $data['inspecNo'];
		isset($data['idCardNo']) && $data['idCardNo'] && $params['idCardNo'] = $data['idCardNo'];
		isset($data['barCode']) && $data['barCode'] && $params['barCode'] = $data['barCode'];
		isset($data['uniqueId']) && $data['uniqueId'] && $params['uniqueId'] = $data['uniqueId'];
		isset($data['beginTime']) && $data['beginTime'] && $params['beginTime'] = $data['beginTime'];
		isset($data['endTime']) && $data['endTime'] && $params['endTime'] = $data['endTime'];
		isset($data['archiveDate']) && $data['archiveDate'] && $params['archiveDate'] = $data['archiveDate'];
		$data 	= $this->request(__FUNCTION__ , $params);
		return $data;
	}

	/**
	 * 检验列表
	 *
	 * @access 	public
	 * @param 	int 	$hosId 			医院ID
	 * @param 	array  	$data 			数据组
	 * ---------------------------------------
	 *  		string 	$cardNo 		就诊卡号
	 *  		string 	$userName 		患者姓名（可传空值）
	 *  		string 	$idCardNo 		身份证号（可传空值）
	 *  		string 	$barCode 		条码号（可传空值）
	 *  		string 	$uniqueId 		病人id（可传空值）
	 *  		string 	$beginTime 		开始时间（可传空值）
	 *  		string 	$endTime 		结束时间（可传空值）
	 * ---------------------------------------
	 * @return 	array	
	 */
	public function examine($hosId , $data)
	{
		$result = self::calling(__FUNCTION__ , $hosId , $data);
		if ($result !== false) { return $result; } 	 	//检测是否有单独配置的访问情况 
		$params 	= [
			'hosId' 	=> $hosId,
			'cardNo' 	=> $data['cardNo'],
		];
		isset($data['userName']) && $data['userName'] && $params['userName'] = $data['userName'];
		isset($data['idCardNo']) && $data['idCardNo'] && $params['idCardNo'] = $data['idCardNo'];
		isset($data['barCode']) && $data['barCode'] && $params['barCode'] = $data['barCode'];
		isset($data['uniqueId']) && $data['uniqueId'] && $params['uniqueId'] = $data['uniqueId'];
		isset($data['beginTime']) && $data['beginTime'] && $params['beginTime'] = $data['beginTime'];
		isset($data['endTime']) && $data['endTime'] && $params['endTime'] = $data['endTime'];
		$data 	= $this->request(__FUNCTION__ , $params);
		return $data;
	}

	/**
	 * 检验详情
	 *
	 * @access 	public
	 * @param 	int 	$hosId 			医院ID
	 * @param 	array  	$data 			数据组
	 * ---------------------------------------
	 *  		string 	$cardNo 		就诊卡号
	 *  		string 	$inspecNo 		检验结果编号（可传空值）
	 *  		string 	$idCardNo 		身份证号（可传空值）
	 *  		string 	$barCode 		条码号（可传空值）
	 *  		string 	$uniqueId 		病人id（可传空值）
	 *  		string 	$beginTime 		开始时间（可传空值）
	 *  		string 	$endTime 		结束时间（可传空值）
	 *  		string 	$archiveDate 	归库时间（可传空值）
	 *  		string 	$devCode 		设备号(可传空值）
	 * ---------------------------------------
	 * @return 	array	
	 */
	public function examineDetail($hosId , $data)
	{
		$result = self::calling(__FUNCTION__ , $hosId , $data);
		if ($result !== false) { return $result; } 	 	//检测是否有单独配置的访问情况 
		$params 	= [
			'hosId' 	=> $hosId,
			'cardNo' 	=> $data['cardNo'],
		];
		isset($data['inspecNo']) && $data['inspecNo'] && $params['inspecNo'] = $data['inspecNo'];
		isset($data['idCardNo']) && $data['idCardNo'] && $params['idCardNo'] = $data['idCardNo'];
		isset($data['barCode']) && $data['barCode'] && $params['barCode'] = $data['barCode'];
		isset($data['uniqueId']) && $data['uniqueId'] && $params['uniqueId'] = $data['uniqueId'];
		isset($data['beginTime']) && $data['beginTime'] && $params['beginTime'] = $data['beginTime'];
		isset($data['endTime']) && $data['endTime'] && $params['endTime'] = $data['endTime'];
		isset($data['archiveDate']) && $data['archiveDate'] && $params['archiveDate'] = $data['archiveDate'];
		isset($data['devCode']) && $data['devCode'] && $params['devCode'] = $data['devCode'];
		$data 	= $this->request(__FUNCTION__ , $params);
		return $data;
	}

    /**
     * 查询住院列表
     *
     * @access 	public
     * @param 	int 	$hosId 			医院ID
     * @param 	array  	$data 			数据组
     * ---------------------------------------
     * 	 		string 	$cardNo 		就诊卡号
     * ---------------------------------------
     * @return  array
     */
    public function searchInpatientCloud($hosId , $data)
    {
        $params 	= [
            'hosId' 	    => $hosId,
            'cardNo' 		=> isset($data['cardNo']) ? $data['cardNo'] : '',
            'uniqueId' 		=> isset($data['uniqueId']) ? $data['uniqueId'] : '',
            'inpatientId' 	=> isset($data['inpatientId']) ? $data['inpatientId'] : '',
        ];
        $data 	= $this->request(__FUNCTION__ , $params);
        return $data;
    }

    /**
     * 查询住院列表
     *
     * @access 	public
     * @param 	int 	$hosId 			医院ID
     * @param 	array  	$data 			数据组
     * ---------------------------------------
     * 	 		string 	$cardNo 		就诊卡号
     * ---------------------------------------
     * @return  array
     */
    public function searchInpatientDetailCloud($hosId , $data)
    {
        $params 	= [
            'hosId' 	    => $hosId,
            'cardNo' 		=> isset($data['cardNo']) ? $data['cardNo'] : '',
            'uniqueId' 		=> isset($data['uniqueId']) ? $data['uniqueId'] : '',
            'beginTime' 	=> isset($data['beginTime']) ? $data['beginTime'] : '',
            'inpatientId' 	=> isset($data['inpatientId']) ? $data['inpatientId'] : '',
        ];
        $data 	= $this->request(__FUNCTION__ , $params);
        return $data;
    }

    /**
     * 查询住院类型
     *
     * @access 	public
     * @param 	int 	$hosId 			医院ID
     * @param 	array  	$data 			数据组
     * ---------------------------------------
     * 	 		string 	$admId 			住院ID
     * 	 		string 	$arpbl 			住院标识
     * ---------------------------------------
     * @return  array
     */
    public function searchInpatientType($data)
    {
        $params 	= [
            'hosId' 	=> 10000,
            'flag' 		=> 'FetchPatFeeInfog',
            'content' 	=> '<Request><AdmId>'.$data['admId'].'</AdmId><Arpbl>'.$data['arpbl'].'</Arpbl></Request>',
        ];
        $data 	= $this->request(__FUNCTION__ , $params);
        return $data;
    }

    /**
     * 实体就诊卡查询
     *
     * @access 	public
     * @param 	int 	$hosId 			医院ID
     * @param 	array  	$data 			数据组
     * ---------------------------------------
     *  		string 	$cardNo 		就诊卡号
     * ---------------------------------------
     * @return 	array
     */
    public function searchPhysicalCard($hosId , $data)
    {
        $content = array('Request'=>$data);
        $content_xml = Response::ToXml($content);
        $params 	= [
            'hosId' 	=> $hosId,
            'flag' 		=> 'queryUserCard',
            'content' 	=> $content_xml,
        ];
        $data 	= $this->request(__FUNCTION__ , $params);
        return $data;
    }

    /**
     *   注册实体就诊卡
     *
     * @access 	public
     * @param 	int 	$hosId 			医院ID
     * @param 	array  	$data 			数据组
     * ---------------------------------------
     *  		string 	$cardNo 		就诊卡号
     * ---------------------------------------
     * @return 	array
     */
    public function addPhysicalCard($hosId , $data)
    {
        $content = array('Request'=>$data);
        $content_xml = Response::ToXml($content);
        $params 	= [
            'hosId' 	=> $hosId,
            'flag' 		=> 'userRegister',
            'content' 	=> $content_xml,
        ];
        $data 	= $this->request(__FUNCTION__ , $params);
        return $data;
    }

    /**
     * 检验检查报告查询
     *
     * @access 	public
     * @param 	int 	$hosId 			医院ID
     * @param 	array  	$data 			数据组
     * ---------------------------------------
     *  		string 	$cardNo 		就诊卡号
     * ---------------------------------------
     * @return 	array
     */
    public function searchReport($hosId , $data)
    {
        $params 	= [
            'hosId' 	=> $hosId,
            'flag' 		=> 'waitDetail',
            'content' 	=> '<Request><CardNo>'.$data['cardId'].'</CardNo></Request>',
        ];
        $data 	= $this->request(__FUNCTION__ , $params);
        return $data;
    }

    /**
     * 获取职业
     *
     * @access 	public
     * @param 	int 	$hosId 			医院ID
     * @param 	array  	$data 			数据组
     * ---------------------------------------
     *  		string 	$cardNo 		就诊卡号
     * ---------------------------------------
     * @return 	array
     */
    public function getCareer($hosId , $data)
    {
        $params 	= [
            'hosId' 	=> $hosId,
            'flag' 		=> 'occupationType',
            'content' 	=> '<Request></Request>',
        ];
        $data 	= $this->request(__FUNCTION__ , $params);
        return $data;
    }

    /**
     * 释放号源
     *
     * @access 	public
     * @param 	int 	$hosId 			医院ID
     * @param 	array  	$data 			数据组
     * ---------------------------------------
     *  		string 	$cardNo 		就诊卡号
     * ---------------------------------------
     * @return 	array
     */
    public function releaseSource($hosId , $data)
    {
        $content = array('Request'=>$data);
        $content_xml = Response::ToXml($content);
        $params 	= [
            'hosId' 	=> $hosId,
            'flag' 		=> 'cancelRegistration',
            'content' 	=> $content_xml,
        ];
        $data 	= $this->request(__FUNCTION__ , $params);
        return $data;
    }

    /**
     * 查询住院详情
     *
     * @access 	public
     * @param 	int 	$hosId 			医院ID
     * @param 	array  	$data 			数据组
     * ---------------------------------------
     * 	 		string 	$arpbl 			住院标识
     * ---------------------------------------
     * @return  array
     */
    /**public function searchInpatientDetail($data)
    {
        $params 	= [
            'hosId' 	=> 10000,
            'flag' 		=> 'GetFymx',
            'content' 	=> '<Request><Arpbl>'.$data['arpbl'].'</Arpbl></Request>',
        ];
        $data 	= $this->request(__FUNCTION__ , $params);
        return $data;
    }*/

    /**
     * 释放号源
     *
     * @access 	public
     * @param 	int 	$hosId 			医院ID
     * @param 	array  	$data 			数据组
     * ---------------------------------------
     *  		string 	$cardNo 		就诊卡号
     * ---------------------------------------
     * @return 	array
     */
    /*public function releaseSource($hosId , $data)
    {
        $content = array('Request'=>$data);
        $content_xml = Response::ToXml($content);
        $params 	= [
            'hosId' 	=> $hosId,
            'flag' 		=> 'cancelRegistration',
            'content' 	=> $content_xml,
        ];
        $data 	= $this->request(__FUNCTION__ , $params);
        return $data;
    }*/

	/**
	 * 请求
	 *
	 * @access 	private
	 * @param 	string 	$method 请求调用的方法
	 * @param   array 	$params 请求的参数
	 * @return 	array
	 */
    private function request($method , $params)
    {
        Log::storageRequest('hospitalRequest_'.$method , $params);
        $params['token'] 	= $this->token;
        $data 	= [];
        $header = [
            'Content-Type:application/x-www-form-urlencoded;charset=utf-8',
            'api-version:'.$this->version,
        ];
        //获取医院信息
        $hosId = $params['hosId'];
        //获取医院信息
        $hosInfo = \app\equip\model\Hospital::detail($hosId);
        //如果有院区，则获取院区信息
        if($hosInfo['have_branch']){
            $districtId = isset($params['districtId'])?$params['districtId']:0;
//            print_r($districtId);exit;
            //如果院区存在
            if($districtId){
                //重置hosid 和 token
                $districtInfo = \app\equip\model\Hospital::getDistrictInfo($districtId);
                $params['hosId'] = $districtInfo['hospital_id'];
            }
        }
        $route 		= self::$route[$method][0];
        $type 		= isset(self::$route[$method][1]) ? strtolower(self::$route[$method][1]) : 'post';
        switch ($type) {
            case 'get':
                $url 	= $this->url.$route.'?'.http_build_query($params);
                break;
            case 'post':
                $url 	= $this->url.$route;
                $data 	= http_build_query($params);
                error_log("DATA => " . $data);
                break;
            case 'put':
                $url 	= $this->url.$route;
                $data 	= http_build_query($params);
                break;
            case 'patch':
                $url 	= $this->url.$route;
                $data 	= http_build_query($params);
                break;
            case 'delete':
                $url 	= $this->url.$route;
                $data 	= http_build_query($params);
                break;
            default:		break;
        }
        
        Log::storageRoute('hospitalRequest_'.$method , $url);
        Curl::init();
        Curl::setUrl($url);
        Curl::setCustomRequest($type);
        $data && Curl::setParams($data);
        Curl::setHttpHeader($header);
        Curl::setOpt();
        $response 	= Curl::execute();
//        print_r($response);exit;
        error_log("RETURN => " . $response);
        error_log("URL => " . $url);
//        die;
        $result 	= json_decode($response , true);
        Log::storageResponse('hospitalRequest_'.$method , $result);
        Log::writeLog('hospitalRequest_'.$method);
        return $result ? $result : [];
    }
}
