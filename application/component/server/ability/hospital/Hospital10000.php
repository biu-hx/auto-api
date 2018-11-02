<?php

namespace app\component\server\ability\hospital;

use app\component\Curl;
use app\component\Log;
use app\component\response\Response;
use think\Config;

class Hospital10000
{

	private $url;

	private $token;

	private $version 	= '1';

	private static $route 	= [
		'barCode' 				=> ['/hospital/transfer/index' , 'get'],
		'markBarCodePrint' 		=> ['/hospital/transfer/index' , 'get'],
		'searchDrug' 			=> ['/hospital/transfer/index' , 'get'],
		'searchDiagnosis' 		=> ['/hospital/transfer/index' , 'get'],
		'searchInpatient'  		=> ['/hospital/transfer/index' , 'get'],
		'searchInpatientType'  	=> ['/hospital/transfer/index' , 'get'],
		'searchInpatientDetail' => ['/hospital/transfer/index' , 'get'],
		'eleHealthCard' 		=> ['/hospital/transfer/index' , 'get'],
        'searchOutHospitalInfo' => ['/hospital/transfer/index' , 'get'],
        'searchOutHospitalList' => ['/hospital/transfer/index' , 'get'],
        'submitLeaveHospital'   => ['/hospital/transfer/index' , 'get'],
        'searchInHospital'      => ['/hospital/transfer/index' , 'get'],
        'getSelectList'         => ['/hospital/transfer/index' , 'get'],
        'submitInHospital'      => ['/hospital/transfer/index' , 'get'],
        'sendMsg'               => ['/hospital/transfer/index' , 'get'],
        'sendCode'              => ['/hospital/transfer/index' , 'get'],
        'checkCode'             => ['/hospital/transfer/index' , 'get'],
        'getCheckCaution'       => ['/hospital/transfer/index' , 'get'],
        'getPatientInfo'        => ['/hospital/transfer/index' , 'get'],

	];

	function __construct($config)
	{
		$config = (array) $config; 		//强制config为数组
		if (!isset($config['url']) || !preg_match('/^((https|http|ftp|rtsp|mms)?:\/\/)[^\s]+/' , $config['url'])) {
			throw new \Exception('error url');	
		}
		if (!isset($config['token'])) {
			throw new \Exception('error token');	
		}
		$this->url 		= $config['url'];
		$this->token 	= $config['token'];
		isset($config['version']) && $this->version = $config['version'];
	}


	/** 
	 * 条码详情
	 *
	 * @access 	public
	 * @param 	int 	$hosId 			医院ID 
	 * @param 	array  	$data 			数据组
	 * ---------------------------------------
	 * 	 		string 	$barCode 		打印码
	 * ---------------------------------------
	 * @return  array
	 */
	public function barCode($data)
	{
		$params 	= [
			'hosId' 	=> 10000,
			'flag' 		=> 'getBarcodeByPrintcode',
			'content' 	=> '<Request><printcode>'.$data['barCode'].'</printcode></Request>',
		];
		$data 	= $this->request(__FUNCTION__ , $params);
		return $data;
		
	}

	/** 
	 * 标记条码已打印
	 *
	 * @access 	public
	 * @param 	int 	$hosId 			医院ID 
	 * @param 	array  	$data 			数据组
	 * ---------------------------------------
	 * 	 		string 	$barCode 		打印码
	 * ---------------------------------------
	 * @return  array
	 */
	public function markBarCodePrint($data)
	{
		$params 	= [
			'hosId' 	=> 10000,
			'flag' 		=> 'setBarcodePrinted',
			'content' 	=> '<Request><printcode>'.$data['barCode'].'</printcode></Request>',
		];
		$data 	= $this->request(__FUNCTION__ , $params);
		return $data;
	}

	/** 
	 * 药品查询
	 *
	 * @access 	public
	 * @param 	int 	$hosId 			医院ID 
	 * @param 	array  	$data 			数据组
	 * ---------------------------------------
	 * 	 		string 	$search 		查询条件
	 * ---------------------------------------
	 * @return  array
	 */
	public function searchDrug($data)
	{
		$params 	= [
			'hosId' 	=> 10000,
			'flag' 		=> 'SearchPrice',
			'content' 	=> '<Request><input>'.$data['search'].'</input></Request>',
		];
		$data 	= $this->request(__FUNCTION__ , $params);
		return $data;
	}

