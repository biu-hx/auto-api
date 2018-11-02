<?php

namespace app\demo\controller;

use app\demo\Common;
use app\component\Curl;

class Transfer
{



	public function index()
	{

		$flag 		= $_GET['flag'];
		$content 	= $_GET['content'];
		$data 	 	= self::$flag($content);
		$response 	= [
			'code' 	=> 10000,
			'msg' 	=> 'ok',
			'data' 	=> $data,
		];
		echo json_encode($response , JSON_UNESCAPED_UNICODE);

	}

	public function SelectAdmByCardOrRegNo($content)
	{
		return [
			'AdmList' 	=> [
				'AdmListInfo' 	=> [
					'AdmId' 	=> '16080583',
					'DateFrom' 	=> '2016-10-17',
					'DateTo' 	=> '2016-11-15',
					'TotalSum' 	=> '22072.37',
					'PayedFlag' => 'B',
					'Arpbl' 	=> '31835950',
					'Admtype' 	=> '住院',
					'PatName' 	=> '鲜兰香',
				],
			],
		];
	}

	public function FetchPatFeeInfog($content)
	{
		return [
			'PatName' 	=> '鲜兰香',
			'RegNo' 	=> '02243488',
			'AdmDate' 	=> '2016-10-17',
			'RoomDesc' 	=> '118^8.13#8.14^6^D^^^^6080', 
			'BedDesc' 	=> '8-14床',
			'LocDesc' 	=> 'FKHLYQ-妇科护理一区',
			'SumFee' 	=> '22072.37',
			'DePosit' 	=> '20000.00',
			'Balance' 	=> '-2072.37',
			'IsPrint' 	=> '0',
			'DateTo' 	=> '2017-07-21',
			'ArcicList' => [
				'FetchPatCatAmtOutInfo' => [
					[
						'ArcicDesc' => '其它费', 
						'SumFee' => '159'
					],
					[
						'ArcicDesc' => '床位费', 
						'SumFee' => '70'
					],
					[
						'ArcicDesc' => '手术费', 
						'SumFee' => '4640'
					],
					[
						'ArcicDesc' => '放射检查', 
						'SumFee' => '120'
					],
					[
						'ArcicDesc' => '材料费', 
						'SumFee' => '3062.1'
					],
					[
						'ArcicDesc' => '检查费', 
						'SumFee' => '2220'
					],
					[
						'ArcicDesc' => '检验费', 
						'SumFee' => '1164'
					],
					[
						'ArcicDesc' => '治疗费', 
						'SumFee' => '2275'
					],
					[
						'ArcicDesc' => '特需床位费', 
						'SumFee' => '4500'
					],
					[
						'ArcicDesc' => '西药', 
						'SumFee' => '2230.27'
					],
					[
						'ArcicDesc' => '麻醉费', 
						'SumFee' => '1632'
					],
				],
			],
		];
	}

