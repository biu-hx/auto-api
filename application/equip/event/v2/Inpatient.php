<?php

namespace app\equip\event\v2;

use think\Loader;
use app\equip\controller\Base;
use app\component\response\Response;
use app\component\server\Server;

class Inpatient extends Base
{


    protected $validate = '\app\equip\validate\v2\Inpatient'; 		//定义validate文件

    protected $scene = [ 											//定义需要验证的方法
        //'inpatientList',
        'inpatintType',
        //'inpatientDetail',
    ];

    protected $mustId  = [
       // 'inpatientList',
        'inpatintType',
        //'inpatientDetail',
    ];


    /**
     * 住院清单详情
     *
     * @access 	public
     * @return 	void
     */
    public function inpatientList()
    {
        $hid = $this->hospitalId;
        if (in_array($hid, [61759])) {
            $params['inpatientId'] = isset($this->data['inpatientId']) ? $this->data['inpatientId'] : $this->data['cardId'];
            $data = Server::ability('hospital')->searchInpatientCloud($hid, $params);
            $data = $data['data'];
        } else {
            $cardId = $this->data['cardId'];
            $uniqueId = isset($this->data['uniqueId']) ? $this->data['uniqueId'] : '';
            $params = [
                'cardNo' => $cardId,
                'uniqueId' => $uniqueId,
            ];
            if ($hid == 10000) {
                $data = Server::ability('hospital')->searchInpatient($hid, $params);
                if (!$data) {
                    Response::message(10102);
                }
                if (!$data || $data['code'] != 10000) Response::message(30000);
                $data = isset($data['data']['AdmList']['AdmListInfo']) ? $data['data']['AdmList']['AdmListInfo'] : array();
            } else {
                $data = Server::ability('hospital')->searchInpatientCloud($hid, $params);
                $data = $data['data'];
            }
        }
            $inpatient = [];
            if ($data) {
                if (isset($data[0])) {
                    foreach ($data as $v) {
                        $inpatient[] = [
                            'admId' => isset($v['AdmId']) ? $v['AdmId'] : $v['treat_no'],
                            'dateFrom' => isset($v['DateFrom']) ? $v['DateFrom'] : $v['inhospital_date'],
                            'dateTo' => isset($v['DateTo']) ? $v['DateTo'] : '--',
                            'totalSum' => isset($v['TotalSum']) ? $v['TotalSum'] : $v['total_fee'],
                            'payedFlag' => isset($v['PayedFlag']) ? $v['PayedFlag'] : '',
                            'arpbl' => isset($v['Arpbl']) ? $v['Arpbl'] : '--',
                            'admtype' => isset($v['Admtype']) ? $v['Admtype'] : $v['dept_name'],
                            'patName' => isset($v['PatName']) ? $v['PatName'] : $v['patient_name'],
                        ];
                    }

                } else {
                    $inpatient = [
                        [
                            'admId' => isset($data['AdmId']) ? $data['AdmId'] : $data['treat_no'],
                            'dateFrom' => isset($data['DateFrom']) ? $data['DateFrom'] : $data['inhospital_date'],
                            'dateTo' => isset($data['DateTo']) ? $data['DateTo'] : '--',
                            'totalSum' => isset($data['TotalSum']) ? $data['TotalSum'] : $data['total_fee'],
                            'payedFlag' => isset($data['PayedFlag']) ? $data['PayedFlag'] : '',
                            'arpbl' => isset($data['Arpbl']) ? $data['Arpbl'] : '--',
                            'admtype' => isset($data['Admtype']) ? $data['Admtype'] : $data['dept_name'],
                            'patName' => isset($data['PatName']) ? $data['PatName'] : $data['patient_name'],
                        ]
                    ];
                }
            }
            Response::success($inpatient);
        }

    /**
     * 住院清单类型
     *
     * @access 	public
     * @return 	void
     */
    public function inpatientType()
    {
        $hid 	= $this->hospitalId;
        $admId  = $this->data['admId'];
        $arpbl  = $this->data['arpbl'];
        $params 	= [
            'arpbl' 	=> $admId,
            'admId' 	=> $arpbl,
        ];
        $data 	= Server::ability('hospital')->searchInpatientType($hid , $params);
        if (!$data || $data['code'] != 10000) Response::message(30000);
        if (!$data) {
            Response::message(10102);
        }
        $data 	= $data['data'];
        $inpatient = ['info' => [
            'dateFrom' 	=> $data['AdmDate'],
            'dateTo' 	=> $data['DateTo'],
            'dept' 		=> $data['LocDesc'],
            'bedDesc' 	=> $data['BedDesc'],
            'treatNo' 	=> $data['RegNo'],
        ], 'item' => ''];
        $data 	   = $data['ArcicList']['FetchPatCatAmtOutInfo'];
        if (isset($data[0])) {
            foreach ($data as $v) {
                $inpatient['item'][] = [
                    'arcicDesc' => $v['ArcicDesc'],
                    'sumFee' 	=> $v['SumFee'],
                ];
            }

        } else {
            $inpatient['item'] = [
                [
                    'arcicDesc' => $data['ArcicDesc'],
                    'sumFee' 	=> $data['SumFee'],
                ]
            ];
        }
        Response::success($inpatient);
    }

