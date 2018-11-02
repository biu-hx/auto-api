<?php

namespace app\equip\controller;

use think\Controller;
use think\Db;
use think\Request;
use think\Loader;
use app\component\Log;
use app\component\Common;
use app\component\server\Server;

class Business extends Controller
{

	/** 
	 * 业务回调
	 *
	 * @access 	public
	 * @return 	void
	 */
	public function index()
	{
		Log::storageRequest('businessCallback' , $_POST);
		Log::storageRoute('businessCallback' , Request::instance()->url());
		Log::writeLog('businessCallback');
		$business 		= $_POST;
		$transactionId 	= $business['data']['transaction_id'];
		//$sign =  $business['sign'];
		//error_log("ID => " . $transactionId);
		$data 			= Loader::model('Order')->payDetailByTransactionId($transactionId);
		if (!$data) {
			echo json_encode(['code' => 401 , 'channel' => 'QIN HAI']);die;
		}
       // $signStr = md5(md5($transactionId).'businessCallBack');
		//if($sign != $signStr){
		    //echo json_encode(['code' => 401 , 'channel' => '签名错误']);die;
        //}
		$orderId	= $data['order_id'];
		$data 		= Loader::model('Order')->detail($orderId);
		$type 		= $data['type'];
		$result 	= false;
		switch ($type) {
			case 2 :
			    $result = self::registration($data , $business);
			    break;
			case 3 :
			    $result = self::fetchReg($data , $business);
			    break;
			case 4 :
			    $result = self::outpatient($data , $business);
			    break;
			case 5 :
			    $result = self::inpatient($data , $business);
			    break;
			case 98 :
			    $result = self::inquiry($data , $business);
			    break;
			case 99 :
			    $result = self::video($data , $business);
			    break;
			default:break;
		}
		if($data['hospital_id'] != 10000 ){
            Common::syncOrder($orderId , 2 , $type);
        }
		if ($business['status']!=1) {
			Loader::model('Order')->setOrderRefund($orderId , $type);
			$log 	= [
				'type' 		=> 'system',
				'person' 	=> 'system',
				'reason' 	=> '业务反馈未成功，进行退款',
			];
			Loader::model('Order')->orderLog($orderId , 2 , time() , $data , $log);
		}
		echo json_encode(['code' => $result ? 200 : 400  , 'channel' => 'QIN HAI']);
	}


	/**
	 * 挂号业务处理
	 * 
	 * @access 	public
	 * @return 	void
	 */
	private function registration($data , $business)
	{
		$orderId= $data['id'];
		$data 	= Loader::model('Registration')->detailByOrderId($orderId);
		if (!$data) return false;
		$id 	= $data['id'];
		$status = $business['status'];
		if ($status == 1) {
			$successInfo = $business['data'];
			$result = Loader::model('Registration')->setSuccess($id , $successInfo);
		} else {
			$result = Loader::model('Registration')->setFail($id);
		}
		return $result;
	}

	/** 
	 * 取号业务处理
	 *
	 * @access 	public
	 * @return 	void
	 */
	private function fetchReg($data , $business)
	{
		$orderId= $data['id'];
		$data 	= Loader::model('Registration')->fetchByOrderId($orderId);
		if (!$data) return false;
		$id 	= $data['id'];
		$status = $business['status'];
		if ($status == 1) {
			$successInfo = $business['data'];
			$result = Loader::model('Registration')->setFetchSuccess($id , $successInfo);
		} else {
			$result = Loader::model('Registration')->setFetchFail($id);
		}
		return $result;
	}

	/** 
	 * 门诊业务处理
	 *
	 * @access 	public
	 * @return 	void
	 */
	private function outpatient($data , $business)
	{
		$orderId= $data['id'];
		$data 	= Loader::model('Payment')->outpatientByOrderId($orderId);
		if (!$data) return false;
		$id 	= $data['id'];
		$status = $business['status'];
		if ($status == 1) {
			$successInfo = $business['data'];
			$result = Loader::model('Payment')->setOutpatientSuccess($id , $successInfo);
		} else {
			$result = Loader::model('Payment')->setOutpatientFail($id);
		}
		return $result;
	}

