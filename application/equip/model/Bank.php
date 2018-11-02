<?php
namespace app\equip\model;

use think\Db;

class Bank
{

    /**
     * ID换取银行信息
     * @param $id
     * @return array|false|\PDOStatement|string|\think\Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
	public function detail($id)
	{
		$map 	= ['id' => $id];
		$data 	= Db::name('resource_bank')->where($map)->find();
		return $data ? $data : [];
	}

}