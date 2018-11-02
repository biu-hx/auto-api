<?php

namespace app\equip\event\v2;

use think\Db;
use think\Loader;
use app\equip\controller\Base;
use app\component\Curl;
use app\component\response\Response;
use app\component\server\Server;

class Propaganda extends Base
{

	protected $validate = '\app\equip\validate\v2\Propaganda';

	protected $scene = [
        'evaluate'
	];


    /**
     * 医院介绍
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
	public function hospitalDes()
	{
		$data 	= Db::name("resource_hospital")->field("name,describe")->where("id={$this->hospitalId}")->find();
		Response::success($data);
	}

    /**
     * 科室介绍
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
	public function deptDes()
	{
		$data 	= Db::name('resource_project_dept')->where("project_id={$this->projectId}")->order('type')->select();
		Response::success($data);
	}

    /**
     * 公告公示
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
	public function notice()
	{
		$data 		= Db::name('project_notice')->where("project_id={$this->projectId}")->order('id desc')->select();
		Response::success($data);
	}


    /**
     * 就医评价
     */
    public function evaluate(){
        $data['order_id'] = $this->data['orderId'];
        $data['stars'] = isset($this->data['stars']) ? $this->data['stars'] : '';
        $data['project_id'] = $this->projectId;
        $data['content'] = isset($this->data['content']) ? $this->data['content'] : '';
        $data['create_time'] = time();
        $result = Db::name("order_evaluate")->insert($data);
        $result && Response::success();
        Response::error(91030);
    }



}