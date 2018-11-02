<?php
namespace app\equip\model;

use think\Db;

class Doctor 
{

	/**
	 * 医生详情
	 *
	 * @access 	public
	 * @param 	int 	$id 	主键
	 * @return 	array
	 */
	public function detail($id)
	{
		$map 	= ['id' => $id];
		$data 	= Db::name('doctor')->where($map)->find();
		return $data ? $data : [];
	}

	/**
	 * 医生详情
	 *
	 * @access 	public
	 * @param 	int 	$hid 	医院ID
	 * @param 	string 	$hisId 	医生HisID 	
	 * @return 	array
	 */
	public function detailByHisId($hid , $hisId)
	{
		$map 	= ['a.hospital_id' => $hid , 'a.his_id' => $hisId];
		$data 	= Db::name('doctor')
				->alias('a')
				->join('emt_doctor_info b' , 'a.id = b.doctor_id')
				->where($map)
				->find();
		return $data ? $data : [];
	}

	/**
	 * 医生列表
	 *
	 * @access 	public
	 * @param 	int 	$hid 	医院ID
	 * @param 	string 	$hisId 	医生HisID 	
	 * @return 	array
	 */
	public static function listByHisId($hid , $hisId)
	{
		$map 	= ['a.hospital_id' => $hid , 'a.his_id' => ['in' , $hisId]];
		$data 	= Db::name('doctor')
				->alias('a')
				->join('emt_doctor_info b' , 'a.id = b.doctor_id')
				->where($map)
				->select();
		return $data ? $data : [];
	}




    /**
     *
     * 咨询医生
     * @param $projectId
     * @param $deptId
     * @param int $hospitalId
     * @return array|false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
	public function inquiryDoctor($projectId , $deptId =0 , $hospitalId = 0)
	{
		$map 	= ['a.project_id' => $projectId , 'a.type' => 98 , 'a.open' => 1];
        $hospitalId && $map['d.hospital_id'] = $hospitalId;
        $deptId && $map['a.dept_id'] = $deptId;
		$data 	= Db::name('doctor_service')
				->alias('a')
				->join('emt_doctor d' , 'd.id = a.doctor_id')
				->join('emt_doctor_info c' , 'a.doctor_id = c.doctor_id')
				->where($map)
				->order("a.online desc,a.busy asc")
				->select();
		return $data ? $data : [];
	}

	public function inquiryDoctorDetail($projectId , $doctorId)
	{
		$map 	= ['a.project_id' => $projectId , 'a.doctor_id' => $doctorId , 'a.type' => 98 , 'a.open' => 1];
		$data 	= Db::name('doctor_service')
				->alias('a')
                ->field('a.*,c.*,d.*,f.name as hospitalName,a.dept_id as deptId')
                ->join('emt_doctor d' , 'd.id = a.doctor_id')
				->join('emt_resource_hospital f' , 'f.id = d.hospital_id')
				->join('emt_doctor_info c' , 'a.doctor_id = c.doctor_id')
				->where($map)
				->find();
		return $data ? $data : [];
	}

	public function accountHuawei($doctorId)
	{
		$map 	= ['doctor_id' => $doctorId];
		$data 	= Db::name('doctor_account_huawei')->where($map)->find();
		return $data ? $data : [];
	}

    /**
     * 查询医生是否是小程序登陆
     * @param $doctorId
     */
    public static function isLittleAppLogin($doctorId){
        $map = ['doctor_id'=>$doctorId];
        $data = Db::name('doctor_auth')->field('login_equip')->where($map)->find();
        return 'littleApp'==$data['login_equip'];
    }

    /**
     * 获取医生小程序openid
     * @param $doctorId
     */
    public static function getLittleAppOpenid($doctorId){
        //查询医生openid
        $docInfo = Db::name('littleapp_user')->field('openid')->where(['bind_doctor'=>$doctorId])->find();
        return $docInfo;
    }

}