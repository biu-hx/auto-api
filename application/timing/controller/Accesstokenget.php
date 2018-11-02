<?php

namespace app\timing\controller;

use app\component\response\Response;
use think\Controller;
use think\Loader;
use app\component\Log;
use \app\timing\model\DiymenSet;
/**
 * 获取微信accesstoke
 * Class AccessTokenGet
 * @package app\timing\controller
 */
class Accesstokenget extends Controller
{
    const ACCESSKEY = 'yh584454454';
    private static $CLIENT_CREDENTIAL = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=APPID&secret=APPSECRET';
	public function getAccessToken()
	{
//	    dump(config("is_test"));
	    if(config("IS_TEST")){
	        $this->renewAccessToken();
	        echo "renew_Ok";
	        return;
        }
//	    echo 11;exit;
        $list = DiymenSet::getDataList();
        foreach ($list as $v){
            $this->refreshCredential($v['id'],$v['appid'],$v['appsecret']);
        }
	}

	public function echoAccessToken(){
	    if(!isset($_GET['key'])||$_GET['key']!=self::ACCESSKEY){
//	        echo $_GET['key'];exit;
            Response::errorMessage("key不正确");
        }
        $list = DiymenSet::getDataList();
	    Response::success($list);
    }

    private function renewAccessToken(){
	    $url = "http://auto-api.mobimedical.cn/timing/echoAccessToken?key=".self::ACCESSKEY;
//        $url = "http://auto-test.mobimedical.cn/timing/echoAccessToken?key=".self::ACCESSKEY;


	    $result = file_get_contents($url);
	    $resArr = json_decode($result,true);
	    if(!isset($resArr['code'])||$resArr['code']!=10000){
	        echo '读取失败';exit;
        }
//        dump($resArr['data']);
        foreach ($resArr['data'] as $v){
	        if($v['accesstoken']){
                DiymenSet::updateAccessTokenByToken($v['token'],$v['accesstoken']);
            }
        }
        echo 'ok';
    }


    /**
     * 刷公众号accesstoken
     * @param $appid
     * @param $appsecret
     */
	private function refreshCredential($id,$appid,$appsecret){
        $url = preg_replace('/APPID/', $appid, self::$CLIENT_CREDENTIAL);
        $url = preg_replace('/APPSECRET/', $appsecret, $url);
        $results = file_get_contents($url);
        //记录刷新日志
//        Log::storageResponse($module.'Response' , $output);
//        Log::writeLog($module.'Response');
        $results = json_decode($results, TRUE);
        if(isset($results['access_token'])&&$results['access_token']){
            $r = DiymenSet::saveAccessToken($id,$results['access_token']);
            echo 'ok';
        }else{
            echo $appid;
            dump($results);
        }


    }


}