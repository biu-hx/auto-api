<?php
namespace app\component\server;
/**
 * WechatAPI实现了和微信后台的交互接口
 *
 *  包括：
 *	1、获取AccessToken
 *	2、自定义菜单
 *	3、群发
 *	4、自定义二维码生成
 *	5、用户分组管理
 *	6、发送客服消息
 *	7、转接客服信息
 *
 *	@copyright 成都米上多科技有限公司
 *	@author chengwq
 */

class WechatAPI {
	
	// 获取客服列表
	private static $CUSTOMER_SERVICE_KFLIST	= 'https://api.weixin.qq.com/cgi-bin/customservice/getkflist?access_token=ACCESS_TOKEN';
	
	// 获取聊天记录
	private static $CUSTOMER_SERVICE_RECORD	= 'https://api.weixin.qq.com/cgi-bin/customservice/getrecord?access_token=ACCESS_TOKEN';

	// 发送客服消息
	private static $MESSAGE_CUSTOM_SEND		= 'https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=ACCESS_TOKEN';
	
	// 创建菜单
	private static $MENU_CREATE		= 'https://api.weixin.qq.com/cgi-bin/menu/create?access_token=ACCESS_TOKEN';
	
	// 获取自定义菜单项
	private static $MENU_GET		= 'https://api.weixin.qq.com/cgi-bin/menu/get?access_token=ACCESS_TOKEN';

	// 删除自定义菜单
	private static $MENU_DELETE		= 'https://api.weixin.qq.com/cgi-bin/menu/delete?access_token=ACCESS_TOKEN';

	// 创建自定义二维码
	private static $QRCODE_CREATE 	= 'https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=ACCESS_TOKEN';

	// 上传图文消息
	private static $UPLOAD_NEWS_URL	= 'https://api.weixin.qq.com/cgi-bin/media/uploadnews?access_token=ACCESS_TOKEN';
	
	// 获取粉丝
	private static $USER_GET	= 'https://api.weixin.qq.com/cgi-bin/user/get?access_token=ACCESS_TOKEN';
	
	// 创建分组
	private static $GROUP_CREATE	= 'https://api.weixin.qq.com/cgi-bin/groups/create?access_token=ACCESS_TOKEN';
	
	// 查询分组
	private static $GROUP_GET		= 'https://api.weixin.qq.com/cgi-bin/groups/get?access_token=ACCESS_TOKEN';
	
	// 修改用户所在分组
	private static $GROUP_MEMBER_UPDATE	= 'https://api.weixin.qq.com/cgi-bin/groups/members/update?access_token=ACCESS_TOKEN';
	
	// 修改微信组信息
	private static $GROUP_UPDATE = 'https://api.weixin.qq.com/cgi-bin/groups/update?access_token=ACCESS_TOKEN';
	
	// 群发消息
	private static $MESSAGE_SEND_ALL = 'https://api.weixin.qq.com/cgi-bin/message/mass/sendall?access_token=ACCESS_TOKEN';
	
	//上传媒体文件
	private static $UPDATE_MEDIA	= 'http://file.api.weixin.qq.com/cgi-bin/media/upload?access_token=ACCESS_TOKEN&type=TYPE';

	//获取模板id
	private static $GET_TEMPLATE='https://api.weixin.qq.com/cgi-bin/template/api_add_template?access_token=ACCESS_TOKEN';

	//发送模板消息
	private static $TEMPLATE_MSG	= 'https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=ACCESS_TOKEN';
	
	//发送小程序模板消息
	private static $TEMPLATE_APP_MSG   = 'https://api.weixin.qq.com/cgi-bin/message/wxopen/template/send?access_token=ACCESS_TOKEN';
										

	private $access_token;

	/**
	 * WechatAPI构造函数
	 * 
	 * @param String $access_token 微信accesstoken（通过appid和appsecret换取）
	 * @return Wechat实例
	 */
	public function __construct($id=1) {
        $info = \app\timing\model\DiymenSet::detail($id);
        $this->access_token = $info['accesstoken'];
	}
	
	/**
	 * 获取分组
	 */
	public function getGroups() {
		$result = $this->curlGet($this->replaceUrl(self::$GROUP_GET));
		$result = json_decode($result, TRUE);
		return (intval($result['errcode']) > 0) ? FALSE : $result['groups'];
	}
	
	/**
	 * 创建分组
	 */
	public function createGroups($name) {
		$data = '{"group":{"name":"'. $name .'"}}';
		$result = $this->curlPost($this->replaceUrl(self::$GROUP_CREATE), $data);
		//var_dump($result);exit;
		$result = json_decode($result, TRUE);
		return (intval($result['errcode']) > 0) ? FALSE : $result['group'];
	}
	
	/**
	 * 修改分组名称 
	 * @param unknown $goupid
	 * @param unknown $name
	 * @return boolean
	 */
	public function updateGroup($goupid, $name) {
		$data 	= '{"group":{"id":'. $goupid .',"name":"'. $name .'"}}';
		$result = $this->curlPost($this->replaceUrl(self::$GROUP_UPDATE), $data);
		$result 	= json_decode($result, TRUE);
		return (intval($result['errcode']) > 0) ? FALSE : TRUE;
	}
	
