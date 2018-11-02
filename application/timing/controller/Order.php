<?php

namespace app\timing\controller;

use think\Controller;
use think\Db;
use think\Loader;
use app\component\Curl;
use app\component\Common;
use app\component\server\Server;

class Order extends Controller
{

	//--锁号订单180s自动取消
	public function cancelRegistration()
	{
	    $projectArr = Loader::model('Project')->getArr();
	    if ($projectArr){
	        foreach ($projectArr as $project){
                $id = $project['project_id'];
	            $registration = json_decode($project['registration'] , true);
	            $DaiZhiFuShiChang = isset($registration['DaiZhiFuShiChang']) ? $registration['DaiZhiFuShiChang'] : 180;
                $orderArr  = Loader::model('Order')->registrationWait($id , $DaiZhiFuShiChang);
                if (!$orderArr) {
                   continue;
                }
                Loader::model('Order')->cancelOrder($orderArr);
                Loader::model('Order')->orderCancelLog(array_column($orderArr , 'id'));
            }
        }
	}

	//--半小时订单无效
	public function cancelOrder()
	{
		$orderId  = Loader::model('Order')->wait();
		if (!$orderId) {
			echo 'no data';die;
		}
		Loader::model('Order')->cancelOrder($orderId);
		Loader::model('Order')->orderCancelLog($orderId);
	}
	
	public function refundOrder()
	{
		$orderArr = Db::name("order")
            ->field("p.app_id,p.app_secret,o.order_number,o.id,a.price,o.type")
            ->alias("o")
            ->join("project_pay p" , "p.id=o.pay_type")
            ->join("order_pay a" , "a.order_id=o.id")
            ->where("o.type in (2,3,4,5,98,99) and o.status=2 and o.hospital_id in (61754,61755,61756,61757,61759) and o.create_time>'1529057888'")
            ->group("o.id")
            ->select();
		$update['status'] = 3;
        Db::name("order")->where("type in (2,3,4,5,98,99) and status=2 and hospital_id in (61754,61755,61756,61757,61759) and create_time>'1529057888'")->update($update);
        $redisObj = Server::cache("redis");
		if($orderArr){
		    foreach ($orderArr as $orders){
                $key = "RefundOrder";
                $redisObj->push($key , json_encode($orders) , true);
                $orders['time'] = date("Y-m-d H:i:s" , time());
                Db::name("order_refund_log")->insert($orders);
            }
        }
        exit('end');
	}

	public function refundInquiryOrder(){
        $needDate = time() - 60;
	    $orderArr = Db::name('order_inquiry')
            ->alias("b")
            ->field("max(a.id),b.id as inquiryId,b.order_id,b.doctor_id,d.project_id")
            ->join("order d" , "d.id=b.order_id")
            ->join("order_pay c" , "c.order_id=b.order_id")
            ->join("inquiry_call a" , "a.inquiry_id=b.id" , 'left')
            ->where("(a.status=0 or a.status is null) and c.pay_time<'{$needDate}' and d.status=1 and b.status=1")
            ->group("b.id")
            ->select();
	    if($orderArr){
	        //1分钟内没有接通的订单  处理退款
            Db::startTrans();
	        foreach ($orderArr as $order){
	            $data['status'] = 2;
	            $result = Db::name("order")->where("id='{$order['order_id']}'")->update($data);
	            if(!$result){
	                Db::rollback();
                }
                $service['busy'] = 0;
                $result = Db::name("doctor_service")->where("doctor_id='{$order['doctor_id']}' and project_id={$order['project_id']}")->update($service);
                if(!$result){
                    Db::rollback();
                }
                $inquiry['status'] = 9;
                $result = Db::name("order_inquiry")->where("id='{$order['inquiryId']}'")->update($inquiry);
                if(!$result){
                    Db::rollback();
                }
            }
            Db::commit();
	        echo 'success';
        }else{
            echo 'no data';
        }

        //处理同时断网的问题
        $needTime = time() - 60;
        $callArr = Db::name("inquiry_call")
            ->alias("a")
            ->field("a.*,b.*,a.id as callId")
            ->join("order_inquiry b" , "b.id=a.inquiry_id")
            ->where("a.status=1 and a.end<'{$needTime}'")->select();
        if($callArr){
            Db::startTrans();
            foreach ($callArr as $call){
                $callData['status'] = 3;
                $callReturn = Db::name("inquiry_call")->where("id='{$call['callId']}'")->update($callData);
                if(!$callReturn){
                    Db::rollback();
                }
                $inquiryData['status'] = 11;
                $inquiryData['holding_time'] = ceil(($call['end'] - $call['begin'])/60);
                $inquiryReturn = Db::name("order_inquiry")->where("id={$call['inquiry_id']}")->update($inquiryData);
                if(!$inquiryReturn){
                    Db::rollback();
                }
                //释放医生状态
                $serviceData['busy'] = 0;
                Db::name("doctor_service")->where("doctor_id={$call['doctor_id']}")->update($serviceData);
                //中断不退款
                /*$orderData['status'] = 2;
                $orderReturn = Db::name('order')->where("id={$call['order_id']}")->update($orderData);
                if(!$orderReturn){
                    Db::rollback();
                }*/
            }
            Db::commit();
            echo 'success2';exit;
        }else{
            echo 'no data2';exit;
        }

    }

