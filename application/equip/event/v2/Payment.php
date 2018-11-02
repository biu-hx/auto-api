<?php

namespace app\equip\event\v2;

use think\Db;
use think\Loader;
use think\Request;
use app\equip\controller\Base;
use app\component\response\Response;
use app\component\server\Server;;

class Payment extends Base
{
	
	protected $validate = '\app\equip\validate\v2\Payment'; 		//定义validate文件

	protected $scene = [ 										//定义需要验证的方法
		'outpatientList',
		'outpatient',
		'outpatientOrder',
		'outpatientQuery',
		//'inpatient',
		//'inpatientOrder',
		'inpatientQuery',
		'getPay',
	];

	protected $mustId  = [
		'outpatientList',
		'outpatient',
		'outpatientOrder',
		//'inpatient',
		//'inpatientOrder',
		'getPay',
	];

	/**
	 * 门诊缴费信息列表
	 *
	 * @access 	public
	 * @return 	void
	 */
	public function outpatientList()
	{
		$hid 	= $this->hospitalId;
		$cardId = $this->data['cardId'];
        $uniqueId 	= isset($this->data['uniqueId']) ? $this->data['uniqueId'] : '';
		$params = [
			'cardNo' 	=> $cardId,
            'uniqueId' 	=> $uniqueId,
		];
        if($hid == 61756){
            $params['IdCardNo'] = $cardId;
        }
		$data   = Server::ability('hospital')->outpatientInfo($hid , $params);
		if (!$data) { Response::message(10101); }
		$data['code'] != 10000 && Response::message(10102); 
		$outpatient = [];
		foreach ($data['data']['payList'] as $v) {
			$outpatient[] = ['fee' => $v['fee'] , 'payDate' => $v['payDate'] , 'recipeId' => isset($v['recipeId']) ? $v['recipeId'] : '' , 'orderNumber' => isset($v['orderNumber']) ? $v['orderNumber'] : ''];
		}
		Response::success($outpatient);

	}

	/**
	 * 门诊缴费信息详情
	 *
	 * @access 	public
	 * @return 	void
	 */
	public function outpatient()
	{
		$hid 	= $this->hospitalId;
		$cardId = $this->data['cardId'];
        $uniqueId 	= isset($this->data['uniqueId']) ? $this->data['uniqueId'] : '';
		$recipeId 	= isset($this->data['recipeId']) ? $this->data['recipeId'] : '';
		$orderNumber 	= isset($this->data['orderNumber']) ? $this->data['orderNumber'] : '';
		$params = [
			'cardNo' 	=> $cardId,
			'recipeId' 	=> $recipeId,
			'uniqueId' 	=> $uniqueId,
			'orderNum' 	=> $orderNumber,
		];
        if($hid == 61756){
            $params['IdCardNo'] = $cardId;
        }
		$data   = Server::ability('hospital')->outpatientPrescription($hid , $params);
		if (!$data) { Response::message(10101); }
		$data['code'] != 10000 && Response::message(10102); 
		$data 	= $data['data'];
		$outpatient = [];
		$num 	= $fee = 0;
		foreach ($data['categories'] as $value) {
			foreach ($value['items'] as $v) {
				$outpatient[] = [
					'cateName' 	=> isset($value['cateName']) ? $value['cateName'] : '医疗费用',
					'itemName' 	=> $v['itemName'],
					'quantity' 	=> $v['quantity'],
					'unit' 	 	=> $v['unit'] ? $v['unit'] : '-',
					'price' 	=> isset($v['price']) ? $v['price'] : (isset($v['Specs']) ? $v['Specs'] : $v['fee'] ),
					'fee' 		=> $v['fee'],
				]; 
				$num ++;
				$fee += $v['fee'];
			}
		}
		Response::success([
			'outpatient' => $outpatient,
			'fee' 		 => round($fee ,2 ),
			'num' 		 => $num,
		]);
	}