	/**
	 * 移动用户所在分组
	 * 
	 */
	public function updateGroupForMember($openid, $toGroupId) {
		$data = '{"openid":"'. $openid .'","to_groupid":'. $toGroupId .'}';
		$result = $this->curlPost($this->replaceUrl(self::$GROUP_MEMBER_UPDATE), $data);
		$result = json_decode($result, TRUE);
		return ($result['errcode'] == 0) ? TRUE : FALSE;
	}
	
	/**
	 * 获取粉丝
	 * 
	 * @param string $nextOpenid
	 */
	public function getUsers($nextOpenid = FALSE) {
		$url = $this->replaceUrl(self::$USER_GET);
		if ($nextOpenid != FALSE) {
			$url .= '&next_openid='. $nextOpenid;
		}
		$result = $this->curlGet($url);
		$result = json_decode($result, TRUE);
		
		return (intval($result['errcode']) > 0) ? FALSE : $result;
	}

	/**
	 * 创建自定义菜单
	 * @return boolean
	 */
	public function createMenu($data) {
		$result = $this->curlPost($this->replaceUrl(self::$MENU_CREATE), $this->arrayToJson($data));
		$result = json_decode($result, TRUE);
		return ($result['errcode'] == 0) ? TRUE : $result['errmsg'];
	}

	/**
	 * 创建永久二维码
	 *
	 * @param int $scene_id 场景ID
	 * @return array qcode信息
	 */
	public function createLimitQcode($scene_id) {
		return $this->_createQcode($scene_id, 'QR_LIMIT_SCENE');
	}

	/**
	 * 创建临时二维码
	 *
	 * @param int $scene_id 场景ID
	 * @return array qcode信息
	 */
	public function createQcode($scene_id) {
		return $this->_createQcode($scene_id, 'QR_SCENE');
	}

	/**
	 * 获取所有微信客服信息
	 * @url http://dkf.qq.com/document-3_1.html
	 * @return Array
	 */
	public function getKFList() {
		$result =  $this->curlGet($this->replaceUrl(self::$CUSTOMER_SERVICE_KFLIST));
		$result = json_decode($result, TRUE);
		return (intval($result['errcode']) > 0) ? FALSE : $result['kf_list'];
	}

	/**
	 * 向关注者发送消息 [用户在48小时内已向公账号发送消息则有效]
	 * 参照 @url http://mp.weixin.qq.com/wiki/index.php?title=%E5%8F%91%E9%80%81%E5%AE%A2%E6%9C%8D%E6%B6%88%E6%81%AF
	 * @param array $data 消息
	 * @return
	 */
	public function sendMessage($openid, $content, $type = 'text') {
		
		$data = array('touser'=>$openid, 'msgtype'=>$type);
		if ($type == 'text') {
			$data['text'] = array('content'=>$content);
		} elseif ($type == 'image') {
			$data['image'] = array('media_id'=>$content);
		} else {
			exit('暂时不支持');
		}
		$result = $this->curlPost($this->replaceUrl(self::$MESSAGE_CUSTOM_SEND), $this->arrayToJson($data));
		$result = json_decode($result, TRUE);
		return ($result['errcode'] == 0) ? TRUE : $result["errmsg"];
	}

	/*
	 *获取微信的模板id
	 * @param $template_short 模板库中模板的编号，有“TM**”和“OPENTMTM**”等形式
	 * @return $template_id 模板id
	 * @author jiangcheng
	 */
	public function getTemplateId($template_short,&$template_id){
		$data=array(
			'template_id_short'=>$template_short
		);
		$result = $this->curlPost($this->replaceUrl(self::$GET_TEMPLATE), $this->arrayToJson($data));
		$result = json_decode($result, TRUE);
		if($result['errcode'] == 0){
			$template_id=$result['template_id'];
		}
		return ($result['errcode'] == 0) ? TRUE : $result["errmsg"];
	}

	public function sendTemplateMsg($openid, $template_id, $url,$content) {
		$topcolor = "#FF0000";
		$data = array('touser'=>$openid,
				 'template_id'=>$template_id,
				'url'=>$url,
				'topcolor'=>$topcolor,
				'data'=>$content,
				);
		
		$result = $this->curlPost($this->replaceUrl(self::$TEMPLATE_MSG), $this->arrayToJson($data));
		$result = json_decode($result, TRUE);

        $fileContent = json_encode($data)."\n".$openid."\n".$template_id."\n".$url."\n".json_encode($result);
        $dir = "soapGetDate/".date("Y-m-d")."/sendTemplateLog/";
        File::createDir($dir);
        $filename = date("H");
        writeLog($dir, $filename, $fileContent,FILE_APPEND);
        if($result['errcode']){
            writeLog2("sendTemplateFailLog",$fileContent,FILE_APPEND);
        }

		return ($result['errcode'] == 0) ? TRUE : $result["errmsg"];
	}
	
