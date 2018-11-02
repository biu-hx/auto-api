<?php
// +----------------------------------------------------------------------
// | 登录
// +----------------------------------------------------------------------
// | Author: duanhy <shongyudxmas@163.com>
// +----------------------------------------------------------------------
// | version: 1.0
// +----------------------------------------------------------------------

namespace app\equip\event\v2;

use app\component\server\EmtAi\AiDirectDoctor;
use app\component\server\EmtAi\AiFunctionKeyword;
use app\component\server\EmtAi\AiTools;
use app\component\server\EmtAi\AiToolsV2;
use think\Db;
use think\Loader;
use app\equip\controller\Base;
use app\component\response\Response;
use app\component\server\Server;

class EmtAiV2 extends Base
{
    const CANCEL_WORD = '/取消|首页/';

    /**
     * 创建一个ai会话
     */
    public function createAiSession()
    {

        $userData = $this->getData("userData");//用户输入的数据
        $response = [];
        //查找服务类型
        $serviceId = \app\component\server\EmtAi\AiToolsV2::pregServiceType($userData);
        //查找服务对应下的属性
        if (!$serviceId) {
            $selectData = \app\equip\model\EmtAi::getServiceTypeList();
            $response['selectData'] = $selectData;
            Response::errorMessage("没有查询到您要的服务", 20000, $response);
        }
        //转换为拼音
        $userDataPinyin = \app\component\Pinyin::Pinyin($userData, 'utf-8', $pinYinPosition);
        //创建一个ai会话
        $session_key = \app\equip\model\EmtAi::createAiSession($serviceId, $userData, $userDataPinyin);
        //判断类型
        if (2 == $serviceId) {//挂号
            $this->registerBusiness($session_key, $serviceId, $userData, $userDataPinyin, $pinYinPosition);
        } elseif (3 == $serviceId) {//智能导诊
            $this->aiDirectDoctor($session_key, $serviceId, $userData, $userDataPinyin, $pinYinPosition);
        }

//        print_r($secondServices);

    }


    /*
     * 挂号业务流程
     */
    private function registerBusiness($session_key, $serviceId, $userData, $userDataPinyin, $pinYinPosition)
    {
        $user_data_original = $userData;
        //写入医院数据
        if ($this->hospitalId) {
            \app\equip\model\EmtAi::createAiHospital($this->hospitalId, $session_key, "", $this->hospitalName);
        } else {
            //查询项目中的医院，并写入
            $hospitalConf = $this->hospitalConf;
            $hospitals = Loader::model('Hospital')->listById($hospitalConf);
            //写入会话医院
            $selectData = [];
            foreach ($hospitals as $k => $v) {
                $selectData[$k]['id'] = $v['id'];
                $selectData[$k]['value'] = $v['name'];
            }
//                print_r($selectData);exit;
            \app\equip\model\EmtAi::createAiHospital("", $session_key, json_encode($selectData), "", 0);
        }
        //获取服务的属性
        $secondServiceByKey = \app\equip\model\EmtAi::getSecondTypeAsKey($serviceId);
        //获取设置的对应服务的关键词
        $attributes = \app\equip\model\EmtAi::getAttributeKeyword($serviceId);
        $secondServices = [];
        $attributesHave = [];//记录已有的属性
        $levelHave = []; //已有的属性
        $pregWord = [];
        foreach ($attributes as $v) {
            if (in_array($v['value'], $attributesHave)) {
                continue;
            }
            $preg = AiToolsV2::replacePreg($v['key_word']);
            if (preg_match($preg, $userData, $matchs)) {
                $selfFunction = $v['function'];
                //检测方法是是否存在
                if (!method_exists(new AiFunctionKeyword(), $selfFunction)) {
                    Response::errorMessage("'" . $v['key_word'] . "'没有找到对应的解析方法");
                }
                $secondServices[$v['value']]['value'] = AiFunctionKeyword::$selfFunction($matchs, $v['function_param']);
                $secondServices[$v['value']]['pregd'] = $matchs[0];
                $attributesHave[] = $v['value'];
                $levelHave[] = $secondServiceByKey[$v['value']]['level'];
                $pregWord[] = $matchs[0];
            }
        }

//        print_r($pinYinPosition);;
        //匹配拼音表数据
        if ($this->hospitalId) {
            $userData = AiToolsV2::pregUserDataPinYin($this->hospitalId, $userData, $levelHave, $userDataPinyin, $pinYinPosition);

        }
//            echo $userData;exit;
        //写入属性表
        foreach ($secondServices as $k => $v) {
            $level = $secondServiceByKey[$k]['level'];
            \app\equip\model\EmtAi::createAiSessionValue($session_key, $k, $v['value'], $level, '', '', $v['pregd']);
        }

        $session_value = $userData;
        //获取服务下所有的属性
        $secondService = \app\equip\model\EmtAi::getSecondServiceLevel($serviceId);
        //获取所有的sessionValue
        $sessionValues = \app\equip\model\EmtAi::getAiSessionValue($session_key);
        $pregWord1 = AiToolsV2::checkAiUserValue($session_key, $secondService, $sessionValues, $serviceId, $session_value, $this->hospitalId, $this->projectId);
        $pregWord = array_merge($pregWord, $pregWord1);
        //刷新sessionValue
        $sessionValues = \app\equip\model\EmtAi::getAiSessionValue($session_key);
        $schedulDocList = AiToolsV2::getSchedulList($sessionValues);
        //检查获取到了哪些属性
        $haveAttribute = AiToolsV2::checkHaveAttribute($secondService, $sessionValues);

        //检查需要哪些属性
        $needAttribute = AiToolsV2::checkNeedAttribute($secondService, $sessionValues);
//        $needAttribute = [];
        //记录用户步骤
        $this->recordSessionStep($session_key, $haveAttribute, $needAttribute, $pregWord, $schedulDocList, $user_data_original, $userData);

        $response['userData'] = $userData;
        $response['session_key'] = $session_key;
        $response['serviceId'] = $serviceId;
        $response['haveAttribute'] = $haveAttribute;
        $response['needAttribute'] = $needAttribute;
        $response['schedulDocList'] = $schedulDocList;
        Response::success($response);
    }

