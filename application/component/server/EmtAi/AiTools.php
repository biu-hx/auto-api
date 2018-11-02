<?php

namespace app\component\server\EmtAi;
use app\component\response\Response;
use app\component\server\Server;
use think\Loader;


/**
 * AI工具类
 * Class AiTools
 * @package app\component\server\EmtAi
 */

class AiTools
{
    /**
     * 检查已有哪些属性
     * @param $session_key
     * @param $serviceId 服务类型
     * @return array
     */
    public static function checkHaveAttribute($session_key,$serviceId){
        //获取服务下所有的属性
        $secondService = \app\equip\model\EmtAi::getSecondServiceLevel($serviceId);
        //获取所有的sessionValue
        $sessionValues = \app\equip\model\EmtAi::getAiSessionValue($session_key);
        $haveAttribute = [];
        foreach ($secondService as $v){
            if(!isset($sessionValues[$v['id']])){
                continue;
            }
            if(!$sessionValues[$v['id']]['value']){
//                break;
                continue;
            }

            $tem = [];
            $tem['level'] = $v['level'];
            $tem['name'] = $v['name'];
            $tem['value'] = $sessionValues[$v['id']]['value'];
            if(in_array($tem['level'],[7])){
                $tem['valueStr'] = $sessionValues[$v['id']]['value_extend'];
            }else{
                $tem['valueStr'] = $sessionValues[$v['id']]['preg_word'];
            }

            $haveAttribute[] = $tem;
        }
        return $haveAttribute;
    }