	public function GetFymx($content)
	{
		return json_decode('{"AmtDetailList":{"OutFymxList":[{"OrdDate":"2016-11-14","OrdName":"住院诊查费","OrdQty":"1","OrdUom":"日","OrdPrice":"6","OrdAmt":"6","OrdCat":"诊察费","YBCAT":"医保"},{"OrdDate":"2016-11-14","OrdName":"冷暖费(2人间)","OrdQty":"1","OrdUom":"日\\/床","OrdPrice":"6","OrdAmt":"6","OrdCat":"其它费","YBCAT":"医保"},{"OrdDate":"2016-11-14","OrdName":"妇科新楼双人间","OrdQty":"1","OrdUom":"日","OrdPrice":"180","OrdAmt":"180","OrdCat":"治疗费","YBCAT":"医保"},{"OrdDate":"2016-11-15","OrdName":"二级护理","OrdQty":"1","OrdUom":"日","OrdPrice":"10","OrdAmt":"10","OrdCat":"治疗费","YBCAT":"医保"},{"OrdDate":"2016-11-15","OrdName":"气压治疗","OrdQty":"6","OrdUom":"每部位","OrdPrice":"8","OrdAmt":"48","OrdCat":"治疗费","YBCAT":"医保"},{"OrdDate":"2016-11-13","OrdName":"住院诊查费","OrdQty":"1","OrdUom":"日","OrdPrice":"6","OrdAmt":"6","OrdCat":"诊察费","YBCAT":"医保"},{"OrdDate":"2016-11-13","OrdName":"冷暖费(2人间)","OrdQty":"1","OrdUom":"日\\/床","OrdPrice":"6","OrdAmt":"6","OrdCat":"其它费","YBCAT":"医保"},{"OrdDate":"2016-11-13","OrdName":"妇科新楼双人间","OrdQty":"1","OrdUom":"日","OrdPrice":"180","OrdAmt":"180","OrdCat":"治疗费","YBCAT":"医保"},{"OrdDate":"2016-11-14","OrdName":"二级护理","OrdQty":"1","OrdUom":"日","OrdPrice":"10","OrdAmt":"10","OrdCat":"治疗费","YBCAT":"医保"},{"OrdDate":"2016-11-14","OrdName":"气压治疗","OrdQty":"6","OrdUom":"每部位","OrdPrice":"8","OrdAmt":"48","OrdCat":"治疗费","YBCAT":"医保"},{"OrdDate":"2016-11-12","OrdName":"住院诊查费","OrdQty":"1","OrdUom":"日","OrdPrice":"6","OrdAmt":"6","OrdCat":"诊察费","YBCAT":"医保"},{"OrdDate":"2016-11-12","OrdName":"冷暖费(2人间)","OrdQty":"1","OrdUom":"日\\/床","OrdPrice":"6","OrdAmt":"6","OrdCat":"其它费","YBCAT":"医保"},{"OrdDate":"2016-11-12","OrdName":"妇科新楼双人间","OrdQty":"1","OrdUom":"日","OrdPrice":"180","OrdAmt":"180","OrdCat":"治疗费","YBCAT":"医保"},{"OrdDate":"2016-11-13","OrdName":"二级护理","OrdQty":"1","OrdUom":"日","OrdPrice":"10","OrdAmt":"10","OrdCat":"治疗费","YBCAT":"医保"},{"OrdDate":"2016-11-13","OrdName":"气压治疗","OrdQty":"6","OrdUom":"每部位","OrdPrice":"8","OrdAmt":"48","OrdCat":"治疗费","YBCAT":"医保"}]}}' , true);
	}


	public function ScanCardGetCardNo($content)
	{
		return [
			'response' => [
				'appcode' 	=> '1',
				'msg' 	=> [
					'tsmsg' => '000000000018^佘竹君',
				]
			]
		];
	}

	public function getBarcodeByPrintcode($content)
	{
		
	}

	public function setBarcodePrinted($content)
	{

	}

	public function SearchPrice($content)
	{
		$params 	= [
			'hosId' 	=> 10000,
			'flag' 		=> 'SearchPrice',
			'content' 	=> $content,
		];
		$url 	= "http://api-gatewayt.mobimedical.cn:8888/hospital/transfer/index?hosId=10000&flag=SearchPrice&content=".$params['content']."&token=e422537b89955bdca1867860a35b9714";
			$type 	= 'get';
		$header = [
			'Content-Type:application/x-www-form-urlencoded;charset=utf-8',
			'api-version:1',
		];
		Curl::init();
		Curl::setUrl($url);
		Curl::setCustomRequest($type);
		isset($data) && Curl::setParams($data);
		Curl::setHttpHeader($header);
		Curl::setOpt();
		$response 	= Curl::execute();
		$result 	= json_decode($response , true);
		return $result && isset($result['data']) ? $result['data'] : [];
	}

	public function GetMedInfo($content)
	{
		$params 	= [
			'hosId' 	=> 10000,
			'flag' 		=> 'GetMedInfo',
			'content' 	=> $content,
		];
		$url 	= "http://api-gatewayt.mobimedical.cn:8888/hospital/transfer/index?hosId=10000&flag=GetMedInfo&content=".$params['content']."&token=e422537b89955bdca1867860a35b9714";
		$type 	= 'get';
		$header = [
			'Content-Type:application/x-www-form-urlencoded;charset=utf-8',
			'api-version:1',
		];
		Curl::init();
		Curl::setUrl($url);
		Curl::setCustomRequest($type);
		isset($data) && Curl::setParams($data);
		Curl::setHttpHeader($header);
		Curl::setOpt();
		$response 	= Curl::execute();
		$result 	= json_decode($response , true);
		return $result && isset($result['data']) ? $result['data'] : [];
	}
 	
 	public function searchOutHospitalInfo($content)
 	{

 	}

 	public function searchOutHospitalList($content)
 	{

 	}

 	public function submitLeaveHospital($content)
 	{

 	}
}