	/**
	 * 向小程序发送模板消息
	 * @param string $openid
	 * @param string $template_id
	 * @param string $page
	 * @param string $content  消息内容
	 */
	public function sendAppTemplateMsg($openid, $template_id, $page,$value,$form_id) {

		$topcolor = "#FF0000";
		$data = array('touser'=>$openid,
				'template_id'=>$template_id,
				'page'=>$page,
				'form_id'=>$form_id,
				'value'=>$value,
		);
		$result = $this->curlPost($this->replaceUrl(self::$TEMPLATE_APP_MSG), $this->arrayToJson($data));
		$result = json_decode($result, TRUE);
		return ($result['errcode'] == 0) ? TRUE : $result["errmsg"];
	}
	
	/**
	 * 群发消息
	 * 
	 * @param String $content 发送的内容 media_id获取文本内容
	 * @param Array $to 接收者，可以是分组编号、微信openid
	 * @param string $type 消息类型 ：mpnews - 图文, text - 文本, video - 视频
	 * @param string $target all - 群发给所有人, group - 群发到指定组, openid - 群发给指定用户
	 */
	public function sendMessageAll($content, $to = '', $target = 'all', $msgtype = 'mpnews') {
		$data = array();
		
		if ('group' == $target) {
			$data['filter'] = array('group_id'=>$to);
		} elseif ('openid' == $target) {
			$data['touser'] = $to;
		}
		
		$data['msgtype'] = $msgtype;
		if ('text' != $msgtype) {
			$data[$msgtype] = array('media_id'=>$content);
		} else {
			$data[$msgtype] = array('content'=>$content);
		}
		$result = $this->curlPost($this->replaceUrl(self::$MESSAGE_SEND_ALL), $this->arrayToJson($data));
		$result = json_decode($result, TRUE);
		
		return (intval($result['errcode']) > 0) ? FALSE : $result['msg_id'];
	}
	
	/**
	 * 上传文件
	 * @param unknown $file
	 * @param string $type
	 */
	public function uploadMedia($file, $type = 'image') {
		$url = $this->replaceUrl(self::$UPDATE_MEDIA);
		$url = preg_replace("/TYPE/s", $type, $url);
		$fields['media'] = '@'.$file;
		$result = $this->curlPost($url, $fields);
		$result = json_decode($result, TRUE);
		return (intval($result['errcode']) > 0) ? FALSE : $result;
	}
	
	/**
	 * 上传图文素材
	 */
	public function uploadNews($news) {
		$result = $this->curlPost($this->replaceUrl(self::$UPLOAD_NEWS_URL), $this->arrayToJson($news));
		$result = json_decode($result, TRUE);
		return (intval($result['errcode']) > 0) ? FALSE : $result;
	}

	/*
	 * 创建二维码
	 */
	private function _createQcode($scene_id, $action_name) {
		$data = array('action_name'=>$action_name, 'action_info'=>array('scene'=>array('scene_id'=>$scene_id)));
		$result = $this->curlPost($this->replaceUrl(self::$QRCODE_CREATE), $this->arrayToJson($data));
		return json_decode($result, TRUE);
	}
	
	/*
	 * 将url中ACCESS_TOKEN替换为真实值
	 */
	private function replaceUrl($url) {
		return preg_replace("/ACCESS_TOKEN/s", $this->access_token, $url);
	}

	/*
	 * 向微信发送GET请求
	 */
	private function curlGet($url) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_TIMEOUT, 3000);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3000);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		$result = curl_exec($ch);
		curl_close($ch);
		return $result;
	}

	/*
	 * 向微信发送请求
	 */
	private function curlPost($url, $data) {
		$ch = curl_init();
		$header = "Accept-Charset: utf-8";
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		//curl_setopt($url, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		$tmpInfo = curl_exec($ch);
		if (curl_errno($ch)) {
			var_dump($data); exit;
			return FALSE;
		} else {
			return $tmpInfo;
		}
	}

	/**************************************************************
       *
       *    使用特定function对数组中所有元素做处理
       *    @param  string  &$array     要处理的字符串
       *    @param  string  $function   要执行的函数
       *    @return boolean $apply_to_keys_also     是否也应用到key上
       *    @access public
       *
     *************************************************************/
    private function arrayRecursive(&$array, $function, $apply_to_keys_also = false)
    {
        static $recursive_counter = 0;
        if (++$recursive_counter > 1000) {
            die('possible deep recursion attack');
        }
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $this->arrayRecursive($array[$key], $function, $apply_to_keys_also);
            } else {
                $array[$key] = $function($value);
            }
     
            if ($apply_to_keys_also && is_string($key)) {
                $new_key = $function($key);
                if ($new_key != $key) {
                    $array[$new_key] = $array[$key];
                    unset($array[$key]);
                }
            }
        }
        $recursive_counter--;
    }
     
    /**************************************************************
     *
     *    将数组转换为JSON字符串（兼容中文）
     *    @param  array   $array      要转换的数组
     *    @return string      转换得到的json字符串
     *    @access public
     *
     *************************************************************/
    private function arrayToJson($array) {
        $this->arrayRecursive($array, 'urlencode', true);
        $json = json_encode($array);
        return urldecode($json);
    }

}
?>