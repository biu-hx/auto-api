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

class Inquiry
{

	/**
	 * 进行中的咨询
	 * 
	 * @access 	public
	 * @param 	int 	$doctorId 	医生id
	 * @param 	int 	$page 		页码
	 * @param 	int 	$pagesize 	每页显示数量
	 * @return 	array
	 */
	public function inquiryDoing($doctorId , $page , $pagesize)
	{
		$map 	= ['a.doctor_id' => $doctorId , 'a.status' => 1 , 'b.status' => 1];
		$field 	= "a.status,a.id as inquiryId , b.order_number as orderNumber,FROM_UNIXTIME(c.pay_time , '%Y-%m-%d %H:%i') as payTime";
		$count 	= Db::name('order_inquiry')
				->alias('a')
				->join('emt_order b' , 'a.order_id = b.id')
				->where($map)
				->count();
		if (!$count) { return ['totalPage' => 1 , 'list' => []];}
		$limit 	= (($page-1) * $pagesize).','.$pagesize;
		$data 	= Db::name('order_inquiry')
				->alias('a')
				->join('emt_order b' , 'a.order_id = b.id')
				->join('emt_order_pay c' , 'a.order_id = c.order_id')
				->field($field)
				->where($map)
				->limit($limit)
				->order('c.pay_time desc')
				->select();
		return ['totalPage'=> ceil($count / 10) , 'list' => $data ? $data : []];
	}

	/**
	 * 结束的咨询
	 * 
	 * @access 	public
	 * @param 	int 	$doctorId 	医生id
	 * @param 	int 	$page 		页码
	 * @param 	int 	$pagesize 	每页显示数量
	 * @return 	array
	 */
	public function inquiryOver($doctorId  , $page , $pagesize)
	{
		$map 	= ['a.doctor_id' => $doctorId , 'a.status' => 8 , 'b.status' => 1];
		$field 	= "a.status,a.id as inquiryId , b.order_number as orderNumber,FROM_UNIXTIME(c.pay_time , '%Y-%m-%d %H:%i') as payTime";
		$count 	= Db::name('order_inquiry')
				->alias('a')
				->join('emt_order b' , 'a.order_id = b.id')
				->where($map)
				->count();
		if (!$count) { return ['totalPage' => 1 , 'list' => []];}
		$limit 	= (($page-1) * $pagesize).','.$pagesize;
		$data 	= Db::name('order_inquiry')
				->alias('a')
				->join('emt_order b' , 'a.order_id = b.id')
				->join('emt_order_pay c' , 'a.order_id = c.order_id')
				->field($field)
				->where($map)
				->limit($limit)
				->order('c.pay_time desc')
				->select();
		return ['totalPage'=> ceil($count / 10) , 'list' => $data ? $data : []];
	}

	/**
	 * 中断的咨询
	 * 
	 * @access 	public
	 * @param 	int 	$doctorId 	医生id
	 * @param 	int 	$page 		页码
	 * @param 	int 	$pagesize 	每页显示数量
	 * @return 	array
	 */
	public function inquiryBreak($doctorId  , $page , $pagesize)
	{
		$map 	= ['a.doctor_id' => $doctorId , 'a.status' => 11 , 'b.status' => 1];
		$field 	= "a.status,a.id as inquiryId , b.order_number as orderNumber,FROM_UNIXTIME(c.pay_time , '%Y-%m-%d %H:%i') as payTime";
		$count 	= Db::name('order_inquiry')
				->alias('a')
				->join('emt_order b' , 'a.order_id = b.id')
				->where($map)
				->count();
		if (!$count) { return ['totalPage' => 1 , 'list' => []];}
		$limit 	= (($page-1) * $pagesize).','.$pagesize;
		$data 	= Db::name('order_inquiry')
				->alias('a')
				->join('emt_order b' , 'a.order_id = b.id')
				->join('emt_order_pay c' , 'a.order_id = c.order_id')
				->field($field)
				->where($map)
				->limit($limit)
				->order('c.pay_time desc')
				->select();
		return ['totalPage'=> ceil($count / 10) , 'list' => $data ? $data : []];
	}


    /**
     * 所有的咨询
     *
     * @access 	public
     * @param 	int 	$doctorId 	医生id
     * @param 	int 	$page 		页码
     * @param 	int 	$pagesize 	每页显示数量
     * @return 	array
     */
    public function inquiryAll($doctorId  , $page , $pagesize)
    {
        $map 	= ['a.doctor_id' => $doctorId ,'a.status'=>['neq',1]];
        $field 	= "a.status,a.id as inquiryId , b.order_number as orderNumber,FROM_UNIXTIME(c.pay_time , '%Y-%m-%d %H:%i') as payTime";
        $count 	= Db::name('order_inquiry')
            ->alias('a')
            ->join('emt_order b' , 'a.order_id = b.id')
            ->where($map)
            ->count();
        if (!$count) { return ['totalPage' => 1 , 'list' => []];}
        $limit 	= (($page-1) * $pagesize).','.$pagesize;
        $data 	= Db::name('order_inquiry')
            ->alias('a')
            ->join('emt_order b' , 'a.order_id = b.id')
            ->join('emt_order_pay c' , 'a.order_id = c.order_id')
            ->field($field)
            ->where($map)
            ->limit($limit)
            ->order('c.pay_time desc')
            ->select();
//        echo Db::name('order_inquiry')->getLastSql();exit;
        return ['totalPage'=> ceil($count / 10) , 'list' => $data ? $data : []];
    }

