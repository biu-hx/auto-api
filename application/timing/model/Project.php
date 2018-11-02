<?php
namespace app\timing\model;

use think\Db;

class Project
{
    public function getArr(){
        $projectArr = Db::name('service_config')->field('registration,project_id')->select();
        return $projectArr;
    }
}