    /**
     * 检查AI里用户输入的值
     * @param $session_key 会话key
     * @param $secondService 服务属性
     * @param $sessionValues 会话值
     * @param $serviceId 服务类型id
     * @param $userData 用户输入的值
     */
    public static function checkAiUserValue($session_key,$secondService,$sessionValues,$serviceId,$userData,$hospitalId,$projectId){
        $needLevel = 0;
        $needLevelName = "";
        $response = [];
//        print_r($secondService);
        foreach ($secondService as $v){

            //当在判断院区的时候，先判断是医院有院区，如果没有，则直接默认
            if($v['level']==2){
                $valueInfo = \app\equip\model\EmtAi::getAiSessionValueByLevel($session_key,1);
                $hospitalInfo = \app\equip\model\Hospital::detail($valueInfo['value']);
                if(!$hospitalInfo['have_branch']){//如果没有院区，则直接写
//                    \app\equip\model\EmtAi::createAiSessionValue($session_key,$v['id'],"","",1,"");
                    continue;
                }
            }

            //如果没有数据
            if($v['level']==5){//如果时段不数据，则重新读取时段数据，因为
                $sessionValues[$v['id']] = \app\equip\model\EmtAi::getAiSessionValueByLevel($session_key,$v['level']);
            }
            if(!isset($sessionValues[$v['id']])){
                $needLevel = $v['level'];
                $needLevelName =$v['name']."1";;
                $selectData = self::getSelectData($session_key,$v['level'],$hospitalId,$projectId);

                $inSelect = self::checkUserDataInSelect($userData,$needLevel,$selectData,$preg_word);
                if($inSelect!==false){
                    //写入一条已匹配数据,
                    \app\equip\model\EmtAi::createAiSessionValue($session_key,$v['id'],$inSelect,$v['level'],json_encode($selectData),1,$preg_word);
                    continue;
                }else{
                    //写入一条空数据
                    \app\equip\model\EmtAi::createAiSessionValue($session_key,$v['id'],"",$v['level'],json_encode($selectData));
                    break;
                }

            }
            //如果已有确定的值，并已验证
            if($sessionValues[$v['id']]['selected']){
                continue;
            }

            //有数据，但还没有查询接口
            if(!$sessionValues[$v['id']]['select_data']){
//                echo $v['level'];
                $needLevel = $v['level'];
                $needLevelName =$v['name']."2";;
                $selectData = self::getSelectData($session_key,$v['level'],$hospitalId,$projectId);
                \app\equip\model\EmtAi::updataAiSessionValue($session_key,false,false,$v['level'],json_encode($selectData));
                $sessionValues[$v['id']]['select_data'] = json_encode($selectData);
//                break;
            }
//            echo $v['level'];echo $v['id'];
//            echo "<pre>";print_r($sessionValues);echo '</pre>';
            $selectData = is_array($sessionValues[$v['id']]['select_data'])?$sessionValues[$v['id']]['select_data']:json_decode($sessionValues[$v['id']]['select_data'],true);
            $inSelect = self::checkUserDataInSelect($userData,$v['level'],$selectData,$preg_word);
            if($inSelect!==false){
                //写入一条已匹配数据,
                \app\equip\model\EmtAi::updataAiSessionValue($session_key,false,$inSelect,$v['level'],false,1,false,$preg_word);
                continue;
            }

            //有数据，但用户没有选，则让用户选择
            if(!$sessionValues[$v['id']]['value']){
                $needLevel = $v['level'];
                $needLevelName =$v['name']."1";
                $selectData = $sessionValues[$v['id']]['select_data'];
                $selectData = json_decode($selectData,true);
                break;
            }
//            echo $v['id'];
//            echo "<pre>";print_r($sessionValues[$v['id']]['selected']);echo '</pre>';
            //判断是否已选定
            if(!$sessionValues[$v['id']]['selected']){

                $selectData = self::checkAiSelected($session_key,$v['level'],$secondService);
                if($selectData!==true){
                    $needLevel = $v['level'];
                    $needLevelName =$v['name'];
                    $response['notice'] = "您选择的".$needLevelName."不匹配，请重新选择";
                    $response['needLevel'] = $needLevel;
                    $response['selectData'] = $selectData;
                    Response::success($response);
                }else{//更新
                    $needLevel = 0;
                }
            }
        }
        $response['haveAttribute'] = AiTools::checkHaveAttribute($session_key,$serviceId);
        if($needLevel){
            //如果没有医生排班，则重新选择时间
            if($needLevel==6){
                $valueInfo = \app\equip\model\EmtAi::getAiSessionValueByLevel($session_key,4);
                $response['notice'] = "没有搜索到您需要的医生，请重新选择时间";
                $response['needLevel'] = 4;
                $selectData = json_decode($valueInfo['select_data'],true);
                $response['selectData'] = $selectData;
                Response::success($response);
            }
            $response['notice'] = "您还需要选择".$needLevelName;
            $response['needLevel'] = $needLevel;
            $response['selectData'] = $selectData;
//            print_r($selectData);exit;
            Response::success($response);
        }else{
            $response['notice'] = "全部通过";
            $response['needLevel'] = 0;
            $response['selectData'] = [];
            Response::success($response);
//            print_r($sessionValues);
        }
    }


