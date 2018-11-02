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

class AiToolsV2
{

    const PEROID_ARR = 'a:4:{s:2:"am";s:6:"上午";s:2:"pm";s:6:"下午";s:3:"npm";s:6:"夜间";s:3:"all";s:6:"全天";}';
    static $ALIAS_NAME = ['特需门诊（妇科、儿科、生殖分泌等）'=>'特需门诊','便民门诊'=>'便民','妇科门诊'=>'妇科',
        '生殖内分泌门诊'=>'生殖内分泌','产科门诊'=>'产科',
        ];//别名
    /**
     * 检查已有哪些属性
     * @param $session_key
     * @param $serviceId 服务类型
     * @return array
     */
    public static function checkHaveAttribute($secondService,$sessionValues){

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

    public static function checkNeedAttribute($secondService,$sessionValues){

        $needAttribute = [];
        foreach ($secondService as $v){

            $tem = [];
            if(isset($sessionValues[$v['id']])&&$sessionValues[$v['id']]['value']){
                continue;
            }
            $tem['needLevel'] = $v['level'];
            $tem['name'] = $v['name'];
            if($v['level']==6) {//医生通过列表已返回
                $tem['selectData'] = [];
                $needAttribute[] = $tem;
                continue;
            }
            if($sessionValues[$v['id']]['select_data']){
                $tem['selectData'] = json_decode($sessionValues[$v['id']]['select_data']);
                $needAttribute[] = $tem;
                continue;
            }
            if($v['level']==4){//日期
                //查询未来的排班日期
                $needAttribute[] = $tem;
                continue;
            }
            if($v['level']==5){//时段
                $selectDataArr = unserialize(self::PEROID_ARR);
                $selectData = [];
                foreach ( $selectDataArr as $kk=>$vv) {
                    $selectData[] = ['key'=>$kk,'value'=>$vv];
                }
                $tem['selectData'] = $selectData;
                $needAttribute[] = $tem;
                continue;
            }
        }
        return $needAttribute;
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
        $pregWords = [];
//        print_r($secondService);
        foreach ($secondService as $v){

            //当在判断院区的时候，先判断是医院有院区，如果没有，则直接默认
            if($v['level']==2){
                $valueInfo = \app\equip\model\EmtAi::getAiSessionValueByLevel($session_key,1);
                $hospitalInfo = \app\equip\model\Hospital::detail($valueInfo['value']);
                if(!$hospitalInfo['have_branch']){//如果没有院区，则直接写
                    \app\equip\model\EmtAi::createAiSessionValue($session_key,$v['id'],"","",1,"");
                    continue;
                }
            }
                $needLevel = $v['level'];
                $needLevelName =$v['name'];
                $selectData = self::getSelectData($session_key,$v['level'],$hospitalId,$projectId);
                $inSelect = self::checkUserDataInSelect($userData,$needLevel,$selectData,$preg_word);
                if($inSelect!==false){
                    $pregWords[] = $preg_word;
                    //写入一条已匹配数据,
                    if(!isset($sessionValues[$v['id']])){
                        \app\equip\model\EmtAi::createAiSessionValue($session_key,$v['id'],$inSelect,$v['level'],json_encode($selectData),1,$preg_word);
                    }else{
                        \app\equip\model\EmtAi::updataAiSessionValue($session_key,false,$inSelect,$v['level'],json_encode($selectData),1,false,$preg_word);
                    }
                    continue;
                }else{
                    //写入一条空数据
                    if(!isset($sessionValues[$v['id']])) {
                        \app\equip\model\EmtAi::createAiSessionValue($session_key, $v['id'], "", $v['level'], json_encode($selectData));
                    }else{
//                        \app\equip\model\EmtAi::updataAiSessionValue($session_key,false,$inSelect,$v['level'],json_encode($selectData),1,false,$preg_word);
                    }
                }


        }
        return $pregWords;
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

        if(3==$level){//获取科室
            //查询医生排班
            $hosDept = [];
            $hosDeptTem = \app\equip\model\AiDept::getDeptListByHospital($hospital_id);
            foreach ($hosDeptTem as $v){
                $hosDept[] 	= ['id' => $v['deptHisId'] , 'value' => $v['deptName']];
            }
            return $hosDept;
        }

        if(4==$level) {//获取排班时间
            //获取医院对应的项目配置
            $projectArr =  Loader::model('Project')->detailByHospital($hospitalId);
            if(isset($projectArr['registration'])){
                $registrationArr =  json_decode($projectArr['registration'],true);
                $YuYueTianShu = $registrationArr['YuYueTianShu'] ? $registrationArr['YuYueTianShu'] : 15;
                $ZhouMoGuaHao = $registrationArr['ZhouMoGuaHao'];
            }else{
                $ZhouMoGuaHao = 1;//周末
                $YuYueTianShu = 15; //预约天数
            }
            $ZhiXianShiYouHaoYiSheng = $registrationArr['ZhiXianShiYouHaoYiSheng'];//只显示有号
            return \app\equip\model\AiDept::allSchedulDate($hospital_id,$YuYueTianShu,$ZhouMoGuaHao,$ZhiXianShiYouHaoYiSheng);
//            return $schedulDocList = AiToolsV2::getSchedulList($sessionValues);;
        }

        if(5==$level) {//时段
            $selectDataArr = unserialize(self::PEROID_ARR);
            $selectData = [];
            foreach ( $selectDataArr as $kk=>$vv) {
                $selectData[] = ['key'=>$kk,'value'=>$vv];
            }
            return $selectData;
        }


        if(6==$level) {//医生
            //查询所有医生
            $hosDocTem = \app\equip\model\AiDept::getDoctorByHospital($hospital_id);
            $hosDoc = [];
            foreach ($hosDocTem as $v) {
                $hosDoc[] = ['id' => $v['doctorHisId'], 'value' => $v['doctorName']];
            }
            return $hosDoc;
        }
        if(7==$level) {//刷卡方式
            $KaLeiXing = \app\equip\model\Service::getServiceConfVal('registration','KaLeiXing',$projectId);
            return $KaLeiXing;
        }
    }





    /*
     * 查询选项中的值 是否在输入内容里存在
     */
    public static function checkUserDataInSelect($userData,$level,$selectData,&$preg_word){
//        echo $level;
        if(in_array($level,[2,3,6,100])) {//2院区是否匹配 3科室
            foreach ($selectData as $v){
                if(!$v['value']){
                    continue;
                }
//                isset(self::$ALIAS_NAME[$v['value']]);exit;
                //如果有别名，则替换
                if(isset(self::$ALIAS_NAME[$v['value']])){
                    $pregWord = self::$ALIAS_NAME[$v['value']];
                }else{
                    $pregWord = $v['value'];
                }

                if(preg_match('/'.$pregWord.'/',$userData)){
                    $preg_word = $v['value'];
                    return $v['id'];
                }

            }
        }


        return false;
    }


    public static function getSchedulList($sessionValues){
//        $sessionValues = \app\equip\model\EmtAi::getAiSessionValue($session_key);
        $where = [];
        $where['scheduleDate'] = ['egt',date("Y-m-d")];
        foreach ($sessionValues as $v){
            if(!$v['value']){
                continue;
            }
            $level = $v['level'];

            if($level==1){//医院
                $hospital_id = $where['hospital_id'] = $v['value'];
            }elseif($level==2){//院区
                $where['district_id'] = $v['value'];
            }elseif($level==3){//科室
                $where['deptHisId'] = $v['value'];
            }elseif($level==4){//时间
                $where['scheduleDate'] = $v['value'];
            }elseif($level==5){//时段
                $where['period'] = $v['value'];
            }elseif($level==6){//医生
                $where['doctorHisId'] = $v['value'];
            }
        }
//        print_r($where);
        $doctorList =  \app\equip\model\AiDept::getAiDoctorList($where);
        return $doctorList;
    }



    public static function getUserSelectPreg($session_key,$userData,$userLevel){
        $valueInfo = \app\equip\model\EmtAi::getAiSessionValueByLevel($session_key,$userLevel);

//        echo 1;exit;
        $selectData = json_decode($valueInfo['select_data'],true);
//        print_r($selectData);exit;
//        echo $key;exit;
//        $selectData = json_decode($sessionValues[$key]['select_data'],true);
        if(in_array($userLevel,[2,3,6])) {//2院区是否匹配 3科室
            foreach ($selectData as $v){
               if($v['id']==$userData){
                   return $v['value'];
               }

            }
        }
        return "";
    }

    static function pregUserDataPinYin($hospital_id,$userData,$levelHave,$userDataPinyin,$pinYinPosition){
        $pinyinList = \app\equip\model\EmtAi::getHospitalRegisterPinyin($hospital_id);
        foreach ($pinyinList as $v) {
            if (in_array($v['level'], $levelHave)) {
                continue;
            }
//                $preg = $this->replacePreg($v['key_word']);
            $pos = strpos($userDataPinyin, $v['key_word']);
            if ($pos !== false) {
                //                    print_r($pinYinPosition);exit;
                $startP = $pinYinPosition[$pos];
                //处理如果是最后一个拼音匹配的情况
                if (strlen($userDataPinyin) <= $pos + strlen($v['key_word'])) {
                    $length = strlen($userDataPinyin) - $startP;
                } else {
                    $length = $pinYinPosition[$pos + strlen($v['key_word'])] - $startP;
                }
                $levelHave[] = $v['level'];
                $userData = \app\component\Pinyin::replacePinYinString($userData, $v['value_str'], $startP, $length);
            }
        }
        return $userData;
    }


    static function pregServiceType($userData){
        $services = \app\equip\model\EmtAi::getServiceKeyword();//获取所有服务的关键字
        //查找用户服务
        $serviceId = 0;
        foreach ($services as $v) {
            $preg = self::replacePreg($v['key_word']);
            if (preg_match($preg, $userData)) {
                $serviceId = $v['service_id'];
                return $serviceId;
            }
        }
        return false;
    }

    /**
     * 替换自己规定的字符，转成正则表达式
     * @param $string
     */
    static function replacePreg($string){
        $preg = '/'.$string.'/';
        return $preg;
    }

}

