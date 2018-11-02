<?php
//语音合成
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2012 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: pengyong <i@pengyong.info>
// +----------------------------------------------------------------------
class XFVoice
{
	const QUEST_URL = 'http://api.xfyun.cn/v1/service/v1/tts';
	const APPID = '5b67bf50';
    const API_KEY = '2a8a343f412790a9c196ed0fa5d0a3dc';


    /**
     * 将一段文字合成为
     * @param $string
     * @return bool|string
     */
	
	static public function SynthesisVoice($string)
	{

        $header = self::getHeader();
        $postData= ['text'=>$string];
		return \app\component\Curl::curlPost(self::QUEST_URL,$postData,$header);
	}

	static private function getHeader(){
	    $time = time();
	    $param = ['auf'=>'audio/L16;rate=16000',
            'aue'=>'lame',
            'voice_name'=>'xiaoyan',
            ];

        $paramStr = base64_encode(json_encode($param));
        $header = [];
        $header[] = 'X-Appid:'.self::APPID;
        $header[] = 'X-CurTime:'.$time;
        $header[] = 'X-Param:'.$paramStr;
        $header[] = 'X-CheckSum:'.MD5(self::API_KEY.$time.$paramStr);
        return $header;
    }

    /**
     * 获取对应语音
     * @param $string 要合成的文字
     * @return string
     */
    static public function serachVoice($string){
	    $stringMd5 = md5($string);
	    $voiceDb = think\Db::name('ai_voice');
	    //查询是否已有语音
        $voiceInfo = $voiceDb->where(array('voice_md5'=>$stringMd5))->find();
//        print_r(M('fey_ai_voice')->_sql());exit;
        if(!$voiceInfo){
            //创建新的语音
            $voiceInfo = self::SynthesisVoice($string);
//           echo "<pre>";print_r($_SERVER);exit;
            $dir = "upload/SynthesisVoice/";
            \app\component\File::createDir($dir);
            $file = $dir.$stringMd5.'.mp3';
            $r = file_put_contents($file,$voiceInfo);
            //写入数据库
            $data = ['voice_md5'=>$stringMd5,'content'=>$string,'voice_url'=>$file,'create_ts'=>time()];
            $voiceDb->insert($data);
            return 'http://'.$_SERVER['HTTP_HOST'].'/'.$file;
        }
        return 'http://'.$_SERVER['HTTP_HOST'].'/'.$voiceInfo['voice_url'];
    }


}