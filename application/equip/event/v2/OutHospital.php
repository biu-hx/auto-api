<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/22 0022
 * Time: 14:47
 */

namespace app\equip\event\v2;


use app\component\response\Response;
use app\component\server\Server;
use app\equip\controller\Base;
use think\Db;

class OutHospital extends Base
{
    protected $validate = '\app\equip\validate\v2\OutHospital'; 		//定义validate文件

    protected $scene = [ 											//定义需要验证的方法
        'search','detail','submit',
    ];


    protected $mustId  = [
        'search','detail','submit',
    ];

    //深度处理数组中 空数组 转 字符串
    protected function deep_filter($data){
        if(!is_array($data)){
            return array();
        }
        foreach ($data as &$val){
            if(is_array($val) && $val){
                $val = $this->deep_filter($val);
            }
            $val = $val ? $val : '';
        }
        return $data;
    }

    //住院信息
    public function search()
    {
        $hid 	= $this->hospitalId;
        $cardId = $this->data['cardId'];
        $params = [
            'cardId' 	=> $cardId,
        ];
        $data 	= Server::ability('hospital')->searchOutHospitalInfo($hid , $params);
        if (!$data) { Response::message(10101); }
        if($data['code'] != 10000 && isset($data['msg'])){
            Response::message(10012,mb_substr($data['msg'],9,null,'utf8'));
        }
        $data = $this->deep_filter($data['data']);
        /*$outHospitalObj = Db::name("order_outhospital")->where("adm_id={$data['AdmID']}")->find();
        if($outHospitalObj){
            $outHospital = [
                'business_info' => json_encode($data),
                'create_time'   => date('Y-m-d H:i:s' , time()),
                'status'        => 1,
                'hid'           => $this->hospitalId,
                'reg_no'        => $data['RegNo'],
                'card_id'       => $cardId,
                'card_name'     => $data['UserName'],
                'adm_id'        => $data['AdmID'],
            ];
            Db::name("order_outhospital")->insert($outHospital);
        }*/
//        if(!$data){
//            $json = '{
//        "UserName": "姓名",
//        "RegNo": "登记号",
//        "CurentDept": "当前科室",
//        "AdmDate": "2017-01-01",
//        "BedNo": "b1",
//        "DepositTotal": "100",
//        "TotalAmount": "100",
//        "Balance": "0",
//        "State": "1",
//        "BankNo": "1234567464",
//        "BankName": "银行姓名",
//        "AdmID": "554777",
//        "needPay": "0"
//    }';
//            $data = json_decode($json);
//        }
        Response::success((object)$data);
    }

    //详情
    public function detail(){
        $hid 	= $this->hospitalId;
        $AdmId = $this->data['admId'];
        $params = [
            'AdmId' 	=> $AdmId,
        ];
        $data 	= Server::ability('hospital')->searchOutHospitalList($hid , $params);
        if (!$data) { Response::message(10101); }
        $data['code'] != 10000 && Response::message(10102);
        $data = $this->deep_filter($data['data']);
        Response::success((object)$data);
    }

    //提交出院信息
    public function submit(){
        $hid 	= $this->hospitalId;
        $AdmId = $this->data['admId'];
        $params = [
            'AdmId' 	=> $AdmId,
        ];

        //拿到详情
        $detail 	= Server::ability('hospital')->searchOutHospitalList($hid , $params);
        $detail = $this->deep_filter($detail['data']);

        $data 	= Server::ability('hospital')->submitLeaveHospital($hid , $params);
        if (!$data) { Response::message(10101); }
        if($data['code'] != 10000 && isset($data['msg'])){
            Response::message(10012,mb_substr($data['msg'],9,null,'utf8'));
        }

        $data = $data['data'];
        /*$outHospitalObj = Db::name("order_outhospital")->where("adm_id={$AdmId}")->find();
        if(!$outHospitalObj['order_id']){
            $orderArr = [
                'equipment_id' => $this->equipId,
                'hospital_id' => $this->hospitalId,
                'project_id' => $this->projectId,
                'type' => 55,
                'order_number' => '55' . time() . mt_rand(1000 , 9999),
                'status' => 1,
                'pre_status' => 0,
                'create_time' => time(),
                'print' => 0,
            ];
            $orderId = Db::name("order")->insertGetId($orderArr);
            $patient = [
                'order_id' => $orderId,
                'card_id' => $outHospitalObj['card_id'],
                'card_name' => $outHospitalObj['card_name'],
            ];
            Db::name("order_patient")->insert($patient);
        }else{
            $orderId = $outHospitalObj['order_id'];
        }
        $updateData = [
            "success_info" => json_encode($data),
            "status" => 1,
            "order_id" => $orderId,
        ];
        Db::name("order_outhospital")->where("adm_id={$AdmId}")->update($updateData);*/

        //快捷出院记录
        model('OutHospital')->addOutHospital($detail);
        Response::success((object)$data);
    }
}