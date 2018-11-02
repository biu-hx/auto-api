<?php

namespace app\timing\controller;

use think\Controller;
use think\Db;

class Network extends Controller
{
    public function index(){
        $equipmentArr = Db::name("equipment")->select();
        if(!$equipmentArr){
            echo 'not equipoment';die;
        }
        foreach ($equipmentArr as $equipment){
            $hardWareStr = $equipment['hardware'];
            if(!$hardWareStr){
                //没有网络数据传上来暂时不处理
                continue;
            }
            $hardWare = json_decode($hardWareStr , true);
            if(!isset($hardWare['network'])){
                //没有网络数据传上来暂时不处理
                continue;
            }
            $netTime = $hardWare['network']['time'];
            $nowTime = time();
            $needTime = $nowTime - $netTime;
            if($needTime > 1200){
                $fault[10] = 20004;
                $fault['time'] = $netTime;
                $monitor['fault_type'] = json_encode($fault);
                $monitor['create_ts'] = date("Y-m-d H:i:s" , time());
                $monitor['equipment_id'] = $equipment['id'];
                $monitor['project_id'] = $equipment['project_id'];
                $monitor['status'] = 0;
                $monitor['task'] = 0;
                $faultArr = Db::name("monitor_fault")
                    ->where("fault_type='{$monitor['fault_type']}' and equipment_id={$equipment['id']} and status=0")
                    ->find();
                if(!$faultArr){
                    Db::name("monitor_fault")->insert($monitor);
                }
            }
        }
        echo '更新成功';die;
    }
}