<?php
// +----------------------------------------------------------------------
// | 渠道数据
// +----------------------------------------------------------------------
// | Author: duanhy <shongyudmxas@163.com> 
// +----------------------------------------------------------------------
// | version: 1.0
// +----------------------------------------------------------------------

namespace app\api\model;

use think\Db;

class Message
{
    const CALL_WAIT_TIME = 60;//问诊呼叫等待时间

    /**
     * 获取最新的视频问诊消息
     * @return bool
     */
	public static function getNewInquiryMsg($doctorId)
	{
        $map = ['to_user'=>$doctorId,'type'=>0,'readed'=>0,'create_ts'=>['gt',date("Y-m-d H:i:s",time()-self::CALL_WAIT_TIME)]];
		$data = Db::name('message')->where($map)->find();
//        echo Db::name('message')->getLastSql();
//        $data = "";
        return $data ? $data : [];
	}

    public static function setMsgReaded($id){
        $map = ['id'=>$id];
        $update = ['readed'=>1];
        return Db::name('message')->where($map)->update($update);
    }


    /**
     * 添加新消息
     * @param $doctorId
     */
    public static function addInquiryMessage($equipId,$callId,$doctorId){
        $nowTs = date("Y-m-d H:i:s");
        //写入消息表
        $data = ['business_id'=>$callId,'from_user'=>$equipId,'to_user'=>$doctorId,'create_ts'=>$nowTs,'update_ts'=>$nowTs];
        return Db::name('message')->insert($data);
    }

    /**
     * 发送模板消息
     * @param $openid
     * @param $form_id
     */
    public static function sendInquiryTemplateMsg($openid,$form_id,$callId,$msgId){
        $template_id = 'mW3reYu3N5OqpB_uDt0YHiFTIU6Ljr1S2nQOw9_gwbo';
//        $page = '/pages/video/video?callId='.$callId.'&msgId='.$msgId;
        $page = '/pages/index/index';
        $data = array(
            "keyword1" => "测试",
            "keyword2" => "测试",
            "keyword3" => "测试",
            "keyword4" => "测试",
            "keyword5" => "测试",
        );
        $wechat = new \app\component\server\WechatAPI();
        $r = $wechat->sendAppTemplateMsg($openid,$template_id,$page,$data,$form_id);
        return $r;
    }

}