	/**
	 * 门诊缴费下单处理
	 *
	 * @access 	public
	 * @return 	void
	 */
	public function outpatientOrder()
	{
		$hid 	= $this->hospitalId;
		$cardId = $this->data['cardId'];
        $uniqueId 	= isset($this->data['uniqueId']) ? $this->data['uniqueId'] : '';
        $payType = isset($this->data['payType']) ? $this->data['payType'] : 5;
		$recipeId 	= isset($this->data['recipeId']) ? $this->data['recipeId'] : '';
        $orderNumber 	= isset($this->data['orderNumber']) ? $this->data['orderNumber'] : '';
		$params = [
			'cardId' 	=> $cardId,
		];
        if($hid == 61756){
            $params['IdCardNo'] = $cardId;
        }
		$card 		= Server::ability('hospital')->patientCard($hid , $params);
		if (!$card || $card['code'] != 10000) Response::message(30000);
		$cardName 	= $card['data']['userName'];
		$params = [
			'cardNo' 	=> $cardId,
			'recipeId' 	=> $recipeId,
            'uniqueId' 	=> $uniqueId,
            'orderNum' 	=> $orderNumber,
		];
        if($hid == 61756){
            $params['IdCardNo'] = $cardId;
        }
		$data     	= Server::ability('hospital')->outpatientPrescription($hid , $params);
		if (!$data) { Response::message(10101); }
		$data['code'] != 10000 && Response::message(10102); 
		$orderNum  	= $data['data']['out_trade_no'];
		$price 	   	= $data['data']['totalFee'];
		$cardInfo 	= [
			'cardId' 		=> $cardId,
			'cardName' 		=> $cardName,
		];
		$outpatient = $data['data']['categories'];
		$equipId   	= $this->equipId;
		$order 	   	= Loader::model('Order')->orderOutpatient($equipId , $orderNum , $hid , $this->projectId , $price , $outpatient , $cardInfo , $payType);
		if (!$order) {
			Response::message(30034); 	//
		}
		$response 	= [
			'cardId' 		=> $cardId,
			'cardName' 		=> $cardName,
			'create_time' 	=> $order['create_time'],
			'orderNum' 		=> $order['orderNum'],
			'price' 		=> $order['price'],
		];
		Response::message(10000 , $response);
		
	}

