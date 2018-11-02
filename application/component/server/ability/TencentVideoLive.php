<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/25 0025
 * Time: 17:45
 */

namespace app\component\server\ability;


class TencentVideoLive
{
// 推流防盗链Key
    private static $API_KEY = 'db18ad990ddc648b5d8606fec5e95335';

    // 腾讯云分配到的bizid
    private static $BIZID = '20623';
    private $docPushUrl;
    private $docPlayUrl;
    private $patPushUrl;
    private $patPlayUrl;


    function __construct($order_id)
    {
        $this->initUrl($order_id);
    }

    /**
     * 函数用途描述：视频问诊创建后生成对应URL
     * @date: 2018年1月31日 下午6:46:27
     * @author: Irwin
     * @param: $cid 咨询ID
     * @param: $dgt_id 道格腾ID
     * @return:
     */
    function initUrl($order_id)
    {
        $docStreamId = 'doc' . $order_id;
        $patStreamId = 'pat' . $order_id;
        $time = time() + 2 * 24 * 3600;
        $this->docPushUrl = $this->getPushUrl($docStreamId, $time);
        $this->docPlayUrl = $this->getPlayUrl($docStreamId);
        $this->patPushUrl = $this->getPushUrl($patStreamId, $time);
        $this->patPlayUrl = $this->getPlayUrl($patStreamId);
    }

    public function getAllUrl(){
        return array("docPushUrl"=>$this->docPushUrl,
            "docPlayUrl"=>$this->docPlayUrl,
            "patPushUrl"=>$this->patPushUrl,
            "patPlayUrl"=>$this->patPlayUrl,
            );
    }

    /**
     * function:获取推流地址
     * parames:$bizId 腾讯云分配到的bizid
     * parames:$streamId 自定义传入的直播码
     * parames:$key
     * parames:$time 时间戳
     * author:Irwin
     * date:2018年1月30日
     */
    private function getPushUrl($streamId,$time = null)
    {
        $bizId = self::$BIZID;
        $key = self::$API_KEY;
        if ($key && $time) {
            $txTime = strtoupper(base_convert($time, 10, 16));
            // txSecret = MD5( KEY + livecode + txTime )
            // livecode = bizid+"_"+stream_id 如 8888_test123456
            $livecode = $bizId . "_" . $streamId; // 直播码
            $txSecret = md5($key . $livecode . $txTime);
            $ext_str = "?" . http_build_query(array(
                    "bizid" => $bizId,
                    "txSecret" => $txSecret,
                    "txTime" => $txTime
                ));
        }
        return "rtmp://" . $bizId . ".livepush.myqcloud.com/live/" . $livecode . (isset($ext_str) ? $ext_str : "");
    }

    /**
     * function:获取播放地址
     * parames:$bizId 腾讯云分配到的bizid
     * parames:$streamId 自定义传入的直播码
     * author:Irwin
     * date:2018年1月30日
     */
    private function getPlayUrl($streamId)
    {
        $bizId = self::$BIZID;
        $livecode = $bizId . "_" . $streamId; // 直播码
        $payUrl = "rtmp://" . $bizId . ".liveplay.myqcloud.com/live/" . $livecode;
        return $payUrl;
    }
}