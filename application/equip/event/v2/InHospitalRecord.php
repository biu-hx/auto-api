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

class InHospitalRecord extends Base
{
    protected $validate = '\app\equip\validate\v2\InHospital'; 		//定义validate文件

    protected $scene = [ 											//定义需要验证的方法
        'searchInHospital','submitInHospital','sendCode', 'checkCode'
    ];


//    protected $mustId  = [
//        'search','detail','submit',
//    ];


//深度处理数组中 空数组 转 字符串
    protected function deep_filter($data){
        if(!is_array($data)){
            return array();
        }
        foreach ($data as &$val){
            if(is_array($val) && $val){
                $val = $this->deep_filter($val);
            }
            if($val===0 || $val==='0'){
                continue;
            }
            $val = $val ? $val : '';
            if(is_array($val)&&count($val)==0){
                $val = '';
            }
        }
        return $data;
    }

    // 查询住院证信息
    public function searchInHospital()
    {
        $hid 	= $this->hospitalId;
        $cardId = $this->data['cardId'];
        $params = [
            'cardId' 	=> $cardId,
        ];
     /*   $json = '{
    "CardId": "",
    "Name": "王净",
    "Age": "28岁",
    "Nation": "219",
    "Sex": "5",
    "State": "1",
    "IDType": "1",
    "IDNo": "132402197809091224",
    "Phone": "18608008100",
    "Country": "",
    "DeptId": "986",
    "DeptName": "妇科",
    "DocId": "",
    "AppointmentId": "7116336",
    "DocName": "黄晟",
    "CreateTs": "2018-04-04 14:18:54",
    "Notice": "你有一个术前检查,请先做检查后再进入妇科",
    "NowAreaName": "",
    "NowAddress": "-四川省-成都市-武侯区-锦晖西二街288号仁和春天国际花园5-1-2304",
    "AreaName": "",
    "Address": "-河北省-保定市-涿州市-尚公街五庙口90号",
    "Relationship": "2",
    "ContactsName": "刘洋",
    "ContactsPhone": "18602805100",
    "Occupition": "",
    "InhospitalDate": "",
    "InhospitalTime": "",
    "InhospitalAdress": "",
    "WaitNumber": "5"
  }';
        Response::success(json_decode($json , true));*/
        $data 	= Server::ability('hospital')->searchInHospital($hid , $params);
        if (!$data) { Response::message(10101); }
        if($data['code'] != 10000 && isset($data['msg'])){
            Response::message(10012,mb_substr($data['msg'],9,null,'utf8'));
        }
        $data = $this->deep_filter($data['data']);
        $response = [];
        foreach ($data as $v){
            $tem = [];
            foreach($v as $kk=>$vv){
                $tem[$kk] = $vv;
                if(is_array($vv)){
                    $tem[$kk] = '';
                }
            }
            $response[] = $tem;
        }
        /*$Appointment = Db::name("order_appointment")->where("appointment_id={$response[0]['AppointmentId']}")->find();
        if(!$Appointment){
            $dataArr = [
                'order_id' => '',
                'appointment_id' => $response[0]['AppointmentId'],
                'business_info' => json_encode($response),
                'create_time' => date("Y-m-d H:i:s" , time()),
                'status' => 0,
                'hid' => $this->hospitalId,
                'card_id' => $response[0]['CardId'],
                'card_name' => $response[0]['Name'],
            ];
            Db::name("order_appointment")->insert($dataArr);
        }*/
        Response::success($response);
    }

    //获取列表选择数据
    public function getSelectList(){
        $hid 	= $this->hospitalId;
        $params = [];
        $data 	= Server::ability('hospital')->getSelectList($hid , $params);
        if (!$data) { Response::message(10101); }
        $data['code'] != 10000 && Response::message(10102);
        $data = $this->deep_filter($data['data']);
        Response::success((object)$data);
    }

    //提交出院信息
    public function submitInHospital(){
        $hid 	= $this->hospitalId;
        $contentArr = json_decode($this->data['content'] , true);
        $content = Response::ToXml(array("Request"=>$contentArr));
        $params = [
            'content' 	=> $content,
        ];
        //拿到详情
        $data 	= Server::ability('hospital')->submitInHospital($hid , $params);
        if (!$data) { Response::message(10101); }
        if($data['code'] != 10000 && isset($data['msg'])){
            Response::message(10012,mb_substr($data['msg'],9,null,'utf8'));
        }
        $data = $this->deep_filter($data['data']);
        /*$AppointmentId = $contentArr['AppointmentId'];
        $orderAppointment = Db::name("order_appointment")->where("appointment_id={$AppointmentId}")->find();
        if(!$orderAppointment['order_id']){
            $orderArr = [
                'equipment_id' => $this->equipId,
                'hospital_id' => $this->hospitalId,
                'project_id' => $this->projectId,
                'type' => 10,
                'order_number' => '10' . time() . mt_rand(1000 , 9999),
                'status' => 1,
                'pre_status' => 0,
                'create_time' => time(),
                'print' => 0,
            ];
            $orderId = Db::name("order")->insertGetId($orderArr);
            $patient = [
                'order_id' => $orderId,
                'card_id' => $orderAppointment['card_id'],
                'card_name' => $orderAppointment['card_name'],
            ];
            Db::name("order_patient")->insert($patient);
        }else{
            $orderId = $orderAppointment['order_id'];
        }
        $AppointmentId = $contentArr['AppointmentId'];
        $updateData = [
            "success_info" => $this->data['content'],
            "status" => 1,
            "order_id" => $orderId,
        ];
        Db::name("order_appointment")->where("appointment_id={$AppointmentId}")->update($updateData);
        */
        Response::success((object)$data);
    }

    //发送短信验证码
    public function sendCode(){
        $hid 	= $this->hospitalId;
        $phone = $this->data['phone'];
        $params = [
            'phone' 	=> $phone,
        ];
        //获取返回信息
        $data 	= Server::ability('hospital')->sendCode($hid , $params);
        if (!$data) { Response::message(10101); }
        if($data['code'] != 10000 && isset($data['msg'])){
            Response::message(10012,mb_substr($data['msg'],9,null,'utf8'));
        }
        Response::success($data);
    }

    //验证短信验证码
    public function checkCode(){
        $hid 	= $this->hospitalId;
        $phone = $this->data['phone'];
        $code  = $this->data['code'];
        $params = [
            'phone' 	=> $phone,
            'code'  	=> $code,
        ];
        //获取返回信息
        $data 	= Server::ability('hospital')->checkCode($hid , $params);
        if (!$data) { Response::message(10101); }
        if($data['code'] != 10000 && isset($data['msg'])){
            Response::message(10012,mb_substr($data['msg'],9,null,'utf8'));
        }
        Response::success($data);
    }
}