    //智能导诊
    private function aiDirectDoctor($session_key, $serviceId, $userData, $userDataPinyin, $pinYinPosition)
    {
        $KaLeiXing = \app\equip\model\Service::getServiceConfVal('registration', 'KaLeiXing', $this->projectId);
        $response['userData'] = $userData;
        $response['session_key'] = $session_key;
        $response['serviceId'] = $serviceId;
        $response['KaLeiXing'] = $KaLeiXing;
        Response::success($response);
    }

    /**
     * 添加
     */
    public function addVoiceSelect()
    {
        $session_key = $this->getData("session_key");
        $user_data_original = $userData = $this->getData("userData");
        $isCard = $this->getData("isCard", false, 0);
        $sessionInfo = \app\equip\model\EmtAi::getAiSessionInfo($session_key);
        if (!$sessionInfo) {
            Response::errorMessage("会话不存在");
        }
        //匹配是否是取消
        if (preg_match(self::CANCEL_WORD, $userData)) {
            $response['notice'] = "取消";
            $response['needLevel'] = -1;
            $response['selectData'] = [];
            Response::success($response);
        }
        //转换为拼音
        $userDataPinyin = \app\component\Pinyin::Pinyin($userData, 'utf-8', $pinYinPosition);
//        $userStr = "妇科今天上午张明华医生的号";
        $serviceId = $sessionInfo['service_type'];
        //获取服务下所有的属性
        $secondService = \app\equip\model\EmtAi::getSecondServiceLevel($serviceId);
        //获取所有的sessionValue
        $sessionValues = \app\equip\model\EmtAi::getAiSessionValue($session_key);
        //挂号业务
        if ($serviceId == 2) {
            if ($isCard) {//如果是就诊卡号，另作处理
                $secondServiceId = $secondService[0]['id'];
                if (isset($sessionValues[$secondServiceId])) {
                    \app\equip\model\EmtAi::updataAiSessionValue($session_key, false, $userData, 7);
                } else {
                    \app\equip\model\EmtAi::createAiSessionValue($session_key, $secondServiceId, $userData, 7);
                }

                //获取用户已选择的医院
                $valueInfo = \app\equip\model\EmtAi::getAiSessionValueByLevel($session_key, 1);
                $hospital_id = $valueInfo['value'];
                $valueInfo = \app\equip\model\EmtAi::getAiSessionValueByLevel($session_key, 7);
                $cardId = $valueInfo['value'];
                $params = [
                    'cardId' => $cardId,
                ];
                $data = Server::ability('hospital')->patientCard($hospital_id, $params);
                if ($data && $data['code'] == 10000) {
                    $patient = [
                        'cardId' => $cardId,
                        'IDCard' => $data['data']['IdCardNo'],
                        'UserIdKey' => $data['data']['UserIdKey'],
                        'cardName' => $data['data']['userName'],
//                        'balance' 	=> '0.00',
                    ];
                    //写入数据
                    \app\equip\model\EmtAi::updataAiSessionValue($session_key, false, false, $valueInfo['level'], false, 1, json_encode($patient));
                }
                $sessionValues[7] = \app\equip\model\EmtAi::getAiSessionValueByLevel($session_key, 7);
            } else {
                //筛选出还需要收集的属性
                $secondServiceIds = [];
                foreach ($secondService as $v) {
                    $secondServiceIds[] = $v['id'];
                }
                $secondServiceByKey = \app\equip\model\EmtAi::getSecondTypeAsKey($sessionInfo['service_type']);
                $attributes = \app\equip\model\EmtAi::getAttributeKeyword($sessionInfo['service_type']);
                //匹配数据
                $secondServices = [];
                $attributesHave = [];//记录已有的属性
                $levelHave = [];
                $pregWord = [];
                foreach ($attributes as $v) {
                    if (in_array($v['value'], $attributesHave)) {
                        continue;
                    }
                    $preg = AiToolsV2::replacePreg($v['key_word']);
                    if (preg_match($preg, $userData, $matchs)) {
                        $selfFunction = $v['function'];
                        //检测方法是是否存在
                        if (!method_exists(new AiFunctionKeyword(), $selfFunction)) {
                            Response::errorMessage("'" . $v['key_word'] . "'没有找到对应的解析方法");
                        }
                        $secondServices[$v['value']]['value'] = AiFunctionKeyword::$selfFunction($matchs, $v['function_param']);
                        $secondServices[$v['value']]['pregd'] = $matchs[0];
                        $pregWord[] = $matchs[0];
//                    $secondServices[$v['value']] = AiFunctionKeyword::$selfFunction($matchs,$v['function_param']);
                        $attributesHave[] = $v['value'];
                    }
                }
                //匹配拼音表数据
                if ($this->hospitalId) {
//                print_r($userDataPinyin);
//                print_r($pinYinPosition);
//                exit;
                    $userData = AiToolsV2::pregUserDataPinYin($this->hospitalId, $userData, $levelHave, $userDataPinyin, $pinYinPosition);

                }

//        print_r($sessionValues);exit;

                //写入属性表
                foreach ($secondServices as $k => $v) {
                    $level = $secondServiceByKey[$k]['level'];
                    if (isset($sessionValues[$k])) {//存在则更新
                        \app\equip\model\EmtAi::updataAiSessionValue($session_key, false, $v['value'], $level, false, false, false, $v['pregd']);
                    } else {//不存在则写入
                        \app\equip\model\EmtAi::createAiSessionValue($session_key, $k, $v['value'], $level, '', '', $v['pregd']);
                    }
                    //更新已查询到的数据
                    $sessionValues[$k]['value'] = $v;//更新数据
                }
            }
//        print_r($sessionValues);exit;
            $pregWord1 = AiToolsV2::checkAiUserValue($session_key, $secondService, $sessionValues, $serviceId, $userData, $this->hospitalId, $this->projectId);
            $pregWord = array_merge($pregWord, $pregWord1);
            //刷新sessionValue
            $sessionValues = \app\equip\model\EmtAi::getAiSessionValue($session_key);
            $schedulDocList = AiToolsV2::getSchedulList($sessionValues);
            //检查获取到了哪些属性
            $haveAttribute = AiToolsV2::checkHaveAttribute($secondService, $sessionValues);

            //检查需要哪些属性
            $needAttribute = AiToolsV2::checkNeedAttribute($secondService, $sessionValues);
//        $needAttribute = [];
            //记录用户步骤
            $this->recordSessionStep($session_key, $haveAttribute, $needAttribute, $pregWord, $schedulDocList, $user_data_original, $userData);
            $response['userData'] = $userData;
            $response['pregWord'] = $pregWord;
            $response['haveAttribute'] = $haveAttribute;
            $response['needAttribute'] = $needAttribute;
            $response['schedulDocList'] = $schedulDocList;
        } elseif ($serviceId == 3) {//智能导诊
            $ai_session_id = $this->getData("ai_session_id");
            $seqno = $this->getData("seqno");
            if($seqno==100){//选择医生
                $anwserInfo = \app\equip\model\EmtAi::getAiSessionValueByLevel($session_key,$seqno);
                $selectData = json_decode($anwserInfo['select_data'],true);
                $r = AiToolsV2::checkUserDataInSelect($userData,$seqno,$selectData,$preg_word);
                if($r===false){
                    Response::errorMessage("没有配置到您的医生");
                }
                $where = ['hospital_id'=>$this->hospitalId,'doctorHisId'=>$r];
                $doctorList =  \app\equip\model\AiDept::getAiDoctorList($where);
                $response = ['status'=>1];
                $response['seqno'] = $seqno;
                $response['doctorList'] = $doctorList;
            }else{
                //查询数据进行匹配
                $anwserInfo = \app\equip\model\EmtAi::getAiSessionValueByLevel($session_key,$seqno);
                $anwserInfo = json_decode($anwserInfo['select_data'],true);
                $query = $anwserInfo['query'];
                //匹配关键词
                $choice = "";
                foreach ($anwserInfo['choices'] as $v){
                    $preg = AiToolsV2::replacePreg($v);
                    if (preg_match($preg, $userData)) {
                        $choice = $v.',';
                    }
                }
                if(!$choice){
                    Response::errorMessage("请从以下诊状中选择您的情况");
                }
                $response = AiDirectDoctor::getAiDirectAnswer($session_key,$this->hospitalId, $ai_session_id, $choice, $seqno, $query);
            }
            Response::success($response);
        }
        Response::success($response);
    }





