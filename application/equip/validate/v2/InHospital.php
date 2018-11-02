<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/22 0022
 * Time: 16:59
 */

namespace app\equip\validate\v2;


use think\Validate;

class InHospital extends Validate
{
    protected $rule 	= [
        'cardId' 	=> 'require|alphaNum',
        'content' 	=> 'require',
        'phone' 	=> 'require|regex:^1\d{10}$',
        'code' 	=> 'require',
    ];

    protected $message 	= [
        'cardId.require' 	=> 90005,
        'admId.require' => 90008,
        'content.require' => 90028,
        'phone.require' => 90029,
    ];

    protected $scene 	= [
        'searchInHospital' 	=> ['cardId'],
        'submitInHospital' 		=> ['content'],
        'sendCode' 		=> ['phone'],
        'checkCode' 		=> ['phone','code'],
    ];
}