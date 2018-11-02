<?php

namespace app\api\event\v1;

use app\api\model\BaseMedical;
use think\Db;
use think\Loader;
use app\api\controller\Base;
use app\component\response\Response;

class Prescription extends Base
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
	 * 搜索药品
	 *
	 * @access 	public
	 * @return 	void
	 */
	public function searchMedical()
	{
        $key_word = $this->getData("key_word");
        if(!$key_word){
            Response::errorMessage("查询内容不能为空");
        }
        $key_word = strtoupper($key_word);
		$response = \app\api\model\Prescription::searchMedical($key_word);
        Response::success($response);
	}




    /**
     * 获取药品相关字典
     */
	public function getMedicalDictionary(){
        $type = $this->getData("type");
        \think\Config::load(APP_PATH.'extra/medicalDictionary.php');
        $medicalDictionary 	= \think\Config::get('medicalDictionary');
        $medicalDictionary = json_decode($medicalDictionary,true);
        if(!isset($medicalDictionary[$type])){
            Response::errorMessage("type类型不正确");
        }
        Response::success($medicalDictionary[$type]);
    }

    /**
     * 提交医嘱
     */
	public function submitPresription(){
	    $nowTime = date("Y-m-d H:i:s");
//        $Adm = $this->getData("Adm");//诊疗号
        $inquiry_id = $this->getData("inquiry_id");//医生编号
        $DocCode = "";//医生编号
        $Diagnose = $this->getData("Diagnose");//诊断
//        $DiagnoseFlag = $this->getData("DiagnoseFlag");
        $medicalListString = $this->getData("medicalList");
        $medicalList = json_decode($medicalListString,true);
        if(!is_array($medicalList)||count($medicalList)==0){
            Response::errorMessage("药品为能为空");
        }
        //写入医嘱表
        $prescription_id = \app\api\model\Prescription::addPrescription($inquiry_id,$DocCode,$Diagnose,$nowTime);
        if(!$prescription_id){
            Response::errorMessage("添加失败");
        }
        $arc_ids = array_column($medicalList,'arc_id');

        //查询药品价格
        $arcList= \app\api\model\Prescription::getMedicalByIds($arc_ids,true);
        //写入医嘱药品表
        $addMedical = [];
        $totalFee = 0;//总价
//        print_r($arcList);exit;
        foreach ($medicalList as $v){
            $totalFee += $arcList[$v['arc_id']]['ArcUnitPrice']*$v['OrderPackQty'];
            $v['prescription_id'] = $prescription_id;
            unset($v['arc_id']);
            $addMedical[] = $v;

        }
        \app\api\model\Prescription::addPrescriptionMedical($addMedical);
        //更新医嘱总费用
        \app\api\model\Prescription::updatePrescription($prescription_id,['totalFee'=>$totalFee]);
        Response::success();
    }


	
}