    public function refundOrderByRedis(){
        $redisObj = Server::cache("redis");
        $orders = $redisObj->pop("RefundOrder" , true);
        //error_log('refund start');
        if($orders){
            $orderArr = json_decode($orders , true);
            $appId = $orderArr['app_id'];
            $nonce = md5(uniqid());
            $params['nonce'] =$nonce;
            $timestamp = time();
            $params['timestamp'] = $timestamp;
            $params['appsecret'] = $orderArr['app_secret'];
            $signature = self::signature($params);
            $dataParams['appid'] = $appId;
            $dataParams['nonce'] = $nonce;
            $dataParams['timestamp'] = $timestamp;
            $dataParams['signature'] = $signature;
            $price = $orderArr['price'] * 100;
            if(!$price){
                return false;
            }
            $dataJson['data'] = json_encode(['hosp_out_trade_no'=>$orderArr['order_number'] , 'refund_fee' => $price , 'notify_url' => 'not now']);
            $refundUrl = config("refund_order_url").http_build_query($dataParams);
//            $ch = curl_init($refundUrl);
//            curl_setopt_array($ch, $options);

            $options = array(
                CURLOPT_RETURNTRANSFER =>true,
                CURLOPT_HEADER =>false,
                CURLOPT_POST =>true,
                CURLOPT_POSTFIELDS => $dataJson,
            );
            $ch = curl_init($refundUrl);
            curl_setopt_array($ch, $options);
            $result = curl_exec($ch);
            curl_close($ch);
            error_log("returnData => " . $result);
            $resultData = json_decode($result , true);
            if($resultData['code'] == 20002){
                Common::syncOrder($orderArr['id'] , 2 , $orderArr['type']);
                $update['status'] = 4;
                $update['refund_time'] = time();
                Db::name("order")->where("id={$orderArr['id']}")->update($update);
                $logData['status'] = 4;
                $logData['refund_id'] = $resultData['data']['refund_id'];
                Db::name("order_refund_log")->where("order_id={$orderArr['id']}")->update($logData);
                echo  'success';
            }
           /* Curl::init();
            Curl::setUrl($refundUrl);
            Curl::setCustomRequest("post");
            $dataJson && Curl::setParams($dataJson);
            //Curl::setHttpHeader($header);
            Curl::setOpt();
            $response 	= Curl::execute();*/
           //error_log("refundOrderId => " . $orderArr['id']);
        }else{
            echo 'not order data';
            //error_log('refund end');
            exit;
        }
    }

    private function signature($params)
    {
        sort($params , SORT_STRING);
        $string = implode($params , '');
        return sha1($string);
    }

    public function checkOrder(){
	    $abilityArr = Db::name("refund_his_check")->where("refund_state=0")->select();
	    echo 'start';
	    if($abilityArr){
	        foreach ($abilityArr as $value){
	            $orderArr = Db::name("order")
                    ->alias("a")
                    ->field("d.app_id,d.app_secret,a.order_number,a.id,b.transaction_id,b.price,a.type")
                    ->join("order_pay b" , "b.order_id=a.id")
                    ->join("project_pay d" , "d.id=a.pay_type")
                    ->where("a.hospital_id={$value['hospital_id']} and a.status=2")->select();
	            $traId = json_decode($value['ability_data'] , true);
	            //比对医院退款订单  数量 商户订单号
                if(count($orderArr) == count($traId)){
                    if(count($traId) == 0){
                        $update['refund_state'] = 1;
                        Db::name("refund_his_check")->where("id={$value['id']}")->update($update);
                        continue;
                    }else{
                        foreach ($orderArr as $order){
                            if(!in_array($order['transaction_id'] , $traId)){
                                $update['refund_state'] = 2;
                                Db::name("refund_his_check")->where("id={$value['id']}")->update($update);
                                break;
                            }
                        }
                        $redisObj = Server::cache("redis");
                        $key = 'RefundOrder';
                        foreach ($orderArr as $orders){
                            $redisObj->push($key , json_encode($orders) , true);
                            $orders['time'] = date("Y-m-d H:i:s" , strtotime());
                            Db::name("order_refund_log")->insert($orders);
                        }
                        $update['refund_state'] = 1;
                        Db::name("refund_his_check")->where("id={$value['id']}")->update($update);
                        continue;
                    }
                }else{
                   //账目不平
                    $update['refund_state'] = 2;
                    Db::name("refund_his_check")->where("id={$value['id']}")->update($update);
                    continue;
                }
            }
        }
        echo 'end';
    }

}