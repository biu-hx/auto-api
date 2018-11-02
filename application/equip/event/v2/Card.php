<?php

namespace app\equip\event\v2;

use think\Loader;
use app\equip\controller\Base;
use app\component\Curl;
use app\component\response\Response;
use app\component\server\Server;

class Card extends Base
{

	protected $validate = '\app\equip\validate\v2\Card';

	protected $scene = [
		'card',
		'cardByPatient',
		'cardByEleHealthCard',
        'getCardInfo'
	];

	protected $mustId  = [
		'card',
		'cardByPatient',
		'cardByEleHealthCard',
        'getCardInfo'
	];

	/**
	 * 身份证查询就诊卡
	 *
	 * @access 	public
	 * @return 	void
	 */
	public function card()
	{
		$IDCard = $this->data['IDCard'];
		$hid 	= $this->hospitalId;
		$params = [
			'IdCardNo' 	=> $IDCard,
		];
		$data 	= Server::ability('hospital')->patientCard($hid , $params);
		if (!$data || $data['code'] != 10000) Response::message(30000);
		$patient= [];
		$card 	= $data['data'];
		if (!isset($card[0])) {
			$patient['cardId'] 	= isset($card['cardId']) ? $card['cardId'] : '';
			$patient['IDCard'] 	= $IDCard;
			$patient['cardName'] 	= isset($card['cardName']) ? $card['cardName'] : '';
			$patient['phone'] 	= isset($card['phone']) ? $card['phone'] : '';
		} else {
			foreach ($card as $v) {
                $patient['cardId'] 	= isset($v['cardId']) ? $v['cardId'] : '';
                $patient['IDCard'] 	= $IDCard;
                $patient['cardName'] 	= isset($v['cardName']) ? $v['cardName'] : '';
                $patient['phone'] 	= isset($v['phone']) ? $v['phone'] : '';
			}
		}
		Response::success($patient);
	}

	/**
	 * 就诊卡号查询就诊卡
	 *
	 * @access 	public
	 * @return 	void
	 */
	public function cardByPatient()
	{
		$cardId = trim($this->data['cardId']);
		$hid 	= $this->hospitalId;
//		if($hid == 61754 or $hid ==  61755){
//            $cardId = '000000000002';
//        }
		$params = [
			'cardId' 	=> $cardId,
		];
        if($hid == 61756){
            $params['IdCardNo'] = $cardId;
        }
		//error_log("carId => " . $cardId);
		//error_log("hisId => " . $hid);
		$data 	= Server::ability('hospital')->patientCard($hid , $params);
		if (!$data || $data['code'] != 10000) Response::message(30000);
		$patient 	= [
			'cardId' 	=> $cardId,
			'IDCard' 	=> $data['data']['IdCardNo'],
            'UserIdKey' 	=> $data['data']['UserIdKey'],
			'cardName' 	=> $data['data']['userName'],
            'phone' 	=> $data['data']['phone'],
			'balance' 	=> '0.00',
		];
		Response::success($patient);
	}

	/**
	 * 电子居民健康卡查询
	 *
	 * @access 	public
	 * @return 	void
	 */
	public function cardByEleHealthCard()
	{
		$healthCard = $this->data['healthCard'];
		$hid 		= $this->hospitalId;
		$params 	= [
			'eleHealthCard' 	=> $healthCard,
		];
		$data 		= Server::ability('hospital')->eleHealthCard($hid , $params);
		if (!$data || $data['code'] != 10000) Response::message(30000);
		$response 	= $data['data']['response'];
		if ($response['appcode'] != 1) {
			Response::message(30000);
		}
		$data 		= explode('^' , $response['msg']['tsmsg']);
		$patient 	= [
			'cardId' 	=> $data[0],
			'cardName' 	=> $data[1],
		];
		Response::success($patient);
	}

