<?php
namespace app\equip\model;

use think\Db;

class Inquiry 
{

	/** 
	 * 订单详情
	 *
	 * @access 	public
	 * @param 	int 	$orderId 	订单ID
	 * @return  array
	 */
	public static function detailByOrderId($orderId)
	{
		$map 	= ['order_id' => $orderId];
		$data 	= Db::name('order_inquiry')->where($map)->find();
		return $data ? $data : [];
	}

    /**
     * 更新问诊状态
     * @param $id
     * @return array|false|\PDOStatement|string|\think\Model
     */
    public static function updateInquiryStatus($id,$status){
        $map 	= ['id' => $id];
        $update 	= ['status' => $status];
        return Db::name('order_inquiry')->where($map)->update($update);
    }

	/** 
	 * 医生
	 *
	 * @access 	public
	 * @param 	int 	$orderId 	订单ID
	 * @return  array
	 */
	public function doctorDoing($doctorId) 
	{
		$map 	= ['doctor_id' => $doctorId , 'status' => 1];
		$data 	= Db::name('order_inquiry')->where($map)->find();
		return $data ? $data : [];
	}

	/**
	 * 设置医生正在咨询
	 *
	 * @access 	public
	 * @param 	int 	$doctorId 	医生id
	 * @param 	int 	$inquiryId 	咨询单id
	 * @return 	array 	找寻最近的一个号
	 */
    public function setDoctorDoing($equipId , $doctorId , $inquiryId)
    {
        $map 	 = ['equip_id' => $equipId];
        /*$account = Db::name('equipment_account_huawei')->where($map)->find();
        if (!$account) {
            $map 	= ['inquiry_id' => 0 , 'call_id' => 0 , 'equip_id' => 0];
            $account= Db::name('equipment_account_huawei')->where($map)->find();
        }
        if (!$account) return [];*/
        Db::startTrans();
        $callData['status'] = 2;
        Db::name('inquiry_call')->where("inquiry_id={$inquiryId}")->update($callData);
        $call 	 = ['inquiry_id' => $inquiryId];
        $callId  = Db::name('inquiry_call')->insertGetId($call);
        if (!$callId) { Db::rollback(); return [];}
        $map 	 = ['id' => $inquiryId];
        $update  = ['status' => 1];
        $result  =  Db::name('order_inquiry')->where($map)->update($update);
        if ($result === false) {
            Db::rollback();return [];
        }
        /*$accountId 	= $account['id'];
        $map 	 = ['id' => $accountId];
        $update  = ['equip_id' => $equipId , 'inquiry_id' => $inquiryId , 'call_id' => $callId];
        $result  = Db::name('equipment_account_huawei')->where($map)->update($update);
        if ($result === false) {
            Db::rollback();
            return [];
        }*/
        $map 	 = ['doctor_id' => $doctorId , 'type' => 98];
        $update  = ['busy' => 1];
        $result  = Db::name('doctor_service')->where($map)->update($update);
        if ($result === false) {{
            Db::rollback();

            return [];
        }}
        Db::commit();
        return [
            'callId' 	=> $callId,
            'inquiryId' => $inquiryId,
            //'account' 	=> $account['number'],
            //'password' 	=> $account['password'],
            'doctorId' 	=> $doctorId,
        ];

    }

	

	/**
	 * 获取报告
	 *
	 * @access 	public
	 * @param 	int 	$inquiryId 	咨询id
	 * @return 	array
	 */
	public function report($inquiryId)
	{
		$map 	= ['inquiry_id' => $inquiryId];
		$data 	= Db::name('inquiry_report')->where($map)->select();
		return $data ? array_column($data , 'report') : [];
	}

	/**
	 * 增加报告
	 *
	 * @access 	public
	 * @param 	int 	$inquiryId 	咨询id
	 * @param 	array 	$pic 		咨询图片
	 * @return 	boolen
	 */
	public function addReport($inquiryId , $pic)
	{
		$add 	= [];
		$time 	= time();
		foreach($pic as $v) {
			$add[] 	= ['inquiry_id' => $inquiryId , 'report' => $v , 'create_time' => $time];
		}	
		return Db::name('inquiry_report')->insertAll($add) ? true : false;
	}

	/**
	 * 增加截图
	 *
	 * @access 	public
	 * @param 	int 	$inquiryId 	咨询id
	 * @param 	int 	$callId 	呼叫id
	 * @param   array 	$pic 		图片
	 * @return 	boolen
	 */
	public function addScreen($inquiryId , $callId , $pic)
	{
		$map 	= ['inquiry_id' => $inquiryId , 'id' => $callId];
		$update = ['screen' => json_encode($pic)];
		return Db::name('inquiry_call')->where($map)->update($update) !== false ? true : false;	
	}

    /**
     *
     * @access 	public
     * @param 	int 	$inquiryId 	咨询id
     * @param 	int 	$callId 	呼叫id
     * @param   array 	$pic 		图片
     * @return 	boolen
     */
    public function inquiry($inquiryId , $callId , $pic)
    {
        $map 	= ['inquiry_id' => $inquiryId , 'id' => $callId];
        $update = ['screen' => json_encode($pic)];
        return Db::name('inquiry_call')->where($map)->update($update) !== false ? true : false;
    }

    /**
     * 设置问诊失败
     *
     * @param $id
     * @return bool
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function setInquiryFail($id){
        $data['status'] = 2;
        $result = Db::name('order_inquiry')->where("id='{$id}'")->update($data);
        return $result !== false ? true : false;
    }

    /**
     * 设置问诊成功
     *
     * @param $id
     * @param $successInfo
     * @return bool
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
     public function setInquirySuccess($id , $successInfo){
        $data['status'] = 1;
        $result = Db::name('order_inquiry')->where("id='{$id}'")->update($data);
        return $result !== false ? true : false;
    }



}