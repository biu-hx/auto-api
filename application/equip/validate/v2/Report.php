<?php

namespace app\equip\validate\v2;

use think\Validate;

class Report extends Validate
{
	protected $rule 	= [
		'cardId' 		=> 'require|alphaNum',	
		'inspecNo' 		=> 'require|alphaNum',
		'printCode' 	=> 'require|alphaNum',
		'reportType' 	=> 'in:1,2'
	];

	protected $message 	= [
		'cardId.require' 	=> 90005,
		'inspecNo.require'  => 90021,
		'printCode.require' => 90023,
		'cardId.alphaNum' 	=> 91005,
		'inspecNo.alphaNum' => 91021,
		'reportType.in' 	=> 91022,
		'printCode.alphaNum'=> 91023,
	];

	protected $scene 	= [
		'reportList' 		=> ['cardId'],
		'reportDetail' 		=> ['cardId' , 'inspecNo' , 'reportType'],
		'reportPrint' 		=> ['cardId' , 'inspecNo' , 'reportType'],
		'barCode' 			=> ['printCode'],
		'barCodePrint' 		=> ['printCode'],
	];

	



}