<?php

namespace app\equip\controller;

use app\component\Common;
use app\component\response\Response;
use think\Config;
use think\Controller;
use think\Db;
use think\Request;
use think\Loader;
use app\component\server\Server;
use app\component\server\aes;
use app\component\Log;
use app\component\Curl;


class Pay extends Controller
{

    const AES_KEY = "9876543212345678";
	/**
	 * 支付处理
	 *
	 * @access 	public
	 * @return 	void
	 */
	public function index()
	{
		Log::storageRequest('payCallback' , $_POST);
		//error_log("payPost => " . json_encode($_POST));
		Log::storageRoute('payCallback' , Request::instance()->url());
		Log::writeLog('payCallback');
		if (!isset($_POST['data'])) {
			echo 'error';die;
		}
		$orderNumber= $_GET['orderNum'];
		$data  	 	= Loader::model('Order')->detailByNumber($orderNumber);
		if (!$data) { 	//如果查询不到订单，则说明这个是错误数据
			echo 'error'; die;
		}
		if(isset($data['pay_type'])){
            $payArr = Db::name("project_pay")->where("id={$data['pay_type']}")->find();
            $payType = $payArr['pay_type'];
        }else{
            $payType = 1;
        }
		$status 		= $data['status'];
		$params 		= json_decode($_POST['data'] , true);
		$params 		= $params['param'];
		$orderId 		= $data['id'];
        $transactionId  = isset($params['transaction_id']) ? $params['transaction_id'] : md5(uniqid().'aa'.time());
		$outTradeNo 	= isset($params['out_trade_no']) ? $params['out_trade_no'] : md5(uniqid());
		$price 			= isset($params['total_fee']) ? $params['total_fee']/100 : $data['price'];
		$payTime 		= isset($params['time_end']) ? strtotime($params['time_end']) : time();
		$mchId 			= isset($params['sub_mch_id']) ? $params['sub_mch_id'] : '1111';
		$openid         = isset($params['sub_openid']) ? $params['sub_openid'] : '1111';
		$result 		= Loader::model('Order')->orderPay($orderId,$outTradeNo,$transactionId,$price,$payType,$payTime,$mchId,$openid);
		if (!$result) { 	//主要不是重复支付的订单 一律接受 防止用户重复付款
			echo 'error'; die;
		}
		//将挂号,缴费,住院订单同步给辉哥
        if(in_array($data['type'] , [2,3,4,5]) && $data['hospital_id'] != 10000){
		    Common::syncOrder($data['id'] , 1 , $data['type']);
        }
		//处理视频问诊 科教视频
        if($data['type'] == 98){
		    $this->setInquiryOrder($orderId);
        }
        if($data['type'] == 99){
            $this->setVideoOrder($orderId);
        }
		if ($status == -2) {
			Loader::model('Order')->setOrderRefund($orderId , $data['type']);
			$log 	= [
				'type' 		=> 'system',
				'person' 	=> 'system',
				'reason' 	=> '已关闭的订单用户支付。',
			];
			Loader::model('Order')->orderLog($orderId , 2 , time() , $data , $log);
		} else if ($status == 0 || $status == 2) {
			if ($data['type'] < 10) {
				$notifyId 	= Loader::model('Notify')->setNotifyBusiness($orderId);
				Server::cache('redis')->push('executeBusiness' , $notifyId);
			}
		}
		echo 'SUCC';
		
	}

    /**
     * 设置问诊订单
     * @param $orderId
     */
	protected function setInquiryOrder($orderId){
	    $data['status'] = 1;
	    Db::name('order_inquiry')->where("order_id='{$orderId}'")->update($data);
    }

    /**
     * 设置科教视频订单
     * @param $orderId
     */
    protected function setVideoOrder($orderId){
        $data['status'] = 1;
        Db::name('order_video')->where("order_id='{$orderId}'")->update($data);
    }


