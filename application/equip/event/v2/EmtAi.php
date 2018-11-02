<?php
// +----------------------------------------------------------------------
// | 登录
// +----------------------------------------------------------------------
// | Author: duanhy <shongyudxmas@163.com> 
// +----------------------------------------------------------------------
// | version: 1.0
// +----------------------------------------------------------------------

namespace app\equip\event\v2;

use app\component\server\EmtAi\AiFunctionKeyword;
use app\component\server\EmtAi\AiTools;
use think\Db;
use think\Loader;
use app\equip\controller\Base;
use app\component\response\Response;
use app\component\server\Server;

class EmtAi extends Base
{
    const CANCEL_WORD = '/取消|首页/';
    /**
     * 创建一个ai会话
     */
    public function createAiSession(){
//        $keyWord="我要挂号今天上午张明华医生";
//        $keyWord="我要挂今天上午张明华医生的号";
        $userData = $this->getData("userData");
//        file_put_contents("../upload/2222.txt",var_export($userData,true));
//        $userData = mb_convert_encoding($userData, "utf-8", "gb2312");
//        print_r("测试");exit;
        $services = \app\equip\model\EmtAi::getServiceKeyword();
        //查找用户服务
        $serviceId = 0;
        foreach ($services as $v){
            $preg = $this->replacePreg($v['key_word']) ;
            if(preg_match($preg,$userData)){
                $serviceId = $v['value'];
                break;
            }
        }
        //查找服务对应下的属性
        if(!$serviceId){
            $response = [];
            $selectData = \app\equip\model\EmtAi::getServiceTypeList();
            $response['selectData'] = $selectData;
            Response::errorMessage("没有查询到您要的服务",20000,$response);
        }
        //创建一个ai会话
        $session_key = \app\equip\model\EmtAi::createAiSession($serviceId,$userData);
        //判断类型
        if(2==$serviceId){//挂号
            //写入医院数据
            if($this->hospitalId){
                \app\equip\model\EmtAi::createAiHospital($this->hospitalId,$session_key,"",$this->hospitalName);
            }else{
                //查询项目中的医院，并写入
                $hospitalConf 	= $this->hospitalConf;
                $hospitals 	= Loader::model('Hospital')->listById($hospitalConf);
                //写入会话医院
                $selectData = [];
                foreach ($hospitals as $k=>$v){
                    $selectData[$k]['id'] = $v['id'];
                    $selectData[$k]['value'] = $v['name'];
                }
//                print_r($selectData);exit;
                \app\equip\model\EmtAi::createAiHospital("",$session_key,json_encode($selectData),"",0);
            }
        }
        //获取服务的属性
        $secondServiceByKey = \app\equip\model\EmtAi::getSecondTypeAsKey($serviceId);
        //获取设置的对应服务的关键词
        $attributes = \app\equip\model\EmtAi::getAttributeKeyword($serviceId);
        $secondServices = [];
        $attributesHave = [];//记录已有的属性
        foreach ($attributes as $v){
            if(in_array($v['value'],$attributesHave)){
                continue;
            }
            $preg = $this->replacePreg($v['key_word']) ;
            if(preg_match($preg,$userData,$matchs)){
                $selfFunction = $v['function'];
                //检测方法是是否存在
                if(!method_exists(new AiFunctionKeyword(),$selfFunction)){
                    Response::errorMessage("'".$v['key_word']."'没有找到对应的解析方法");
                }
                $secondServices[$v['value']]['value'] = AiFunctionKeyword::$selfFunction($matchs,$v['function_param']);
                $secondServices[$v['value']]['pregd'] = $matchs[0];
                $attributesHave[] = $v['value'];
            }
        }
//        print_r($secondServices);exit;
        //写入属性表
        foreach ($secondServices as $k=>$v){
            $level = $secondServiceByKey[$k]['level'];
            \app\equip\model\EmtAi::createAiSessionValue($session_key,$k,$v['value'],$level,'','',$v['pregd']);
        }
        //检查获取到了哪些属性
        $haveAttribute = AiTools::checkHaveAttribute($session_key,$serviceId);
        $response['session_key'] = $session_key;
        Response::success($response);
//        print_r($secondServices);
    }




