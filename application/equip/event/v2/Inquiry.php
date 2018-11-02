<?php

namespace app\equip\event\v2;

use app\component\server\ability\TencentVideoLive;
use app\component\server\cache\Redis;
use app\equip\model\Dept;
use think\Db;
use think\Loader;
use app\equip\controller\Base;
use app\component\response\Response;
use app\component\server\Server;

class Inquiry extends Base
{
	protected $validate = '\app\equip\validate\v2\Inquiry';

	protected $scene = [
		//'doctor',
		'doctorDetail',
		'inquiryOrder',
		'inquiryQuery',
		'inquiryConnect',
		'inquiryScreen',
		'inquiryReport',
		'outStatus',
		'putError',
		'getStatus',
		'orderStatus',
		'callStatus',
		'answer',
	];

	/**
	 * 科室列表
	 *
	 * @access 	public
	 * @return 	void
	 */
	public function dept()
	{
		$projectId 	    =   $this->projectId;
		$hospitalId 	=   isset($this->data['hospitalId']) ? $this->data['hospitalId'] : 0 ;
		$data 		    =   Loader::model('Dept')->inquiryDept($projectId , $hospitalId);
		$dept 		    =   [];
		foreach ($data as $v) {
			$dept[] 	= ['deptId' => $v['id'] , 'deptName' => $v['name']];
		}
		Response::success($dept);
		
	}

	/**
	 * 医生列表
	 *
	 * @access 	public
	 * @return 	void
	 */
	public function doctor()
	{
		$projectId  	= $this->projectId;
		$deptId  	    = isset($this->data['deptId']) ? $this->data['deptId'] : 0;
        $hospitalId  	= isset($this->data['hospitalId']) ? $this->data['hospitalId'] : 0;
		$data 		= Loader::model('Doctor')->inquiryDoctor($projectId , $deptId , $hospitalId);
		$doctor 	= [];
		foreach ($data as $v) {
			if ($v['online'] == 1 && $v['busy'] == 1) {
				$v['online'] = 2;
			}
			$doctor[] 	= [
				'doctorId' 	=> $v['doctor_id'],
				'picture' 	=> $v['avatar'],
				'doctorName'=> $v['name'],
				'disease' 	=> $v['sections'],
				'online' 	=> $v['online'],
				'title' 	=> $v['title'],
			];
		}
		Response::success($doctor);
	}

	/**
	 * 医生详情
	 *
	 * @access 	public
	 * @return 	void
	 */
	public function doctorDetail()
	{
		$projectId 	= $this->projectId;
		$doctorId 	= $this->data['doctorId'];
		$data 		= Loader::model('Doctor')->inquiryDoctorDetail($projectId , $doctorId);
		if (!$data) {
			Response::message(30013);
		}
		if ($data['online'] == 1 && $data['busy'] == 1) {
			$data['online'] = 2;
		}
		$doctor = [
			'doctorId' 	=> $data['doctor_id'],
			'picture' 	=> $data['avatar'],
			'doctorName'=> $data['name'],
			'title' 	=> $data['title'],
			'profile' 	=> $data['content'],
			'uptime' 	=> $data['uptime'],
			'disease' 	=> $data['sections'],
			'price' 	=> $data['price'],
			'online' 	=> $data['online'],
			'hospitalName' 	=> $data['hospitalName'],
		];
		Response::message(10000 , $doctor);
	}