    //登记号获取就诊卡信息
    public function getCardInfo(){
        $data['hospitalId'] = $this->data['hospitalId'];
        $data['uniqueId'] = $this->data['uniqueId'];
        $url = 'http://his.mobimedical.cn/index.php?g=Notify&m=FrontServerZL&a=getCardList';
        $header = [
            'Content-Type:application/x-www-form-urlencoded;charset=utf-8',
            'api-version:1',
        ];
        Curl::init();
        Curl::setUrl($url);
        Curl::setCustomRequest('post');
        $data && Curl::setParams(http_build_query($data));
        Curl::setHttpHeader($header);
        Curl::setOpt();
        $response 	= Curl::execute();
        error_log("response => " . $response);
        $cardInfo = json_decode($response , true);
        if (isset($cardInfo['data'][0])){
            $cardInfo['data'][0]['cardName'] = $cardInfo['data'][0]['name'];
            $cardInfo['data'][0]['UserIdKey'] = $cardInfo['data'][0]['uniqueId'];
        }else{
            Response::error(30000);
        }
        Response::success($cardInfo['data']);
    }

    /**
     * 身份证查询是否办卡
     */
    public function searchCard(){
        $idCardNo = $this->data['idCardNo'];
        $userName = $this->data['userName'];
        $hid 		= $this->hospitalId;
        $params 	= [
            'identyId' 	=> $idCardNo,
            'userName' 	=> $userName
        ];
        $data 		= Server::ability('hospital')->searchPhysicalCard($hid , $params);
        if (!$data || $data['code'] != 10000) Response::errorMessage($data['msg']);
        Response::success($data['data']);
    }

    /**
     * 直接办卡
     */
    public function addPhysicalCard(){
        $idCardNo = $this->data['idCardNo'];
        $userName = $this->data['userName'];
        $Address = $this->data['address'];
        $BirthDate = isset($this->data['birthDate']) ? $this->data['birthDate'] : '';
        $ContactPerson = isset($this->data['contactPerson']) ? $this->data['contactPerson'] : ''; // 联系人
        $IdentityType = isset($this->data['identityType']) ? $this->data['identityType'] : '';  // 证件类型
        $Job = $this->data['job']; //职业
        $Occupation = isset($this->data['occupation']) ? $this->data['occupation'] : '';  //
        $Sex = isset($this->data['sex']) ? $this->data['sex'] : '';
        $medicalCardNo = isset($this->data['medicalCardNo']) ? $this->data['medicalCardNo'] : '';
        $phone = $this->data['phone'];
        $password = isset($this->data['password']) ? $this->data['password'] : '';
        $nation = isset($this->data['nation']) ? $this->data['nation'] : '';
        $hid 		= $this->hospitalId;
        $params 	= [
            'identyId' 	=> $idCardNo,
            'name' 	=> $userName,
            'Address' 	=> $Address,
            'BirthDate' 	=> $BirthDate,
            'ContactPerson' 	=> $ContactPerson,
            'IdentityType' 	=> $IdentityType,
            'Job' 	=> $Job,
            'Occupation' 	=> $Occupation,
            'Sex' 	=> $Sex,
            'medicalCardNo' 	=> $medicalCardNo,
            'phone' 	=> $phone,
            'nation' 	=> $nation,
            'password' 	=> $password,
        ];
        $data 		= Server::ability('hospital')->addPhysicalCard($hid , $params);
        if (!$data || $data['code'] != 10000) Response::message(30000);
        $response 	= $data['data']['response'];
        if ($response['appcode'] != 1) {
            Response::message(30000);
        }
        $data 		= explode('^' , $response['msg']['tsmsg']);
        $patient 	= [
            'cardId' 	=> $data[0],
            'cardName' 	=> $data[1],
        ];
        Response::success($patient);
    }

    /**
     * 获取职业
     */
    public function getCareer(){
        $hid 		= $this->hospitalId;
        $params 	= [
        ];
        $data 		= Server::ability('hospital')->getCareer($hid , $params);
        if (!$data || $data['code'] != 10000) Response::message(30000);
        Response::success($data['data']);
    }

    /**
     * 病人ID查询报告
     */
    public function searchReport(){
        $idCardNo = $this->data['idCardNo'];
        $hid 		= $this->hospitalId;
        $params 	= [
            'idCardNo' 	=> $idCardNo,
        ];
        $data 		= Server::ability('hospital')->searchPhysicalCard($hid , $params);
        if (!$data || $data['code'] != 10000) Response::message(30000);
        $response 	= $data['data']['response'];
        if ($response['appcode'] != 1) {
            Response::message(30000);
        }
        $data 		= explode('^' , $response['msg']['tsmsg']);
        $patient 	= [
            'cardId' 	=> $data[0],
            'cardName' 	=> $data[1],
        ];
        Response::success($patient);
    }


}