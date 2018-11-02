<?php
// +----------------------------------------------------------------------
// | 登录
// +----------------------------------------------------------------------
// | Author: duanhy <shongyudxmas@163.com> 
// +----------------------------------------------------------------------
// | version: 1.0
// +----------------------------------------------------------------------

namespace app\equip\event\v2;

use think\Db;
use think\Loader;
use app\equip\controller\Base;
use app\component\response\Response;
use app\component\server\Server;

class Equip extends Base
{

	protected $validate = '\app\equip\validate\v2\Equip';

	protected $scene = [
		'number',
		'hardware',
		'network',
		'version',
		'paper',
	];

	/**
	 * 获取设备编号
	 *
	 * @access 	public
	 * @return 	void
	 */
	public function number()
	{
		$mac 	= $this->data['mac'];
		$data 	= Loader::model('Equip')->detailByMac($mac);
		if (!$data) Response::message(20001);
		$projectId 	= $data['project_id'];
		$project 	= Loader::model('Project')->detail($projectId);
		$bankArr 	= Loader::model('Bank')->detail($data['bank_id']);
		$projectService = explode(',' , $project['service_config']);
		$equipService 	= explode(',' , $data['service_config']);
		$number = ['number' => $data['code']];
		$number['projectName'] 		= $project['name'];
        $number['close_config'] 		= $data['close_config'];
		$number['projectName_en'] 	= $project['name_en'];
		if($project['type'] == 1){
            $number['hospital_id'] 	    = $project['hospital_config'];
        }else{
            $number['hospital_id'] 	    = 0;
        }
		$number['voiceVersion'] 	= $project['voice_version'];
		$number['logo'] 	   		= $project['logo'];
		$number['logoAll'] 			= $project['logo_all'];
		$number['bankName'] 	    = isset($bankArr['name']) ? $bankArr['name'] : '';
		$number['bankIcon'] 	    = isset($bankArr['icon']) ? $bankArr['icon'] : '';
		$number['bankFullIcon']     = isset($bankArr['full_icon']) ? $bankArr['full_icon'] : '';
		$number['status'] 	   		= 1;
		$serviceId 	= array_intersect($projectService , $equipService);
		$service 	= Loader::model('Service')->listById($serviceId);
		$item 		= $serviceConf = $pay = [];
        $serviceConf = Loader::model('Service')->getServiceTypeConf();

		foreach ($service as $v) {
			$item[] = [
				'id' 		=> $v['id'],
				'name' 		=> $v['name'],
				'name_en'  	=> $v['name_en'],
				'important' => $v['important'],
				'logo' 		=> $v['icon'],
			];
			$serviceConf['type'.$v['id']] = true; 
		}

        $pay =Loader::model('PayConf')->getPayConf();
        $projectService = explode(',' , $project['pay_config']);
        $equipService 	= explode(',' , $data['pay_config']);
        $pay_type_ids = array_intersect($projectService,$equipService);
		$pay_listByid =Loader::model('PayConf')->payTypeListByIds($pay_type_ids);
		$pay_item = [];
		foreach ($pay_listByid as $v){
		    $pay_item[] = $v;
		    $pay['type'.$v['id']] = true;
        }

		$number['item'] = $item;
        $number['pay_item'] = $pay_item;
		$number['pay'] 	= $pay;
		$number['service'] = $serviceConf;
        $number['serviceConf'] =  Loader::model('Service')->getServiceConf($projectId);
        $number['device_port'] = $data['device_port'];
        $number['servicePhone'] = $number['serviceConf']['server_number']['FuWuDianHua']['serverNumber'];
		Response::success($number);
	}

