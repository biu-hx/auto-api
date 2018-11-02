<?php

namespace app\equip\event\v1;

use think\Loader;
use app\equip\controller\Base;
use app\component\response\Response;
use app\component\server\Server;

class Card extends Base
{

	protected $validate = '\app\equip\validate\v1\Card';

	protected $scene = [
		'card',
	];

	public function card()
	{
		$IDCard = $this->data['IDCard'];
		$hid 	= $this->data['hospitalId'];
		$data 	= Server::ability('hospital')->patientCard($hid , $IDCard);
		if (!$data || $data['code'] != 10000) Response::message(30000);
		$patient= [];
		$card 	= $data['data'];
		if (!isset($card[0])) {
			$patient[] 	= ['cardId' => $card['cardId'] , 'IDCard' => $IDCard , 'cardName' => $card['userName']];
		} else {
			foreach ($card as $v) {
				$patient[] 	= ['cardId' => $v['cardId'] , 'IDCard' => $IDCard , 'cardName' => $v['userName']];
			}
		}
		Response::message(10000 , $patient);
	}


}