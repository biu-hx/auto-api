<?php
namespace app\equip\model;

use app\component\response\Response;
use think\Db;

class Dept 
{

    /**
     * 问诊科室
     *
     * @param $projectId
     * @param int $hospitalId
     * @return array|false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
	public function inquiryDept($projectId , $hospitalId = 0)
	{
		$map 	= ['a.project_id' => $projectId];
		$hospitalId && $map['c.hospital_id'] = $hospitalId;
		if($hospitalId){
            $sql = "SELECT * FROM `emt_doctor_service` AS b 
JOIN `emt_doctor` AS d ON d.id=b.`doctor_id` 
JOIN `emt_project_dept` AS a ON a.`id`=b.`dept_id` 
WHERE b.`project_id`='{$projectId}' AND d.`hospital_id`='{$hospitalId}' AND a.status=1
GROUP BY a.`id` ORDER BY a.sort ASC ; ";
        }else{
            $sql = "SELECT * FROM `emt_project_dept` WHERE project_id={$projectId} AND status=1 ORDER BY sort ASC ";
        }
        $data = Db::query($sql);
		return $data ? $data : [];
	}

	/**
	 * 问诊科室详情
	 *
	 * @access 	public
	 * @param 	int 	$id 		科室ID
	 * @return 	array
	 */
	public function inquiryDetail($id)
	{
		$map 	= ['id' => $id];
		$data 	= Db::name('project_dept')->where($map)->find();
		return $data ? $data : [];
	}

	/**
	 * 科室列表
	 *
	 * @access 	public
	 * @return 	array
	 */
	public static function deptList()
	{
		$data 	= Db::name('dept')->select();
		return $data ? $data : [];
	}

	/**
	 * 医院科室信息
	 *
	 * @access 	public
	 * @param 	int 	$hid 		医院ID
	 * @return 	array
	 */
	public function deptByHospital($hid)
	{
		$map 	= ['hospital_id' => $hid];
		$data 	= Db::name('resource_hospital_dept')->where($map)->select();
		return $data ? $data : [];
	}

	/**
	 * 科室详情
	 *
	 * @access 	public
	 * @param 	int 	$id 	科室ID
	 * @return 	array
	 */
	public function detail($id)
	{
		$map 	= ['id' => $id];
		$data 	= Db::name('dept')->where($map)->find();
		return $data ? $data : [];
	}

	/**
	 * 同步科室数据
	 *
	 * @access 	public
	 * @param 	int 	$hid 	医院ID
	 * @param 	array 	$add 	新增科室
	 * @param 	array 	$update 更新科室
	 * @return 	array
	 */
	public function sync($hid , $add , $update)
	{
		if (!$add && !$update) return true;
		Db::startTrans();
		if ($add) {
			$addAll 	= [];
			foreach ($add as $v) {
				$addAll[] 	= ['name' => $v['name'] , 'his_id' => $v['hisId'] , 'logo' => '' , 'hospital_id' => $hid];
			}
			if (!Db::name('resource_hospital_dept')->insertAll($addAll)) {
				Db::rollback();
				return false;
			}
		}
		if ($update) {
			foreach ($update as $v) {
				$map 	= ['hospital_id' => $hid , 'his_id' => $v['hisId']];
				$update = ['name' => $v['name']];
				if (Db::name('resource_hospital_dept')->where($map)->update($update) === false) {
					Db::rollback();
					return false;
				}
			}
		}
		Db::commit();
		return true;
	}

	public static function hospitalList($deptId , $projectId)
    {
        $where = "";
        $deptId && $where = " and a.`dept_id`='{$deptId}'";
        $sql = "SELECT c.`name`,c.`id`,c.logo FROM `emt_doctor_service` AS a 
JOIN `emt_doctor` AS b ON b.id=a.`doctor_id` 
JOIN `emt_resource_hospital` AS c ON c.`id`=b.`hospital_id` WHERE a.open=1 AND a.project_id='{$projectId}' {$where} GROUP BY c.`id`;";
        $hospitals = Db::query($sql);
        return $hospitals;
    }

}