<?php

namespace app\timing\controller;

use think\Controller;
use think\Db;
use think\Loader;

class Little extends Controller
{

	//--清除过期formId
	public function delId()
	{
	    $oldTime = time() - 86400;
	    $data['used'] = 1;
	    Db::name("littleapp_form_id")->where("create_ts<'{$oldTime}'")->update($data);
	    echo $oldTime; exit('<br/>end');
	}


}