    public function deleteUserSelected()
    {
        $session_key = $this->getData("session_key");
        $userLevel = $this->getData("needLevel");

//        $selectType = $this->getData("selectType");
        $sessionInfo = \app\equip\model\EmtAi::getAiSessionInfo($session_key);
        if (!$sessionInfo) {
            Response::errorMessage("会话不存在");
        }
        $serviceId = $sessionInfo['service_type'];
        //清除掉所有已有的数据
        \app\equip\model\EmtAi::updataAiSessionValue($session_key, false, "", $userLevel, false, 0);
        //获取服务下所有的属性
        $secondService = \app\equip\model\EmtAi::getSecondServiceLevel($serviceId);
        //刷新sessionValue
        $sessionValues = \app\equip\model\EmtAi::getAiSessionValue($session_key);
        $schedulDocList = AiToolsV2::getSchedulList($sessionValues);
        //检查获取到了哪些属性
        $haveAttribute = AiToolsV2::checkHaveAttribute($secondService, $sessionValues);

        //检查需要哪些属性
        $needAttribute = AiToolsV2::checkNeedAttribute($secondService, $sessionValues);
//        $needAttribute = [];

        //记录用户步骤
        $this->recordSessionStep($session_key, $haveAttribute, $needAttribute, "", $schedulDocList, $userLevel, "", 2);
        $response['haveAttribute'] = $haveAttribute;
        $response['needAttribute'] = $needAttribute;
        $response['schedulDocList'] = $schedulDocList;
        Response::success($response);
    }
    public function createAiDirectSession()
    {
        $session_key = $this->getData("session_key");
        $cardId = $this->getData("cardId");
        $data = AiDirectDoctor::createAiDirectSession($this->hospitalId, $cardId);
        if (!$data) { Response::message(10101); }
        $data['code'] != 10000 && Response::message(10102);
        //将可选项写入数据
        $select_data = json_encode($data['data']['json_data']);
        \app\equip\model\EmtAi::createAiSessionValue($session_key,0,0,1,$select_data,$selected="",$preg_word='');
        Response::success($data['data']);
    }