	/** 
	 * 诊疗项目查询
	 *
	 * @access 	public
	 * @param 	int 	$hosId 			医院ID 
	 * @param 	array  	$data 			数据组
	 * ---------------------------------------
	 * 	 		string 	$search 		查询条件
	 * ---------------------------------------
	 * @return  array
	 */
	public function searchDiagnosis($data)
	{
		$params 	= [
			'hosId' 	=> 10000,
			'flag' 		=> 'GetMedInfo',
			'content' 	=> '<Request><input>'.$data['search'].'</input></Request>',
		];
		$data 	= $this->request(__FUNCTION__ , $params);
		return $data;
	}

	/** 
	 * 查询住院列表
	 *
	 * @access 	public
	 * @param 	int 	$hosId 			医院ID 
	 * @param 	array  	$data 			数据组
	 * ---------------------------------------
	 * 	 		string 	$cardNo 		就诊卡号
	 * ---------------------------------------
	 * @return  array
	 */
	public function searchInpatient($data)
	{
		$params 	= [
			'hosId' 	=> 10000,
			'flag' 		=> 'SelectAdmByCardOrRegNo',
			'content' 	=> '<Request><CardNo>'.$data['cardNo'].'</CardNo><RegNo></RegNo><PatType>I</PatType></Request>',
		];
		$data 	= $this->request(__FUNCTION__ , $params);
		return $data;
	}

	/** 
	 * 查询住院类型
	 *
	 * @access 	public
	 * @param 	int 	$hosId 			医院ID 
	 * @param 	array  	$data 			数据组
	 * ---------------------------------------
	 * 	 		string 	$admId 			住院ID
	 * 	 		string 	$arpbl 			住院标识
	 * ---------------------------------------
	 * @return  array
	 */
	public function searchInpatientType($data)
	{
		$params 	= [
			'hosId' 	=> 10000,
			'flag' 		=> 'FetchPatFeeInfog',
			'content' 	=> '<Request><AdmId>'.$data['admId'].'</AdmId><Arpbl>'.$data['arpbl'].'</Arpbl></Request>',
		];
		$data 	= $this->request(__FUNCTION__ , $params);
		return $data;
	} 

	/** 
	 * 查询住院详情
	 *
	 * @access 	public
	 * @param 	int 	$hosId 			医院ID 
	 * @param 	array  	$data 			数据组
	 * ---------------------------------------
	 * 	 		string 	$arpbl 			住院标识
	 * ---------------------------------------
	 * @return  array
	 */
	public function searchInpatientDetail($data)
	{
		$params 	= [
			'hosId' 	=> 10000,
			'flag' 		=> 'GetFymx',
			'content' 	=> '<Request><Arpbl>'.$data['arpbl'].'</Arpbl></Request>',
		];
		$data 	= $this->request(__FUNCTION__ , $params);
		return $data;
	}	

	/** 
	 * 电子居民健康卡查询
	 *
	 * @access 	public
	 * @param 	int 	$hosId 			医院ID 
	 * @param 	array  	$data 			数据组
	 * ---------------------------------------
	 * 	 		string 	$eleHealthCard 	电子居民健康卡加密信息
	 * ---------------------------------------
	 * @return  array
	 */
	public function eleHealthCard($data)
	{
		$params 	= [
			'hosId' 	=> 10000,
			'flag' 		=> 'ScanCardGetCardNo',
			'content' 	=> $data['eleHealthCard'],
		];
		$data 	= $this->request(__FUNCTION__ , $params);
		return $data;
	}

    /**
     *
     * @param $data
     * @return array
     */
    public function searchOutHospitalInfo($data){
        $params 	= [
            'hosId' 	=> 10000,
            'flag' 		=> 'searchOutHospitalInfo',
            'content' 	=> '<Request><CardNo>'.$data['cardId'].'</CardNo></Request>',
        ];
        $data 	= $this->request(__FUNCTION__ , $params);
        return $data;
    }


    /**
     *
     * @param $data
     * @return array
     */
    public function searchOutHospitalList($data){
        $params 	= [
            'hosId' 	=> 10000,
            'flag' 		=> 'searchOutHospitalList',
            'content' 	=> '<Request><AdmId>'.$data['AdmId'].'</AdmId></Request>',
        ];
        $data 	= $this->request(__FUNCTION__ , $params);
        return $data;
    }

    /**
     *
     * @param $data
     * @return array
     */
    public function submitLeaveHospital($data){
        $params 	= [
            'hosId' 	=> 10000,
            'flag' 		=> 'submitLeaveHospital',
            'content' 	=> '<Request><AdmId>'.$data['AdmId'].'</AdmId></Request>',
        ];
        $data 	= $this->request(__FUNCTION__ , $params);
        return $data;
    }