	/**
	 * 问诊咨询下单
	 *
	 * @access 	public
	 * @return 	void
	 */
	public function inquiryOrder()
	{
		$projectId 	= $this->projectId;
		$doctorId 	= $this->data['doctorId'];
        $payType 	= $this->data['payType'];
		$equipId 	= $this->equipId;
		$data 		= Loader::model('Doctor')->inquiryDoctorDetail($projectId , $doctorId);
		if (!$data) {
			Response::message(30013);
		}
		if ($data['online'] != 1 || $data['busy'] == 1) { 	//如果为在线才可以发起订单
			Response::message(30014);
		} 
		$hid 		= $data['hospital_id'];
		$fee 		= $data['price'];
		$equip 		= Loader::model('Equip')->detail($equipId);
        $confObj    = Db::name("service_config")->field("inquiry")->where("project_id={$this->projectId}")->find();
        if(!$confObj){
            //没有配置分成比列
        }
        $FenRun = json_decode($confObj['inquiry'] , true)['FenRun'];
		$order 	 	= \app\equip\model\Order::orderInquiry($equipId , $equip , $hid , $projectId , $doctorId , $fee , $payType , $FenRun);
		if (!$order) {
			Response::message(30015); 	//
		}
		//$hid  		= $order['hospital_id'];
		$hospital 	= Loader::model('Hospital')->detail($hid);
		$deptId 	= $data['deptId'];
		$dept 		= [];
		if ($deptId) {
			$dept 	= Loader::model('Dept')->inquiryDetail($deptId);
		}
		$order['hospitalName'] 	= $hospital['name'];
		$order['deptName'] 		= $dept ? $dept['name'] : '暂无';
		$order['doctorName'] 	= $data['name'];
		$order['title'] 	    = $data['title'];
		Response::message(10000 , $order);
	}

	/**
	 * 问诊订单查询
	 *
	 * @access 	public
	 * @return 	void
	 */
	public function inquiryQuery()
	{
		$projectId 	= $this->projectId;
		$orderNumber = $this->data['orderNum'];
		$data 		= Loader::model('Order')->detailByNumber($orderNumber);
		if (!$data || $data['type'] != 98 || $data['project_id'] != $this->projectId) {
			Response::message(30011);
		}
		if ($data['status'] <= 0) {
			Response::message(10000 , [
				'orderNum' 	=> $orderNumber,
				'payStatus' => $data['status'],
			]);
		}
		$orderId 	= $data['id'];
		$inquiry 	= Loader::model('Inquiry')->detailByOrderId($orderId);
		$doctorId 	= $inquiry['doctor_id'];
		$inquiryId  = $inquiry['id'];
		$hid  		= $inquiry['hospital_id'];
		$doctor 	= Loader::model('Doctor')->inquiryDoctorDetail($projectId , $doctorId);
		$hospital 	= Loader::model('Hospital')->detail($hid);
		$deptId 	= $doctor['dept_id'];
		$dept 		= [];
		if ($deptId) {
			$dept 	= Loader::model('Dept')->inquiryDetail($deptId);
		}
		$response 	= [
			'orderNum' 		=> $orderNumber,
			'payStatus' 	=> $data['status'],
			'hospitalName' 	=> $hospital['name'],
			'deptName' 		=> $dept ? $dept['name'] : '暂无',
			'doctorName' 	=> $doctor['name'],
		];
		if ($data['status'] > 0) {
			$orderPay 	= Loader::model('Order')->payDetail($orderId);	
			$response['payPrice'] 	= $orderPay['price'];
			$response['payTime'] 	= $orderPay['pay_time'];
			$response['transactionId'] 	= $orderPay['transaction_id'];
			if ($data['status'] == 1) {
				$response['status'] = $inquiry['status'];
				$response['report']	= Loader::model('Inquiry')->report($inquiryId);
			}
		}
		Response::message(10000 , $response);
	}

