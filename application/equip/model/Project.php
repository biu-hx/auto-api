<?php
namespace app\equip\model;

use think\Db;

class Project 
{

	/**
	 * ID 换取项目详情
	 *
	 * @access 	public
	 * @param 	int 	$id 	项目ID
	 * @return 	array3
	 */
	public function detail($id)
	{
		$map 	= ['id' => $id];
		$data 	= Db::name('project')->where($map)->find();
		return $data ? $data : [];
	}

    /**
     * hospital_config 换取项目详情
     * @param $hospital
     * @return array|false|\PDOStatement|string|\think\Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function detailByHospital($hospital)
    {
        $where 	= ['hospital_config' => $hospital];
        $data 	= Db::name('project')->alias('pro')->where($where)->join("service_config c" , "c.project_id=pro.id")->find();
        return $data ? $data : [];
    }


}