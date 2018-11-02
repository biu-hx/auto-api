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

class Personal
{

	/**
	 * 医生基本信息
	 * 
	 * @access 	public
	 * @param 	int 	$doctorId 	医生id
	 * @return 	array
	 */
	public static function basic($doctorId)
	{
		$map 	= ['id' => $doctorId];
		$data 	= Db::name('doctor')->where($map)->find();
		return $data ? $data : [];
	}


    public static function docAuthInfo($doctorId)
    {
        $map 	= ['doctor_id' => $doctorId];
        $data 	= Db::name('doctor_auth')->where($map)->find();
        return $data ? $data : [];
    }

	/**
	 * 医生简介信息
	 * 
	 * @access 	public
	 * @param 	int 	$doctorId 	医生id
	 * @return 	array
	 */
	public static function info($doctorId)
	{
		$map 	= ['doctor_id' => $doctorId];
		$data 	= Db::name('doctor_info')->where($map)->find();
		return $data ? $data : [];
	}

	/**
	 * 查询医生是否开通服务
	 * 
	 * @access 	public
	 * @param 	int 	$doctorId 	医生id
	 * @return 	array
	 */
	public static function isAutoOpen($doctorId)
	{
		$map 	= ['doctor_id' => $doctorId , 'type' => 98];
		$data 	=  Db::name('doctor_service')->where($map)->find();
		return $data ? $data['open'] : 0;
	}

	/**
	 * 查询是否在线
	 * 
	 * @access 	public
	 * @param 	int 	$doctorId 	医生id
	 * @return 	int
	 */
	public function isAutoOnline($doctorId)
	{
		$map 	= ['doctor_id' => $doctorId , 'type' => 98];
		$data 	=  Db::name('doctor_service')->where($map)->find();
		return $data ? $data : [];
	}

	/**
	 * 获取华为家真账号
	 * 
	 * @access 	public
	 * @param 	int 	$doctorId 	医生id
	 * @return 	boolen
	 */
	public function huawei($doctorId)
	{
		$map 	= ['doctor_id' => $doctorId];
		$data 	= Db::name('doctor_account_huawei')->where($map)->find();
		return $data ? $data : [];
	}

	/**
	 * 设置上线
	 * 
	 * @access 	public
	 * @param 	int 	$doctorId 	医生id
	 * @return 	boolen
	 */
	public static function autoOnline($doctorId)
	{
	    //设置医生为不忙碌
        
		$map 	= ['doctor_id' => $doctorId , 'type' => 98];
		$update	= ['online' => 1,'busy'=>0];
		//将所有正在问诊改为异常
        Db::name('order_inquiry')->where(['doctor_id' => $doctorId,'status'=>1 ])->update(['status'=>9]);
		return Db::name('doctor_service')->where($map)->update($update) !== false ? true : false;
	}

	/**
	 * 设置下线
	 * 
	 * @access 	public
	 * @param 	int 	$doctorId 	医生id
	 * @return 	boolen
	 */
	public function autoOffline($doctorId)
	{
		$map 	= ['doctor_id' => $doctorId , 'type' => 98];
		$update	= ['online' => 0];
		return Db::name('doctor_service')->where($map)->update($update) !== false ? true : false;
	}

	/**
	 * 获取收入
	 *
	 * @access 	public
	 * @param 	int 	$doctorId 	医生id
	 * @param 	int 	$page 		收入页码
	 * @param 	int 	$pagesize 	收入页码
	 * @return 	array
	 */
	public function income($doctorId , $page , $pagesize)
	{
		$map 	= ['doctor_id' => $doctorId , 'type' => 98];
		$data 	= Db::name('doctor_service')->where($map)->find();
		if (!$data || !$data['open_time']) { return ['price' => '0.00' , 'count' => 0 , 'totalPage' => 1 , 'list' => []]; }
		$open 	= $data['open_time'];
		$year 	= date('Y' , $open);
		$month 	= date('m' , $open);
		$pcount	= (date('Y') - $year) * 12 + (date('m') - $month) + 1; 	//总数据条数
		$begin 	= strtotime(date('Y-m' , strtotime('- '.($page * $pagesize - 1).' month')).'-01');  //开始时间
		$end 	= $page == 1 ? (strtotime(date('Y-m-01' , strtotime('+1 month'))) - 1 ): (strtotime(date('Y-m-01' , strtotime('- '.(($page - 1) * $pagesize - 1).' month'))) - 1); 
		$map 	= [ 	
			'a.doctor_id'	=> $doctorId ,
			'a.status' 		=> 8 ,  
			'b.status' 		=> 1 , 
		];
		$total 	= Db::name('order_inquiry')
				->alias('a')
				->join('emt_order b' , 'a.order_id = b.id')
				->field('SUM(a.YiSheng) as price , COUNT(*) as count')
				->where($map)
				->find();
		$price 	= $total['price'] ? $total['price'] : '0.00';
		$count 	= $total['count'] ? $total['count'] : 0;
		$map['c.pay_time'] 	= ['between' , [$begin , $end]];
		$field 	= [
			'SUM(a.YiSheng)' 	=> 'price',
			'COUNT(*)' 		=> 'count',
			"FROM_UNIXTIME(c.pay_time,'%Y-%m')"	=> 'date',
		];
		$data 	= Db::name('order_inquiry')
				->alias('a')
				->join('emt_order b' , 'a.order_id = b.id')
				->join('emt_order_pay c ' , ' a.order_id = c.order_id')
				->field($field)
				->where($map)
				->group('date')
				->select();
		$data 	|| $data = [];
		$date 	= [];
		for ($i = ($pagesize - 1) ; $i >= 0 ; $i --) {
			if ($open > strtotime('+ '.$i.' month' , $begin)) continue;
			$key 		= date('Y-m' , strtotime('+ '.$i.' month' , $begin));
			list($year , $month)= explode('-' , $key);
			$date[$year.$month] = ['price' => '0.00' , 'count' => 0 , 'year' => (int) $year , 'month' => (string) $month];
		}
		foreach ($data as $v) {
			list($year , $month)= explode('-' , $v['date']);
			$date[$year.$month] 	= ['price' => (float) $v['price'] , 'count' => (int) $v['count'] , 'year' => (int)$year , 'month' => (string)$month];
		}
		return ['price' => $price ? (float) $price : '0.00' , 'count' => (int) $count  , 'totalPage' => (int) ceil($pcount / 10) , 'list' => array_merge($date)];
	}

	/**
	 * 修改医生基本信息
	 *
	 * @access 	public
	 * @param 	int 	$doctorId 	医生id
	 * @param 	string 	$field 		修改的字段
	 * @param 	string 	$content 	修改的内容
	 * @return 	boolen
	 */
	public function modifyField($doctorId , $field , $content)
	{
		$map 	= ['doctor_id' => $doctorId];
		$update	= [$field => $content];
		return Db::name('doctor_info')->where($map)->update($update) !== false ? true : false;
	}


}