    /**
     * 用户自选
     */
    public function addUserSelect()
    {
        $session_key = $this->getData("session_key");
        $userLevel = $this->getData("needLevel");
        $userData = $this->getData("userData");
//        $selectType = $this->getData("selectType");
        $sessionInfo = \app\equip\model\EmtAi::getAiSessionInfo($session_key);
        if (!$sessionInfo) {
            Response::errorMessage("会话不存在");
        }

        $serviceId = $sessionInfo['service_type'];
        //获取服务下所有的属性
        $secondService = \app\equip\model\EmtAi::getSecondServiceLevel($serviceId);
        $mySecondService = \app\equip\model\EmtAi::findSecondServiceLevel($sessionInfo['service_type'], $userLevel);
        //直接将用户选择写入数据库
        $valueInfo = \app\equip\model\EmtAi::getAiSessionValueByLevel($session_key, $userLevel);

//        if($valueInfo){//存在就更新
        $preg_word = AiToolsV2::getUserSelectPreg($session_key, $userData, $userLevel);
//            exit;
        \app\equip\model\EmtAi::updataAiSessionValue($session_key, false, $userData, $userLevel, false, 1, false, $preg_word);
//        }else{//不存在就写入
//            $preg_word = AiToolsV2::getUserSelectPreg($userData,$sessionValues,$userLevel,$secondService);
//            \app\equip\model\EmtAi::createAiSessionValue($session_key,$mySecondService['id'],$userData,$userLevel);
//        }

        $sessionValues = \app\equip\model\EmtAi::getAiSessionValue($session_key);
        AiToolsV2::checkAiUserValue($session_key, $secondService, $sessionValues, $serviceId, $userData, $this->hospitalId, $this->projectId);

        $schedulDocList = AiToolsV2::getSchedulList($sessionValues);
        //检查获取到了哪些属性
        $haveAttribute = AiToolsV2::checkHaveAttribute($secondService, $sessionValues);

        //检查需要哪些属性
        $needAttribute = AiToolsV2::checkNeedAttribute($secondService, $sessionValues);
//        $needAttribute = [];

        //更新会话为手动调用过
        \app\equip\model\EmtAi::updateAiSession($session_key, ['hand_touch' => 1]);
        //记录用户步骤
        $this->recordSessionStep($session_key, $haveAttribute, $needAttribute, "", $schedulDocList, $userLevel, $userData, 1);
        $response['haveAttribute'] = $haveAttribute;
        $response['needAttribute'] = $needAttribute;
        $response['schedulDocList'] = $schedulDocList;
        Response::success($response);

//        AiTools::checkAiUserValue($session_key,$secondService,$sessionValues,$serviceId,'',$this->hospitalId,$this->projectId);
    }