	/**
	 * 设置硬件相关信息
	 *
	 * @access 	public
	 * @return 	void
	 */
	public function hardware()
	{
		$mac 	= $this->data['mac'];
		$type 	= $this->data['hardware'];
		$value  = $this->data['value'];
		$data 	= Loader::model('Equip')->detailByMac($mac);
		if (!$data) Response::message(20001);
		$projectArr = Db::name("project")->where("id={$data['project_id']}")->find();
        $equipId = $data['id'];
		if($value == 20001){
            $fault[$type] = $value;
            $where['fault_type'] = json_encode($fault);
            $where['equipment_id'] = $equipId;
            $where['status'] = 0;
            $faultArr = Db::name("monitor_fault")->where($where)->find();
            if(!$faultArr){
                $monitor['fault_type'] = json_encode($fault);
                $monitor['create_ts'] = date("Y-m-d H:i:s" , time());
                $monitor['equipment_id'] = $equipId;
                $monitor['project_id'] = $data['project_id'];
                $monitor['status'] = 0;
                $monitor['task'] = 0;
                Db::name("monitor_fault")->insert($monitor);
                /*$address = $data['address'] . '| 设备编码 : ' .$data['code'];
                $content = $this->content($type , $address , $projectArr['name']);
                $openidData = Loader::model('Bank')->detail($data['bank_id']);
                if (!$openidData) Response::message(20001);
                $openidArr = explode(',' , $openidData['server_openid']);
                foreach ($openidArr as $openId){
                    $contentArr = array('Request'=>array("openId" => $openId, "title" => $content['title'], "msg" => $content['msg'], "linkUrl" => "", "remark" => ''));
                    $contentXml = Response::ToXml($contentArr);
                    Server::ability('hospital')->sendMsg(10000 , $contentXml);
                }*/
            }
        }

        if($type == 1 && $value == 20003){
            $fault[$type] = $value;
            $where['fault_type'] = json_encode($fault);
            $where['equipment_id'] = $equipId;
            $where['status'] = 0;
            $faultArr = Db::name("monitor_fault")->where($where)->find();
            if(!$faultArr){
                $monitor['fault_type'] = json_encode($fault);
                $monitor['create_ts'] = date("Y-m-d H:i:s" , time());
                $monitor['equipment_id'] = $equipId;
                $monitor['project_id'] = $data['project_id'];
                $monitor['status'] = 0;
                $monitor['task'] = 0;
                Db::name("monitor_fault")->insert($monitor);
               /* $content['title'] = $projectArr['name'] .'-设备缺纸';
                $content['msg'] = '设备缺纸，请及时更换打印纸。' . $data['address'] .'| 设备编码: ' . $data['code'];
                $openidData = Loader::model('Bank')->detail($data['bank_id']);
                if ($openidData){
                    $openidArr = explode(',' , $openidData['server_openid']);
                    foreach ($openidArr as $openId){
                        $contentArr = array('Request'=>array("openId" => $openId, "title" => $content['title'], "msg" => $content['msg'], "linkUrl" => "", "remark" => ''));
                        $contentXml = Response::ToXml($contentArr);
                        Server::ability('hospital')->sendMsg(10000 , $contentXml);
                    }
                }*/
            }
        }

		$result = Loader::model('Equip')->setHardware($equipId , $type , $value );
		Response::success();
	}

	/**
	 * 设置网络环境
	 *
	 * @access 	public
	 * @return 	void
	 */
	public function network()
	{
		$mac 	= $this->data['mac'];
		$data 	= Loader::model('Equip')->detailByMac($mac);
		if (!$data) Response::message(20001);
		$equipId 	= $data['id'];
		$result = Loader::model('Equip')->setNetwork($equipId);
		Response::success($data['web_status']);
	}
	
	/**
	 * 设置版本号
	 *
	 * @access 	public
	 * @return 	void
	 */
	public function version()
	{
		$mac 	= $this->data['mac'];
		$version= $this->data['version'];
		$data 	= Loader::model('Equip')->detailByMac($mac);
		if (!$data) Response::message(20001);
		$equipId 	= $data['id'];
		$result = Loader::model('Equip')->setVersion($equipId , $version);
		Response::success();
	}

	/**
	 * 设置打印纸问题
	 *
	 * @access 	public
	 * @return 	void
	 */
	public function paper()
	{
		$mac 	= $this->data['mac'];
		$type 	= $this->data['hardware'];
		$value  = $this->data['value'];
		$data 	= Loader::model('Equip')->detailByMac($mac);
		if (!$data) Response::message(20001);
		$equipId 	= $data['id'];
		if($value == 20003){
            $content['title'] = '设备缺纸';
            $content['msg'] = '设备缺纸，请及时更换打印纸。' . $data['address'] .'| 设备编码: ' . $data['code'];
            $openidData = Loader::model('Bank')->detail($data['bank_id']);
            if ($openidData){
                $openidArr = explode(',' , $openidData['server_openid']);
                foreach ($openidArr as $openId){
                    $contentArr = array('Request'=>array("openId" => $openId, "title" => $content['title'], "msg" => $content['msg'], "linkUrl" => "", "remark" => ''));
                    $contentXml = Response::ToXml($contentArr);
                    Server::ability('hospital')->sendMsg(10000 , $contentXml);
                }
            }
        }
		$result = Loader::model('Equip')->setPaper($equipId , $type , $value);
		Response::success();
	}

