<?php

namespace app\equip\event\v2;

use think\Loader;
use app\equip\controller\Base;
use app\component\response\Response;
use app\component\server\Server;

class Report extends Base
{
	protected $validate = '\app\equip\validate\v2\Report'; 		//定义validate文件

	protected $scene = [ 										//定义需要验证的方法
		'reportList',									
		'reportDetail',
		'reportPrint',
		'barCode',
		'barCodePrint',
	];

	protected $mustId  = [
		'reportList',
		'reportDetail',
		'reportPrint',
		'barCode',
		'barCodePrint',
	];

	/**
	 * 报告列表
	 *
	 * @access 	public
	 * @return 	void
	 */
	public function reportList()
	{
		$hid 	= $this->hospitalId;
		$cardId = $this->data['cardId'];
		/*if ($cardId == '020000000222') {
		Response::message(10000 , [
			['inspecNo' => '2265179600' , 'reportType' => 2 , 'inspecName' => '孕检' , 'date' => '2017-10-10'],
			['inspecNo' => '2262535500' , 'reportType' => 1 , 'inspecName' => '孕检（血检）' , 'date' => '2017-10-10'],
		]);
		}*/
		$response 	= [];
		$params 	= [
			'cardNo' 	=> $cardId,
		];
		$data 	= Server::ability('hospital')->examine($hid , $params);
		if ($data && $data['code'] == 10000) {
			foreach ($data['data'] as $v) {
				$response[] = ['inspecNo' => $v['inspec_no'] , 'reportType' => 2 , 'inspecName' => $v['item_name'] , 'date' => $v['report_date']];
			}
		}
		$params 	= [
			'cardNo' 	=> $cardId,
		];
		$data 	= Server::ability('hospital')->inspect($hid , $params);
		if ($data && $data['code'] == 10000) {
			foreach ($data['data'] as $v) {
				$response[] = ['inspecNo' => $v['inspec_no'] , 'reportType' => 1 , 'inspecName' => $v['item_name'] , 'date' => $v['report_date']];
			}
		}
		Response::success($response);
	}


	/**
	 * 报告详情
	 *
	 * @access 	public
	 * @return 	void
	 */
	public function reportDetail()
	{
		$hid 		= $this->hospitalId;
		$cardId 	= $this->data['cardId'];
		$inspecNo 	= $this->data['inspecNo'];
		$reportType = isset($this->data['reportType']) ? $this->data['reportType'] : 0;
		$params 	= [
			'cardId' 	=> $cardId,
		];
		$card 		= Server::ability('hospital')->patientCard($hid , '' , $cardId);
		if (!$card || $card['code'] != 10000) Response::message(30000);
		$cardName = $card['data']['userName'];
		$report 	= Loader::model('Report')->detailByInspecNo($hid , $inspecNo , $reportType);
		if ($report) {

			if ($report['print'] == 1) {
				Response::message(30014);
			} else {
				$response 	= json_decode($report['report'] , true);
				Response::message(10000 , $response);
			}
		}
		$params 	= [
			'cardNo' 	=> $cardId,
			'inspecNo' 	=> $inspecNo,
		];
		if ($reportType == 1) { 	
			$data 	= Server::ability('hospital')->inspectDetail($hid , $params);
		} else {
			$data 	= Server::ability('hospital')->examineDetail($hid , $params);
		}
		if (!$data) { Response::message(10101);}
		$data['code'] != 10000 && Response::message(10102);
		$cardInfo 		= [
			'cardId' 		=> $cardId,
			'cardName' 		=> $cardName,
		];
		$report 	= $data['data'];
		$result 	= Loader::model('Report')->addReport($hid , $inspecNo , $report , $cardInfo);
		if ($result) {
			Response::success($report);
		} else {
			Response::message(10103);
		}
		
	}

	/**
	 * 标记报告已打印
	 *
	 * @access 	public
	 * @return 	void
	 */
	public function reportPrint()
	{
		$hid 		= $this->hospitalId;
		$cardId 	= $this->data['cardId'];
		$inspecNo 	= $this->data['inspecNo'];
		$reportType = isset($this->data['reportType']) ? $this->data['reportType'] : 0;
		$report 	= Loader::model('Report')->detailByInspecNo($hid , $inspecNo , $reportType);
		if (!$report) {
			Response::message(30014);
		}
		if ($report['print'] == 1) {
			Response::success();
		}
		$id 		= $report['id'];
		$result 	= Loader::model('Report')->markPrint($id);
		if ($result) {
			Response::success();
		} else {
			Response::message(10103);
		}
	}

	/**
	 * 获取打印条码
	 *
	 * @access 	public
	 * @return 	void
	 */
	public function barCode()
	{
		$hid 		= $this->hospitalId;
		$printCode  = $this->data['printCode'];
		$barCode 	= Loader::model('Report')->barCodeDetail($hid , $printCode);
		if ($barCode) {
			Response::success($barCode['bar_code']);
			//Response::message(30015);
		}
		$params 	= [
			'barCode' 	=> $printCode,
		];
		$data 		= Server::ability('hospital')->barCode($hid , $params);
		if (!$data) { Response::message(10101); }
		$data['code'] != 10000 && Response::message(10102);
		if ($data['data']['printed'] == 1) {
			Response::message(30015);
		}
		$barCode 	= $data['data']['printStr'];
		$result 	= Loader::model('Report')->addBarCode($hid , $printCode , $barCode);
		if ($result) {
			Response::success($barCode);
		} else {
			Response::message(10103);
		}
	}

	/**
	 * 标记检验条码已经打印
	 *
	 * @access 	public
	 * @return 	void
	 */
	public function barCodePrint()
	{
		$hid 		= $this->hospitalId;
		$printCode  = $this->data['printCode'];
		$barCode 	= Loader::model('Report')->barCodeDetail($hid , $printCode);
		if (!$barCode) {
			Response::message(30015);
		}
		if ($barCode['print'] == 1) {
			Response::message(10000 , true);
		}
		$params 	= [
			'barCode' 	=> $printCode,
		];
		$data 	 	= Server::ability('hospital')->markBarCodePrint($hid , $params);
		if (!$data) { Response::message(10101); }
		$data['code'] != 10000 && Response::message(10102);
		$id 		= $barCode['id'];
		$result 	= Loader::model('Report')->markBarCodePrint($id);
		if ($result) {
			Response::success();
		} else {
			Response::message(10103);
		}
	}


}