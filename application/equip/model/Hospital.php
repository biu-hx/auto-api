<?php
namespace app\equip\model;

use think\Db;

class Hospital 
{

	/**
	 * 医院详情
	 * 
	 * @access 	public
	 * @param 	int 	$id 	医院ID
	 * @return 	array
	 */
	public static function detail($id)
	{
		$map 	= ['id' => $id];
		$data 	= Db::name('resource_hospital')->where($map)->find();
		return $data ? $data : [];
	}
    


    /**
     *  获取院区信息
     * @param $id
     * @return array|false|\PDOStatement|string|\think\Model
     */
    public static function getDistrictInfo($id){
        $map 	= ['id' => $id];
        $data 	= Db::name('resource_hospital_district')->where($map)->find();
        return $data ? $data : [];
    }



	/**
	 * 医院详情列表
	 * 
	 * @access 	public
	 * @param 	array 	$id 	医院ID array
	 * @return 	array
	 */
	public function listById($id)
	{
		if (!$id) return [];
		$map 	= ['id' => ['in' , $id]];
		$data 	= Db::name('resource_hospital')->where($map)->select();
		return $data ? $data : [];
	}

    public static function getHospitalDistrict($main_hospital_id){
        $map 	= ['main_hospital_id' => $main_hospital_id];
        $data 	= Db::name('resource_hospital_district')->where($map)->select();
        return $data ? $data : [];
    }
}