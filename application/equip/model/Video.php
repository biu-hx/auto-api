<?php
namespace app\equip\model;

use think\Db;

class Video 
{
	
	public function videoList($projectId)
	{
		$map 	= ['a.project_id' => $projectId , 'a.status' => 1 , 'b.status' => 1];
		$data 	= Db::name('project_video')
                ->field("a.*,b.*,a.price as price")
		 		->alias('a')
                ->where($map)
		 		->join('emt_resource_video b' , 'a.video_id = b.id')
		 		->order('a.sort desc')
		 		->select();
		return $data ? $data : [];
	}

	public function detail($projectId , $id)
	{
		$map 	= ['a.project_id' => $projectId , 'a.video_id' => $id , 'a.status' => 1 , 'b.status' => 1];
		$data 	= Db::name('project_video')
                ->field("a.*,b.*,a.price as price")
		 		->alias('a')
                ->where($map)
		 		->join('emt_resource_video b' , 'a.video_id = b.id')
		 		->find();
		return $data ? $data : [];
	}

	public function detailByOrderId($orderId)
	{
		$map 	= ['order_id' => $orderId];
		$data 	= Db::name('order_video')->where($map)->find();
		return $data ? $data : [];
	}

    /**
     * 设置科教视频失败
     * @param $id
     * @return bool
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function setVideoFail($id){
        $data['status'] = 2;
        $result = Db::name('order_video')->where("id='{$id}'")->update($data);
        return $result !== false ? true : false;
    }

    /**
     * 设置科教视频成功
     *
     * @param $id
     * @param $successInfo
     * @return bool
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function setVideoSuccess($id , $successInfo){
        $data['status'] = 1;
        $result = Db::name('order_video')->where("id='{$id}'")->update($data);
        return $result !== false ? true : false;
    }

}