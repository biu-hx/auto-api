<?php

namespace app\api\event\v1;

use think\Loader;
use app\api\controller\Base;
use app\component\response\Response;
use app\component\server\Server;
use app\component\Common;

class User extends Base
{

    protected $validate = '\app\api\validate\v1\User';

    protected $scene 	= [
        'login',
        'code',
        'verify',
        'modify',
    ];

    /**
     * 医生用户登录
     *
     * @access 	public
     * @return 	void
     */
    public function login(){
        $equip 	= \think\Request::instance()->header('user-equip');
        $equip 	|| Response::message(20003);
        $phone 	= $this->data['phone'];
        $pass 	= $this->data['password'];
        $data  	= Loader::model('User')->userByPhone($phone);
        $data 	|| Response::message(13001);
        $doctorId 	= $data['doctor_id'];
        $password 	= $data['password'];
        $salt 		= \think\Config::get('salt');
        $auth 		= Common::password($doctorId , $pass , $salt);
        $password 	!= $auth && Response::message(20111);
        $token 		= Common::token($doctorId);
        $expire_time 	= time() + 600;
        Loader::model('User')->saveToken($doctorId , $token , $expire_time);
        $response 	= [
            'phone' 	=> $phone,
            'token' 	=> $token,
            'expire' 	=> $expire_time,
        ];
        //如果是小程序，则登陆后直接绑定到小程序上
        if($this->isLittleApp()){
//            $form_id || Response::errorMessage("必须有formid");
            $form_id = "";
            $response['token'] = $this->token;
            \app\api\model\Littleapp::appTokenAddDoctor($this->openid,$doctorId,$form_id);
            //设备设置为小程序
            \app\api\model\User::setLoginEquip($doctorId,$equip);
            //设置其它的用户为未登陆状态
            \app\api\model\Littleapp::setOtherUnbind($this->openid,$doctorId);

        }else{
            $this->token = $token;
        }
        $this->appSession("doctorPhone",$phone);

        Response::message(10000 , $response);
    }


    private function isTester($openid){
        $testers = ['o2GLW5UqCtBbhy0PAF23hZnm7rvg',//魏建
//            'o2GLW5Y8qX_MLNf93EIQ1y-3eAww',//张明慧
            'o2GLW5XMOQ0EK-ru62x54acAjdu0',//叶平
            'o2GLW5WbwKRrmOoy0rDltwWX57Sw',//陈素珍
            'o2GLW5VqcEOKVU-9gk9bMaA9seY8',//杨磊
        ];

        $versionList = [
            ['id'=>1,'name'=>'demo版本','apiUrl'=>'https://rytt.mobimedical.cn'],
            ['id'=>2,'name'=>'测试版本','apiUrl'=>'https://auto-test.mobimedical.cn'],
            ['id'=>3,'name'=>'正式版本','apiUrl'=>'https://auto-api.mobimedical.cn'],
            ['id'=>4,'name'=>'准正式版本','apiUrl'=>'http://139.199.206.91:8051'],
            ['id'=>5,'name'=>'正式版本','apiUrl'=>'http://auto-api.mobimedical.cn'],
        ];
        if(in_array($openid,$testers));{
            return $versionList;
        }
        return false;
    }
    /**
     * 小程序获取apiTokey
     */
    public function littleAppLogin(){
        $js_code= $this->data['js_code'];
        $encryptedData= $this->data['encryptedData'];
        $iv= $this->data['iv'];//偏移量
        if(!$js_code){
            Response::message(33105);
        }
        if(!$iv){
            Response::message(33106);
        }
        $appInfo = \app\timing\model\DiymenSet::detail(1);
        $appInfo || Response::errorMessage("未找到小程序相关配置");
        $url=sprintf(self::LITTLE_APP_URL,$appInfo['appid'],$appInfo['appsecret'],$js_code);
        $str  	= file_get_contents($url);
        $fans		= json_decode($str, TRUE);
        import('AesCode.WXBizDataCrypt');
        if(isset($fans['errcode'])&&$fans['errcode']){
            Response::errorMessage($fans['errmsg']);
        }

        //解密
        $pc = new \WXBizDataCrypt($appInfo['appid'], $fans['session_key']);
        $data=array();
        $errCode = $pc->decryptData($encryptedData, $iv, $data );
        if ($errCode != 0) {
            Response::errorMessage('获取用户基本信息失败');
            return false;
        }
        $userInfo = json_decode($data,true);
        if(!$userInfo){
            Response::errorMessage("没有找到用户信息");
        }
        $little_app = \app\timing\model\DiymenSet::createTokenAndSave($userInfo,self::LITTLE_APP_EXPITRE);
        $little_app_token = $little_app['token'];
        $this->token = $little_app_token;
        $result['token'] = $little_app_token;
        $result['expire'] = time()+self::LITTLE_APP_EXPITRE;
        if($little_app['bind_doctor']){
            $docInfo = \app\api\model\Personal::docAuthInfo($little_app['bind_doctor']);
            $result['bind_doctor'] = $docInfo['phone'];
        }else{
            $result['bind_doctor'] = "";
        }
        //判断 是否是测试员
        $versionList = $this->isTester($userInfo['openId']);
        if($versionList!==false){
            $result['isTester'] = 1;
            $result['versionList'] = $versionList;
        }else{
            $result['isTester'] = 0;
            $result['versionList'] = [];
        }


        //标记为小程序
        $this->appSession("loginType",'littleApp');
        Response::message(10000,$result);
    }



