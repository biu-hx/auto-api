<?php

namespace app\equip\controller;

use app\component\response\Response;
use app\component\server\ability\TencentVideoLive;
use think\Controller;
use think\Request;
use think\Loader;
use app\component\Log;
use app\component\server\Server;

class Test extends Controller
{

    public function testVideoUrl(){

        $arr = [ 'am' => '上午' , 'pm' => '下午' , 'npm' => '夜间' , 'all' => '全天'];
        var_dump($arr , serialize($arr));die;
        $key = 'testUrl123456';
//        $redisObj = new Redis();
        $redisObj = Server::cache('redis');

        $orderid = 'mmm'.time();
        $data = $redisObj->get($key);
        if($data){
            Response::message(10000 , json_decode($data,true));
        }else{
            $liveV = new TencentVideoLive($orderid);
            $videoUrl = $liveV->getAllUrl();
            $data = [
                'pushUrl' 	=> $videoUrl['patPushUrl'],
                'playUrl' 		=> $videoUrl['patPlayUrl'],
            ];
            $redisObj->set($key,json_encode($data),3600);
            Response::message(10000 , $data);
        }

    }


    public function testSend(){
        $doctorId = 12065000;

        $docInfo = \app\equip\model\Doctor::getLittleAppOpenid($doctorId);
        $openid = $docInfo['openid'];
        $openid = "o2GLW5Y8qX_MLNf93EIQ1y-3eAww";
        $form_id = \app\api\model\Inquiry::selectOneFormId($openid);
        //$form_id = 'wx08161534419257f35a00074f3744468969';

        echo \app\api\model\Message::sendInquiryTemplateMsg($openid,$form_id,1,1);
    }

}