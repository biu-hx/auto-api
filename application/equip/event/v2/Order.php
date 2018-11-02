<?php

namespace app\equip\event\v2;

use think\Db;
use think\Loader;
use think\Config;
use app\equip\controller\Base;
use app\component\response\Response;
use app\component\server\Server;
use app\component\Common;

class Order extends Base
{

    protected $validate = '\app\equip\validate\v2\Order';

    protected $scene = [
        'searchByOrder',
        'searchByCard',
        'searchByLink',
        'searchByTreat',
        'detail',
        'markPrint',
        'wechatQR',
        'alipayQR',
        'addDinner',
        'setStatus',
    ];

    static $print = 2;

    /**
     * 订单查询-订单号
     *
     * @access 	public
     * @return 	void
     */
    public function searchByOrder()
    {
        $print =Loader::model('Service')->getServiceConfVal( 'receipt','DaYinCiShu',$this->projectId);
        $orderNumber= $this->data['orderNum'];
        $orderType = isset($this->data['orderType']) ? json_decode($this->data['orderType'] , true) : [];
        $data 		= Loader::model('Order')->listByNumber($orderNumber , $orderType);
        $order 		= [];
        if (!$data) {
            Response::success($order);
        }
        foreach ($data as $v) {
            if ($v['project_id'] != $this->projectId || $v['status'] <= 0) {
                continue;
            }
            $order[] 	= [
                'type' 		=> $v['type'],
                'cardId' 	=> $v['card_id'],
                'cardName' 	=> $v['card_name'],
                'price' 	=> $v['price'],
                'status' 	=> $v['status'],
                'orderNum' 	=> $v['order_number'],
                'pay_time'	=> date('Y-m-d' , $v['pay_time']),
                'print' 	=> $print - $v['print'],
            ];
        }
        Response::success($order);

    }

    /**
     * 订单查询-就诊卡
     *
     * @access 	public
     * @return 	void
     */
    public function searchByCard()
    {
        $print =Loader::model('Service')->getServiceConfVal( 'receipt','DaYinCiShu',$this->projectId);
        $cardId = $this->data['cardId'];
        $orderType = isset($this->data['orderType']) ? json_decode($this->data['orderType'] , true) : [];
        $data 	= Loader::model('Order')->listByCardId($cardId , $orderType);
        $order 	= [];
        if (!$data) {
            Response::success($order);
        }
        foreach ($data as $v) {
            if ($v['project_id'] != $this->projectId || $v['status'] <= 0) {
                continue;
            }
            $order[] 	= [
                'type' 		=> $v['type'],
                'cardId' 	=> $v['card_id'],
                'cardName' 	=> $v['card_name'],
                'price' 	=> $v['price'],
                'status' 	=> $v['status'],
                'orderNum' 	=> $v['order_number'],
                'pay_time'	=> date('Y-m-d' , $v['pay_time']),
                'print' 	=> $print - $v['print'],
                'equipmentCode' 	=> $this->number,
            ];
        }
        Response::success($order);

    }

    /**
     * 订单查询-二维码（实际是连接 需要做处理）
     *
     * @access 	public
     * @return 	void
     */
    public function searchByLink()
    {
        $print =Loader::model('Service')->getServiceConfVal( 'receipt','DaYinCiShu',$this->projectId);
        $link 	= $this->data['link'];
        $orderType 	= isset($this->data['orderType']) ? json_decode($this->data['orderType'] , true) : [];
        $orderNumber = '';
        $order 	= [];
        if(preg_match_all("/orderNum=(.*)/i", $link, $result)) {
            $orderNumber = $result[1][0];
        }
        if (!$orderNumber) {
            Response::success($order);
        }
        $data 		= Loader::model('Order')->listByNumber($orderNumber , $orderType);
        if (!$data) {
            Response::success($order);
        }
        foreach ($data as $v) {
            if ($v['project_id'] != $this->projectId || $v['status'] <= 0) {
                continue;
            }
            $order[] 	= [
                'type' 		=> $v['type'],
                'cardId' 	=> $v['card_id'],
                'cardName' 	=> $v['card_name'],
                'price' 	=> $v['price'],
                'status' 	=> $v['status'],
                'orderNum' 	=> $v['order_number'],
                'pay_time'	=> date('Y-m-d' , $v['pay_time']),
                'print' 	=> $print - $v['print'],
            ];
        }
        Response::success($order);
    }