	/**
	 * 问诊连接
	 *
	 * @access 	public
	 * @return 	void
	 */
	public function inquiryConnect()
	{
		$orderNum 	= $this->data['orderNum'];
		$equipId 	= $this->equipId;
		$order 	    = Loader::model('Order')->detailByNumber($orderNum);
		if ($order['type'] != 98 || $order['status'] != 1) {
			Response::message(30011);
		}
		$orderId 	= $order['id'];
		$inquiry 	= Loader::model('Inquiry')->detailByOrderId($orderId);
		$doctorId 	= $inquiry['doctor_id'];
		$projectId 	= $this->projectId;
		if (!in_array($inquiry['status'] , [0 , 1 , 11])) {
			Response::message(30020);
		}
		$data 		= Loader::model('Doctor')->inquiryDoctorDetail($projectId , $doctorId);
		if ($data['online'] != 1) { 	//如果为在线才可以发起订单
			Response::message(30014);
		}
		$doing 		= Loader::model('Inquiry')->doctorDoing($doctorId);
		if ($doing && $doing['order_id'] != $orderId) {
			Response::message(30018);
		} 
		$result 	= Loader::model('Inquiry')->setDoctorDoing($equipId , $doctorId , $inquiry['id']);
		if (!$result) {
			Response::message(30019);
		}
		//发送模板消息和写入消息表
        $msgId = \app\api\model\Message::addInquiryMessage($equipId,$result['callId'],$inquiry['doctor_id']);
		//发送模板消息
        if(\app\equip\model\Doctor::isLittleAppLogin($doctorId)){
            $docInfo = \app\equip\model\Doctor::getLittleAppOpenid($doctorId);
            $openid = $docInfo['openid'];
            $form_id = \app\api\model\Inquiry::selectOneFormId($openid);
            if($form_id){
                \app\api\model\Message::sendInquiryTemplateMsg($openid,$form_id,$result['callId'],$msgId);
            }

        }
//		$dAcount 	= Loader::model('Doctor')->accountHuawei($doctorId);
		Response::message(10000 , [
			'pushUrl' 	=> $inquiry['patPushUrl'],
			'playUrl' 		=> $inquiry['docPlayUrl'],
			'callId' 			=> $result['callId'],
		]);


	}







	
	/**
	 * 问诊截图
	 *
	 * @access 	public
	 * @return 	void
	 */
	public function inquiryScreen()
	{
		$orderNum 	= $this->data['orderNum'];
		$callId 	= $this->data['callId'];
		$picture 	= $this->data['picture'];
		$order 	= Loader::model('Order')->detailByNumber($orderNum);
		if ($order['type'] != 98 || $order['status'] != 1) {
			Response::message(30011);
		}
		$orderId 	= $order['id'];
		$inquiry 	= Loader::model('Inquiry')->detailByOrderId($orderId);
		$inquiryId 	= $inquiry['id'];
		$result 	= Loader::model('Inquiry')->addScreen($inquiryId , $callId , $picture);
		Response::message(10000 , [
			'orderNum' 	=> $orderNum,
			'callId' 	=> $callId,
			'picture' 	=> $picture,
		]);
	
	}

	/**
	 * 问诊报告
	 *
	 * @access 	public
	 * @return 	void
	 */
	public function inquiryReport()
	{
		$orderNum 	= $this->data['orderNum'];
		$picture 	= $this->data['picture'];
		$order 	= Loader::model('Order')->detailByNumber($orderNum);
		if ($order['type'] != 98 || $order['status'] != 1) {
			Response::message(30011);
		}
		$orderId 	= $order['id'];
		$inquiry 	= Loader::model('Inquiry')->detailByOrderId($orderId);
		$inquiryId 	= $inquiry['id'];
		$result 	= Loader::model('Inquiry')->addReport($inquiryId , $picture);
		Response::message(10000 , [
			'orderNum' 	=> $orderNum,
			'picture' 	=> $picture,
		]);
	}

    /**
     * 科室的医院列表
     * @return array
     */
    public function hospitalList()
    {
        $deptId = isset($this->data['deptId']) ? $this->data['deptId'] : 0;
        $hospitalArr = Dept::hospitalList($deptId , $this->projectId);
        Response::success($hospitalArr);
    }

    /**
     *   查询是否接通
     * @return array
     */
    public function answer()
    {
        $callId = $this->data['callId'] - 0;
        //error_log("CALLID => " . $callId);
        $callArr =  Db::name("inquiry_call")->where("id='{$callId}' and begin!=0")->find();
        if($callArr){
            $data['end'] = time();
            Db::name("inquiry_call")->where("id='{$callId}'")->update($data);
            Response::success();
        }else{
            Response::error(91025);
        }
    }

