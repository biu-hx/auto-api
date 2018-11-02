<?php

namespace app\timing\controller;

use app\component\response\Response;
use think\Controller;
use think\Loader;
use think\Config;
use app\component\server\Server;
use app\component\Curl;
use app\component\Log;

class Business extends Controller
{
	
	//--1
	public function executeBusiness()
	{
	    $id= isset($_GET['id'])?$_GET['id']:0;
	    if(!$id){
            $id 	= Server::cache('redis')->pop('executeBusiness');
        }
		$id 	= intval($id);
		if (!$id) {
			echo 'no data';die;
		}
		$data 	= Loader::model('Notify')->businessDetail($id);
		if (!$data) {
			echo 'error data';die;
		}
		if ($data['http_code'] == 200 || $data['num'] >= 5) {
			echo 'limit';die;
		}	
		$orderId 	= $data['order_id'];
		$order 		= Loader::model('Order')->detail($orderId);
		if (!$order || $order['status'] != 1) {
		    //支付超时，并且是华二年，将数据发到华二进行对账
            if($order['hospital_id'] == 10000&&$order['status']==2 ){
                $this->huaxiRefundNotice($order);
            }
			echo 'error order step 1';die;
		}
		$orderPay 	= Loader::model('Order')->orderPay($orderId);
		if (!$orderPay) {
			echo 'error order';die;
		}
        $equipment 	= Loader::model('Equipment')->detail($order['equipment_id']);
        if (!$equipment) {
            echo 'error order';die;
        }
        //能力层到前置机透传信息，json格式
        $extends = array();
        $extends['pay_source'] = $order['type']."_".$order['project_id']."_".$equipment['bank_id'].'_'.$equipment['type'];
        $extends['pay_openid'] = $orderPay['openid'];
        $extends['pay_type'] = $orderPay['pay_type'];
		$url 		= 'http://'.Config::get('business_link').'?out_trade_no='.$order['order_number'].'&transaction_id='.$orderPay['transaction_id'].'&extends='.json_encode($extends);
		Curl::init();
		Curl::setUrl($url);
		Curl::setOpt();
		$response 	= Curl::execute();	
		$httpCode 	= Curl::getHttpCode();
		$num 		= $data['num'] + 1;
		if ($httpCode == 200) {
			Loader::model('Notify')->businessSuccess($id , $num);
			echo 'success';
		} else {
			Loader::model('Notify')->businessFail($id , $num , $httpCode);
			Server::cache('redis')->push('executeBusiness' , $id);
			echo 'retry';
		}
		
	}

	public function test(){
	    $order = \think\Db::name('order')->where(array("id"=>37))->find();
        $this->huaxiRefundNotice($order);
    }

    /**
     * 将退款信息直接发到华二
     * @param $order 订单信息
     */
	private function huaxiRefundNotice($order){
//	    print_r($order);exit;
	    //查询支付相关信息
        $orderPay 	= Loader::model('Order')->orderPay($order['id']);
        $card_id = Loader::model('Order')->getCardByOrderId($order['type'],$order['id']);
        $url = Config::get('huaxi_ability_link');
        $data = array();
        $data['flag'] = 'paySuccessBusinessFail';
        $content = array('Request'=>array("payType" => $order['type'], "card_id" => $card_id, "AmtSum" => $order['price'], "UserId" => "WXGHJF", "BankNo" => $orderPay['transaction_id'], "FlowNo" => $orderPay['out_trade_no']));
        $content_xml = Response::ToXml($content);
        $data['content'] = $content_xml;
        //访问华二签名
        $data['sign'] = $this->getDataEnCodeSign($data,'AbilityService');
        $response = Curl::curlPost($url, $data);
        echo $response;exit;

    }

    private function getDataEnCodeSign($params,$keyString){
        ksort($params);
        $preString = "";
        foreach ($params as $k=>$v){
            if($k=='sign')
                continue;
            $preString .= $k."=".$v;
        }
        //加密
        return sha1($preString.$keyString);
    }

    /**
     * 获取华二退款状态
     */
    public function getHuaxiRefundStatus(){
        $date = input('date');
        $date = $date?$date:date('Y-m-d');
        $orderM = Loader::model('Order');
        $list = $orderM->getHuaxiRefundStatus();
        $requstData = array();
        foreach ($list as $v){
            $requstData[] =  $v['transaction_id'];
        }
        $url = Config::get('huaxi_ability_link');
        $data = array();
        $data['flag'] = 'GetFefundOrder';
        $content = array('Request'=>array("Data" => $requstData,"Date" => $date));
        $content_xml = Response::ToXml($content);
//        echo $content_xml;exit;
        $data['content'] = $content_xml;
        //访问华二签名
        $data['sign'] = $this->getDataEnCodeSign($data,'AbilityService');
        $response = Curl::curlPost($url, $data);
        $resultArr = json_decode($response,true);
        if(isset($resultArr['ResultCode'])&&0==$resultArr['ResultCode']){
            foreach ($resultArr['data'] as $k=>$v){
                $orderM->setTradeiddStatus($k,$v);
            }
        }
        echo $response;exit;
    }

