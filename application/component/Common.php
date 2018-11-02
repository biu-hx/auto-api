<?php

namespace app\component;

use think\Db;
use think\Loader;
use app\component\Log;

class Common
{

	static $string 	= ['A' , 'B' , 'C' , 'D' , 'E' , 'F' , 'G' , 'H' , 'I' , 'J'];

	/**
	 * 生成新的jwt
	 *
	 * @access 	public
	 * @static
	 * @param 	int 	$doctorId 	医生id
	 * @return 	string
	 */
	public static function jwt($doctorId)
	{
		return md5('jwt'.$doctorId.self::$string[rand(0 , 9)].uniqid().microtime());
	}

	/**
	 * 生成新的code
	 *
	 * @access 	public
	 * @static
	 * @return 	string
	 */
	public static function code()
	{
		return rand(1000 , 9999);
	}

	/**
	 * 生成新的token
	 *
	 * @access 	public
	 * @static
	 * @return 	string
	 */
	public static function token($doctorId)
	{
		return md5($doctorId.self::$string[rand(0 , 9)].uniqid().microtime());
	}

	/**
	 * 验证用户的密码
	 *
	 * @access 	public
	 * @static
	 * @param 	int 	$doctorId 	医生id
	 * @param 	string 	$password 	密码
	 * @param 	string 	$salt 		加密的盐
	 * @return 	string
	 */
	public static function password($doctorId , $password , $salt)
	{
		return md5($doctorId.$salt.$password);
	}


	/**
	 * 生成二维码图片
	 *
	 * @static
	 * @access 	public
	 * @param 	string 	$string 	需要生成二维码的内容
	 * @return 	string 				返回图片路径
	 */
	public static function qr($string , $logo = '')
	{
		Loader::import('QR.phpqrcode');
		$name 	= md5($string.uniqid()).'.png';
		$file 	= '../upload/qr/'.$name;
		$result = \QRcode::png($string , $file , '' , 20 , 1 , false);
		if (!$logo) { return $file;}
		$logo 	= "../upload/logo/".$logo;
		$QR = imagecreatefromstring(file_get_contents($file)); 
		$logo = imagecreatefromstring(file_get_contents($logo)); 
		if (imageistruecolor($logo))
        {
            imagetruecolortopalette($logo, false, 65535);//添加这行代码来解决颜色失真问题
        }
		$QR_width = imagesx($QR);//二维码图片宽度 
		$QR_height = imagesy($QR);//二维码图片高度 
		$logo_width = imagesx($logo);//logo图片宽度 
		$logo_height = imagesy($logo);//logo图片高度 
		$logo_qr_width = $QR_width / 5; 
		$scale = $logo_width/$logo_qr_width; 
		$logo_qr_height = $logo_height/$scale; 
		$from_width = ($QR_width - $logo_qr_width) / 2; 
		//重新组合图片并调整大小 
		imagecopyresampled($QR, $logo, $from_width, $from_width, 0, 0, $logo_qr_width, 
		$logo_qr_height, $logo_width, $logo_height); 
		
		//输出图片 
		imagepng($QR, $file); 
		return $file; 
	}

	/**
	 * 上传至腾讯云存储
	 *
	 * @static
	 * @access 	public
	 * @param 	string 	$file  		图片路径
	 * @param  	string 	$folder 	文件夹
	 * @return 	string
	 */
	public static function TencentCloud($file , $folder = 'source')
	{
		import('QCloud.qcloud');
		\qcloudcos\Cosapi::setTimeout(180);
		$bucketName = 'auto';
		$bizAttr 	= "";
    	$insertOnly = 0;
   	 	$sliceSize 	= 3 * 1024 * 1024;
   	 	$srcPath 	= $file;
   	 	$dstPath 	= '/'.$folder.'/'.md5($file).'.png';
		$data 	= \qcloudcos\Cosapi::upload($bucketName, $srcPath, $dstPath,$bizAttr,$sliceSize, $insertOnly);
		unlink($file);
		return $data['code'] == 0 ? $data['data']['source_url'] : false;
	}


	 /**
     * 签名生成
     * 
     * @static 
     * @access 	public
     * @param 	string 	$appsecret	签名secret 
     * @param 	array 	$params 	签名数组
     * @return 	string
     */
	public static function signature($appsecret , $params)
	{
		$signature = '';
		ksort($params);
		foreach ($params as $key => $value) {
			if ($key == 'signature') { continue; }
			$signature .= $key.'='.$value;
		}
		return md5($signature.$appsecret);
	}

    /**
     * 同步挂号,门诊,住院订单给辉哥
     *
     * @param $orderId  订单ID
     * @param $syncType 同步类型 1 新增 2 更新
     * @param $type 订单类型 2 挂号 3 取号 4 门诊 5 住院
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
	public static function syncOrder($orderId , $syncType , $type){
        $orderObj = Db::name("order")
            ->alias("o")
            ->field("o.hospital_id,o.type,o.status,p.price,p.transaction_id,p.openid,a.card_id,a.card_name,a.business_info,a.success_info")
            ->join("order_pay p" , "p.order_id=o.id");
        switch ($type){
            case 2:
                $orderObj->join("order_registration a" , "a.order_id=o.id");
                $orderType = 1;
                break;
            case 3:
                $orderObj->join("order_fetch a" , "a.order_id=o.id");
                $orderType = 1;
                break;
            case 4:
                $orderObj->join("order_outpatient a" , "a.order_id=o.id");
                $orderType = 2;
                //$syncOrder['recipeId'] = $orderArr['hospital_id'];   //门诊缴费处方号 暂无
                break;
            case 5:
                $orderObj->join("order_inpatient a" , "a.order_id=o.id");
                $orderType = 3;
                break;
        }
        $orderArr = $orderObj->where("o.id={$orderId}")->find();
        if(!$orderArr){
            //没有订单
            return false;
        }
        if($type == 2 && $orderArr['status'] != 1){
            if($orderArr['success_info'] != '[]'){
                $syncOrder['isCancelReg'] = 1;
            }
        }
        $syncOrder['hosId'] = $orderArr['hospital_id'];
        $syncOrder['syncType'] = $syncType;
        $syncOrder['type'] = $orderType;
        $syncOrder['state'] = $orderArr['status'];
        $syncOrder['totalFee'] = $orderArr['price'] * 100;
        $syncOrder['transactionId'] = $orderArr['transaction_id'];
        $syncOrder['userData'] = json_encode(['openId' => $orderArr['openid'] , 'userName' => $orderArr['card_name'] , 'cardId' => $orderArr['card_id']]);
        $syncOrder['businessData'] = $orderArr['business_info'];
        $syncOrder['frontData'] = $orderArr['success_info'];
        ksort($syncOrder);
        $signStr = '';
        foreach ($syncOrder as $key=>$value){
            if($value == ''){
                continue;
            }
            $signStr .= $key . '=' .$value ;
        }
        $syncOrder['sign'] = sha1($signStr . 'AUTO_MACHINE');
        $options = array(
            CURLOPT_RETURNTRANSFER =>true,
            CURLOPT_HEADER =>false,
            CURLOPT_POST =>true,
            CURLOPT_POSTFIELDS => $syncOrder,
        );
        Log::storageRequest('syncOrderLog' , $options);
        Log::storageRoute('syncOrderLog' , config("sync_order_url"));
        Log::writeLog('syncOrderLog');
        $ch = curl_init(config("sync_order_url"));
        curl_setopt_array($ch, $options);
        $result = curl_exec($ch);
        curl_close($ch);
        if(json_decode($result , true)['code'] == 200){
            return true;
        }else{
            return false;
        }
    }

}