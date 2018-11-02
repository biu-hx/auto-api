<?php

namespace app\api\event\v1;

use think\Db;
use think\Loader;
use app\api\controller\Base;
use app\component\response\Response;

class Inquiry extends Base
{

	protected $validate = '\app\api\validate\v3\Inquiry';

	protected $scene 	= [
		'inquiry',
		'detail',
		'mark',
		'markCall',
		'relate',
		'report',
        'setStatus',
	];

	/**
	 * 订单列表
	 *
	 * @access 	public
	 * @return 	void
	 */
	public function inquiry()
	{	
		$doctorId 	= $this->doctorId;
		$eventType 	= $this->data['eventType'];
		$page 		= isset($this->data['page']) ? $this->data['page'] : 1;
		$pagesize 	= isset($this->data['pagesize']) ? $this->data['pagesize'] : \think\Config::get('pagesize');
		switch ($eventType) {
			case 'EVENT_DOING':$data = Loader::model('Inquiry')->inquiryDoing($doctorId , $page , $pagesize);break;
			case 'EVENT_OVER':$data = Loader::model('Inquiry')->inquiryOver($doctorId , $page , $pagesize);break;
			case 'EVENT_BREAK':$data = Loader::model('Inquiry')->inquiryBreak($doctorId , $page , $pagesize);break;
            case 'EVENT_ALL':$data = Loader::model('Inquiry')->inquiryAll($doctorId , $page , $pagesize);break;
			default:break;
		}
		$response 	= [
            'envetType' => $eventType,
			'totalPage' => $data['totalPage'],
			'pagesize'  => $pagesize,
			'page' 		=> $page,
			'list' 		=> $data['list'],
		];
		Response::message(10000 , $response);
	}

	/**
	 * 订单详情
	 *
	 * @access 	public
	 * @return 	void
	 */
    public function detail()
    {
        $doctorId 	= $this->doctorId;
        $inquiryId 	= $this->data['inquiryId'];
        $data 		= Loader::model('Inquiry')->detail($doctorId , $inquiryId);
        $data 		|| Response::message(13002);
        $orderId 	= $data['order_id'];
        $order 		= Loader::model('Order')->basic($orderId);
        $pay 		= Loader::model('Order')->pay($orderId);
        $equip 		= Loader::model('Equip')->equip($order['equipment_id']);
        $report 	= Loader::model('Inquiry')->report($inquiryId);
        $call  		= Loader::model('Inquiry')->call($inquiryId);
        \think\Config::load(APP_PATH.'extra/selfconfig.php');
        $status 	= \think\Config::get('status_name');
        $response 	= [
            'inquiryId' 	=> $inquiryId,
            'orderNumber' 	=> $order['order_number'],
            'status' 		=> $data['status'],
            'statusStr' 		=> $status[$data['status']],
            'detail' 		=> [
                'equipment' 	=> $equip['code'],
                'address' 		=> $equip['address'],
                'price' 		=> $order['price'],
                'payTime' 		=> date('Y-m-d H:i' , $pay['pay_time']),
                'holdingTime' 	=> $data['holding_time'],
            ],
            'report' 		=> $report,
            'call' 			=> $call,
        ];
        Response::message(10000 , $response);
    }

    /**
     * 获取最新的问诊消息
     */
    public function getNewInquiryMsg(){
        $doctorId 	= $this->doctorId;
        //获取最新消息
        $newMsg = \app\api\model\Message::getNewInquiryMsg($doctorId);
        $newMsg || Response::errorMessage("暂无新消息");

        //查询问诊消息信息
        $callId = $newMsg['business_id'];
//        $inquiryInfo 		= Loader::model('Inquiry')->detail($doctorId , $inquiryId);
        //返回信息
        $response = [
            'msgId'=>$newMsg['id'],
            'callId'=>$callId,
            'restTime'=>60-(time()-strtotime($newMsg['create_ts'])),
//            'pushUrl'=>$inquiryInfo['docPushUrl'],
//            'playUrl'=>$inquiryInfo['patPlayUrl'],
        ];
//        echo time()-strtotime($newMsg['create_ts']);exit;
        Response::message(10000,$response);
    }

    /**
     * 获取问诊视频信息
     */
    public function getInquiryVideo(){
        $doctorId 	= $this->doctorId;
        $callId 	= $this->data['callId'];
        $msgId = $this->getData('msgId',false,0);
        if($msgId){
            //清除掉消息
            \app\api\model\Message::setMsgReaded($msgId);
        }
        //查询通话详情
        $callInfo = \app\api\model\Inquiry::getCallDetail($callId);
        $callInfo || Response::errorMessage("不存的通话信息");
        $inquiryId = $callInfo['inquiry_id'];
        $inquiryInfo 		= Loader::model('Inquiry')->detail($doctorId , $inquiryId);
        $inquiryInfo || Response::errorMessage("不存的问诊信息");
        //获取订单信息
        $orderInfo = \app\api\model\Order::basic($inquiryInfo['order_id']);
        if($orderInfo['status']!=1){
            Response::errorMessage("订单状态不正确");
        }
        //生成通话记录
        \app\api\model\Inquiry::markCallBegin($inquiryId,$callId,time());
        $response = [
            'pushUrl'=>$inquiryInfo['docPushUrl'],
            'playUrl'=>$inquiryInfo['patPlayUrl'],
            'inquiryId'=>$inquiryId,
        ];
        Response::message(10000,$response);
    }