    /**
     * 更新为流程已完
     */
    public function updateUserSelectedSchedul()
    {
        $session_key = $this->getData("session_key");
        $scheduleId = $this->getData("scheduleId");
        $sessionInfo = \app\equip\model\EmtAi::getAiSessionInfo($session_key);
        if (!$sessionInfo) {
            Response::errorMessage("会话不存在");
        }
        \app\equip\model\EmtAi::updataAiSessionValue($session_key, ['selected_schedul' => $scheduleId]);
        $this->success();
    }

    /**
     * 将数据写入步骤表中
     * @param $session_key
     * @param $haveAttribute
     * @param $needAttribute
     * @param $pregWord
     * @param $schedulDocList
     * @param $userData
     * @param int $ai_type
     */
    private function recordSessionStep($session_key, $haveAttribute, $needAttribute, $pregWord, $schedulDocList, $user_data_original, $userData, $ai_type = 0)
    {
        $data = [];
        //去掉多余待选择部分
        foreach ($needAttribute as &$v) {
            unset($v['selectData']);
        }
        $data['create_ts'] = date("Y-m-d H:i:s");
        $data['session_key'] = $session_key;
        $data['haveAttribute'] = json_encode($haveAttribute);
        $data['needAttribute'] = json_encode($needAttribute);
        $data['ai_type'] = $ai_type;
        $data['pregWord'] = json_encode($pregWord);
        $data['schedulDocList'] = json_encode($schedulDocList);
        $data['user_data_original'] = $user_data_original;
        $data['user_data'] = $userData;
        Db::name('ai_session_step')->insert($data);
    }

    public function testPregfunction()
    {
//        $userStr = "我要挂2日的";
//        $setStr = "(\d+)(号|日)";
//        $preg = AiToolsV2::replacePreg($setStr) ;
//        if(preg_match($preg,$userStr,$matchs)){
//            echo '匹配成功';
//            print_r($matchs);
//        }
    }


}