    /**
     * 搜索会话中业务情况
     */
    public function searchAiBusinessSm(){
        $session_key = $this->getData("session_key");
        $sessionInfo = \app\equip\model\EmtAi::getAiSessionInfo($session_key);
        if(!$sessionInfo){
            Response::errorMessage("会话不存在");
        }
        $serviceId = $sessionInfo['service_type'];
        $session_value = $sessionInfo['session_value'];
        //获取服务下所有的属性
        $secondService = \app\equip\model\EmtAi::getSecondServiceLevel($sessionInfo['service_type']);
        //获取所有的sessionValue
        $sessionValues = \app\equip\model\EmtAi::getAiSessionValue($session_key);
        AiTools::checkAiUserValue($session_key,$secondService,$sessionValues,$serviceId,$session_value,$this->hospitalId,$this->projectId);
    }

    /**
     * 添加
     */
    public function addVoiceSelect(){
        $session_key = $this->getData("session_key");
        $userLevel = $this->getData("needLevel");
        $userData = $this->getData("userData");
//        $selectType = $this->getData("selectType");
        $sessionInfo = \app\equip\model\EmtAi::getAiSessionInfo($session_key);
        if(!$sessionInfo){
            Response::errorMessage("会话不存在");
        }
        //匹配是否是取消
        if(preg_match(self::CANCEL_WORD,$userData)){
            $response['notice'] = "取消";
            $response['needLevel'] = -1;
            $response['selectData'] = [];
            Response::success($response);
        }
//        $userStr = "妇科今天上午张明华医生的号";
        $serviceId = $sessionInfo['service_type'];
        //获取服务下所有的属性
        $secondService = \app\equip\model\EmtAi::getSecondServiceLevel($sessionInfo['service_type'],$userLevel);
        //清除掉所有已有的数据
        \app\equip\model\EmtAi::updataAiSessionValue($session_key,false,false,['gt',$userLevel],'',0);
        $sessionValues = \app\equip\model\EmtAi::getAiSessionValue($session_key);
        if(7==$userLevel){//如果是就诊卡号，另作处理
            $secondServiceId = $secondService[0]['id'];
            if(isset($sessionValues[$secondServiceId])){
                \app\equip\model\EmtAi::updataAiSessionValue($session_key,false,$userData,$userLevel);
            }else{
                \app\equip\model\EmtAi::createAiSessionValue($session_key,$secondServiceId,$userData,$userLevel);
            }
            $sessionValues[$userLevel] = \app\equip\model\EmtAi::getAiSessionValueByLevel($session_key,$userLevel);
        }else{

            //筛选出还需要收集的属性
            $secondServiceIds = [];
            foreach ($secondService as $v){
                $secondServiceIds[] = $v['id'];
            }
            $secondServiceByKey = \app\equip\model\EmtAi::getSecondTypeAsKey($sessionInfo['service_type']);
            $attributes = \app\equip\model\EmtAi::getAttributeKeyword($sessionInfo['service_type']);
            //匹配数据
            $secondServices = [];
            $attributesHave = [];//记录已有的属性
            foreach ($attributes as $v){
                if(in_array($v['value'],$attributesHave)){
                    continue;
                }
                $preg = $this->replacePreg($v['key_word']) ;
                if(preg_match($preg,$userData,$matchs)){
                    $selfFunction = $v['function'];
                    //检测方法是是否存在
                    if(!method_exists(new AiFunctionKeyword(),$selfFunction)){
                        Response::errorMessage("'".$v['key_word']."'没有找到对应的解析方法");
                    }
                    $secondServices[$v['value']]['value'] = AiFunctionKeyword::$selfFunction($matchs,$v['function_param']);
                    $secondServices[$v['value']]['pregd'] = $matchs[0];
//                    $secondServices[$v['value']] = AiFunctionKeyword::$selfFunction($matchs,$v['function_param']);
                    $attributesHave[] = $v['value'];
                }
            }

//        print_r($sessionValues);exit;

            //写入属性表
            foreach ($secondServices as $k=>$v){
                $level = $secondServiceByKey[$k]['level'];
                if(isset($sessionValues[$k])){//存在则更新
                    \app\equip\model\EmtAi::updataAiSessionValue($session_key,false,$v['value'],$level,false,false,false,$v['pregd']);
                }else{//不存在则写入
                    \app\equip\model\EmtAi::createAiSessionValue($session_key,$k,$v['value'],$level,'','',$v['pregd']);
                }
                //更新已查询到的数据
                $sessionValues[$k]['value'] = $v;//更新数据
            }
//print_r($sessionValues);exit;
            $valueInfo = \app\equip\model\EmtAi::getAiSessionValueByLevel($session_key, $userLevel);
//            print_r($valueInfo);
            $selectData = json_decode($valueInfo['select_data'], true);
            //匹配对应选项数据
            if($userLevel==3||$userLevel==2||$userLevel==1) {//院区 科室 医院
                foreach ($selectData as $v) {
                    if (strpos($userData,$v['value'])!==false) {
//                    echo 22;exit;
                        \app\equip\model\EmtAi::updataAiSessionValue($session_key, false, $v['id'], $userLevel, false, 1,false,$v['value']);
                        $sessionValues[$valueInfo['attribute']]['value'] = $v['id'];//更新数据
                        break;
                    }
                }
            }

            if($userLevel==6) {//匹配医生
                foreach ($selectData as $v) {
                    if (strpos($userData,$v['doctorName'])!==false) {
                        \app\equip\model\EmtAi::updataAiSessionValue($session_key, false, $v['scheduleId'], $userLevel, false, 1,json_encode($v),$v);
                        $sessionValues[$valueInfo['attribute']]['value'] = $v['scheduleId'];//更新数据
                        break;
                    }
                }
            }
        }
//        print_r($sessionValues);exit;
        AiTools::checkAiUserValue($session_key,$secondService,$sessionValues,$serviceId,$userData,$this->hospitalId,$this->projectId);
    }

