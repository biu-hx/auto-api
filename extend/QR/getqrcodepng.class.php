<?php
/**
 * @created by PhpStorm.
 * @author: wanghui
 * @since: 2017/3/17 0017 14:21
 */
namespace Lib\Pay\Wxpay\phpqrcode;
require_once dirname(__FILE__).'/phpqrcode.php';
class getqrcodepng{
     public static function s($request_url){
          error_reporting(E_ERROR);
          $url = urldecode($request_url);
          \QRcode::png($url,false,'',8,1,false);
     }
}