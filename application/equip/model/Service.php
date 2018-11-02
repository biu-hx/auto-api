<?php
namespace app\equip\model;

use think\Db;

class Service 
{

    protected static $ServiceConf = [];
	/**
	 * 
	 *
	 *
	 *
	 */
 	public function detail($id)
 	{
 		$map 	= ['id' => $id];
 		$data 	= Db::name('service_type')->where($map)->find();
 		return $data ? $data : [];
 	}

 	/**
 	 * 
 	 *
 	 *
 	 *
 	 */
 	public function listById($serviceId)
 	{
 		$serviceId = is_array($serviceId) ? $serviceId : explode(',' , $serviceId);
 		if (!$serviceId) return [];
 		$map 	= ['id' => ['in' , $serviceId]];
 		$data 	= Db::name('service_type')->where($map)->select();
 		return $data ? $data : [];
 	}

    /**
     *
     * @return array
     */
    public static function getServiceTypeConf()
    {
        $list 	= Db::name('service_type')->select();
        $list ? $list : [];
        $serviceConf = [];
        foreach ($list as $v) {
            $serviceConf['type'.$v['id']] = false;
        }
        return $serviceConf;
    }

    public static function getServiceConf($project_id){
        if(!self::$ServiceConf){
            $config 	= Db::name('service_config')->where(['project_id'=>$project_id])->find();
            if($config){
                foreach ($config as &$v) {
                    if($v && is_string($v)){
                        $json_decode = json_decode($v,true);
                        $json_decode && $v = $json_decode;
                    }
                }
            }
            self::$ServiceConf = $config;
        }
        return self::$ServiceConf;
    }

    /**
     * 获取配置值 ,第一次使用需要给 项目id
     * @param $service 服务名称
     * @param $item 配置项
     * @param int $project_id 项目
     * @return bool
     */
    public static function getServiceConfVal($service,$item,$project_id = 0){
        if (!self::$ServiceConf){
             self::getServiceConf($project_id);
        }
        return isset(self::$ServiceConf[$service][$item]) ? self::$ServiceConf[$service][$item] : false;
    }






}
	