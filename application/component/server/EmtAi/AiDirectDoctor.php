<?php

namespace app\component\server\EmtAi;
use app\component\response\Response;
use app\component\server\Server;
use think\Loader;


/**
 * AI导诊工具类
 * Class AiTools
 * @package app\component\server\EmtAi
 */

class AiDirectDoctor
{
    public static function createAiDirectSession($hospitalId,$cardId){
        $params = [
            'CardNo' 	=> $cardId,
        ];
        return $data 	= Server::ability('hospital')->createAiDirectSession($hospitalId , $params);
    }

    public static function getAiDirectAnswer($session_key,$hospitalId,$ai_session_id,$choice,$seqno,$query){
        $params = [
            'ai_session_id' 	=> $ai_session_id,
            'choice' 	=> $choice,
            'seqno' 	=> $seqno,
            'query' 	=> $query,
        ];
        $data 	= Server::ability('hospital')->getAiDirectAnswer($hospitalId , $params);
//        print_r($data);exit;
        if($data['data']['status']=='doctors'){//返回医生列表
            $his_doctor_ids = array_column($data['data']['json_data']['trueDoc'],'id');
            if(!is_array($his_doctor_ids)||count($his_doctor_ids)==0){
                return [];
            }
            $where = ['hospital_id'=>$hospitalId,'doctorHisId'=>['in',$his_doctor_ids]];
            $doctorList =  \app\equip\model\AiDept::getAiDoctorList($where);
            $seqno = 100;
            //查询是否已有数据
            $anwserInfo = \app\equip\model\EmtAi::getAiSessionValueByLevel($session_key,$seqno);
            $select_data = [];
            foreach ($doctorList as $v){
                $select_data[] = ['id'=>$v['doctorHisId'],'value'=>$v['doctorName']];
            }
            //回写数据库
            if($anwserInfo){
                \app\equip\model\EmtAi::updataAiSessionValue($session_key,false,false,$seqno,json_encode($select_data),false,false);
            }else{
                \app\equip\model\EmtAi::createAiSessionValue($session_key,0,0,$seqno,json_encode($select_data),$selected="",$preg_word='');
            }
            $returnArr = ['status'=>1];
            $returnArr['seqno'] = $seqno;
            $returnArr['doctorList'] = $doctorList;
            return $returnArr;
        }elseif($data['data']['status']=='followup') {//返回医生列表
            $select_data = $data['data']['json_data'];
            //查询是否已有数据
            $anwserInfo = \app\equip\model\EmtAi::getAiSessionValueByLevel($session_key,$select_data['seqno']);
            //回写数据库
            if($anwserInfo){
                \app\equip\model\EmtAi::updataAiSessionValue($session_key,false,false,$select_data['seqno'],json_encode($select_data),false,false);
            }else{
                \app\equip\model\EmtAi::createAiSessionValue($session_key,0,0,$select_data['seqno'],json_encode($select_data),$selected="",$preg_word='');
            }
            $data['data']['json_data']['status'] = 0;
            return $data['data']['json_data'];
        }
    }

}

