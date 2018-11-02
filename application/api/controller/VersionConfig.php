<?php

namespace app\api\controller;


use app\component\response\Response;
use think\Controller;

class VersionConfig extends Controller
{
    public function getTesterList(){
        $testers = ['o2GLW5UqCtBbhy0PAF23hZnm7rvg',//魏建
//            'o2GLW5Y8qX_MLNf93EIQ1y-3eAww',//张明慧
        'o2GLW5XMOQ0EK-ru62x54acAjdu0',//叶平
            'o2GLW5WbwKRrmOoy0rDltwWX57Sw',//陈素珍
            'o2GLW5VqcEOKVU-9gk9bMaA9seY8',//杨磊
            ];
        $versionList = [
            ['name'=>'demo版本','apiUrl'=>'https://rytt.mobimedical.cn'],
            ['name'=>'测试版本','apiUrl'=>'https://auto-test.mobimedical.cn'],
            ['name'=>'正式版本','apiUrl'=>'https://auto-api.mobimedical.cn'],
            ['name'=>'准正式版本','apiUrl'=>'http://139.199.206.91:8051'],
            ['name'=>'正式版本','apiUrl'=>'http://auto-api.mobimedical.cn'],
        ];
        $response = ['testers'=>$testers,'versionList'=>$versionList];
        Response::success($response);
    }
}