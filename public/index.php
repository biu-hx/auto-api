<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// [ 应用入口文件 ]
header('Content-type:text/json');
header('Access-Control-Allow-Origin:*');     
header('Access-Control-Allow-Methods:"PUT,POST,GET,OPTIONS,PATCH,DELETE"');       
header('Access-Control-Allow-Headers:api-version,number,nonce,timestamp,signature');  
// 定义应用目录
define('APP_PATH', __DIR__ . '/../application/');
define('VIDEO_PATH', __DIR__ . '/static/video/');
// 加载框架引导文件
require __DIR__ . '/../thinkphp/start.php';