    /**
     * 查询入院证信息
     * @param $data
     * @return array
     */
    public function searchInHospital($data){
        $params 	= [
            'hosId' 	=> 10000,
            'flag' 		=> 'searchInHospital',
            'content' 	=> '<Request><CardNo>'.$data['cardId'].'</CardNo></Request>',
        ];
        $data 	= $this->request(__FUNCTION__ , $params);
//        print_r($data);exit;
        return $data;
    }

    /**
     * 提交入院
     * @param $content
     * @return array
     */
    public function submitInHospital($content){
        $params 	= [
            'hosId' 	=> 10000,
            'flag' 		=> 'submitInHospital',
            'content' 	=> $content['content'],
        ];
        $data 	= $this->request(__FUNCTION__ , $params);
        return $data;
    }

    /**
     *  查询选项
     * @return array
     */
    public function getSelectList(){
        $params 	= [
            'hosId' 	=> 10000,
            'flag' 		=> 'getSelectList',
            'content' 	=> '<Request><CardNo></CardNo></Request>',
        ];
        $data 	= $this->request(__FUNCTION__ , $params);
        return $data;
    }




    /**
     * 请求
     *
     * @access 	private
     * @param 	string 	$method 请求调用的方法
     * @param   array 	$params 请求的参数
     * @return 	array
     */
    private function request($method , $params)
    {
        Log::storageRequest('hospitalRequest_'.$method , $params);
        $params['token'] 	= $this->token;
        $data 	= [];
        $header = [
            'Content-Type:application/x-www-form-urlencoded;charset=utf-8',
            'api-version:'.$this->version,
        ];
        //获取医院信息
        $hosId = $params['hosId'];
        //获取医院信息
        $hosInfo = \app\equip\model\Hospital::detail($hosId);
        //如果有院区，则获取院区信息
        if($hosInfo['have_branch']){
            $districtId = isset($params['districtId'])?$params['districtId']:0;
//            print_r($districtId);exit;
            //如果院区存在
            if($districtId){
                //重置hosid 和 token
                $districtInfo = \app\equip\model\Hospital::getDistrictInfo($districtId);
                $params['hosId'] = $districtInfo['hospital_id'];
            }
        }
        $method = 'barCode';//因为地址全部一样，所以默认为一个，免得去配置
		$route 		= self::$route[$method][0];
		$type 		= isset(self::$route[$method][1]) ? strtolower(self::$route[$method][1]) : 'post';
		switch ($type) {
			case 'get':			
				$url 	= $this->url.$route.'?'.http_build_query($params);
				break;
			case 'post':		
				$url 	= $this->url.$route;
				$data 	= http_build_query($params);
				break;
			case 'put':
				$url 	= $this->url.$route;
				$data 	= http_build_query($params);
			break;
			case 'patch':	
				$url 	= $this->url.$route;
				$data 	= http_build_query($params);
			break;
			case 'delete':	
				$url 	= $this->url.$route;
				$data 	= http_build_query($params);
			break;
			default:		break;
		}
		Log::storageRoute('hospitalRequest_'.$method , $url);
		Curl::init();
		Curl::setUrl($url);
		Curl::setCustomRequest($type);
		$data && Curl::setParams($data);
		Curl::setHttpHeader($header);
		Curl::setOpt();
		$response = Curl::execute();
        error_log("RETURN => " . $response);
        error_log("URL => " . $url);
        $result 	= json_decode($response , true);
        Log::storageResponse('hospitalRequest_'.$method , $result);
        Log::writeLog('hospitalRequest_'.$method);
        return $result ? $result : [];
    }

    /**
     *  发送故障消息
     * @param $content
     * @return bool|mixed
     */
	public function sendMsg($content){
        $params 	= [
            'hosId' 	=> 10000,
            'flag' 		=> 'sendFaultMessage',
            'content' 	=> $content,
        ];
        $data 	= $this->request(__FUNCTION__ , $params);
        return $data;
    }

    /**
     * 发送短信验证码
     * @param $phone
     * @return array
     */
    public function sendCode($phone){
        $params 	= [
            'hosId' 	=> 10000,
            'flag' 		=> 'sendCode',
            'content' 	=> '<Request><Phone>'.$phone['phone'].'</Phone></Request>',
        ];
        $data 	= $this->request(__FUNCTION__ , $params);
        return $data;
    }