    public static function getSelectData($session_key,$level,$hospitalId,$projectId){
        //获取用户已选择的医院
        $valueInfo = \app\equip\model\EmtAi::getAiSessionValueByLevel($session_key,1);
        $hospital_id = $valueInfo['value'];
        if(2==$level){//查询院区
            //查询项目中的医院，并写入
            $districtList = \app\equip\model\Hospital::getHospitalDistrict($hospital_id);
            //写入会话医院
            $selectData = [];
            foreach ($districtList as $k=>$v){
                $selectData[$k]['id'] = $v['id'];
                $selectData[$k]['value'] = $v['name'];
            }
            return $selectData;
        }
        //获取院区
        $valueInfo = \app\equip\model\EmtAi::getAiSessionValueByLevel($session_key,2);
        $districtId = $valueInfo['value'];;
        if(3==$level){//获取科室
            $params = [
                'districtId' 	=> $districtId,
            ];
//            print_r($districtId);exit;
            $data 	= Server::ability('hospital')->dutyDept($hospital_id,$params);
            if (!$data) { Response::message(10101); }
            $data['code'] != 10000 && Response::message(10102);
            $data 	= $data['data'];
            $dept 	= [];
            $hDept 	= Loader::model('Dept')->deptByHospital($hospital_id);
            $hDept 	= array_column($hDept , 'name' , 'his_id');
            $add 	= $update 	= [];
            foreach ($data as $key => $value) {
                if (!isset($hDept[$value['deptHisId']])) {
                    $add[] 	= ['hisId' => $value['deptHisId'] , 'name' => $value['deptName']];
                } else if ($hDept[$value['deptHisId']] != $value['deptName']) {
                    $update[] = ['hisId' => $value['deptHisId'] , 'name' => $value['deptName']];
                }
                $dept[] 	= ['id' => $value['deptHisId'] , 'value' => $value['deptName']];
            }
//            Loader::model('Dept')->sync($hospital_id , $add , $update);
            return $dept;
        }
        //获取用户已选择的科室
        $valueInfo = \app\equip\model\EmtAi::getAiSessionValueByLevel($session_key,3);
        $deptId = $valueInfo['value'];
        if(4==$level) {//获取可选日期
            $params = [
                'deptId' 	=> $deptId,
                'districtId' 	=> $districtId,
            ];
            $data 	= Server::ability('hospital')->dutyDoctor($hospital_id , $params);
            if (!$data) { Response::message(10101); }
            $data['code'] != 10000 && Response::message(10102); 	//接口响应失败
            $data 	= $data['data'];
            $date 	= [];
            //Response::success($data);die;
            //获取医院对应的项目配置
            $projectArr =  Loader::model('Project')->detailByHospital($hospital_id);
            if(isset($projectArr['registration'])){
                $registrationArr =  json_decode($projectArr['registration'],true);
                $YuYueTianShu = $registrationArr['YuYueTianShu'] ? $registrationArr['YuYueTianShu'] : 15;
                //$startDay = date('Y-m-d');
                //$endDay = date('Y-m-d',strtotime("+{$YuYueTianShu} day"));
            }else{
                $registrationArr['ZhouMoGuaHao'] = 1;
                $YuYueTianShu = 15;
            }
            // 将每个医生的每个时段剩余挂号数 叠加
            foreach ($data as $value) {
                foreach ($value['scheduleList'] as $v) {
                    $key 	= date('Ymd' , strtotime($v['scheduleDate']));
                    //周末是否可预约
                    if(!$registrationArr['ZhouMoGuaHao']){
                        $weekDay = date('w',strtotime($v['scheduleDate']));
                        $weekDayArr = [6,0];
                        if(in_array($weekDay,$weekDayArr)){
                            continue;
                        }
                    }
                    // 是否只显示有号的医生
                    if($registrationArr['ZhiXianShiYouHaoYiSheng']){
                        if($v['availableNum'] <= 0) {
                            continue;
                        }
                    }
                    // 预约天数
                    $endDay = date('Ymd',strtotime("+{$YuYueTianShu} day"));
                    if($key > $endDay){
                        continue;
                    }
                    !isset($date[$key]) && $date[$key] 	= ['date' => $v['scheduleDate'] , 'period' => []];
                    if ($v['period'] == 'am') {
                        !in_array($v['period'] , $date[$key]['period']) && $date[$key]['period'][0] = 'am';
                    } else if ($v['period'] == 'pm') {
                        !in_array($v['period'], $date[$key]['period']) && $date[$key]['period'][1] = 'pm';
                    } else if ($v['period'] == 'npm') {
                        !in_array($v['period'], $date[$key]['period']) && $date[$key]['period'][2] = 'npm';
                    } else if ($v['period'] == 'all') {
                        $date[$key]['period'] = ['am', 'pm', 'npm'];
                    }
                    $date[$key]['period'] 	= array_merge($date[$key]['period']);
                }
            }


            $date = self::limitRegister($date , $deptId,$hospitalId);
            sort($date);
            $date 	= array_merge($date);
            return $date;
        }

        //日期
        $valueInfo = \app\equip\model\EmtAi::getAiSessionValueByLevel($session_key,4);
        $date = $valueInfo['value'];
        //时段
        $valueInfo = \app\equip\model\EmtAi::getAiSessionValueByLevel($session_key,5);
        $period = $valueInfo['value'];
        if(6==$level) {//医生
            /*$params = [
                'deptId' 	=> $deptId,
                'date' 		=> $date,
                'period' 	=> $period,
            ];*/
            $scheduleList 	= [];
            if ($date != date('Y-m-d')) {
                $params = [
                    'deptId' 	=> $deptId,
                    'date' 		=> $date,
                    'districtId' 	=> $districtId,
                ];
                $data 	= Server::ability('hospital')->dutyDoctor($hospital_id , $params);
                if ($data && $data['code'] == 10000) {
                    $scheduleList 	= $data['data'];
                }
            } else {
                $params = [
                    'deptId' 	=> $deptId,
                    'date' 		=> $date,
                    'period' 	=> $period,
                    'districtId' 	=> $districtId,
                ];
                $data 	= Server::ability('hospital')->dutyDoctor($hospital_id , $params);
                if ($data && $data['code'] == 10000) {
                    $scheduleList 	= $data['data'];
                }
                if ($period != 'all') {
                    $params = [
                        'deptId' 	=> $deptId,
                        'date' 		=> $date,
                        'period' 	=> 'all',
                        'districtId' 	=> $districtId,
                    ];
                    $data 	= Server::ability('hospital')->dutyDoctor($hospital_id , $params);
                    if ($data && $data['code'] == 10000) {
                        $scheduleList2 	= $data['data'];
                        foreach ($scheduleList2 as $v) {
                            $scheduleList[] = $v;
                        }
                    }
                }
            }
            if (!$scheduleList) { Response::message(10102); }
//		print_r($scheduleList);
            $schedule = [];
            foreach ($scheduleList as $key => $value) {
                foreach ($value['scheduleList'] as $v) {
                    if($period != 'all'){
                        if (($v['period'] != 'all' && $v['period'] != $period) || $v['availableNum'] <= 0) continue;
                    }
                    $schedule[] = ['scheduleId' => $v['scheduleId'] , 'doctorId' => $value['doctorHisId'] , 'period' => $v['period'] , 'num' => $v['availableNum'] , 'image' => 'http://auto-1253714281.cosgz.myqcloud.com/Upload/e5591c9106b74189933ee815ad593951.png' ,'doctorName' => $value['doctorName'] , 'title' => $value['deptName'] , 'fee' => $v['feeSum']];
                }
            }
            return $schedule;
        }
        if(7==$level) {//刷卡方式
            $KaLeiXing = \app\equip\model\Service::getServiceConfVal('registration','KaLeiXing',$projectId);
            return $KaLeiXing;
        }
    }

