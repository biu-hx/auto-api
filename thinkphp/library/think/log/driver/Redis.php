<?php
// +----------------------------------------------------------------------
// | TOPThink [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://topthink.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

namespace think\log\driver;

use think\App;

class Redis {

    protected $config  =   array(
        'APP_ID' =>  '327425',
    );

    // 实例化链接上redis
    function __construct() {
    		$logRedis 	= \think\Config::get('log_redis');
    		$this->config['APP_ID'] = $logRedis['app_id'];
            $this->redis= new \Redis();
            $this->redis->connect($logRedis['host'],$logRedis['port']); 
            $auth     	= $logRedis['auth'];
            if($auth) { $this->redis->auth($auth); }
            $this->redis->select(6);
    }

    /**
     * 日志写入接口
     * @access public
     * @param string $log 日志信息
     * @param string $destination  写入目标
     * @return void
     */
    public function save($log , $destination='') {
        foreach ($log as $key => $val){
            if($key == 'sql'){ continue;}
            $data = [
                'app_id' => $this->config['APP_ID'],
                'date' 	 => time(),
                'type' 	 => $key,
                'text'   => json_encode($val, JSON_UNESCAPED_UNICODE)
            ];
            $this->redis->rpush('yh_apply_log',json_encode($data, JSON_UNESCAPED_UNICODE));
        }
        return true;
    }
}