    /**
     * 订单查询-住院号
     *
     * @access 	public
     * @return 	void
     */
    public function searchByTreat()
    {
        $print =Loader::model('Service')->getServiceConfVal( 'receipt','DaYinCiShu',$this->projectId);
        $treatNo 	= $this->data['treatNo'];
        $orderType 	= isset($this->data['orderType']) ? json_decode($this->data['orderType'] , true) : [];
        $data 		= Loader::model('Order')->listByTreat($treatNo , $orderType);
        $order 		= [];
        if (!$data) {
            Response::success($order);
        }
        foreach ($data as $v) {
            if ($v['project_id'] != $this->projectId || $v['status'] <= 0) {
                continue;
            }
            $order[] 	= [
                'type' 		=> $v['type'],
                'cardId' 	=> $v['card_id'],
                'cardName' 	=> $v['card_name'],
                'price' 	=> $v['price'],
                'status' 	=> $v['status'],
                'orderNum' 	=> $v['order_number'],
                'pay_time'	=> date('Y-m-d' , $v['pay_time']),
                'print' 	=> $print - $v['print'],
            ];
        }
        Response::success($order);

    }

    /**
     * 订单查询-订单详细信息
     *
     * @access 	public
     * @return 	void
     */
    public function detail()
    {
        $print =Loader::model('Service')->getServiceConfVal( 'receipt','DaYinCiShu',$this->projectId);
        $orderNumber= $this->data['orderNum'];
        //error_log("orderNumber => " . $orderNumber);
        if(preg_match_all("/orderNum=(.*)/i", $orderNumber , $result)) {
            $orderNumber = $result[1][0];
        }
        $data 		= Loader::model('Order')->listByNumber($orderNumber);
        $order 		= [];
        if (!$data) {
            Response::success($order);
        }
        $data 	= $data[0];
        if ($data['project_id'] != $this->projectId || $data['status'] <= 0) {
            Response::success($order);
        }
        $order 		= [
            'type' 		=> $data['type'],
            'hospital_id' => $data['hospital_id'],
            'cardId' 	=> $data['card_id'],
            'cardName' 	=> $data['card_name'],
            'price' 	=> $data['price'],
            'status' 	=> $data['status'],
            'orderNum' 	=> $data['order_number'],
            'pay_time'	=> date('Y-m-d H:i:s' , $data['pay_time']),
            'pay_type' 	=> $data['pay_type'],
            'number' 	=> $data['code'],
            'print' 	=> $print - $data['print'],
            'outTradeNo'    => $data['out_trade_no'],
            'transactionId' => $data['transaction_id'],
            'link' 		=> "http://".Config::get('pay_link')."/index.php?s=Home/Pay/index&cardId=".$data['card_id'],
        ];
        /*if ($order['status'] != 1) {
            Response::success($order);
        }*/
        $orderId 	= $data['order_id'];
        if ($order['type'] == 2) {//挂号
            $data 	= Loader::model('Registration')->detailByOrderId($orderId);
            $businessInfo = json_decode($data['business_info'] , true);
            $order['businessInfo']= $businessInfo;
            $successInfo = json_decode($data['success_info'] , true);
            $order['successInfo']= $this->checkRefundReg($successInfo,$businessInfo);
            $order['districtName'] = isset($businessInfo['districtName']) ? $businessInfo['districtName'] : '';
        } else if ($order['type'] == 3) {
            $data 	= Loader::model('Registration')->fetchByOrderId($orderId);
            $order['businessInfo']= json_decode($data['business_info'] , true);
            $order['successInfo']= json_decode($data['success_info'] , true);
        } else if ($order['type'] == 4) {
            $data 	= Loader::model('Payment')->outpatientByOrderId($orderId);
            $order['businessInfo']= json_decode($data['business_info'] , true);
            $order['successInfo']= json_decode($data['success_info'] , true);
        } else if ($order['type'] == 5) {
            $data 	= Loader::model('Payment')->inpatientByOrderId($orderId);
            $order['businessInfo']= json_decode($data['business_info'] , true);
            $order['successInfo']= json_decode($data['success_info'] , true);
        }else if ($order['type'] == 10){
            $data 	= Db::name("order_appointment")->where("order_id={$orderId}")->find();
            $order['businessInfo']= json_decode($data['business_info'] , true);
            $order['successInfo']= json_decode($data['success_info'] , true);
        }else if ($order['type'] == 98){
            $orderInquiry 	= Db::name("order_inquiry")->where("order_id={$orderId}")->find();
            $hospitalName 	= Db::name("resource_hospital")->field("name")->where("id={$orderInquiry['hospital_id']}")->find();
            $doctorArr      = Db::name("doctor_service")->alias("a")
                ->field("b.name as doctorName,d.name as deptName")
                ->join("doctor b" , "b.id=a.doctor_id")
                ->join("project_dept d","d.id=a.dept_id")
                ->where("a.doctor_id={$orderInquiry['doctor_id']} and a.project_id={$this->projectId}")
                ->find();
            $order['hospitalName']= $hospitalName['name'];
            $order['deptName']= $doctorArr['deptName'];
            $order['doctorName']= $doctorArr['doctorName'];
        }else if ($order['type'] == 99){
            $videoArr = Db::name("order_video")->field("b.title")->alias("a")
                ->join("resource_video b" , "b.id=a.video_id")
                ->where("order_id={$orderId}")->find();
            $order['title']= $videoArr['title'];
        }else if ($order['type'] == 55){
            $videoArr = Db::name("order_outhospital")->where("order_id={$orderId}")->find();
            $order = array_merge($videoArr , $order);
        }
        Response::success($order);
    }

