<?php
namespace app\timing\model;

use think\Db;

class Notify 
{

	public function businessDetail($id)
	{
		$map 	= ['id' => $id];
		$data 	= Db::name('notify_business')->where($map)->find();
		return $data ? $data : [];  
	}

	public function businessSuccess($id , $num)
	{
		$map 	= ['id' => $id];
		$update = ['http_code' => 200 , 'num' => $num];
		$result = Db::name('notify_business')->where($map)->update($update);
		return $result !== false ? true : false;  
	}

	public function businessFail($id , $num , $httpCode)
	{
		$map 	= ['id' => $id];
		$update = ['http_code' => $httpCode ? $httpCode : 0 , 'num' => $num];
		$result = Db::name('notify_business')->where($map)->update($update);
		return $result !== false ? true : false;  
	}


}