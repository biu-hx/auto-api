<?php
// +----------------------------------------------------------------------
// | 基础继承控制
// +----------------------------------------------------------------------
// | Author: duanhy <shongyudxmas@163.com> 
// +----------------------------------------------------------------------
// | version: 1.0
// +----------------------------------------------------------------------

namespace app\api\controller;

use think\Controller;
use think\Request;
use think\Loader;
use app\component\response\Response;
use app\component\Log;
use \app\timing\model\DiymenSet;

class Base extends Controller
{
    protected $action;                //定义当前请求类型

    protected $controller;            //定义当前执行文件

    protected $openid;            //定义当前执行文件

    protected $validate;            //设置验证器

    protected $scene = [];    //控制需要验证的关系

    protected $jwtAuth = ['User', 'App'];
    protected $loginType;//['littleApp', 'app'];

    protected $data;                //数据接收参数

    protected $doctorId;            //定义是哪个医生的数据
    protected $doctorHead; 	//医生头像
    protected $doctorName; 	//医生姓名
    protected $doctorPhone; 	//医生登陆手机号
    protected $bindDoctor = 1; 	//是否已绑定医生
    protected $doctordept_id; 	//所在科室



    protected $apiVersion = 1;    //定义api版本
    protected $token 	= ""; 	//前端token
    const LITTLE_APP_URL = 'https://api.weixin.qq.com/sns/jscode2session?appid=%s&secret=%s&js_code=%s&grant_type=authorization_code';
    const LITTLE_APP_EXPITRE = 172800;//两天

    function __construct()
    {
        parent::__construct();
//        $this->keyAuth();
        $this->apiVersion = Request::instance()->header('api-version', $this->apiVersion);
        $this->loginType 	= Request::instance()->header('loginType' , $this->loginType);
        $this->token 	= Request::instance()->header('token' , $this->token);
        $this->action = Request::instance()->action();        //获取当前方法名称
        $this->controller = Request::instance()->controller();    //获取当前执行文件
        $this->data = self::params();
        //app和小程序登陆地址不作处理
//        echo $this->action;exit;
//        if(in_array($this->action,array('littleAppLogin','login'))){
//            return;
//        }
        //如果是小程序登陆，则不初始化
        if('littleAppLogin' == $this->action){
            return;
        }
        //如果是app，登陆时也不初始化
        if($this->isApp()&&'login' == $this->action){
            return;
        }
        //忘记密码流程不需要登陆
        if($this->controller=='User' && in_array($this->action,['code','verify','modify'])){
            return;
        }


        $this->token || Response::message(31104);

        if($this->isLittleApp()){
            $this->initLittleAppInfo();
            //app登陆，初始化
        }elseif ($this->isApp()){
            $this->initAppInfo();
        }else{
            Response::errorMessage("登陆类型异常");
        }

        //初始化通用信息
//        $this->doctorPhone = $this->appSession("doctorPhone");

//        exit;
//        Log::storageRequest('apiResponse', $this->data);
//        Log::storageRoute('apiResponse', Request::instance()->url());
//        if (!in_array($this->controller, $this->jwtAuth)) {
//            $this->jwtAuth();
//        }
//        if (in_array($this->action, $this->scene) && $this->validate) {
//            $result = $this->validate($this->data, $this->validate . '.' . $this->action);
//            $result !== true && Response::message($result);
//        }
//        //如果是小程序，刷初始化小程序数据
//        if($this->little_app_token){
//            $this->initLittleAppInfo();
//        }

    }

    /**
     * 初始化小程序数据
     */
    function initLittleAppInfo(){
        $littleAppInfo = \app\api\model\Littleapp::littleAppTokenDetail($this->token);
        if(!$littleAppInfo){//如果没有查到，去查历史记录
            $littleAppLog = \app\api\model\Littleapp::getLittleTokenLog($this->token);
            if(!$littleAppLog){//未登录过
                Response::message(33107);
            }else{//有登录过
                Response::message(33108);
            }
        }
        //判断登录是否已过期
        if(time()>$littleAppInfo['token_expireTs']){
            Response::message(33109);
        }
//        print_r($littleAppInfo);exit;
        //查询是否已绑定了医生
        if($littleAppInfo['bind_doctor']){
            $docInfo = \app\api\model\Personal::basic($littleAppInfo['bind_doctor']);
            $this->doctorHead = $docInfo['avatar'];
            $this->doctorName = $docInfo['name'];
            $this->doctorPhone = $docInfo['phone'];
            $this->doctorId = $littleAppInfo['bind_doctor'];
            $this->doctordept_id = $docInfo['dept_id'];
        }else{
            $this->bindDoctor = 0;
            //除了登陆的情况下都必须要有医生
            if(!$this->doctorId&&'login' != $this->action){
                Response::errorMessage("您还没有登陆",33107);
            }
        }

       $this->openid = $littleAppInfo['openid'];
        $this->loginType = 'littleApp';
        $this->little_app_token = $littleAppInfo['little_app_token'];
        //更新token时间
        \app\api\model\Littleapp::resetLittleAppExpireTs($this->openid,self::LITTLE_APP_EXPITRE);

    }

