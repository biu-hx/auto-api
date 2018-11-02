<?php

namespace app\demo;

class Common
{

	public static function orderNumber($type)
	{

		$array 	= ['A' , 'B' , 'C' , 'D' , 'E' , 'F' , 'G'];
		$key 	= rand(0 , 6);
		return date('Ymd').str_pad($type , 2 , '0').$array[$key].str_pad(rand(0 , 999999) , 6 , '0');
	}




}