<?php
// +----------------------------------------------------------------------
// | 登录
// +----------------------------------------------------------------------
// | Author: duanhy <shongyudxmas@163.com> 
// +----------------------------------------------------------------------
// | version: 1.0
// +----------------------------------------------------------------------

namespace app\equip\event\v2;

use app\component\Curl;
use app\component\Log;
use app\component\Common;
use app\component\server\Server;
use app\equip\controller\Base;
use app\component\response\Response;

class Face extends Base
{

	protected $validate = '\app\equip\validate\v2\Face';

	protected $scene = [
		'addFace',
		'searchFace',
		'addPerson',
		'searchPerson'
	];

    private static $hospital;

    private $url = 'http://139.199.206.91:8084';  //'http://139.199.206.91:8084'

    private $token;

    private $version 	= '2';

    private $config 	= [];

    private static $route 	= [
        'addFace' 			=> ['/face/add' , 'post'],
        'searchFace' 		=> ['/face/search' , 'post'],
        'addPerson' 		=> ['/user/add' , 'post'],
        'searchPerson' 		=> ['/user/search' , 'post']
    ];

  /*  function __construct()
    {
        $config = (array) $config; 		//强制config为数组
        $this->config = $config;
        if (!isset($config['url']) || !preg_match('/^((https|http|ftp|rtsp|mms)?:\/\/)[^\s]+/' , $config['url'])) {
            throw new \Exception('error url');
        }
        if (!isset($config['token'])) {
            throw new \Exception('error token');
        }
        $this->url 		= $config['url'];
        $this->token 	= $config['token'];
        isset($config['version']) && $this->version = $config['version'];
    }*/

	/**
	 * 添加人脸
	 *
	 * @access 	public
	 * @return 	void
	 */
	public function addFace()
	{
		$personId 	= $this->data['personId'];
		$image 	= $this->data['image'];
        $params 	= [
            'image' 	=> $image,  //arr
            'personId' 	=> $personId,
        ];
		//加上签名请求人脸项目接口
		$data 	=$this->request($this->action , $params);
        if($data['code'] == 10000){
            Response::success();
        }else{
            Response::message(10102);
        }
	}

	/**
	 *  搜索人脸
	 *
	 * @access 	public
	 * @return 	void
	 */
	public function searchFace()
	{
		$image 	= $this->data['image'];
        $params 	= [
            'image' 	=> $image,
        ];
		//加上签名请求人脸项目
		$data 	= $this->request($this->action , $params);
//		print_r($data);exit;
        if($data['code'] != 10000){
            Response::message(10102);
        }
        $params 	= [
            'IDCardNo' 	=> $data['data']['identify_id'],
        ];
        //去医院搜索身份证对应的卡
        $data 	= Server::ability('hospital')->getCardListByFace($this->hospitalId , $params);
        if (!$data) { Response::message(10101); }
        if($data['code'] != 10000 && isset($data['msg'])){
            Response::message(10012,mb_substr($data['msg'],9,null,'utf8'));
        }
        Response::success($data['data']);
	}

    /**
     *  查询用户
     *
     * @access 	public
     * @return 	void
     */
    public function searchPerson()
    {
        $IDCard 	= $this->data['IDCard'];
        $params 	= [
            'IDCard' 	=> $IDCard,
        ];
        //请求人脸项目
        $data = $this->request($this->action,$params);
        if($data['code'] == 10000){
            Response::success($data['data']);
        }else{
            Response::message(10102);
        }

    }

    /**
     *  创建用户
     *
     * @access 	public
     * @return 	void
     */
    public function addPerson()
    {
        $IDCard 	= $this->data['IDCard'];
        $name 	= $this->data['name'];
        $image  = $this->data['image'];
        $params 	= [
            'IDCard' 	=> $IDCard,
            'name' 	    => $name,
            'image' 	=> $image
        ];
        //请求人脸项目接口
        $data 	= $this->request($this->action , $params);
        if($data['code'] == 10000){
            Response::success($data['data']);
        }else{
            Response::success($data);
        }
    }

