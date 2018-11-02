<?php
namespace app\equip\model;

use think\Db;

class Report 
{
	
	/**
	 * 检验检查报告信息
	 *
	 * @access 	public 
	 * @param 	int 	$hid 			医院ID
	 * @param 	string 	$inspecNo 		详情编号
	 * @return 	array
	 */	
	public function detailByInspecNo($hid , $inspecNo , $reportType = 0)
	{
		$map 	= ['hid' => $hid , 'inspec_no' => $inspecNo];
		$reportType > 0 && $map['type'] = $reportType;
		$data 	= Db::name('report')->where($map)->find();
		return $data ? $data : [];
	}

	/**
	 * 添加检验检查报告详情
	 *
	 * @access 	public 
	 * @param 	int 	$hid 			医院ID
	 * @param 	string 	$inspecNo 		详情编号
	 * @param 	array 	$report 		报告信息
	 * @param 	arrat 	$cardInfo 		就诊人信息
	 * @return 	boolen
	 */	
	public function addReport($hid , $inspecNo , $report , $cardInfo)
	{
		$insert = [
			'hid' 			=> $hid,
			'inspec_no' 	=> $inspecNo,
			'report' 		=> json_encode($report , JSON_UNESCAPED_UNICODE),
			'card_id' 		=> $cardInfo['cardId'],
			'card_name' 	=> $cardInfo['cardName'],
			'print' 		=> 0,
			'type' 			=> $report['report_type'],
			'create_time' 	=> time(),
		]; 
		return Db::name('report')->insert($insert) ? true : false;
	}

	/**
	 * 标记报告已打印
	 *
	 * @access 	public 
	 * @param 	int 	$hid 			报告ID
	 * @return 	boolen
	 */	
	public function markPrint($id)
	{
		$map 	= ['id' => $id];
		$update = ['print' => 1];
		return Db::name('report')->where($map)->update($update) !== false ? true : false;
	}

	/**
	 * 条码详情
	 *
	 * @access 	public 
	 * @param 	int 	$hid 			医院ID
	 * @param 	string 	$printCode 		打印码
	 * @return 	array
	 */	
	public function barCodeDetail($hid , $printCode)
	{
		$map 	= ['hid' => $hid , 'print_code' => $printCode];
		$data 	= Db::name('barcode')->where($map)->find();
		return $data ? $data : [];
	}

	/**
	 * 标记条码已打印
	 *
	 * @access 	public 
	 * @param 	int 	$hid 			医院ID
	 * @return 	boolen
	 */	
	public function markBarCodePrint($id)
	{
		$map 	= ['id' => $id];
		$update = ['print' => 1];
		return Db::name('barcode')->where($map)->update($update) !== false ? true : false;
	}
	
	/**
	 * 添加条码详情
	 *
	 * @access 	public 
	 * @param 	int 	$hid 			医院ID
	 * @param 	string 	$printCode 		打印码
	 * @param 	string 	$barCode 		条码详情
	 * @return 	boolen
	 */	
	public function addBarCode($hid , $printCode , $barCode)
	{
		$insert = [
			'hid' 			=> $hid,
			'print_code' 	=> $printCode,
			'bar_code' 		=> $barCode,
			'print' 		=> 0,
			'create_time' 	=> time(),
		]; 
		return Db::name('barcode')->insert($insert) ? true : false;
	}

}