    /**
     * @return 判断是否是小程序
     */
    protected function isLittleApp(){
        if(!$this->token){
            return false;
        }
        $loginType = $this->appSession("loginType");
        if($loginType){
            $this->loginType = $loginType;
        }
        return $this->loginType == 'littleApp';
    }

    /**
     * @return 判断是否是普通app
     */
    protected function isApp(){
        if(!$this->token){
            return true;
        }
        $loginType = $this->appSession("loginType");
        if($loginType){
            $this->loginType = $loginType;
        }
        return $this->loginType != 'littleApp';
    }

    /**
     * 初始化app数据
     */
    function initAppInfo(){
        $appInfo = \app\api\model\Littleapp::appTokenDetail($this->token);
        if(!$appInfo){
            Response::message(33107);
        }
        //判断登录是否已过期
        if(time()>$appInfo['token_expire']){
            Response::message(33109);
        }
        $this->loginType = 'app';

        $docInfo = \app\api\model\Personal::basic($appInfo['doctor_id']);
        $docInfo || Response::errorMessage("没有找到对应的医生");
        $this->doctorHead = $docInfo['avatar'];
        $this->doctorId = $appInfo['doctor_id'];
        $this->doctorName = $docInfo['name'];
        $this->doctordept_id = $docInfo['dept_id'];
        $this->doctorPhone = $docInfo['phone'];
    }





	/**
	 * 验证请求权限
	 *
	 * @access 	private
	 * @return 	void
	 */
	private function keyAuth()
	{
		$key 	= Request::instance()->header('emt-key');
		$key 	|| Response::message(20001); 
		$auth 	= \think\Config::get('auth_key');
		$key != $auth 	&& Response::message(20101); 		//如果key不符合，不予访问
	}

	/**
	 * 验证jwt
	 *
	 * @access 	private
	 * @return 	void
	 */
	private function jwtAuth()
	{
		$jwt 	= Request::instance()->header('emt-jwt');
		$jwt 	|| Response::message(20002); 					//没有jwt
		$equip 	= Request::instance()->header('user-equip');
		$equip 	|| Response::message(20003);
		$data 	= Loader::model('User')->jwt($jwt);				//获取数据
		$data  	|| Response::message(19000); 					//表明jwt已失效，说明存在别的设备登录
		$data['expire'] < time() && Response::message(20201); 	//jwt时间过期
		//$equip 	!= $data['login_equip'] && Response::message(19000);
		$this->doctorId = $data['doctor_id'];	
	}

	/**
	 * 转化参数
	 * 
	 * @access 	protected
	 * @return 	array
	 */
	protected function params()
	{
		if (Request::instance()->isGet()) { // 是否为 GET 请求
			$data 	= Request::instance()->get();
		} else if (Request::instance()->isPost()) { // 是否为 POST 请求
			$data 	= Request::instance()->post();
		} else if (Request::instance()->isPut()) {// 是否为 PUT 请求
			$data 	= Request::instance()->put();
		} else if (Request::instance()->isDelete()) { // 是否为 DELETE 请求
			$data 	= Request::instance()->delete();
		} else if (Request::instance()->isPatch()) { // 是否为 Patch 请求
			$data 	= Request::instance()->patch();
		}
		return $data ? $data : [];
	}

    /**
     * app用的session
     * @param $key
     * @param $value
     */
	protected function appSession($key,$value=false){
	    if(!$this->token){
	        Response::errorMessage("还没有初始化token");
        }
        //获取数据
        $session_key = 'appSession'.$this->token;
        $oldValueStr = \app\component\server\Server::cache('redis')->get($session_key);

        if($oldValueStr){
            $oldValue = json_decode($oldValueStr,true);
        }
        //如果有值，则设置值
        if($value){
            $oldValue[$key] = $value;
            \app\component\server\Server::cache('redis')->set($session_key,json_encode($oldValue),3600*24);
        }else{

            return isset($oldValue[$key])?$oldValue[$key]:false;
        }
    }

    /**
     * 清空数据
     * @param $key
     * @param bool $value
     */
    protected function unsetAppSession($key,$value=false){
        if(!$this->token){
            Response::errorMessage("还没有初始化token");
        }
        //获取数据
        $session_key = 'appSession'.$this->token;
        \app\component\server\Server::cache('redis')->set($session_key,'',3600*24);
    }

    protected function getData($key,$must=true,$default=""){
        if($must){
            if(!isset($this->data[$key])){
                Response::errorMessage("需少参数".$key);
            }
        }
        return isset($this->data[$key])?$this->data[$key]:$default;
    }

}