    /**
     *   挂断状态
     * @return array
     */
    public function outStatus()
    {
        $callId = $this->data['callId'];
        $inquiryId = Db::name("inquiry_call")->where("id={$callId}")->find();
        !$inquiryId && Response::error(10102);
        Db::startTrans();
        $call['status'] = $this->data['status'];
        $call['end'] = time();
        $callArr =  Db::name("inquiry_call")->where("id={$callId}")->update($call);
        if(!$callArr){
            Db::rollback();
            Response::error(30055);
        }
        if($call['status'] == 2){
            $inquiry['status'] = 8;
        }
        if($call['status'] == 3){
            $inquiry['status'] = 11;
        }
        $inquiryArr = Db::name("order_inquiry")->where("id={$inquiryId['inquiry_id']}")->find();
        if(!$inquiryArr){
            Db::rollback();
            Response::error(10102);
        }
        if(isset($inquiry['status'])){
            $holdTime = ($call['end'] - $inquiryId['begin'])/60;
            $inquiry['holding_time'] = ceil($holdTime);
            $return = Db::name("order_inquiry")->where("id={$inquiryId['inquiry_id']}")->update($inquiry);
            if(!$return){
                Db::rollback();
                Response::error(30055);
            }
        }
        $service['busy'] = 0;
        $serviceArr = Db::name("doctor_service")->where("doctor_id={$inquiryArr['doctor_id']} and project_id={$this->projectId}")->update($service);
        if(!$serviceArr){
            Db::rollback();
            Response::error(30055);
        }
        Db::commit();
        Response::success();
    }

    /**
     *   上报异常获取视频状态
     * @return array
     */
    public function putError()
    {
        $callId = $this->data['callId'];
        $inquiryId = Db::name("inquiry_call")->where("id={$callId}")->find();
        !$inquiryId && Response::error(10102);
        Db::startTrans();
        $call['status'] = $this->data['status'];
        $call['end'] = time();
        $callArr =  Db::name("inquiry_call")->where("id={$callId}")->update($call);
        if(!$callArr){
            Db::rollback();
            Response::error(30055);
        }
        if($call['status'] == 2){
            $inquiry['status'] = 8;
        }
        if($call['status'] == 3){
            $inquiry['status'] = 11;
        }
        $inquiryArr = Db::name("order_inquiry")->where("id={$inquiryId['inquiry_id']}")->find();
        if(!$inquiryArr){
            Db::rollback();
            Response::error(10102);
        }
        if(isset($inquiry['status'])){
            $holdTime = ($call['end'] - $inquiryId['begin'])/60;
            $inquiry['holding_time'] = ceil($holdTime);
            $return = Db::name("order_inquiry")->where("id={$inquiryId['inquiry_id']}")->update($inquiry);
            if(!$return){
                Db::rollback();
                Response::error(30055);
            }
        }
        $service['busy'] = 0;
        $serviceArr = Db::name("doctor_service")->where("doctor_id={$inquiryArr['doctor_id']} and project_id={$this->projectId}")->update($service);
        if(!$serviceArr){
            Db::rollback();
            Response::error(30055);
        }
        Db::commit();
        Response::success();
    }


    /**
     *   获取视频状态
     * @return array
     */
    public function callStatus()
    {
        $callId = $this->data['callId'];
        $callArr =  Db::name("inquiry_call")->where("id={$callId}")->find();
        Response::success($callArr);
    }


    /**
     *   获取订单状态
     * @return array
     */
    public function orderStatus()
    {
        $orderNum = $this->data['orderNum'];
        $orderArr =  Db::name("order")->alias("a")->field("a.status as orderStatus,b.status as inquiryStatus")->join("emt_order_inquiry b on b.order_id=a.id" , true)->where("order_number='{$orderNum}'")->find();
        Response::success($orderArr);
    }





}