    /** 查询输入是否在选项中匹配
     * @param $session_key
     * @param $level
     * @return bool|mixed
     */
    public static function checkAiSelected($session_key,$level,$secondService){
        $valueInfo = \app\equip\model\EmtAi::getAiSessionValueByLevel($session_key,$level);
        $selectData = json_decode($valueInfo['select_data'],true);

        if(in_array($level,[1,2,3])) {//2院区是否匹配 3科室
            foreach ($selectData as $v){
                if($valueInfo['value']==$v['id']){
                    //更新为已匹配并返回
                    \app\equip\model\EmtAi::updataAiSessionValue($session_key,false,false,$valueInfo['level'],false,1);
                    return true;
                }
            }
        }
//echo $level;
        if(in_array($level,[4])) {//匹配日期，同时匹配时段
            foreach ($selectData as $v){
                if($valueInfo['value']==$v['date']){

//                    \app\equip\model\EmtAi::updataAiSessionValue($session_key,"","",$valueInfo['level'],"",1);
                    //查询时段是否有数据
                    $periodInfo = \app\equip\model\EmtAi::getAiSessionValueByLevel($session_key,5);
//                    print_r($v['period']);exit;
                    //进行匹配
                    \app\equip\model\EmtAi::updataAiSessionValue($session_key,false,false,$valueInfo['level'],false,1);
                    if($periodInfo&&in_array($periodInfo['value'],$v['period'])){
                        \app\equip\model\EmtAi::updataAiSessionValue($session_key,false,false,5,json_encode($v['period']),1);
                    }elseif($periodInfo){
                        \app\equip\model\EmtAi::updataAiSessionValue($session_key,false,false,5,json_encode($v['period']));
                    }else{
                        //获取时段的属性值
                        foreach ($secondService as $vv){
                            if(5==$vv['level']){
                                $attribute = $vv['id'];
                                \app\equip\model\EmtAi::createAiSessionValue($session_key,$attribute,false,5,json_encode($v['period']));
                                break;
                            }
                        }


                    }
                    return true;
                }
            }
        }
        if(in_array($level,[5])) {//匹配日期，同时匹配时段
            if(in_array($valueInfo['value'],$selectData)){
                return true;
            }
        }

        if(in_array($level,[6])) {//匹配选择的排班
            foreach ($selectData as $v) {
                if ($valueInfo['value'] == $v['scheduleId']) {
                    //更新为已匹配并返回
                    \app\equip\model\EmtAi::updataAiSessionValue($session_key, false, false, $valueInfo['level'], false, 1);
                }
            }
        }
        if(in_array($level,[7])) {//确认用户信息

            //获取用户已选择的医院
            $valueInfo = \app\equip\model\EmtAi::getAiSessionValueByLevel($session_key,1);
            $hospital_id = $valueInfo['value'];
            $valueInfo = \app\equip\model\EmtAi::getAiSessionValueByLevel($session_key,$level);
            $cardId = $valueInfo['value'];
            $params = [
                'cardId' 	=> $cardId,
            ];
            $data 	= Server::ability('hospital')->patientCard($hospital_id , $params);
            if ($data && $data['code'] == 10000) {
                $patient 	= [
                    'cardId' 	=> $cardId,
                    'IDCard' 	=> $data['data']['IdCardNo'],
                    'UserIdKey' 	=> $data['data']['UserIdKey'],
                    'cardName' 	=> $data['data']['userName'],
                    'balance' 	=> '0.00',
                ];
                //写入数据
                \app\equip\model\EmtAi::updataAiSessionValue($session_key, false, false, $valueInfo['level'], false, 1,json_encode($patient));
                return true;
            }

        }
        return $selectData;

    }