	/**
	 * 门诊缴费信息查询
	 *
	 * @access 	public
	 * @return 	void
	 */
	public function outpatientQuery()
	{
		$orderNumber = $this->data['orderNum'];
		$hid 	= $this->hospitalId;
		$data 	= Loader::model('Order')->detailByNumber($orderNumber);
		if (!$data || $data['type'] != 4 || $data['project_id'] != $this->projectId) {
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
		$outpatient = Loader::model('Payment')->outpatientByOrderId($orderId);
		$successInfo= json_decode($outpatient['success_info'] , true);
		$response 	= [
			'orderNum' 	=> $orderNumber,
			'payStatus' => $data['status'],
			'cardId' 	=> $outpatient['card_id'],
			'cardName' 	=> $outpatient['card_name'],
			'payType' 	=> $orderPay['pay_type'],
			'payPrice' 	=> $orderPay['price'],
			'transactionId' => $orderPay['transaction_id'],
		];
		if ($data['status'] == 1) {
			$response['status'] 	= $outpatient['status'];
			if ($outpatient['status'] == 1) {
			    if($hid == 10000){
                    foreach ($successInfo['invoiceInfo'] as $v) {
                        $response['window'][] 	= $v['window'];
                    }
			    }else{
                    $response = array_merge($response , $successInfo);
                }
				$response['isBar'] 	= 0;
				if (isset($successInfo['barCodeInfo'])) {
					$response['isBar'] 		= 1;
					$response['printCode']  = $successInfo['printcode'];
					if(in_array($hid ,[61757 , 61760])){
                        $response['barCode'] 	= $successInfo['barCodeInfo'];
                    }else{
                        $response['barCode'] 	= $successInfo['barCodeInfo'][0]['barCode'];
                    }
				}
			}
		}
		Response::message(10000 , $response);
	}

	/**
	 * 住院信息查询
	 *
	 * @access 	public
	 * @return 	void
	 */
	public function inpatient()
	{
		$hid 	= $this->hospitalId;
		if(in_array($hid, [61757, 61759])){
            $inpatientId = isset($this->data['inpatientId']) ? $this->data['inpatientId'] : $this->data['cardId'];
            $params['inpatientId'] = $inpatientId;
            $data = Server::ability('hospital')->inpatientInfo($hid, $params);
            if (!$data) {
                Response::message(10101);
            }
            $data['code'] != 10000 && Response::message(10102);
            $data = $data['data'];
            $inpatient = [
                'patientName' => $data['patient_name'] ? $data['patient_name'] : '',
                'treatNo' => $data['treat_no'],
                'inDate' => $data['inhospital_date'],
                'deptName' => $data['dept_name'],
                'totalFee' => $data['total_fee'],
                'prePayFee' => $data['prepay_fee'],
                'arrearsFee' => $data['arrears_fee'],
            ];
            Response::success($inpatient);
        }else {
            $cardId = $this->data['cardId'];
            $uniqueId = isset($this->data['uniqueId']) ? $this->data['uniqueId'] : '';
            $params = [
                'cardId' => $cardId,
            ];
            if ($hid == 61756) {
                $params['IdCardNo'] = $cardId;
            }
            $card = Server::ability('hospital')->patientCard($hid, $params);
            if (!$card || $card['code'] != 10000) Response::message(30000);
            $cardName = $card['data']['userName'];
            $params = [
                'cardNo' => $cardId,
                'uniqueId' => $uniqueId,
            ];
            if ($hid == 61756) {
                $params['IdCardNo'] = $cardId;
            }
            $data = Server::ability('hospital')->inpatientInfo($hid, $params);
            if (!$data) {
                Response::message(10101);
            }
            $data['code'] != 10000 && Response::message(10102);
            $data = $data['data'];
            $inpatient = [
                'patientName' => $data['patient_name'] ? $data['patient_name'] : $cardName,
                'treatNo' => $data['treat_no'],
                'inDate' => $data['inhospital_date'],
                'deptName' => $data['dept_name'],
                'totalFee' => $data['total_fee'],
                'prePayFee' => $data['prepay_fee'],
                'arrearsFee' => $data['arrears_fee'],
            ];
            Response::success($inpatient);
        }
	}

	/**
	 * 住院信息下订单
	 *
	 * @access 	public
	 * @return 	void
	 */
	public function inpatientOrder()
	{
		$hid 	= $this->hospitalId;
        $uniqueId 	= isset($this->data['uniqueId']) ? $this->data['uniqueId'] : '';
        $payType = isset($this->data['payType']) ? $this->data['payType'] : 3;
        $treatNo= $this->data['treatNo'];
        $fee 	= $this->data['fee'];
        $type = isset($this->data['type']) ? $this->data['type'] : 0;//0,住院下单,1快捷出院补缴
		if(in_array($hid ,[61757 , 61759])){
            $inpatientId = isset($this->data['cardId']) ? $this->data['cardId'] : $this->data['inpatientId'];
            $params['inpatientId'] = $inpatientId;
            if ($type == 1) {
                $data = Server::ability('hospital')->searchOutHospitalInfo($hid, $params);
            } else {
                $data = Server::ability('hospital')->inpatientInfo($hid, $params);
            }
            if (!$data) {
                Response::message(10101);
            }
            $data['code'] != 10000 && Response::message(10102);
            if ($type == 1) {
                $temp = $data['data'];
                $inpatient['treat_no'] = $temp['AdmID'];////住院号
                $inpatient['patient_name'] = $temp['UserName'];//患者姓名
                $inpatient['dept_name'] = $temp['CurentDept'];//住院科室
                $inpatient['inhospital_date'] = $temp['AdmDate'];//入院日期
                $inpatient['bed_no'] = $temp['BedNo'];//床号
                $inpatient['total_fee'] = $temp['TotalAmount'];//总费用
                $inpatient['prepay_fee'] = $temp['DepositTotal'];//预交金总额
                $inpatient['arrears_fee'] = $temp['Balance'];//预交金余额
            } else {
                $inpatient = $data['data'];
            }
            $cardName = isset($inpatient['patient_name']) ? $inpatient['patient_name'] : '';
            $params = [
                'inpatientId' => $inpatientId,
                'cardNo' => isset($inpatient['cardNo']) ? $inpatient['cardNo'] : '',
                'treatNo' => isset($inpatient['treatNo']) ? $inpatient['treatNo'] : '',
                'fee' => $fee,
            ];
            $data = Server::ability('hospital')->inpatientOrder($hid, $params);
            if (!$data || $data['code'] != 10000) {
                Response::message(30035);    //失败
            }
            $equipId = $this->equipId;
            $orderNum = $data['data']['out_trade_no'];
            $price = $fee;
            $cardInfo = [
                'cardId' => isset($cardId) ? $cardId : $inpatientId,
                'cardName' => isset($cardName) ? $cardName : '',
            ];
            //验证住院预缴金额范围
            $projectArr = Loader::model('Project')->detailByHospital($hid);
            if (isset($projectArr['inpatient'])) {
                $registrationArr = json_decode($projectArr['inpatient'], true);
                $min = isset($registrationArr['JiaoFeiFanWei']['min']) ? $registrationArr['JiaoFeiFanWei']['min'] : 0;
                $max = isset($registrationArr['JiaoFeiFanWei']['max']) ? $registrationArr['JiaoFeiFanWei']['max'] : 999999999;
                if ($price < $min || $price > $max) {
                    Response::message(30054);
                }
            }
            $order = Loader::model('Order')->orderInpatient($equipId, $orderNum, $hid, $this->projectId, $price, $inpatient, $cardInfo, $payType);
            if (!$order) {
                Response::message(30036);    //
            }
            $response = [
                'cardId' => isset($cardId) ? $cardId : $inpatientId,
                //'cardName' 		=> $cardName,
                'create_time' => $order['create_time'],
                'orderNum' => $order['orderNum'],
                'price' => $order['price'],
                'treatNo' => $treatNo,
            ];
            Response::message(10000, $response);

        }else{
            $cardId = $this->data['cardId'];

            $params = [
                'cardId' 	=> $cardId,
                'uniqueId' 	=> $uniqueId,
            ];
            if($hid == 61756){
                $params['IdCardNo'] = $cardId;
            }
            $card 	= Server::ability('hospital')->patientCard($hid , $params);
            if (!$card || $card['code'] != 10000) Response::message(30000);
            $cardName = $card['data']['userName'];

            if($type == 1){
                $data 	= Server::ability('hospital')->searchOutHospitalInfo($hid , $params);
            }else{
                $params = [
                    'cardNo' 	=> $cardId,
                    'uniqueId' 	=> $uniqueId,
                ];
                $data 	= Server::ability('hospital')->inpatientInfo($hid , $params);
            }

            if (!$data) { Response::message(10101); }
            $data['code'] != 10000 && Response::message(10102);

            if ($type == 1){
                $temp = $data['data'];
                $inpatient['treat_no'] = $temp['AdmID'];////住院号
                $inpatient['patient_name'] = $temp['UserName'];//患者姓名
                $inpatient['dept_name'] = $temp['CurentDept'];//住院科室
                $inpatient['inhospital_date'] = $temp['AdmDate'];//入院日期
                $inpatient['bed_no'] = $temp['BedNo'];//床号
                $inpatient['total_fee'] = $temp['TotalAmount'];//总费用
                $inpatient['prepay_fee'] = $temp['DepositTotal'];//预交金总额
                $inpatient['arrears_fee'] = $temp['Balance'];//预交金余额
            }else{
                $inpatient 	= $data['data'];
            }

            $params = [
                'cardNo' 	=> $cardId,
                'treatNo' 	=> $treatNo,
                'fee' 		=> $fee,
                'uniqueId' 		=> $uniqueId,
            ];
            if($hid == 61756){
                $params['IdCardNo'] = $cardId;
            }
            $data 	= Server::ability('hospital')->inpatientOrder($hid , $params);
            if (!$data || $data['code'] != 10000) {
                Response::message(30035); 	//失败
            }
            $equipId   = $this->equipId;
            $orderNum  = $data['data']['out_trade_no'];
            $price 	   = $fee;
            $cardInfo 		= [
                'cardId' 		=> $cardId,
                'cardName' 		=> $cardName,
            ];
            //验证住院预缴金额范围
            $projectArr =  Loader::model('Project')->detailByHospital($hid);
            if(isset($projectArr['inpatient'])){
                $registrationArr =  json_decode($projectArr['inpatient'],true);
                $min = isset($registrationArr['JiaoFeiFanWei']['min']) ? $registrationArr['JiaoFeiFanWei']['min'] : 0 ;
                $max = isset($registrationArr['JiaoFeiFanWei']['max']) ? $registrationArr['JiaoFeiFanWei']['max'] : 999999999 ;
                if($price < $min || $price > $max){
                    Response::message(30054);
                }
            }
            $order 	   = Loader::model('Order')->orderInpatient($equipId , $orderNum , $hid , $this->projectId , $price , $inpatient , $cardInfo , $payType);
            if (!$order) {
                Response::message(30036); 	//
            }
            $response 	= [
                'cardId' 		=> $cardId,
                'cardName' 		=> $cardName,
                'create_time' 	=> $order['create_time'],
                'orderNum' 		=> $order['orderNum'],
                'price' 		=> $order['price'],
                'treatNo' 		=> $treatNo,
            ];
            Response::message(10000 , $response);
        }
	}

	/**
	 * 住院订单查询
	 *
	 * @access 	public
	 * @return 	void
	 */
	public function inpatientQuery()
	{
		$orderNumber = $this->data['orderNum'];
		$hid 	= $this->hospitalId;
		$data 	= Loader::model('Order')->detailByNumber($orderNumber);
		if (!$data || $data['type'] != 5 || $data['project_id'] != $this->projectId) {
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
		$inpatient 	= Loader::model('Payment')->inpatientByOrderId($orderId);
		$successInfo  	= json_decode($inpatient['success_info'] , true);
		$inpatientInfo  = json_decode($inpatient['business_info'] , true);
		$response 	= [
			'orderNum' 	=> $orderNumber,
			'payStatus' => $data['status'],
			'cardId' 	=> $inpatient['card_id'],
			'cardName' 	=> $inpatient['card_name'],
			'treatNo' 	=> $inpatientInfo['treat_no'],
			'deptName' 	=> $inpatientInfo['dept_name'],
			'payType' 	=> $orderPay['pay_type'],
			'payPrice' 	=> $orderPay['price'],
			'transactionId' => $orderPay['transaction_id'],
		];
		
		if ($data['status'] == 1) {
			$response['status'] 	= $inpatient['status'];
			if ($inpatient['status'] == 1) {
				$response['prePayFee'] 	= isset($successInfo['prepay_fee']) ? $successInfo['prepay_fee'] : '';
				$response['arrearsFee'] = isset($successInfo['arrears_fee']) ? $successInfo['arrears_fee'] : '';
			}
		}
		Response::message(10000 , $response);
		
	}

    /**
     * 获取设备 业务对应的支付方式
     */
    public function getPay(){
        $deviceCode = Request::instance()->header('number');
        $business = $this->data['business'];
        $payConfig = Db::name('equipment')->field("pay_config,project_id")->where("code='{$deviceCode}'")->find();
        $itemArr[] = "{$business}";
        $itemArr[] = "{$business},%";
        $itemArr[] = "%,{$business},%";
        $itemArr[] = "%,{$business}";
        $where['p.business_type'] = ['LIKE' ,$itemArr , 'OR'];
        $where['p.id'] = ['IN' ,explode(',' , $payConfig['pay_config'])];
        $where['p.status'] = ['EQ' ,1];
        $payIdArr = Db::name('project_pay')->alias("p")->join("pay_type pt " ,"pt.id=p.pay_type")->field("p.id,pt.name,pt.icon,p.pay_type")->where($where)->select();
        Response::success($payIdArr);
    }

}