    public function syncOrder(){
        $orderId = $_POST['orderId'];
        $syncType = $_POST['syncType'];
        $type = $_POST['type'];
        $orderObj = Db::name("order")
            ->alias("o")
            ->field("o.hospital_id,o.type,o.status,p.price,p.transaction_id,p.openid,a.card_id,a.card_name,a.business_info,a.success_info")
            ->join("order_pay p" , "p.order_id=o.id");
        switch ($type){
            case 2:
                $orderObj->join("order_registration a" , "a.order_id=o.id");
                $orderType = 1;
                break;
            case 3:
                $orderObj->join("order_fetch a" , "a.order_id=o.id");
                $orderType = 1;
                break;
            case 4:
                $orderObj->join("order_outpatient a" , "a.order_id=o.id");
                $orderType = 2;
                //$syncOrder['recipeId'] = $orderArr['hospital_id'];   //门诊缴费处方号 暂无
                break;
            case 5:
                $orderObj->join("order_inpatient a" , "a.order_id=o.id");
                $orderType = 3;
                break;
        }
        $orderArr = $orderObj->where("o.id={$orderId}")->find();
        if(!$orderArr){
            //没有订单
            return false;
        }
        $syncOrder['hosId'] = $orderArr['hospital_id'];
        $syncOrder['syncType'] = $syncType;
        $syncOrder['type'] = $orderType;
        $syncOrder['state'] = $orderArr['status'];
        $syncOrder['totalFee'] = $orderArr['price'] * 100;
        $syncOrder['transactionId'] = $orderArr['transaction_id'];
        $syncOrder['userData'] = json_encode(['openId' => $orderArr['openid'] , 'userName' => $orderArr['card_name'] , 'cardId' => $orderArr['card_id']]);
        $syncOrder['businessData'] = $orderArr['business_info'];
        $syncOrder['frontData'] = $orderArr['success_info'];
        ksort($syncOrder);
        $signStr = '';
        foreach ($syncOrder as $key=>$value){
            if($value == ''){
                continue;
            }
            $signStr .= $key . '=' .$value ;
        }
        $syncOrder['sign'] = sha1($signStr . 'AUTO_MACHINE');
       /* Curl::init();
        Curl::setUrl(config("sync_order_url"));
        Curl::setCustomRequest("post");
        $syncOrder && Curl::setParams($syncOrder);
        //Curl::setHttpHeader($header);
        Curl::setOpt();
        $response 	= Curl::execute();*/
        $options = array(
            CURLOPT_RETURNTRANSFER =>true,
            CURLOPT_HEADER =>false,
            CURLOPT_POST =>true,
            CURLOPT_POSTFIELDS => $syncOrder,
        );
        $ch = curl_init(config("sync_order_url"));
        curl_setopt_array($ch, $options);
        $result = curl_exec($ch);
        curl_close($ch);
        var_dump($result);die;
    }

    /**
     *  银联支付回调
     *  return json
     */
    public function callBackUnionPay()
    {
        Log::storageRequest('unionPayCallback' , $_POST);
        Log::storageRoute('unionPayCallback' , Request::instance()->url());
        Log::writeLog('unionPayCallback');
        if (!isset($_POST['signData'])) {
            Response::errorMessage("请传入数据");
        }
        //验证签名加密处理
        $encodeData = $_POST['signData'];
        //空格转加号
        $encodeData = str_replace(" ","+",$encodeData);
        $aes = new aes();
        $decodeData = $aes->AESDecryptResponse(self::AES_KEY,$encodeData);
        if(!$decodeData){
            Response::errorMessage("解密失败");
        }
        $payData = json_decode($decodeData,true);

//        print_r($payData);
        $orderNumber= isset($payData['orderNumber']) ? $payData['orderNumber'] : '';
        if(!$orderNumber){
            Response::errorMessage("订单不能为空");
        }
        $data  	 	= Loader::model('Order')->detailByNumber($orderNumber);
        if (!$data) { 	//如果查询不到订单，则说明这个是错误数据
            Response::errorMessage("没有搜索到订单");
        }
        $status 		= $payData['strRespCode'];
        if($status != '00'){
            eResponse::errorMessage("支付失败的订单!");
        }
        if(!isset($payData['strTrace'])){
            eResponse::errorMessage("支付流水号不能为空!");
        }
        $transactionId  = isset($payData['total_fee']) ? $payData['transaction_id'] : md5(uniqid().'aa'.time());
        $orderId 		= $data['id'];
        $payType 		= 3;
        $outTradeNo 	= isset($payData['strTrace ']) ? $payData['strTrace '] : md5(uniqid())	;
        $price 			= isset($payData['total_fee']) ? $payData['total_fee']/100 : $data['price'];
        $payTime 		= time();//isset($payData['strTransDate']) ? strtotime($payData['strTransDate'] . $payData['strTransTime'] ) : time();
        $mchId 			= isset($payData['strBatch']) ? $payData['strBatch'] : '1111';
        $openid         = isset($payData['sub_openid']) ? $payData['sub_openid'] : '1111';
        $result 		= Loader::model('Order')->orderPay($orderId,$outTradeNo,$transactionId,$price,$payType,$payTime,$mchId,$openid);
        if (!$result) { 	//主要不是重复支付的订单 一律接受 防止用户重复付款
            echo 'error2'; die;
        }
        //将挂号,缴费,住院订单同步给辉哥
        if(in_array($data['type'] , [2,3,4,5]) && $data['hospital_id'] != 10000){
            Common::syncOrder($data['id'] , 1 , $data['type']);
        }
        //处理视频问诊 科教视频
        if($data['type'] == 98){
            $this->setInquiryOrder($orderId);
        }
        if($data['type'] == 99){
            $this->setVideoOrder($orderId);
        }
        if ($status == -2) {
            Loader::model('Order')->setOrderRefund($orderId , $data['type']);
            $log 	= [
                'type' 		=> 'system',
                'person' 	=> 'system',
                'reason' 	=> '已关闭的订单用户支付。',
            ];
            Loader::model('Order')->orderLog($orderId , 2 , time() , $data , $log);
        } else if ($status == 0 || $status == 2) {
            if ($data['type'] < 10) {
                $notifyId 	= Loader::model('Notify')->setNotifyBusiness($orderId);
                Server::cache('redis')->push('executeBusiness' , $notifyId);
            }
        }
        Response::success();
    }

}