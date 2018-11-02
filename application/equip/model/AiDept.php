<?php
namespace app\equip\model;

use think\Db;
use think\Loader;

class AiDept
{


    public static function getDeptListByHospital($hospital_id){
        $map = ['hospital_id'=>$hospital_id,'date'=>['egt',date("Y-m-d")]];
        $data 	= Db::name('ai_schedul_depart')->where($map)->select();
        $returnArr = [];
        $have = [];
        foreach ($data as $v){
            if(isset($have[$v['deptHisId']])){
                continue;
            }
            $have[$v['deptHisId']] = 1;
            $returnArr[] = $v;

        }
        return $returnArr;
    }

    public static function addDept($deptName,$date,$period,$hospital_id,$district_id,$deptHisId){
        $data = ['deptName'=>$deptName,'date'=>$date,'period'=>$period,'hospital_id'=>$hospital_id,'district_id'=>$district_id,'deptHisId'=>$deptHisId];
        $data 	= Db::name('ai_schedul_depart')->insert($data);
    }

    public static function getSchedulByHospital($hospital_id){
        $map = ['hospital_id'=>$hospital_id];
        $data 	= Db::name('ai_schedul_doctor')->where($map)->select();
        return $data ? $data : [];
    }

    public static function addSchedul($data){
//        $data = ['deptName'=>$deptName,'date'=>$date,'period'=>$period,'hospital_id'=>$hospital_id,'district_id'=>$district_id,'deptHisId'=>$deptHisId];
        $data 	= Db::name('ai_schedul_doctor')->insert($data);
    }

    public static function stopWorkSchedul($id){
        $data 	= Db::name('ai_schedul_doctor')->where(['id'=>$id])->update(['stop_work'=>1]);
    }

    /**
     * 获取所有排班的时间
     * @param $hospital_id 医院
     * @param $YuYueTianShu 预约天数
     * @param $ZhouMoGuaHao 是否可以周末挂号
     * @param $ZhiXianShiYouHaoYiSheng 只显示有号医生
     * @return array
     */
    public static function allSchedulDate($hospital_id,$YuYueTianShu,$ZhouMoGuaHao,$ZhiXianShiYouHaoYiSheng){
        $map = ['hospital_id'=>$hospital_id,'scheduleDate'=>['egt',date("Y-m-d")]];
        $data 	= Db::name('ai_schedul_doctor')->where($map)->select();
//        $data = $data ? $data : [];
        $returnArr = [];
        foreach ($data as $v){
            //预约天数控制
            if($v['scheduleDate']>date("Y-m-d",time()+3600*24*$YuYueTianShu)){
                continue;
            }
            //周末挂号控制
            if($ZhouMoGuaHao){
                $weekDay = date('w',strtotime($v['scheduleDate']));
                $weekDayArr = [6,0];
                if(in_array($weekDay,$weekDayArr)){
                    continue;
                }
            }
            //只显示有号控制
            if($ZhiXianShiYouHaoYiSheng){
                if($v['availableNum'] <= 0) {
                    continue;
                }
            }
            $returnArr[$v['scheduleDate']] = $v['scheduleDate'];

        }
        return array_values($returnArr);
    }


    public static function getDoctorByHospital($hospital_id){
        $map = ['hospital_id'=>$hospital_id];
        $data 	= Db::name('ai_doctor')->where($map)->select();
        return $data ? $data : [];
    }

    public static function addDoctor($data){
//        $data = ['deptName'=>$deptName,'date'=>$date,'period'=>$period,'hospital_id'=>$hospital_id,'district_id'=>$district_id,'deptHisId'=>$deptHisId];
        $data 	= Db::name('ai_doctor')->insert($data);
    }