    private function checkRefundReg($success_info,$business_info){
        if(10000==$this->hospitalId){//华二判断是否可退号
            if($business_info['scheduleDate']==date("Y-m-d")){
                $success_info['refund'] =  0;
            }
            if($business_info['scheduleDate']>date("Y-m-d")){
                $refundTime = time()+1*24*3600;
                if('am' == $business_info['period']&&$refundTime>strtotime($business_info['scheduleDate']." 08:00:00")){
                    $success_info['refund'] = 0;
                }
                if('pm' == $business_info['period']&&$refundTime>strtotime($business_info['scheduleDate']." 14:00:00")){
                    $success_info['refund'] = 0;
                }
            }
            $success_info['refund'] = 1;
        }
        $success_info['refund'] = 1;
        return $success_info;
    }

    /**
     * 标记订单打印
     *
     * @access 	public
     * @return 	void
     */
    public function markPrint()
    {
        $orderNumber = $this->data['orderNum'];
        $data 		 = Loader::model('Order')->detailByNumber($orderNumber);
        if (!$data || $data['project_id'] != $this->projectId || $data['status'] <= 0) Response::message(30012);
        $orderId     = $data['id'];
        $result 	 = Loader::model('Order')->markPrint($orderId);
        if ($result) {
            Response::success();
        }
    }

    /**
     * 微信支付QR
     *
     * @access 	public
     * @return 	void
     */
    public function wechatQR()
    {
        $orderNumber = $this->data['orderNum'];
        $data 	= Loader::model('Order')->detailByNumber($orderNumber);
        if (!$data) Response::message(30037);
        $orderId= $data['id'];
        $qr 	= Loader::model('Order')->wechatQR($orderId);
        if ($qr) { Response::message(10000 , ['orderNum' => $orderNumber , 'qr' => $qr , 'price' => $data['price'] , 'create_time' => $data['create_time'],'now_time' => time()]); }

        $url 	= "http://".Config::get('pay_link')."/index.php?s=Home/Pay/index&orderNum=".$orderNumber;
        $qr 	= Common::qr($url , 'wechat.png');
        $qr 	= Common::TencentCloud($qr);
        $result = Loader::model('order')->insertWechatQR($orderId , $qr);
        if (!$result) Response::message(30011);
        Response::message(10000 , ['orderNum' => $orderNumber , 'qr' => $qr , 'price' => $data['price'] , 'create_time' => $data['create_time'],'now_time' => time()]);

    }