    /**
     * 限制华二挂号时间
     * @param $date
     * @param $deptId
     */
    public static function limitRegister($date , $deptId,$hospitalId){
        //针对华二做挂号限制
        if($hospitalId == 10000){
            $today = date('Ymd');
            $toTime = date('His');
            if(!isset($date[$today])) return $date;
            if($deptId == 15){
                if($toTime >= 173000){
                    if( $date[$today]['period']){
                        $date[$today]['period'] = array_flip($date[$today]['period']);
                        unset($date[$today]['period']['pm']);
                        unset($date[$today]['period']['am']);
                        $date[$today]['period'] = array_flip($date[$today]['period']);
                    }
                }
            }else{
                if($toTime < 120000){
                    $timeInterval = 'am';
                    if($toTime >= 93000){
                        if($date[$today]['period']) {
                            if (in_array($timeInterval, $date[$today]['period'])) unset($date[$today]['period'][0]);
                        }
                    }
                }else{
                    $timeInterval = 'pm';
                    if($toTime >= 153000){
                        if($date[$today]['period']) {
                            if (in_array($timeInterval, $date[$today]['period'])) {
                                $date[$today]['period'] = array_flip($date[$today]['period']);
                                unset($date[$today]['period']['pm']);
                                unset($date[$today]['period']['am']);
                                $date[$today]['period'] = array_flip($date[$today]['period']);
                            }
                        }
                    }
                }
            }
        }
        return $date;
    }

    /*
     * 查询选项中的值 是否在输入内容里存在
     */
    public static function checkUserDataInSelect($userData,$level,$selectData,&$preg_word){
        if(in_array($level,[1,2,3])) {//2院区是否匹配 3科室
            foreach ($selectData as $v){
                if(preg_match('/'.$v['value'].'/',$userData)){
                    $preg_word = $v['value'];
                    return $v['id'];
                }
            }
        }

        if(in_array($level,[6])) {//匹配选择医生
            foreach ($selectData as $v) {
                if(preg_match('/'.$v['doctorName'].'/',$userData)){
                    //更新为已匹配并返回
                    $preg_word = $v['doctorName'];
                    return $v['scheduleId'];
                }
            }
        }

        return false;
    }




}