    /**
     *  验证短信验证码
     * @param $data
     * @return array
     */
    public function checkCode($data){
        $params 	= [
            'hosId' 	=> 10000,
            'flag' 		=> 'checkCode',
            'content' 	=> '<Request><Phone>'.$data['phone'].'</Phone><Code>'.$data['code'].'</Code></Request>',
        ];
        $data 	= $this->request(__FUNCTION__ , $params);
        return $data;
    }

    /**
     *  获取B超提醒
     * @param $data
     * @return array
     */
    public function getCheckCaution($data){
        $params 	= [
            'hosId' 	=> 10000,
            'flag' 		=> 'getCheckCaution',
            'content' 	=> '<Request><cardId>'.$data['cardId'].'</cardId></Request>',
        ];
        $data 	= $this->request(__FUNCTION__ , $params);
        return $data;
    }

    /**
     *  获取B超个人信息
     * @param $data
     * @return array
     */
    public function getPatientInfo($data){
        $params 	= [
            'hosId' 	=> 10000,
            'flag' 		=> 'getPatientInfo',
            'content' 	=> '<Request><cardNo>'.$data['cardId'].'</cardNo></Request>',
        ];
        $data 	= $this->request(__FUNCTION__ , $params);
        return $data;
    }

    /**
     *  获取B超时间限制
     * @param $data
     * @return array
     */
    public function getCheckTimeSet($data){
        $params 	= [
            'hosId' 	=> 10000,
            'flag' 		=> 'getCheckTimeSet',
            'content' 	=> '<Request></Request>',
        ];
        $data 	= $this->request(__FUNCTION__ , $params);
        return $data;
    }


    /**
     *  获取B超可预约时间
     * @param $data
     * @return array
     */
    public function getCheckCalendar($data){
        $params 	= [
            'hosId' 	=> 10000,
            'flag' 		=> 'getCheckCalendar',
            'content' 	=> '<Request><patient_id>'.$data['patient_id'].'</patient_id></Request>',
        ];
        $data 	= $this->request(__FUNCTION__ , $params);
        return $data;
    }
    /**
     *  获取B超检查状态
     * @param $data
     * @return array
     */
    public function getPatientState($data){
        $params 	= [
            'hosId' 	=> 10000,
            'flag' 		=> 'getPatientState',
            'content' 	=> '<Request><patient_id>'.$data['patient_id'].'</patient_id></Request>',
        ];
        $data 	= $this->request(__FUNCTION__ , $params);
        return $data;
    }


    /**
     *  提交B超预约
     * @param $data
     * @return array
     */
    public function putConfirmCheck($data){
        $params 	= [
            'hosId' 	=> 10000,
            'flag' 		=> 'putConfirmCheck',
            'content' 	=> '<Request><patient_id>'.$data['patient_id'].'</patient_id><appdate>'.$data['appdate'].'</appdate><apptime>'.$data['apptime'].'</apptime></Request>',
        ];
        $data 	= $this->request(__FUNCTION__ , $params);
        return $data;
    }

    /**
     *  提交B超预约
     * @param $data
     * @return array
     */
    public function waitDetail($data){
        $params 	= [
            'hosId' 	=> 10000,
            'flag' 		=> 'waitDetail',
            'content' 	=> '<Request><CardNo>'.$data['cardId'].'</CardNo></Request>',
        ];
        $data 	= $this->request(__FUNCTION__ , $params);
        return $data;
    }

    /**
     *  提交B超预约
     * @param $data
     * @return array
     */
    public function getCardListByFace($data){
        $params 	= [
            'hosId' 	=> 10000,
            'flag' 		=> 'getCardListByFace',
            'content' 	=> '<Request><IDCardNo>'.$data['IDCardNo'].'</IDCardNo></Request>',
        ];
        $data 	= $this->request(__FUNCTION__ , $params);
        return $data;
    }

    public function createAiDirectSession($data){
        $content = array('Request'=>$data);
        $content_xml = Response::ToXml($content);
        $params 	= [
            'hosId' 	=> 10000,
            'flag' 		=> 'createAiDirectSession',
            'content' 	=> $content_xml,
        ];
        $data 	= $this->request(__FUNCTION__ , $params);
        return $data;
    }


    public function getAiDirectAnswer($data){
        $content = array('Request'=>$data);
        $content_xml = Response::ToXml($content);
        $params 	= [
            'hosId' 	=> 10000,
            'flag' 		=> 'getAiDirectAnswer',
            'content' 	=> $content_xml,
        ];
        $data 	= $this->request(__FUNCTION__ , $params);
        return $data;
    }




}