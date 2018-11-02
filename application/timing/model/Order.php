<?php
namespace app\timing\model;

use think\Db;
use app\component\server\Server;

class Order 
{


	/**
	 * 订单详情
	 *
	 * @access 	public
	 * @param 	int 	$orderId 		订单ID
	 * @return 	array
	 */
	public function detail($orderId)
	{
		$map 	= ['id' => $orderId];
		$data 	= Db::name('order')->where($map)->find();
		return $data ? $data : [];
	}

	/**
	 * 订单支付详情
	 *
	 * @access 	public
	 * @param 	int 	$orderId 		订单ID
	 * @return 	array
	 */
	public function orderPay($orderId)
	{
		$map 	= ['order_id' => $orderId];
		$data 	= Db::name('order_pay')->where($map)->find();
		return $data ? $data : [];
	}

    /**
     * 180s内未支付的挂号订单
     * @param $id
     * @param $time
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
	public function registrationWait($id , $time = 180)
	{
		$time 	= time() - $time;

		$map 	= ['o.type' => 2, 'o.pre_status' => 0 , 'o.status' => 0 , 'o.create_time' => ['lt' , $time] , 'o.project_id' => $id ];
		$data 	= Db::name('order')->alias("o")->field('o.id,o.hospital_id,r.success_info,r.business_info,r.card_id')->join("order_registration r" , "r.order_id=o.id")->where($map)->select();
		return $data;
	}

	/**
	 * 1800s内未支付的其他订单
	 *
	 * @access 	public
	 * @return 	array
	 */
	public function wait()
	{
		$time 	= time() - 1800;
		$map 	= ['type' => ['neq' , 2] , 'pre_status' => 0 , 'status' => 0 , 'create_time' => ['lt' , $time]];
		$data 	= Db::name('order')->field('id')->where($map)->select();
		return $data ? array_column($data , 'id') : [];
	}

	/** 
	 * 取消订单
	 *
	 * @access 	public
	 * @param 	int 	$orderId 		订单ID
	 * @return  boolen
	 */
	public function cancelOrder($orderArr)
	{
	    //释放号源
        foreach ($orderArr as $order){
            if($order['hospital_id'] == 61757){
                $business = json_decode($order['business_info'] , true);
                $params['periodId'] = isset($business['periodId']) ? $business['periodId'] : '';
                $params['apptId'] = $business['apptId'];
                $params['CardNo'] = $order['card_id'];
                $params['date'] = $business['scheduleDate'];
                $result = Server::ability('hospital')->releaseSource($order['hospital_id'],$params);
            }
            $map 	= ['id' => ['EQ' , $order['id']]];
            $update = ['status' => -2];
            $result = Db::name('order')->where($map)->update($update);
            if($result === false){
                return false;
            }
        }
		return true;
	}

	/** 
	 * 订单退款
	 *
	 * @access 	public
	 * @param 	int 	$orderId 		订单ID
	 * @return  boolen
	 */
	public function refundOrder($orderId)
	{
		$map 	= ['id' => ['in' , $orderId]];
		$update = ['status' => 2];
		$result = Db::name('order')->where($map)->update($update);
		return $result !== false ? true : false; 
	}

    /**
     * 设置订单状态按交易号
     *
     * @access 	public
     * @param 	int 	$orderId 		订单ID
     * @return  boolen
     */
    public function setTradeiddStatus($trade_id,$info)
    {
        if(is_array($info)){
            $status = $info['status'];
            $refund_time = $info['refund_time'];
        }else{
            $status = $info;
            $refund_time = 0;
        }

        //查询微信订单号
        $where = array('transaction_id'=>$trade_id);
        $payInfo = Db::name('order_pay')->where($where)->find();
        if(!$payInfo){
            return;
        }
        $map 	= ['id' => ['in' , $payInfo['order_id']]];
        $update = ['status' => $status,'refund_time'=>$refund_time];
        $result = Db::name('order')->where($map)->update($update);
        return $result !== false ? true : false;
    }

	/**
	 * 订单日志处理
	 *
	 * @access 	public
	 * @param 	int 	$orderId 	订单ID
	 * @param 	int 	$status 	订单状态
	 * @param 	array 	$orderInfo 	订单信息
	 * @param   array 	$log 		日志信息
	 * @param 	int 	$time 		时间
	 * @return  boolen
	 */
	public function orderCancelLog($orderId , $time = 0) 
	{
		$time || $time = time();
		if (!$orderId) return true;
		$log 		= [
			'type' 		=> 'cancel',
			'person'  	=> 'system',
		];
		foreach ($orderId as $v) {
			$insert[] = [
				'order_id' 	=> $v,
				'status' 	=> -2,
				'time' 		=> $time,
				'order_info'=> [],
				'log' 		=> json_encode($log , JSON_UNESCAPED_UNICODE),
			];
		}
		$result  	= Db::name('order_log')->insertAll($insert);
		return $result ? true : false;
	}

	public function getCardByOrderId($type,$order_id){
        if($type==2){//挂号
            $regInfo = Db::name('order_registration')->where(array("order_id"=>$order_id))->find();
            return $regInfo['card_id'];
        }elseif($type==5){
            $regInfo = Db::name('order_outpatient')->where(array("order_id"=>$order_id))->find();
            return $regInfo['card_id'];
        }
        return "no_type";
    }

    public function getHuaxiRefundStatus(){
	    $where = array("r.status"=>array("in",array(2,3)),"r.hospital_id"=>10000);
        $data = Db::name('order')->alias('r')->join('order_pay p', 'r.id=p.order_id')->field('p.transaction_id')->where($where)->select();
        return $data ? $data : [];
    }
}