    /**
     * 请求
     *
     * @access 	private
     * @param 	string 	$method 请求调用的方法
     * @param   array 	$params 请求的参数
     * @return 	array
     */
    private function request($method , $params)
    {
        Log::storageRequest('FaceRequest_'.$method , $params);
        //$params['token'] 	= $this->token;
        $data 	= [];
        $header = [
            'Content-Type:application/x-www-form-urlencoded;charset=utf-8',
            'api-version:'.$this->version,
            'appid:123',
            'timestamp:' . time(),
        ];
        $route 		= self::$route[$method][0];
        $type 		= isset(self::$route[$method][1]) ? strtolower(self::$route[$method][1]) : 'post';
        switch ($type) {
            case 'get':
                $url 	= $this->url.$route.'?'.http_build_query($params);
                break;
            case 'post':
                $url 	= $this->url.$route;
                $data 	= http_build_query($params);;
                break;
            case 'put':
                $url 	= $this->url.$route;
                $data 	= http_build_query($params);
                break;
            case 'patch':
                $url 	= $this->url.$route;
                $data 	= http_build_query($params);
                break;
            case 'delete':
                $url 	= $this->url.$route;
                $data 	= http_build_query($params);
                break;
            default:		break;
        }
        Log::storageRoute('FaceRequest_'.$method , $url);
        Curl::init();
        Curl::setUrl($url);
        Curl::setCustomRequest($type);
        $data && Curl::setParams($data);
        Curl::setHttpHeader($header);
        Curl::setOpt();
        $response 	= Curl::execute();
        $result 	= json_decode($response , true);
        Log::storageResponse('FaceRequest_'.$method , $result);
        Log::writeLog('FaceRequest_'.$method);
        return $result ? $result : [];
    }



    /**
     * 上传图片
     *
     * @access 	public
     * @return 	void
     */
    public function upPic()
    {
        //Response::success();
        $base_img = $this->data['image'];
        //Response::success($base_img);
        //  $base_img是获取到前端传递的src里面的值，也就是我们的数据流文件
        $base_img = str_replace('data:image/jpg;base64,', '', $base_img);
//  设置文件路径和文件前缀名称
        $path = ROOT_PATH  . 'upload';
        $prefix='face';
        $output_file = $prefix.time().rand(100,999).'.jpg';
        $path = $path.$output_file;
//  创建将数据流文件写入我们创建的文件内容中
        $ifp = fopen( $path, "wb" );
        fwrite( $ifp, base64_decode( $base_img) );
        fclose( $ifp );
        $result = Common::TencentCloud($path);
        if($result){
            $resultData['code'] = 1;
            $resultData['url'] = $result;
        }else{
            $resultData['code'] = 0;
            $resultData['msg'] = '上传腾讯云失败!';
        }
        echo json_encode($resultData);exit;
// 第二种方式
// file_put_contents($path, base64_decode($base_img));
// 输出文件
        //print_r($output_file);
        // 获取表单上传文件 例如上传了001.jpg
        //$file = request()->file('image');

        // 移动到框架应用根目录/public/uploads/ 目录下
       /* if($file){
            $info = $file->move(ROOT_PATH  . 'upload');
            $path = ROOT_PATH  . 'upload';
            if($info){
                $path .= DS .$info->getSaveName();
                $result = Common::TencentCloud($path);
                if($result){
                    $resultData['code'] = 1;
                    $resultData['url'] = $result;
                }else{
                    $resultData['code'] = 0;
                    $resultData['msg'] = '上传腾讯云失败!';
                }
            }else{
                $resultData['code'] = 0;
                $resultData['msg'] = $file->getError();
                // 上传失败获取错误信息
            }
            Response::success($resultData);
        }*/

    }

}
