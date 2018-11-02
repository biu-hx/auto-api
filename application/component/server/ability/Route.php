<?php

namespace app\component\server\ability;

class Route
{	
	
	
	static $route 	= [
		10000 		=> [
			'barCode' 				=> true,
			'markBarCodePrint'  	=> true,
			'searchDrug' 			=> true,
			'searchDiagnosis' 		=> true,
			'searchInpatient' 		=> true,
			'searchInpatientType' 	=> true,
			'searchInpatientDetail' => true,
			'eleHealthCard' 		=> true,
            'searchOutHospitalInfo' => true,
            'searchOutHospitalList' => true,
            'submitLeaveHospital'   => true,
            'searchInHospital'      => true,
            'getSelectList'         => true,
            'submitInHospital'      => true,
            'sendMsg'               => true,
            'sendCode'              => true,
            'checkCode'             => true,
            'getCheckCaution'             => true,
            'getPatientInfo'             => true,


		],
	];
		
	/**
	 * 访问规则
	 *
	 * @static 
	 * @access 	public
	 * @param 	int 	$hosId 		医院ID
	 * @param 	string 	$method 	路由方法
	 */
	public static function rule($hosId , $method)
	{
		if (!isset(self::$route[$hosId])) return false;
		$rule 	= self::$route[$hosId];
		return isset($rule[$method]) ? $rule[$method] : false;
	} 
	

}
