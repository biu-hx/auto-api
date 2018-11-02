<?php

namespace app\index\controller;

use app\component\response\Response;
use app\component\Log;
use think\Request;

class Index
{
    /**
     * 404 Not Found
     *
     * @access 	public
     * @return 	void
     */
    public function _empty()
    {
        Log::storageRequest('indexResponse' , $_REQUEST);
        Log::storageRoute('indexResponse' , Request::instance()->url());
    	Response::message(404);
    }
}