    /**
     * 支付宝支付QR
     *
     * @access 	public
     * @return 	void
     */
    public function alipayQR()
    {
        $orderNumber = $this->data['orderNum'];
        $data 	= Loader::model('Order')->detailByNumber($orderNumber);
        if (!$data) Response::message(30038);
        $orderId= $data['id'];
        $qr 	= Loader::model('Order')->alipayQR($orderId);
        if ($qr) { Response::message(10000 , ['orderNum' => $orderNumber , 'qr' => $qr , 'price' => $data['price'] , 'create_time' => $data['create_time'] , 'now_time' => time()]); }
        $url 	= "http://".Config::get('pay_link')."/index.php?s=Home/Pay/index&orderNum=".$orderNumber;
        $qr 	= Common::qr($url , 'alipay.png');
        $qr 	= Common::TencentCloud($qr);
        $result = Loader::model('order')->insertAlipayQR($orderId , $qr);
        if (!$result) Response::message(30011);
        Response::message(10000 , ['orderNum' => $orderNumber , 'qr' => $qr , 'price' => $data['price'] , 'create_time' => $data['create_time'] , 'now_time' => time()]);
    }

    /**
     * 添加订餐订单
     *
     */
    public function addDinner(){
        $order['equipment_id'] = $this->equipId;
        $order['hospital_id'] = $this->hospitalId;
        $order['project_id'] = $this->projectId;
        $order['type'] = 9;
        $order['status'] = $this->data['status'];
        $order['order_number'] = $this->data['orderNum'];
        $order['price'] = $this->data['price'];
        $order['create_time'] = time();
        $order['print'] = 1;
        $orderObj = Db::name("order")->where("order_number='{$order['order_number']}'")->find();
        if($orderObj){
            Response::error(91028);
        }
        Db::startTrans();
        $orderId = Db::name('order')->insertGetId($order);
        if(!$orderId){
            Db::rollback();
            Response::error(30055);
        }
        $dinner['order_id'] = $orderId;
        $dinner['card_nane'] = $this->data['cardName'];
        $dinner['card_no'] = $this->data['cardNo'];
        $dinner['pay_time'] = $this->data['payTime'];
        $dinner['create_time'] = date('Y-m-d H:i:s' , time());
        $dinner['eating_code'] = $this->data['eatingCode'];
        $dinner['reg_number'] = $this->data['regNumber'];
        $result = Db::name('order_dinner')->insert($dinner);
        if(!$result){
            Db::rollback();
            Response::error(30055);
        }
        $patient['order_id'] = $orderId;
        $patient['card_id'] = $this->data['cardNo'];
        $patient['card_name'] = $this->data['cardName'];
        $resultPatient = Db::name("order_patient")->insert($patient);
        if(!$resultPatient){
            Db::rollback();
            Response::error(30055);
        }
        Db::commit();
        Response::success();
    }

    /**
     * 更新订餐订单状态
     */

    public function setStatus(){
        $orderNumber = $this->data['orderNum'];
        $orderObj = Db::name("order")->where("order_number='{$orderNumber}' and type=9")->find();
        !$orderObj && Response::error(30011);
        $order['status'] = $this->data['status'];
        if(isset($this->data['refundTime'])){
            $order['refund_time'] = $this->data['refundTime'];
        }
        $result = Db::name("order")->where("order_number='{$orderNumber}' and type=9")->update($order);
        $result && Response::success();
        Response::error(30055);
    }

}