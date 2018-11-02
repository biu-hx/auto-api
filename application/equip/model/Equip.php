<?php
namespace app\equip\model;

use think\Db;

class Equip 
{
	
	/**
	 * ID 换设备信息
	 *
	 * @access 	public	
	 * @param 	int 	$id 	设备主键
	 * @return 	array
	 */
	public function detail($id)
	{
		$map 	= ['id' => $id];
		$data 	= Db::name('equipment')->where($map)->find();
		return $data ? $data : [];
	}

	/**
	 * 设备编码换设备信息
	 *
	 * @access 	public	
	 * @param 	string 	$number 设备编码
	 * @return 	array
	 */
	public function detailByNumber($number)
	{
		$map 	= ['code' => $number];
		$data 	= Db::name('equipment')->where($map)->find();
		return $data ? $data : [];
	}

	/**
	 * Mac 换设备信息
	 *  
	 * @access 	public
	 * @param 	string 	$mac 	mac地址
	 * @return 	array
	 */
	public function detailByMac($mac)
	{
		$map 	= ['mac' => $mac];
		$data 	= Db::name('equipment')->where($map)->find();
		return $data ? $data : [];
	}

	/**
	 * 设置硬件信息
	 *
	 * @access 	public
	 * @param 	int 	$equipId 	设备ID
	 * @param 	int 	$type 	 	外设编码
	 * @param 	int 	$code 		纸张枚举值
	 * @return 	boolen
	 */
	public function setPaper($equipId , $type , $code)
	{
		$map 	= ['id' => $equipId];
		$data 	= Db::name('equipment')->where($map)->find();
		$paper 	= json_decode($data['paper'] , true);
		$paper[$type] = $code;
		$paper 	= json_encode($paper);
		$update = ['paper' => $paper];
		return Db::name('equipment')->where($map)->update($update) !== false ? true : false;
	}

	/**
	 * 设置硬件信息
	 *
	 * @access 	public
	 * @param 	int 	$equipId 	设备ID
	 * @param 	int 	$type 	 	外设编码
	 * @param 	int 	$code 		状态枚举值
	 * @return 	boolen
	 */
	public function setHardware($equipId , $type , $code )
	{
		$map 	= ['id' => $equipId];
		$data 	= Db::name('equipment')->where($map)->find();
		$hardware 	= json_decode($data['hardware'] , true);
		$hardware[$type] = $code;
		$hardware 	= json_encode($hardware);
		$update['hardware'] = $hardware;
        if($code == '20001') $update['status'] = 2;
        if($type == 9 && $code != 1000){
            $log['equipment_id'] = $equipId;
            $log['create_time'] = time();
            $log['fault_type'] = 'H5';
            Db::name('equipment_fault_log')->insert($log);
        }
        if($type == 1 && $code == 20003){
            $paper[$type] = $code;
            $update['paper'] = $paper;
        }
        return Db::name('equipment')->where($map)->update($update) !== false ? true : false;
	}

	/** 
	 * 设置网络状态
	 * 
	 * @access 	public
	 * @param 	int 	$equipId  	设备ID
	 * @return 	boolen
	 */
	public function setNetwork($equipId)
	{
		$map 	= ['id' => $equipId];
		$data 	= Db::name('equipment')->where($map)->find();
		$hardware 	= json_decode($data['hardware'] , true);
		$hardware['network'] = ['code' => 10000 , 'time' => time()];
		$hardware 	= json_encode($hardware);
		$update 	= ['hardware' => $hardware , 'last_time'=> time()];
		return Db::name('equipment')->where($map)->update($update) !== false ? true : false;
	}

	/**
	 * 设置当前版本 
	 *
	 * @access 	public
	 * @param 	int 	$equipId 	设备ID
	 * @param 	string 	$version 	设置的版本
	 * @return 	boolen
	 */
	public function setVersion($equipId , $version)
	{
		$map 	= ['id' => $equipId];
		$update = ['version' => $version]; 
		return Db::name('equipment')->where($map)->update($update) !== false ? true : false;
	}


}