    public function deleteUserSelected(){
        $session_key = $this->getData("session_key");
        $userLevel = $this->getData("needLevel");

//        $selectType = $this->getData("selectType");
        $sessionInfo = \app\equip\model\EmtAi::getAiSessionInfo($session_key);
        if(!$sessionInfo){
            Response::errorMessage("会话不存在");
        }
        $serviceId = $sessionInfo['service_type'];
        //清除掉所有已有的数据
        \app\equip\model\EmtAi::updataAiSessionValue($session_key,false,"",['egt',$userLevel],'',0);
        //获取服务下所有的属性
        $secondService = \app\equip\model\EmtAi::getSecondServiceLevel($sessionInfo['service_type'],$userLevel);
        $sessionValues = \app\equip\model\EmtAi::getAiSessionValue($session_key);
        AiTools::checkAiUserValue($session_key,$secondService,$sessionValues,$serviceId,"",$this->hospitalId,$this->projectId);
    }


    /**
     * 获取标准回复
     */
    public function getBaseAnswer(){
        $list = \app\equip\model\EmtAi::getBaseAnswer();
        $response = [];
        foreach ($list as $v){
            $response[$v['action']][] = $v['answer'];
        }
        Response::success($response);
    }

    /**
     * 用户自选
     */
    public function addUserSelect(){
        $session_key = $this->getData("session_key");
        $userLevel = $this->getData("needLevel");
        $userData = $this->getData("userData");
//        $selectType = $this->getData("selectType");
        $sessionInfo = \app\equip\model\EmtAi::getAiSessionInfo($session_key);
        if(!$sessionInfo){
            Response::errorMessage("会话不存在");
        }

        $serviceId = $sessionInfo['service_type'];
        //获取服务下所有的属性
        $secondService = \app\equip\model\EmtAi::getSecondServiceLevel($sessionInfo['service_type'],$userLevel);
        $mySecondService = \app\equip\model\EmtAi::findSecondServiceLevel($sessionInfo['service_type'],$userLevel);
        //直接将用户选择写入数据库
        $valueInfo = \app\equip\model\EmtAi::getAiSessionValueByLevel($session_key, $userLevel);
        if($valueInfo){//存在就更新
            \app\equip\model\EmtAi::updataAiSessionValue($session_key,false,$userData,$userLevel);
        }else{//不存在就写入
            \app\equip\model\EmtAi::createAiSessionValue($session_key,$mySecondService['id'],$userData,$userLevel);
        }
        $sessionValues = \app\equip\model\EmtAi::getAiSessionValue($session_key);
        AiTools::checkAiUserValue($session_key,$secondService,$sessionValues,$serviceId,'',$this->hospitalId,$this->projectId);
    }





    /**
     * 替换自己规定的字符，转成正则表达式
     * @param $string
     */
    private function replacePreg($string){
        $preg = '/'.$string.'/';
        return $preg;
    }


    public function testPregfunction(){
        $userStr = "我要挂2日的";
        $setStr = "(\d+)(号|日)";
        $preg = $this->replacePreg($setStr) ;
        if(preg_match($preg,$userStr,$matchs)){
            echo '匹配成功';
            print_r($matchs);
//            echo AiFunctionKeyword::getWeekday(1);
//            $selfFunction = $v['function'];
//            //检测方法是是否存在
//            if(!method_exists(new AiFunctionKeyword(),$selfFunction)){
//                Response::errorMessage("'".$v['key_word']."'没有找到对应的解析方法");
//            }
//            $secondServices[$v['value']] = AiFunctionKeyword::$selfFunction();
//            $attributesHave[] = $v['value'];
        }
    }





}
