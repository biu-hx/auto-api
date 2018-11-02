<?php
/**
 * 通用接口
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/22 0022
 * Time: 14:47
 */

namespace app\equip\event\v2;


use app\component\response\Response;
use app\component\server\Server;
use app\equip\controller\Base;
use think\Loader;

class Common extends Base
{

    protected $validate = '\app\equip\validate\v2\Common';

    protected $scene = [
        'getAreaList'
    ];


    // 查询住院证信息
    public function getAreaList()
    {
        $hid 	= $this->hospitalId;
        $parent_id = $this->data['parent_id'];
        $areaList = Loader::model("ResourceArea")->getAreaList($parent_id);
        $data['code'] = '10000';
        $data['msg']  = 'ok';
        $data['data']  = $areaList;
        echo json_encode($data);exit;
    }

}