    /**
     * 住院清单详情
     *
     * @access 	public
     * @return 	void
     */
    public function inpatientDetail()
    {
        $hid 	= $this->hospitalId;
        if(in_array($hid , [61759])){
            $params['inpatientId'] = isset($this->data['cardId']) ? $this->data['cardId'] : $this->data['inpatientId'];
            $startTime = isset($this->data['startTime']) ? $this->data['startTime'] : '';
            $params['beginTime'] = $startTime;
            $data 	= Server::ability('hospital')->searchInpatientDetailCloud($hid , $params);
            $data = $data['data'];
        }else{
            $arpbl  = isset($this->data['arpbl']) ? $this->data['arpbl'] : '';
            $cardId = isset($this->data['cardId']) ? $this->data['cardId'] : '';
            $startTime = isset($this->data['startTime']) ? $this->data['startTime'] : '';
            $uniqueId = isset($this->data['uniqueId']) ? $this->data['uniqueId'] : '';
            $inpatientId = isset($this->data['inpatientId']) ? $this->data['inpatientId'] : '';
            if($hid == 61757){
                $startTime = date("Y-m-d" , strtotime($startTime));
            }
            $params 	= [
                'arpbl' => $arpbl,
                'cardNo' => $cardId,
                'uniqueId' => $uniqueId,
                'beginTime' => $startTime,
                'inpatientId' => $inpatientId,
            ];
            if($hid == 10000){
                $data 	= Server::ability('hospital')->searchInpatientDetail($hid , $params);
                if (!$data || $data['code'] != 10000) Response::message(30000);
                if (!$data) {
                    Response::message(10102);
                }
                $data 	= $data['data']['AmtDetailList']['OutFymxList'];
            }else{
                $data 	= Server::ability('hospital')->searchInpatientDetailCloud($hid , $params);
                $data = $data['data'];
            }
        }
        $inpatient = [];
        if($data){
            if (isset($data[0])) {
                foreach ($data as $v) {
                    $inpatient[] 	= [
                        'date' 	=> isset($v['OrdDate']) ? $v['OrdDate'] : (isset($v['OperDateTime']) ? $v['OperDateTime'] : date('Y-m-d')),
                        'name' 	=> isset($v['OrdName']) ? $v['OrdName'] : $v['ItemName'],
                        'uom' 	=> isset($v['OrdUom']) ? $v['OrdUom'] : $v['Specs'],
                        'qty' 	=> isset($v['OrdQty']) ? $v['OrdQty'] : $v['Num'],
                        'price' => isset($v['OrdPrice']) ? $v['OrdPrice'] : $v['Price'],
                        'yb' 	=> isset($v['YBCAT']) ? $v['YBCAT'] : '',
                        'cat' 	=> isset($v['OrdCat']) ? $v['OrdCat'] : '--',
                        'amt' 	=> isset($v['OrdAmt']) ? $v['OrdAmt'] : $v['Cost'],
                    ];
                }
            } else {
                $inpatient 	= [
                    [
                        'date' 	=> isset($data['OrdDate']) ? $data['OrdDate'] : (isset($data['OperDateTime']) ? $data['OperDateTime'] : date('Y-m-d')),
                        'name' 	=> isset($data['OrdName']) ? $data['OrdName'] : $data['ItemName'],
                        'uom' 	=> isset($data['OrdUom']) ? $data['OrdUom'] : $data['Specs'],
                        'qty' 	=> isset($data['OrdQty']) ? $data['OrdQty'] : $data['Num'],
                        'price' => isset($data['OrdPrice']) ? $data['OrdPrice'] : $data['Price'],
                        'yb' 	=> isset($data['YBCAT']) ? $data['YBCAT'] : '',
                        'cat' 	=> isset($data['OrdCat']) ? $data['OrdCat'] : '--',
                        'amt' 	=> isset($data['OrdAmt']) ? $data['OrdAmt'] : $data['Cost'],
                    ]
                ];
            }
        }
        Response::success($inpatient);
    }



}