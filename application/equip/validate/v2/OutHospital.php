<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/22 0022
 * Time: 16:59
 */

namespace app\equip\validate\v2;


use think\Validate;

class OutHospital extends Validate
{
    protected $rule 	= [
        'cardId' 	=> 'require|alphaNum',
        'admId' 	=> 'require',
    ];

    protected $message 	= [
        'cardId.require' 	=> 90005,
        'admId.require' => 90008,
    ];

    protected $scene 	= [
        'search' 	=> ['cardId'],
        'detail' 		=> ['admId'],
        'submit' 		=> ['admId'],
    ];
}