    /**
     * 跟据相应条件获取医生列表
     * @param $where
     * @return array
     */
    public static function getAiDoctorList($where){
        $hospital_id = isset($where['hospital_id'])?$where['hospital_id']:0;
        $ZhiXianShiYouHaoYiSheng = 0;
        $ZhouMoGuaHao = 1;//周末
        $YuYueTianShu = 15; //预约天数
        if($hospital_id){
            //获取医院对应的项目配置
            $projectArr =  Loader::model('Project')->detailByHospital($hospital_id);
            if(isset($projectArr['registration'])){
                $registrationArr =  json_decode($projectArr['registration'],true);
                $YuYueTianShu = $registrationArr['YuYueTianShu'] ? $registrationArr['YuYueTianShu'] : 15;
            }
            $ZhiXianShiYouHaoYiSheng = $registrationArr['ZhiXianShiYouHaoYiSheng'];//只显示有号
        }


        $data 	= Db::name('ai_schedul_doctor')->where($where)->select();
//        echo Db::name('ai_schedul_doctor')->getLastSql();
        $data = $data ? $data : [];
        //时间限制
        $data = self::limitRegister($data,$hospital_id);
        $returnArr = [];
        $haveDoc = [];
        $doctorIds = [];
        foreach ($data as $v){
            //检查是否还有号源
            if(isset($haveDoc[$v['doctorHisId']])){
                //记录是否有号源
                if($v['availableNum']>0){
                    $haveDoc[$v['doctorHisId']] = 1;
                }
                continue;
            }
            //预约天数控制
            if($v['scheduleDate']>date("Y-m-d",time()+3600*24*$YuYueTianShu)){
                continue;
            }
            //周末挂号控制
            if($ZhouMoGuaHao){
                $weekDay = date('w',strtotime($v['scheduleDate']));
                $weekDayArr = [6,0];
                if(in_array($weekDay,$weekDayArr)){
                    continue;
                }
            }
            //只显示有号控制
            if($ZhiXianShiYouHaoYiSheng){
                if($v['availableNum'] <= 0) {
                    continue;
                }
            }
            //记录是否有号源
            if($v['availableNum']>0){
                $haveDoc[$v['doctorHisId']] = 1;
            }else{
                $haveDoc[$v['doctorHisId']] = 0;
            }
            $returnArr[] = ['doctorHisId'=>$v['doctorHisId'],'doctorName'=>$v['doctorName'],'schedulList'=>[]];
            $doctorIds[] = $v['doctorHisId'];
        }
        //查询所有医生
        $doctorList = [];
        $doctorListTem = \app\equip\model\Doctor::listByHisId($hospital_id,$doctorIds);
        foreach ($doctorListTem as $v){
            $doctorList[$v['his_id']] = $v;
        }

        foreach ($returnArr as $k=>$v){
            $returnArr[$k]['available'] = $haveDoc[$v['doctorHisId']];
            $returnArr[$k]['header'] =  isset($doctorList[$v['doctorHisId']]['avatar'])?$doctorList[$v['doctorHisId']]['avatar']:"";//头像
            $returnArr[$k]['sections'] =  isset($doctorList[$v['doctorHisId']]['sections'])?$doctorList[$v['doctorHisId']]['sections']:"";//擅长
        }
//        print_r($returnArr);
//        exit;

        //如果只有一个医生，则列出它的排班
        if(count($returnArr)==1){
            foreach ($data as $v){
               if($returnArr[0]['doctorHisId'] == $v['doctorHisId']){
                   unset($v['scheduleCode']);
                   unset($v['stop_work']);
                   unset($v['startTime']);
                   unset($v['endTime']);
                   $periodArr = unserialize(\app\component\server\EmtAi\AiToolsV2::PEROID_ARR);
//                   $v['period'] = $periodArr[$v['period']];
                   $returnArr[0]['schedulList'][] = $v;
               }
            }
        }
        return $returnArr;
    }

    /**
     * 限制华二挂号时间
     * @param $date
     * @param $deptId
     */
    public static function limitRegister($doctorList,$hospitalId){
        //针对华二做挂号限制
        if($hospitalId == 10000) {
            $today = date('Y-m-d');
            $toTime = date('His');
            $newDoctorList = [];
//            echo "<pre>";print_r($doctorList);exit;
            foreach ($doctorList as $v) {
                if ($v['scheduleDate'] != $today) {
                    $newDoctorList[] = $v;
                    continue;
                }
                if ($v['deptHisId'] == 15) {//便民门诊
                    if ($toTime >= 173000 && in_array($v['period'], ['am', 'pm'])) {
                        continue;
                    }
                } else {
                    if ($toTime >= 93000 && in_array($v['period'], ['am'])) {
                        continue;
                    }
                    if ($toTime >= 153000 && in_array($v['period'], ['am', 'pm'])) {
                        continue;
                    }
                }
                $newDoctorList[] = $v;
            }
            return $newDoctorList;
        }else{
            return $doctorList;
        }

    }





	


}