	/**
	 * 查询问诊单详情
	 * 
	 * @access 	public
	 * @param 	int 	$doctorId 	医生id
	 * @param 	int 	$inquiryId 	问诊id
	 * @return 	array
	 */
	public static function detail($doctorId , $inquiryId)
	{
		$map 	= ['doctor_id' => $doctorId , 'id' => $inquiryId];
		$data	= Db::name('order_inquiry')->where($map)->find();
//		echo Db::name('order_inquiry')->getLastSql();exit;
		return $data ? $data : [];
	}

	/**
	 * 获取问诊单报告
	 * 
	 * @access 	public
	 * @param 	int 	$inquiryId 	问诊id
	 * @return  array
	 */
	public function report($inquiryId)
	{
		$map 	= ['inquiry_id' => $inquiryId];
		$data	= Db::name('inquiry_report')->where($map)->select();
		return $data ? array_column($data , 'report') : [];
	}

	/**
	 * 获取问诊单截图
	 * 
	 * @access 	public
	 * @param 	int 	$inquiryId 	问诊id
	 * @return  array
	 */
	public static function call($inquiryId)
	{
		$map 	= ['inquiry_id' => $inquiryId , 'begin' => ['gt' , 0]];
		$data	= Db::name('inquiry_call')->where($map)->select();
		foreach ($data as &$v) {
			$v['begin'] 	= $v['begin'] ? date('Y-m-d H:i' , $v['begin']) : '';
			$v['end'] 		= $v['end'] ? date('Y-m-d H:i' , $v['end']) : '';
			$v['screen']	= json_decode($v['screen'] , true);
		}
		return $data ? $data : [];
	}

	/**
	 * 标记问诊结束
	 * 
	 * @access 	public
	 * @param 	int 	$inquiryId 	问诊id
	 * @return  array
	 */
	public function markOver($inquiryId , $doctorId)
	{
		$map 	= ['id' => $inquiryId];
		$update = ['status' => 8];
		$insert = [
			'inquiry_id' 	=> $inquiryId,
			'status' 		=> 8,
			'control_time' 	=> time()
		];
		Db::startTrans();
		if (Db::name('order_inquiry')->where($map)->update($update) === false) {
			Db::rollback();
			return false;
		}

		$map 	= ['doctor_id' => $doctorId , 'type' => 1 , 'busy' => 1];
		$update = ['busy' => 0];
		if (Db::name('doctor_service')->where($map)->update($update) === false) {
			Db::rollback();
			return false;
		}
		if (!Db::name('inquiry_control')->insert($insert)) {
			Db::rollback();
			return false;
		}
		Db::commit();
		return true;


	}

	/**
	 * 标记问诊中断
	 * 
	 * @access 	public
	 * @param 	int 	$inquiryId 	问诊id
	 * @return  array
	 */
	public function markAbnormal($inquiryId  , $doctorId)
	{
		$map 	= ['id' => $inquiryId];
		$update = ['status' => 11];
		$insert = [
			'inquiry_id' 	=> $inquiryId,
			'status' 		=> 11,
			'control_time' 	=> time()
		];
		Db::startTrans();
		if (Db::name('inquiry')->where($map)->update($update) === false) {
			Db::rollback();
			return false;
		}
		$update = ['equip_id' => 0 , 'inquiry_id' => 0 , 'call_id' => 0];
		$map 	= ['inquiry_id' => $inquiryId];
		if (Db::name('equipment_account_huawei')->where($map)->update($update) === false) {
			Db::rollback();
			return false;
		}
		$map 	= ['doctor_id' => $doctorId , 'type' => 1 , 'busy' => 1];
		$update = ['busy' => 0];
		if (Db::name('doctor_service')->where($map)->update($update) === false) {
			Db::rollback();
			return false;
		}
		if (!Db::name('inquiry_control')->insert($insert)) {
			Db::rollback();
			return false;
		}
		Db::commit();
		return true;
	}

	/**
	 * 查询是否有这个通话单
	 *
	 * @access 	public
	 * @param   int 	$inquiryId 	问诊单id	
	 * @param 	int 	$callId 	通话单id
	 * @return 	boolen 	
	 */
	public function isCall($inquiryId , $callId)
	{
		$map 	= ['inquiry_id' => $inquiryId , 'id' => $callId];
		return Db::name('inquiry_call')->where($map)->count() ? true : false;
	}




	/**
	 * 标记通话开始时间
	 *
	 * @access 	public
	 * @param   int 	$inquiryId 	问诊单id	
	 * @param 	int 	$callId 	通话单id
	 * @param 	int 	$begin 		开始时间
	 * @return 	boolen 	
	 */
	public static function markCallBegin($inquiryId , $callId , $begin)
	{
		$map 	= ['inquiry_id' => $inquiryId , 'id' => $callId];
		$update	= ['begin' => $begin,'status' => 1];
		return Db::name('inquiry_call')->where($map)->update($update) !== false ? true : false;
	}

