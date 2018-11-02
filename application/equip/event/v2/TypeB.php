<?php

namespace app\equip\event\v2;

use think\Loader;
use app\equip\controller\Base;
use app\component\response\Response;
use app\component\server\Server;

class TypeB extends Base
{
//	protected $validate = '\app\equip\validate\v2\Registration'; 	//定义validate文件
//
//	protected $scene = [ 									//定义需要验证的方法
//		'date',
//		'doctorDetail',
//		'schedule',
//		'lock',
//		'registrationQuery',
//		'fetchReg',
//		'fetchRegOrder',
//		'fetchQuery',
//	];
//
//	protected $mustId  = [
//		'dept',
//		'date',
//		'doctorDetail',
//		'schedule',
//		'lock',
//		'fetchReg',
//		'fetchRegOrder',
//	];

	/**
	 * 获取医院数据
	 *
	 * @access 	public
	 * @return 	void
	 */
	public function getCheckCaution()
	{
	    $cardId = $this->getData('cardId',0);
        $params = [
            'cardId' 	=> $cardId,
        ];
        $hid = $this->hospitalId;
        $data 	= Server::ability('hospital')->getCheckCaution($hid , $params);
        if (!$data) { Response::message(10101); }
        if($data['code'] != 10000 && isset($data['msg'])){
            Response::message(10012,mb_substr($data['msg'],9,null,'utf8'));
        }
//        print_r($data);exit;
		Response::success($data['data']);
	}

    /**
     * 获取病人基本信息
     *
     * @access 	public
     * @return 	void
     */
    public function getPatientInfo()
    {
        $cardId = $this->getData('cardId');
        $params = [
            'cardId' 	=> $cardId,
        ];
        $hid = $this->hospitalId;
        $data 	= Server::ability('hospital')->getPatientInfo($hid , $params);
        if (!$data) { Response::message(10101); }
        if($data['code'] != 10000 && isset($data['msg'])){
            Response::message(10012,mb_substr($data['msg'],9,null,'utf8'));
        }
//        print_r($data);exit;
        Response::success($data['data']);
    }

    /**
     * 获取病人时间限制
     *
     * @access 	public
     * @return 	void
     */
    public function getCheckTimeSet()
    {
        $cardId = $this->getData('cardId');
        $params = [
            'cardId' 	=> $cardId,
        ];
        $hid = $this->hospitalId;
        $data 	= Server::ability('hospital')->getCheckTimeSet($hid , $params);
        if (!$data) { Response::message(10101); }
        if($data['code'] != 10000 && isset($data['msg'])){
            Response::message(10012,mb_substr($data['msg'],9,null,'utf8'));
        }
//        print_r($data);exit;
        Response::success($data['data']);
    }


    /**
     * 获取病人时间限制
     *
     * @access 	public
     * @return 	void
     */
    public function getCheckCalendar()
    {
        $patient_id= $this->getData('patient_id');
        $params = [
            'patient_id' 	=> $patient_id,
        ];
        $hid = $this->hospitalId;
        $data 	= Server::ability('hospital')->getCheckCalendar($hid , $params);
        if (!$data) { Response::message(10101); }
        if($data['code'] != 10000 && isset($data['msg'])){
            Response::message(10012,mb_substr($data['msg'],9,null,'utf8'));
        }
        if($data['data']){
            foreach ($data['data']['item'] as &$value){
                $value['date'] = $value['@attributes']['date'];
                $value['week'] = date("w" , strtotime($value['@attributes']['date']));
            }
        }
//        print_r($data);exit;
        Response::success($data['data']);
    }


    /**
     * 获取病人检查状态
     *
     * @access 	public
     * @return 	void
     */
    public function getPatientState()
    {
        $patient_id= $this->getData('patient_id');
        $params = [
            'patient_id' 	=> $patient_id,
        ];
        $hid = $this->hospitalId;
        $data 	= Server::ability('hospital')->getPatientState($hid , $params);
        if (!$data) { Response::message(10101); }
        if($data['code'] != 10000 && isset($data['msg'])){
            Response::message(10012,mb_substr($data['msg'],9,null,'utf8'));
        }
//        print_r($data);exit;
        Response::success($data['data']);
    }


    /**
     * 提交B超预约
     *
     * @access 	public
     * @return 	void
     */
    public function putConfirmCheck()
    {
        $patient_id= $this->getData('patient_id');
        $appdate = $this->getData('appdate');
        $apptime = $this->getData('apptime');
        $params = [
            'patient_id' 	=> $patient_id,
            'appdate' 	=> $appdate,
            'apptime' 	=> $apptime,
        ];
        $hid = $this->hospitalId;
        $data 	= Server::ability('hospital')->putConfirmCheck($hid , $params);
        if (!$data) { Response::message(10101); }
        if($data['code'] != 10000 && isset($data['msg'])){
            Response::message(10012,mb_substr($data['msg'],9,null,'utf8'));
        }
//        print_r($data);exit;
        Response::success($data['data']);
    }










}