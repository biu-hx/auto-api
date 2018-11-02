<?php

namespace app\demo\controller;

use app\demo\Common;
use app\component\server\Server;
use app\component\Curl;
use think\Loader;

class Timing
{


	public function index()
	{
		$pop 	= Server::cache('redis')->pop('execBu');
		if (!$pop) {
			echo 'no data';die;
		}
		$data 		= json_decode($pop , true);
		$orderNumber  	= $data['order_number'];
		$order  = Loader::model('equip/Order')->detailByNumber($orderNumber);
		if (!$order) {
			echo 'error';die;
		}
		$order['transactionId'] = $data['transaction_id'];
		$type 	= $order['type'];
		if ($type == 2) {
			$params = [
				'status' => '1',
				'data' 	 => json_decode('{"RegYear":"2017","RegMonth":"09","RegDay":"30","patName":"辜枫之婴","appId":"3185||1305||2","queueNo":"13","depDesc":"特需门诊儿科","docDesc":"陶于洪(下午)","regType":"特需门诊200","LocInfo":"03诊断室","fee":"202","regSerNo":"64556","patientRegNo":"08898486","invoiceNo":"18646650","ability_out_trade_no":"'.$orderNumber.'","transaction_id":"'.$order['transactionId'].'"}' , true),
			];
		} else if ($type == 3) {
			$params = [
				'status' => '1',
				'data' 	 => json_decode('{"RegYear":"2017","RegMonth":"09","RegDay":"30","patName":"辜枫之婴","appId":"3185||1305||2","queueNo":"13","depDesc":"特需门诊儿科","docDesc":"陶于洪(下午)","regType":"特需门诊200","LocInfo":"03诊断室","fee":"202","regSerNo":"64556","patientRegNo":"08898486","invoiceNo":"18646650","ability_out_trade_no":"'.$orderNumber.'","transaction_id":"'.$order['transactionId'].'"}' , true),
			];
		} else if ($type == 4) {
			$params = [
				'status' => '1',
				'data' 	 => json_decode('{"invoiceInfo":[{"invoiceNo":"33058447","invoiceFee":"85.57","window":"检验科(请到辅助01楼)^孕酮测定@1^检验科采血@1^HCG测定@1^一次性使用真空静脉血样采集容器(BD绿色3ml)@"}],"barCodeInfo":[{"barCode":"2689854500|2689854500^2^08925223^邢筠^女^31岁^.便民门诊^^内分泌^胡蝶.胡蝶^黄+^020000223222^75^^^^^^^^^^0^^^^^^^^0^^#2689854500:18400428@2,18400428@3,|血清:|THCG,P,||11点前采下午4点取|11点后采明日下午4点取|自助报告打印机(采血室外)||打印:17-09-28 07:35:52|;"}],"printcode":"000207737","ability_out_trade_no":"'.$orderNumber.'","transaction_id":"'.$order['transactionId'].'"}' , true),
			];
		} else if ($type == 5) {
			$params = [
				'status' => '1',
				'data' 	 => json_decode('{"total_fee":"25720.11","prepay_fee":"30000.00","arrears_fee":"4279.89","invoiceNo":"812528","ability_out_trade_no":"'.$orderNumber.'","transaction_id":"'.$order['transactionId'].'"}' , true),
			];
		}

		$url 	= "http://139.199.206.91:8094/business/callback";
		$header = [
			'Content-Type:application/x-www-form-urlencoded;charset=utf-8',
			'api-version:1',
		];
		Curl::init();
		Curl::setUrl($url);
		Curl::setCustomRequest('post');
		$params && Curl::setParams(http_build_query($params));
		Curl::setHttpHeader($header);
		Curl::setOpt();
		$response 	= Curl::execute();
		$result 	= json_decode($response , true);
		var_dump($result);
		if ($result && $result['code'] == '200') {
			echo 'true';die;
		} else {
			echo 'retry';
			Server::cache('redis')->push('execBu' , $pop);
		}
		

	}





}