    /**
     * 获取当前设备状态
     *
     * @access 	public
     * @return 	json
     */
    public function equiplist()
    {
        $equipList =  Db::name('equipment')->field("hardware,web_status,code")->select();
        $htmlStr = '<table border="0"><tr><td style="width: 20%;">编号</td><td style="width: 20%;">页面状态</td><td style="width: 20%;">当前状态</td><td style="width: 20%;">操作</td></tr>';
        if($equipList){
            foreach ($equipList as $equip){
                if($equip['hardware']){
                    $hardware = json_decode($equip['hardware'] , true);
                    if(isset($hardware[9])){
                        $webStatus = $hardware[9] != 1000 ? "页面卡死" : "页面正常";
                    }else{
                        $webStatus = "页面正常";
                    }
                }else{
                    $webStatus = "页面正常";
                }
                $code = $equip['code'];
                $status = $equip['web_status'] ? '未处理' : '已处理';
                $htmlStr .= '<tr><td>'. $code .'</td><td> '.$webStatus .'</td><td> '.$status .'</td><td ><a href="/equip/restart?code='.$code .'">重启</a></td></tr>';
            }

        }
        $htmlStr .= '</table>';
        echo html_entity_decode($htmlStr);
        //$this->assign('list' , $equipList);
        //$this->display();
    }

    /**
     * 设置重启页面
     *
     * @access 	public
     * @return 	void
     */
    public function restart()
    {
        $code 	= $this->data['code'];
        $where['code'] = $code;
        $update['web_status'] = 1;
        $result = Db::name('equipment')->where($where)->update($update);
        if($result){
            $htmlStr = 'success';
        }else{
            $htmlStr =  'error';
        }
        $htmlStr .= '        <a href="/equip/equiplist">点击跳回列表</a>';
        echo $htmlStr;
    }

    /**
     * 设置页面已经重启
     *
     * @access 	public
     * @return 	void
     */
    public function reset()
    {
        $mac 	= $this->data['mac'];
        $where['mac'] = $mac;
        $data 	= Loader::model('Equip')->detailByMac($mac);
        if (!$data) Response::message(20001);
        $update['web_status'] = 0;
        Db::name('equipment')->where($where)->update($update);
        Response::success();
    }

    /**
     * @param $hardware
     * @param $address
     */
    protected function content($hardware , $address , $projectName){
        $content = ['title'=> $projectName . ' -设备异常' , 'msg' => '设备异常请及时处理。 '.$address];
        switch ($hardware) {
            case 1:
                $content['title'] = $projectName . '-凭条打印异常';
                break;
            case 2:
                $content['title'] = $projectName . '-密码键盘异常';
                break;
            case 3:
                $content['title'] = $projectName . '-诊疗卡读卡取器异常';
                break;
            case 4:
                $content['title'] = $projectName . '-银联读卡器异常';
                break;
            case 5:
                $content['title'] = $projectName . '-报告单打印机异常';
                break;
            case 6:
                $content['title'] = $projectName . '-条码扫描器异常';
                break;
            case 7:
                $content['title'] = $projectName . '-摄像头异常';
                break;
            case 8:
                $content['title'] = $projectName . '-高拍仪异常';
                break;
            case 9:
                $content['title'] = $projectName . '-系统页面异常';
                break;
        }
        return $content;
    }

    /**
     * 语音转文字
     */
    public function stringToVoice(){
        $string = $this->getData("string");
        $stringArr = json_decode($string,true);
        if(!$stringArr||count($string)==0){
            Response::errorMessage("数据必须为json");
        }
        Loader::import('XFvoice.XFVoice');
        $voiceFIle = [];
        foreach ($stringArr as $v){
            $voiceFIle[] = \XFVoice::serachVoice($v);
        }
        Response::success($voiceFIle);
    }

    /**
     * 语音列表
     */
    public function voiceList(){
        $voiceList = Db::name("ai_voice")->select();
        $dataList = array_column($voiceList , 'voice_url' , 'key');
        Response::success($dataList);
    }

}