    public function test1(){
        echo sdfasf();
    }

    /**
     * 缓存医生号源
     */
    public function cacheDoctorSchedel(){
        $hospital_id = isset($_GET['hospital_id'])?$_GET['hospital_id']:0;
        if(!$hospital_id){
            echo "医院id不能为空";exit;
        }
        //查询医院信息
        $hospitalInfo = \app\equip\model\Hospital::detail($hospital_id);
        //如果有院区，
        $hospitalList = [];
        if($hospitalInfo['have_branch']){
            //查询院区
            $districtList = \app\equip\model\Hospital::getHospitalDistrict($hospital_id);
            foreach ($districtList as $v){
                $tem = ['district_id'=>$v['id'],'hospital_id'=>$v['hospital_id']];
                $hospitalList[] = $tem;
            }
        }else{
            $hospitalList[] = ['district_id'=>0,'hospital_id'=>$hospital_id];
        }
        //查询所有的排班科室
        $hosDept = [];
        $hosDeptTem = \app\equip\model\AiDept::getDeptListByHospital($hospital_id);
        foreach ($hosDeptTem as $v){
            $hosDept[$v['deptHisId']] =$v;
        }


//        echo "<pre>";print_r($hospitalList);exit;
        //
        //查询能力层排班科室
        foreach ($hospitalList as $v){
            $data 	= Server::ability('hospital')->dutyDept($v['hospital_id'],[]);
            if (!$data||$data['code'] != 10000) { echo "没有数据";exit; }
//            echo "<pre>";print_r($data);exit;
            foreach ($data['data'] as $vv){
                if(!isset($hosDept[$vv['deptHisId']])){//不存在，则写入
                    \app\equip\model\AiDept::addDept($vv['deptName'],$vv['date'],$vv['period'],$hospital_id,$v['district_id'],$vv['deptHisId']);
                }else{//存在则从中删除
                    unset($hosDept[$vv['deptHisId']]);
                }
            }
        }
        //查询所有医生
        $hosDocTem = \app\equip\model\AiDept::getDoctorByHospital($hospital_id);
        $hosDoc = [];
        foreach ($hosDocTem as $v){
            $hosDoc[$v['doctorHisId']] =$v;
        }
//print_r($hosDoc);exit;
        //查询所有医生排班
        $hosDocSchedulTem = \app\equip\model\AiDept::getSchedulByHospital($hospital_id);
        $hosDocSchedul = [];
        foreach ($hosDocSchedulTem as $v){
            $hosDocSchedul[$v['scheduleId']] =$v;
        }
        //查询医生排班
        $hosDeptTem = \app\equip\model\AiDept::getDeptListByHospital($hospital_id);
        foreach ($hosDeptTem as $v){
            $hosDept[$v['deptHisId']] =$v;
        }
        //获取医生信息

//        echo "<pre>";print_r($hospitalList);exit;
        foreach ($hospitalList as $v){
//            foreach ($hosDept as $vv){
//                $params = ['deptId' 	=> $vv['deptHisId']];
            $params = [];
            $data 	= Server::ability('hospital')->dutyDoctor($v['hospital_id'] , $params);
//            echo "<pre>";print_r($data);exit;
                //写入排班数据
                foreach ($data['data'] as $v1){
                    //处理医生
                    if(!isset($hosDoc[$v1['doctorHisId']])){
                        $doctor['doctorHisId'] = $v1['doctorHisId'];
                        $doctor['doctorName'] = $v1['doctorName'];
                        $doctor['hospital_id'] = $hospital_id;
                        \app\equip\model\AiDept::addDoctor($doctor);
                    }else{
                        unset($hosDoc[$v1['doctorHisId']]);
                    }
                    foreach ($v1['scheduleList'] as $v2){
                        $v2['district_id'] = $v['district_id'];
                        $v2['hospital_id'] = $hospital_id;
                        $v2['doctorHisId'] = $v1['doctorHisId'];
                        $v2['doctorName'] = $v1['doctorName'];
                        $v2['deptHisId'] = $v1['deptHisId'];
                        $v2['deptName'] = $v1['deptName'];
                        $schedule = $v2;
                        if(!isset($hosDocSchedul[$v2['scheduleId']])){
                            \app\equip\model\AiDept::addSchedul($schedule);
                        }else{
                            unset($hosDocSchedul[$v2['scheduleId']]);
                        }
//                        echo "<pre>";print_r($schedule);exit;
                    }
//                }
//                echo "<pre>";print_r($data);exit;
            }

        }
        //多余的更新为停诊
        if(count($hosDocSchedul)>0){
            foreach ($hosDocSchedul as $v){
                \app\equip\model\AiDept::stopWorkSchedul($v['id']);
            }
        }
        echo 'ok';

    }
	



}