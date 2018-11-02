<?php
// +----------------------------------------------------------------------
// | 渠道数据
// +----------------------------------------------------------------------
// | Author: duanhy <shongyudmxas@163.com> 
// +----------------------------------------------------------------------
// | version: 1.0
// +----------------------------------------------------------------------

namespace app\api\model;

use think\Db;

class Prescription
{
	public static function searchMedical($key_word)
	{
		$map 	= ['ArcDesc' => ['like','%'.$key_word.'%']];
        $map1 = ['ArcAlias'=>['like','%'.$key_word.'%']];
		$data 	= Db::name('resource_base_medical')->field('update_ts,hospital_id,ArcTypeDesc',true)->where($map)->whereOr($map1)->select();
		return $data ? $data : [];
	}


    public static function getMedicalByIds($arc_ids,$key=false)
    {
        $map 	= ['arc_id' => ['in',$arc_ids]];
        $data 	= Db::name('resource_base_medical')->field('update_ts,hospital_id,ArcTypeDesc',true)->where($map)->select();
        if(!$key){
            return $data ? $data : [];
        }
        $returnArr = [];
        foreach ($data as $v){
            $returnArr[$v['arc_id']] = $v;
        }
        return $returnArr;
    }

    public static function updatePrescription($id,$data){
        Db::name('inquiry_prescription')->where(['id'=>$id])->update($data);
//        echo Db::name('inquiry_prescription')->getLastSql();
    }

	public static function addPrescription($inquiry_id,$DocCode,$Diagnose,$nowTime){
        $db = Db::name('inquiry_prescription');
        $addPres = [];
        $addPres['inquiry_id'] = $inquiry_id;
        $addPres['DocCode'] = $DocCode;
        $addPres['Diagnose'] = $Diagnose;
        $addPres['create_ts'] = $nowTime;
        $prescription_id = $db->insert($addPres,false,true);
        return $prescription_id;
    }

    public static function addPrescriptionMedical($addMedical){
        Db::name('inquiry_prescription_medical')->insertAll($addMedical);
    }



}