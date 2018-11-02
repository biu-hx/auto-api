<?php

namespace app\equip\event\v2;

use think\Loader;
use app\equip\controller\Base;
use app\component\response\Response;
use app\component\server\Server;

class ListSearch extends Base
{

	protected $validate = '\app\equip\validate\v2\ListSearch'; 		//定义validate文件

	protected $scene = [ 											//定义需要验证的方法
		'drug',
		'diagnosis',
	];


	protected $mustId  = [
		'drug',
		'diagnosis'
	];

	/** 
	 * 药品查询
	 *
	 * @access 	public
	 * @return 	void
	 */
	public function drug()
	{
		$hid 	= $this->hospitalId;
		$search = $this->data['search'];
		$params = [
			'search' 	=> $search,
		];
		$data 	= Server::ability('hospital')->searchDrug($hid , $params);
		if (!$data) { Response::message(10101); }
		$data['code'] != 10000 && Response::message(10102); 
		$data 	= $data['data'];
		$drug = [];
		if (!$data) {
			Response::success($drug);
		}
		$data 	= $data['OrdPriceList'];
		if (!$data[0]) {
			$drug = [
				[
					'number' 	=> $data['Number'] ? $data['Number'] : '',
					'ordName' 	=> $data['OrdName'] ? $data['OrdName'] : '',
					'ordPrice' 	=> $data['OrdPrice'] ? $data['OrdPrice'] : '',
					'ordQty' 	=> $data['OrdQty'] ? $data['OrdQty'] : '',
					'ordAmt' 	=> $data['OrdAmt'] ? $data['OrdAmt'] : '',
					'ordInsur' 	=> $data['OrdInsur']  ? $data['OrdInsur'] : '-',
					'ordFeeCat' => $data['OrdFeeCat'] ? $data['OrdFeeCat'] : '',
				]
			];
		} else {
			foreach ($data as $v) {
				$drug[] = [
					'number' 	=> $v['Number'] ? $v['Number'] : '',
					'ordName' 	=> $v['OrdName'] ? $v['OrdName'] : '',
					'ordPrice' 	=> $v['OrdPrice'] ? $v['OrdPrice'] : '',
					'ordQty' 	=> $v['OrdQty'] ? $v['OrdQty'] : '',
					'ordAmt' 	=> $v['OrdAmt'] ? $v['OrdAmt'] : '',
					'ordInsur' 	=> $v['OrdInsur'] ? $v['OrdInsur'] : '-',
					'ordFeeCat' => $v['OrdFeeCat'] ? $v['OrdName'] : '',
				];
			}
		}
		Response::success($drug);
	}

	/** 
	 * 诊疗项目查询
	 *
	 * @access 	public
	 * @return 	void
	 */
	public function diagnosis()
	{
		$hid 	= $this->hospitalId;
		$search = $this->data['search'];
		$params = [
			'search' 	=> $search,
		];
		$data 	= Server::ability('hospital')->searchDiagnosis($hid , $params);
		if (!$data) { Response::message(10101); }
		$data['code'] != 10000 && Response::message(10102); 
		$data 	= $data['data'];
		$diagnosis = [];
		if (!$data) {
			Response::success($diagnosis);
		}
		$data 	= $data['INCIPriceList'];
		if (!$data[0]) {
			$diagnosis = [
				[
					'inciDesc' 	=> $data['InciDesc'] ? $data['InciDesc'] : '',
					'inciPrice' => $data['InciPrice'] ? $data['InciPrice'] : '',
					'incCat' 	=> $data['IncCat'] ? $data['IncCat'] : '',
					'incUom' 	=> $data['IncUom'] ? $data['IncUom'] : '',
					'incManf' 	=> $data['IncManf'] ? $data['IncManf'] : '',
				]
			];
		} else {
			foreach ($data as $v) {
				$diagnosis[] = [
					'inciDesc' 	=> $v['InciDesc'] ? $v['InciDesc'] : '',
					'inciPrice' => $v['InciPrice'] ? $v['InciPrice'] : '',
					'incCat' 	=> $v['IncCat'] ? $v['IncCat'] : '',
					'incUom' 	=> $v['IncUom'] ? $v['IncUom'] : '',
					'incManf' 	=> $v['IncManf'] ? $v['IncManf'] : '',
				];
			}
		}
		Response::success($diagnosis);
	}



}