    /**
     * 设置异常视频状态
     */
    public function setInquiryStauts(){
        $callId = $this->getData("callId");
        $status = $this->getData("status",0,1);
        //查询通话详情
        $callInfo = \app\api\model\Inquiry::getCallDetail($callId);
        $callInfo || Response::errorMessage("不存的通话信息");
        \app\api\model\Inquiry::setCallStatus($callId,3);
        Response::success();

    }


    /**
     * 获取视频状态
     */
    public function getInquiryStauts(){
        $callId = $this->getData("callId");
        $status = $this->getData("status",0,1);
        //查询通话详情
        $callInfo = \app\api\model\Inquiry::getCallDetail($callId);
        $callInfo || Response::errorMessage("不存的通话信息");
        $response = [
            'status'=>$callInfo['status'],
        ];
        //如果一直是通话中，则不断的更新结束时间
        if(1==$callInfo['status']){
            \app\api\model\Inquiry::setCallEndTime($callId);
        }
        Response::success($response);

    }

	/**
	 * 标记结束
	 *
	 * @access 	public
	 * @return 	void
	 */
	public function mark()
	{
		$doctorId 	= $this->doctorId;
		$inquiryId 	= $this->data['inquiryId'];
        $callId 	= $this->data['callId'];
		$eventType  = $this->data['eventType'];
		$time = time();
		$data 		= Loader::model('Inquiry')->detail($doctorId , $inquiryId);
		$data 		|| Response::message(13002);
		$data['status'] == 0 && Response::message(14001);
		if ($data['status'] != 1) {
	        //如果已结束，则不能再更改
            8==$data['status'] && Response::message(14001);
            //如果异常，则不能再更为异常
			$data['status'] == 11 && $eventType == 'EVENT_BREAK' && Response::message(14001);
		}
		$result 	= $eventType == 'EVENT_OVER' ? Loader::model('Inquiry')->markOver($inquiryId , $doctorId) : Loader::model('Inquiry')->markAbnormal($inquiryId , $doctorId);
		//标记为结束
		\app\api\model\Inquiry::markCallEnd($inquiryId,$callId,$time,$eventType);
		$holdingTime= Loader::model('Inquiry')->holdingTime($inquiryId);
		Loader::model('Inquiry')->setHoldingTime($inquiryId , $holdingTime);
		//设置医生状态为不忙
        \app\api\model\Personal::autoOnline($doctorId);
		$response 	= [
			'inquiryId' 	=> $inquiryId,
			'eventType' 	=> $eventType,
		];
		Response::message(10000 , $response);
	}

    /**
     * 标记订单状态
     */
    public function setStatus(){
        $orderNum = $this->data['orderNum'];
        $orderArr = Db::name("order")->where("order_number='{$orderNum}' and type=98")->find();
        if(!$orderArr){
            Response::error(13002);
        }
        Db::startTrans();
        $inquiry['status'] = $this->data['inquiryStatus'];
        $response = Db::name("order_inquiry")->where("order_id={$orderArr['id']}")->update($inquiry);
        if(!$response){
            Db::rollback();
            Response::error(20000);
        }
        if($this->data['inquiryStatus'] == 12){
            $order['status'] = 2;
            $return = Db::name("order")->where("order_number='{$orderNum}'")->update($order);
            if(!$return){
                Db::rollback();
                Response::error(20000);
            }
        }
        Db::commit();
        Response::success();
    }

	/**
	 * 家真号换取订单的相关信息
	 *
	 * @access 	public
	 * @return 	void
	 */
	public function relate()
	{
		Response::message(13002);
		$doctorId 	= $this->doctorId;
		$number 	= $this->data['number'];
		$data 		= Loader::model('Inquiry')->relate($number);
		(!$data 	|| !$data['inquiry_id']) && Response::message(13002); 	//如果没有 说明数据错误
		$equipId 	= $data['equip_id'];
		$equip 		= Loader::model('Equip')->equip($equipId);
		$response 	= [
			'inquiryId' 	=> $data['inquiry_id'],
			'callId' 		=> $data['call_id'],
			'equipment' 	=> $equip['number'],
		];
		Response::message(10000 , $response);
	}

	/**
	 * 获取订单的报告
	 *
	 * @access 	public
	 * @return 	void
	 */
	public function report()
	{
		$doctorId 	= $this->doctorId;
		$inquiryId 	= $this->data['inquiryId'];
		$data 		= Loader::model('Inquiry')->detail($doctorId , $inquiryId);
		$data 		|| Response::message(13002);
		$report 	= Loader::model('Inquiry')->report($inquiryId);
		$response 	= [
			'report' 	=> $report,
		];
		Response::message(10000 , $response);
	}

    /**
     * 获取小程序formid
     */
    public function addLittleAppFormId(){
        $form_id = $this->getData("form_id");
        $doctorId 	= $this->doctorId;
//        echo $this->data['form_id'];exit;
        //写入到form_id记录表
        $openid = $this->openid;
        \app\api\model\Inquiry::insertFormid($form_id,$openid);
        Response::success();
    }

	
}