	/** 
	 * 住院业务处理
	 *
	 * @access 	public
	 * @return 	void
	 */
	private function inpatient($data , $business)
	{
		$orderId= $data['id'];
		$data 	= Loader::model('Payment')->inpatientByOrderId($orderId);
		if (!$data) return false;
		$id 	= $data['id'];
		$status = $business['status'];
		if ($status == 1) {
			$successInfo = $business['data'];
			$result = Loader::model('Payment')->setInpatientSuccess($id , $successInfo);
		} else {
			$result = Loader::model('Payment')->setInpatientFail($id);
		}
		return $result;
	}

    /**
     * 视频问诊业务处理
     *
     * @access 	public
     * @return 	void
     */
    private function inquiry($data , $business)
    {
        $orderId= $data['id'];
        $data 	= Loader::model('Inquiry')->detailByOrderId($orderId);
        if (!$data) return false;
        $id 	= $data['id'];
        $status = $business['status'];
        if ($status == 1) {
            $successInfo = $business['data'];
            $result = Loader::model('Inquiry')->setInquirySuccess($id , $successInfo);
        } else {
            $result = Loader::model('Inquiry')->setInquiryFail($id);
        }
        return $result;
    }


    /**
     * 科教视频业务处理
     *
     * @access 	public
     * @return 	void
     */
    private function video($data , $business)
    {
        $orderId= $data['id'];
        $data 	= Loader::model('Video')->detailByOrderId($orderId);
        if (!$data) return false;
        $id 	= $data['id'];
        $status = $business['status'];
        if ($status == 1) {
            $successInfo = $business['data'];
            $result = Loader::model('Video')->setVideoSuccess($id , $successInfo);
        } else {
            $result = Loader::model('Video')->setVideoFail($id);
        }
        return $result;
    }


    /**
     * 窗口退款接口
     *
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function windowRefund(){
        Log::storageRequest('windowCallback' , $_POST);
        Log::storageRoute('windowCallback' , Request::instance()->url());
        Log::writeLog('windowCallback');
        $signature = isset($_POST['signature']) ? $_POST['signature'] : '';
        $data = $_POST;
        if(!$signature){
            $returnArr = ['code'=>405 , 'msg'=>'缺少参数signature'];
            echo json_encode($returnArr); die;
        }
        ksort($data);
        unset($data['signature']);
        $string = implode('=' , $data) . 'WindowRefund';
        $signatureStr = sha1($string);
        if($signature != $signatureStr){
            $returnArr['code'] = 406;
            $returnArr['msg'] = '签名错误';
            echo json_encode($returnArr); die;
        }
        if(!$data){
            echo '无参数!';die;
        }
        if(!isset($data['transactionId'])){
            echo '参数错误';die;
        }
        if(!isset($data['price']) || ($data['price'] == 0)){
            echo '参数错误';die;
        }
        $transactionId = $data['transactionId'];
        //查询有没有这笔订单
        $orderArr = Db::name("order_pay")->alias("p")
            ->field("a.app_id,a.app_secret,o.order_number,o.id,p.price,o.type")
            ->join("order o" , "o.id=p.order_id")
            ->join("project_pay a" , "a.id=o.pay_type")
            ->where("p.transaction_id='{$transactionId}' and o.status=1")->find();
        if(!$orderArr){
            echo '订单流水号错误!';die;
        }
        $price = $data['price'];
        $windowData['transaction_id'] = $transactionId;
        $windowData['price'] = $price;
        $windowData['create_time'] = date("Y-m-d H:i:s" , time());
        Db::name("window_refund")->insert($windowData);
        $redisObj = Server::cache("redis");
        $key = "RefundOrder";
        $redisObj->push($key , json_encode($orderArr) , true);
        $orderArr['time'] = date("Y-m-d H:i:s" , strtotime());
        Db::name("order_refund_log")->insert($orderArr);
        $update['status'] = 2;
        Db::name("order")->where("id={$orderArr['id']}")->update($update);
        echo '退款中 : ' . $transactionId;die;

    }

}