	/**
 * 标记通话结束时间
 *
 * @access 	public
 * @param   int 	$inquiryId 	问诊单id
 * @param 	int 	$callId 	通话单id
 * @param 	int 	$end 		结束时间
 * @return 	boolen
 */
    public static function markCallEnd($inquiryId , $callId , $end,$eventType='EVENT_OVER')
    {
        if($eventType=='EVENT_OVER'){
            $status = 2;
        }else{
            $status = 3;
        }
        $callDb = Db::name('inquiry_call');
        $map 	= ['inquiry_id' => $inquiryId , 'id' => $callId];
        $callInfo = $callDb->where($map)->find();
        $update	= ['end' => $end,'status'=>$status,'holding_time'=>$end-$callInfo['begin']];
        //将其它未结束的标记为异常结束
        $map_1 = ['inquiry_id' => $inquiryId ,'status'=>1, 'id' => ['neq',$callId]];
        $callDb->where($map_1)->update(['status'=>4,'holding_time'=>0,'end'=>$end]);
        return Db::name('inquiry_call')->where($map)->update($update) !== false ? true : false;
    }

    /**
     * 标记通话结束时间
     *
     * @access 	public
     * @param   int 	$inquiryId 	问诊单id
     * @param 	int 	$callId 	通话单id
     * @param 	int 	$end 		结束时间
     * @return 	boolen
     */
    public static function getCallDetail($callId)
    {
        $map 	= ['id' => $callId];
        $data = Db::name('inquiry_call')->where($map)->find();
        return $data ? $data : [];
    }

    public static function setCallEndTime($callId)
    {
        $map 	= ['id' => $callId];
        $data = Db::name('inquiry_call')->where($map)->update(['end'=>time()]);
        return $data;
    }


    /**
     * 设置通话异常
     *
     * @param 	int 	$callId 	通话单id
     */
    public static function setCallStatus($callId,$status=3)
    {
        $callDb = Db::name('inquiry_call');
        $map 	= ['id' => $callId];
        $callInfo = $callDb->where($map)->find();
        $update	= ['status'=>$status,'holding_time'=>time()-$callInfo['begin']];
        return $callDb->where($map)->update($update);
    }

	/**
	 * 获取这个咨询单的总通话时间
	 * 
	 * @access 	public
	 * @param 	$int 	$inquiryId 	问诊单id
	 * @return 	int 
	 */
	public function holdingTime($inquiryId)
	{
		$map 	= ['inquiry_id' => $inquiryId , 'status' => ['in' , [2,3]]];
		$time 	= Db::name('inquiry_call')->where($map)->sum('holding_time');
		return $time ? ceil($time/60) : 0;
	}

	/**
	 * 更新总通话时间
	 *
	 * @access 	public
	 * @param 	$int 	$inquiryId 	问诊单id
	 * @param 	$int 	$time  		总通话时间
	 * @return 	boolen 
	 */
	public function setHoldingTime($inquiryId , $time)
	{
		$map 	= ['id' => $inquiryId];
		$update = ['holding_time' => $time];
		return Db::name('order_inquiry')->where($map)->update($update) !== false ? true : false;
	}

	/**
	 * 获取当前通话问诊单
	 * 
	 * @access 	public
	 * @param 	string 	$number 家真号码
	 * @return  array
	 */
	public function relate($number)
	{
		$number = substr($number , 0 , 1) == '0' ? '+86'.substr($number , 1) : $number;
		$map 	= ['number' => $number];
		$data 	= Db::name('equipment_account_huawei')->where($map)->find();
		return $data ? $data : [];
	}

	/**
	 * 获取购买时的自助机
	 * 
	 * @access 	public
	 * @param 	string 	$inquiryId 问诊id
	 * @return  array
	 */
	public function equip($inquiryId)
	{
		$map 	= ['inquiry_id' => $inquiryId];
		$data 	= Db::name('inquiry_equipment')->where($map)->find();
		return $data ? $data : [];

	}

    /**
     * 写入formid表
     * @param $form_id 小程序formid
     * @return array|false|\PDOStatement|string|\think\Model
     */
    public static function insertFormid($form_id,$openid)
    {
        $insert 	= ['form_id' => $form_id,'openid'=>$openid,'create_ts'=>time()];
        return Db::name('littleapp_form_id')->insert($insert);
    }

    /**
     * 查找一条
     * @return mixed
     */
    public static function selectOneFormId($openid)
    {
        //删除六天前的formid
        $map 	= ['create_ts' => ['lt',time()-6*24*3600]];
        Db::name('littleapp_form_id')->where($map)->delete();
        //查询一条有效的formid
        $map 	= ['used' => 0,'openid'=>$openid];
        $findData = Db::name('littleapp_form_id')->order("id asc")->where($map)->find();
        if(!$findData){
            return false;
        }
        //标记为已使用
        Db::name('littleapp_form_id')->where(['id'=>$findData['id']])->update(['used'=>1]);
        return $findData['form_id'];
    }
}