    /**
     * 用户登出
     */
    public function loginout(){
        //小程序则取消绑定
        if($this->isLittleApp()){
            \app\api\model\Littleapp::appTokenAddDoctor($this->openid,0);
        }else{
            \app\api\model\User::setTokenTimeout($this->token);
        }
        Response::message(10000);
    }

    /**
     * 获取验证码
     *
     * @access 	public
     * @return 	void
     */
    public function code()
    {
        $phone 	= $this->data['phone'];
        $data 	= Loader::model('User')->userByPhone($phone);
        $data   || Response::message(13001); 				//没有数据 表示没有这个手机号的账户
        $doctorId 	= $data['doctor_id'];
        $time 		= time();
        $expire 	= $time + 600;
        $code 		= Common::code();
        $tempId 	= 'SMS_30120257';
        $temp 		= ['code' => (string) $code , 'product' => '问诊通'];
        $signname 	= '身份验证';
        $result 	= Server::ability('message')->sendMessage($phone , $tempId , $temp , $signname); 		//调用消息中心服务
        (!$result || $result['code'] != 200) && Response::message(11001); 				//调用消息中心失败
        Loader::model('User')->saveCode($doctorId , $code , $expire);
        $response 	= [
            'phone' 	=> $phone,
            'expire' 	=> 600,
            'time' 		=> $time,
        ];
        Response::message('10000' , $response);
    }

    /**
     * 验证验证码
     *
     * @access 	public
     * @return 	void
     */
    public function verify()
    {
        $phone 	= $this->data['phone'];
        $code 	= $this->data['code'];
        $data 	= Loader::model('User')->userByPhone($phone); 	//验证码是否正确
        $data   || Response::message(13001); 					//没有数据 表示没有这个手机号的账户
//        $doctorId 	= $data['doctor_id'];
        $time 		= time();
//        print_r($data);exit;
        $data['code'] != $code && Response::message(20103); 		//返回验证码不匹配
        $data['code_expire'] < $time && Response::message(20202); 	//返回验证码已过期
        \app\api\model\CommonFunction::setCodeVerifyEd($phone);//设置为已验证
        Response::message(10000);
        /**
        $token 		= Common::token($doctorId);
        $expire 	= $time + 600;
        Loader::model('User')->saveToken($doctorId , $token , $expire);
        $response 	= [
            'phone' 	=> $phone,
            'token' 	=> $token,
            'expire' 	=> 600,
            'time' 		=> $time,
        ];
        Response::message(10000 , $response);
         **/
    }

    /**
     *
     *
     * @access 	public
     * @return 	void
     */
    public function modify()
    {
        $phone 	= $this->data['phone'];
        //检测是否已检验过验证码
        $checked = \app\api\model\CommonFunction::checkCodeVerifyEd($phone);//设置为已验证
        if(!$checked){
            Response::message(20202);
        }
        $pass 	= $this->data['password'];
        $data 	= Loader::model('User')->userByPhone($phone); 	//验证码是否正确
        $data   || Response::message(13001); 					//没有数据 表示没有这个手机号的账户
        $time 	= time();
        $data['token_expire'] < $time && Response::message(20203); 	//返回已过期
        $salt 		= \think\Config::get('salt');
        $doctorId 	= $data['doctor_id'];
        $password 	= Common::password($doctorId , $pass , $salt);
        Loader::model('User')->savePassword($doctorId , $password);
        Response::message(10000 , true);
    }


    /**
     * 修改密码
     *
     * @access 	public
     * @return 	void
     */
    public function reSetPassword()
    {
        $old_password 	= $this->data['old_password'];
        //检测是否已检验过验证码
        $new_pass 	= $this->data['password'];
        $phone = $this->doctorPhone;
        $doctorId = $this->doctorId;
        $data 	= Loader::model('User')->userByPhone($phone); 	//验证码是否正确
        $data   || Response::message(13001); 					//没有数据 表示没有这个手机号的账户
        $salt 		= \think\Config::get('salt');
        $old_password 	= Common::password($doctorId , $old_password , $salt);
        if($old_password!=$data['password']){
            Response::message(20111);//密码验证不正确
        }
        $password 	= Common::password($doctorId , $new_pass , $salt);
        Loader::model('User')->savePassword($doctorId , $password);
        //清除小程序绑定
        //小程序则取消绑定
        if($this->isLittleApp()){
            \app\api\model\Littleapp::appTokenAddDoctor($this->openid,0);
        }
        Response::message(10000 , true);
    }


}