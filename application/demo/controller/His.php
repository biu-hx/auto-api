<?php

namespace app\demo\controller;

use app\demo\Common;
use app\component\server\Server;

class His
{


	public function index()
	{	
		$transactionId 	= $_GET['transaction_id'];
		$orderNum 	= $_GET['out_trade_no'];
		$push 		= [
			'transaction_id' => $transactionId,
			'order_number' 	 => $orderNum, 
		];
		Server::cache('redis')->push('execBu' , json_encode($push));
		echo 'true';
	}

}