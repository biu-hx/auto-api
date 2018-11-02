<?php
namespace app\equip\model;

use think\Db;

class EmtAi
{

	/**
	 * 
	 * @access 	public
	 * @param 	int 	$id 	业务处理的订单ID
	 * @return 	int
	 */
	public static function getServiceKeyword()
	{
		$map = ['service_type' => 'service' ];
        $data 	= Db::name('ai_keyword')->where($map)->select();
        return $data ? $data : [];
	}

    public static function getAttributeKeyword($service_id)
    {
        $map = ['service_type' => 'attribute','service_id'=>$service_id ];
        $data 	= Db::name('ai_keyword')->where($map)->select();
        return $data ? $data : [];
    }

    /**
     * @return array|false|\PDOStatement|string|\think\Collection
     */
    public static function getSecondServiceLevel($service_type_id,$level=0)
    {
        $map = ['service_type_id'=>$service_type_id ];
        if($level){
            $map['level'] = ['egt',$level];
        }
        $data 	= Db::name('ai_second_service_type')->order("level asc")->where($map)->select();
        return $data ? $data : [];
    }

    public static function findSecondServiceLevel($service_type_id,$level=0)
    {
        $map = ['service_type_id'=>$service_type_id ];
        $map['level'] = $level;
        $data 	= Db::name('ai_second_service_type')->where($map)->find();
        return $data ? $data : [];
    }

    public static function getSecondTypeAsKey($service_type_id)
    {
        $map = ['service_type_id'=>$service_type_id ];
        $data 	= Db::name('ai_second_service_type')->where($map)->select();
        $data ? $data : [];
        $tem = [];
        foreach ($data as $v){
            $tem[$v['id']] = $v;
        }
        return $tem;
    }


    /**
     * 创建ai会话
     * @param $service_type
     */
    public static function createAiSession($service_type,$session_value,$pinyin=""){
        $session_key = uniqid();
        $data = ['createTs'=>date("Y-m-d H:i:s"),'service_type'=>$service_type,'session_key'=>$session_key,'session_value'=>$session_value,'pinyin'=>$pinyin];
        Db::name('ai_session')->insert($data);
        return $session_key;
    }

    public static function updateAiSession($session_key,$data){
        return Db::name('ai_session')->where(['session_key'=>$session_key])->update($data);
    }


    public static function createAiHospital($hospitalId,$session_key,$select_data='',$preg_word="",$selected=1){
        $data = ['create_ts'=>date("Y-m-d H:i:s"),'attribute'=>1,'value'=>$hospitalId,'session_key'=>$session_key,'level'=>1,
            'select_data'=>$select_data,'selected'=>$selected,'preg_word'=>$preg_word];
        Db::name('ai_session_value')->insert($data);
    }

    public static function createAiSessionValue($session_key,$attribute,$value,$level,$select_data='',$selected="",$preg_word=''){
        $data = ['create_ts'=>date("Y-m-d H:i:s"),'attribute'=>$attribute,'value'=>$value,'session_key'=>$session_key,'level'=>$level,
            'select_data'=>$select_data,
            'selected'=>$selected,
            'preg_word'=>$preg_word,
        ];
        Db::name('ai_session_value')->insert($data);
    }

    public static function updataAiSessionValue($session_key,$attribute=false,$value=false,$level,$select_data=false,$selected=false,$value_extend=false,$preg_word=false){
        $data = ['update_ts'=>date("Y-m-d H:i:s")];
        if($attribute!==false){
            $data['attribute'] = $attribute;
        }
        if($value!==false){
            $data['value'] = $value;
        }
        if($select_data!==false){
            $data['select_data'] = $select_data;
        }
        if($selected!==false){
            $data['selected'] = $selected;
        }
        if($value_extend!==false){
            $data['value_extend'] = $value_extend;
        }
        if($preg_word!==false){
            $data['preg_word'] = $preg_word;
        }
        $map = ['session_key'=>$session_key,'level'=>$level ];
        Db::name('ai_session_value')->where($map)->update($data);
//        echo Db::name('ai_session_value')->getLastSql();
    }

    public static function getAiSessionValue($session_key){
        $where = ['session_key'=>$session_key];
        $data = Db::name('ai_session_value')->where($where)->select();
        $returnArr = [];
        foreach ($data as $v){
            $returnArr[$v['attribute']] = $v;
        }
        return $returnArr;
    }

    /**
     * 获取ai session信息
     * @param $service_type_id
     * @return array|false|\PDOStatement|string|\think\Collection
     */
    public static function getAiSessionInfo($session_key)
    {
        $map = ['session_key'=>$session_key ];
        $data 	= Db::name('ai_session')->where($map)->find();
        return $data ? $data : [];
    }

    public static function getAiSessionValueByLevel($session_key,$level)
    {
        $map = ['session_key'=>$session_key,'level'=>$level ];
        $data 	= Db::name('ai_session_value')->where($map)->find();
//        echo Db::name('ai_session_value')->getLastSql();
        return $data ? $data : [];
    }

    public static function getServiceTypeList(){
        $map = [];
        $data 	= Db::name('ai_service_type')->where($map)->select();

        return $data ? $data : [];
    }
    public static function getBaseAnswer(){
        $map = [];
        $data 	= Db::name('ai_answer')->where($map)->select();
        return $data ? $data : [];
    }

    /**
     * 获取转换的拼音
     * @return array
     */
    public static function getHospitalRegisterPinyin($hospital_id){
        $map = ['service_id'=>2,'hospital_id'=>$hospital_id];
        $data 	= Db::name('ai_keyword_pinyin')->where($map)->select();
        return $data ? $data : [];
    }












	


}