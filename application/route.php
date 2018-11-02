<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

return [
    '__pattern__' => [
        'name' => '\w+',
    ],
    '[hello]'     => [
        ':id'   => ['index/hello', ['method' => 'get'], ['id' => '\d+']],
        ':name' => ['index/hello', ['method' => 'post']],
    ],
    '[user]' 	=> [
        'littleapp/login' 	=> ['api/User/littleAppLogin' , ['method' => 'post']],
    	'login' 	=> ['api/User/login' , ['method' => 'post']],
        'loginout' 	=> ['api/User/loginout' ],
        'auto/online' 	=> ['api/Personal/autoOnline' ],
        'code'      => ['api/User/code' , ['method' => 'post']],
        'verify'    => ['api/User/verify' , ['method' => 'post']],
        'password/modify'=> ['api/User/modify' , ['method' => 'post']],
        'password/reset'=> ['api/User/reSetPassword' , ['method' => 'post']],
        'unlogin'   => ['api/Personal/unlogin' , ['method' => 'delete']],
        'basic'     => ['api/Personal/basic' , ['method' => 'get|post']],
        'info'      => ['api/Personal/info' ,],
        'modify/info'       => ['api/Personal/modify' ,['method' => 'post'] ],
        'income'    => ['api/Personal/income' , ['method' => 'post|get']],
        'account'   => ['api/Personal/account' , ['method' => 'get']],
        'getTesterList'   => ['api/VersionConfig/getTesterList' , ['method' => 'get']],
    ],
    '[inquiry]'     => [
        'list'      => ['api/Inquiry/inquiry' , ['method' => 'post']],
        'detail'    => ['api/Inquiry/detail' , ['method' => 'post']],
        'newInquiryMsg'    => ['api/Inquiry/getNewInquiryMsg' , ['method' => 'post']],
        'inquiryVideo'    => ['api/Inquiry/getInquiryVideo' , ['method' => 'post']],
        'report'    => ['api/Inquiry/report' , ['method' => 'get']],
        'mark'      => ['api/Inquiry/mark' , ['method' => 'post']],
        'setStatus'     => ['api/Inquiry/setStatus' , ['method' => 'post']],
        'call/mark' => ['api/App/markCall' , ['method' => 'patch|post']],
        'relate'    => ['api/Inquiry/relate' , ['method' => 'get']],
        'addFormId'    => ['api/Inquiry/addLittleAppFormId' , ['method' => 'get|post']],
        'setAndGetStauts'    => ['api/Inquiry/setAndGetStauts' , ['method' => 'post']],
        'getInquiryStauts'    => ['api/Inquiry/getInquiryStauts' , ['method' => 'post']],
    ],
    '[Prescription]'     => [
        'searchMedical'      => ['api/Prescription/searchMedical' , ['method' => 'post']],
        'getMedicalDictionary'      => ['api/Prescription/getMedicalDictionary' , ['method' => 'post']],
        'submitPresription'      => ['api/Prescription/submitPresription' , ['method' => 'post']],
    ],

    'app/version'   => ['api/App/version' , ['method' => 'get']],



    '[typeb]'       => [
        'getCheckCaution'    => ['equip/TypeB/getCheckCaution' ],
        'getPatientInfo'  => ['equip/TypeB/getPatientInfo' , ['method' => 'post|options']],
        'getCheckTimeSet'   => ['equip/TypeB/getCheckTimeSet' , ['method' => 'post|options']],
        'getCheckCalendar'   => ['equip/TypeB/getCheckCalendar' , ['method' => 'post|options']],
        'getPatientState'     => ['equip/TypeB/getPatientState' , ['method' => 'post|options']],
        'putConfirmCheck' => ['equip/TypeB/putConfirmCheck' , ['method' => 'post|options']],
    ],



    '[equip]'       => [
        'number'    => ['equip/Equip/number' , ['method' => 'get|options']],
        'hardware'  => ['equip/Equip/hardware' , ['method' => 'get|patch|options']],
        'network'   => ['equip/Equip/network' , ['method' => 'get|patch|options']],
        'version'   => ['equip/Equip/version' , ['method' => 'get|patch|options']],
        'paper'     => ['equip/Equip/paper' , ['method' => 'get|patch|options']],
        'equiplist' => ['equip/Equip/equiplist' , ['method' => 'get|patch|options']],
        'restart' => ['equip/Equip/restart' , ['method' => 'get|patch|options']],
        'reset' => ['equip/Equip/reset' , ['method' => 'get|patch|options']],
        'getVoice' => ['equip/Equip/stringToVoice' , ['method' => 'get|post|options']],
        'voiceList' => ['equip/Equip/voiceList' , ['method' => 'get|post|options']],
    ],
    '[patient]'     => [
        'card'      => ['equip/Card/cardByPatient' , ['method' => 'get|options']],
    ],
    '[card]'        => [
        'patient'   => ['equip/Card/cardByPatient' , ['method' => 'get|options']],
        'searchCard'   => ['equip/Card/searchCard' , ['method' => 'get|options|post']],
        'getCareer'   => ['equip/Card/getCareer' , ['method' => 'get|options|post']],
        'addPhysicalCard'   => ['equip/Card/addPhysicalCard' , ['method' => 'get|options|post']],
        'identify'  => ['equip/Card/card' , ['method' => 'get|options']],
        'getCardInfo'  => ['equip/Card/getCardInfo' , ['method' => 'get|post|options']],
        'elehealth' => ['equip/Card/cardByEleHealthCard' , ['method' => 'get|options']],
    ],
    '[registration]'=> [
        'hospital'  => ['equip/Registration/hospital' , ['method' => 'get|options']],
        'dept'      => ['equip/Registration/dept' , ['method' => 'get|options']],
        'date'      => ['equip/Registration/date' , ['method' => 'get|options']],
        'doctor'    => ['equip/Registration/doctor' , ['method' => 'get|options']],
        'detail'    => ['equip/Registration/doctorDetail' , ['method' => 'get|options']],
        'time'      => ['equip/Registration/doctorTime' , ['method' => 'get|post|options']],
        'cancel'    => ['equip/Registration/cancel' , ['method' => 'get|options']],
        'schedule'  => ['equip/Registration/schedule' , ['method' => 'get|options']],
        'lock'      => ['equip/Registration/lock' , ['method' => 'post|options']],
        'fetch'     => ['equip/Registration/fetchReg' , ['method' => 'get|options']],
        'order/fetch'   => ['equip/Registration/fetchRegOrder' , ['method' => 'post|options']],
        'getwait'   => ['equip/Registration/getWaitDetail' , ['method' => 'post|options']],

    ],
    '[video]'       => [
        'list'      => ['equip/Video/videoList' , ['method' => 'get|options']],
        'detail'    => ['equip/Video/videoDetail' , ['method' => 'get|options']],
        'order'     => ['equip/Video/videoOrder' , ['method' => 'post|options']],
        'play'     => ['equip/Video/playVideo' , ['method' => 'get|options']],
        'getUrl'    => ['equip/Video/getUrl' , ['method' => 'get|options']],
    ],
    '[consultant]'     => [
        'dept'      => ['equip/Inquiry/dept' , ['method' => 'get|options']],
        'doctor'    => ['equip/Inquiry/doctor' , ['method' => 'get|options']],
        'detail'    => ['equip/Inquiry/doctorDetail' , ['method' => 'get|options']],
        'order'     => ['equip/Inquiry/inquiryOrder' , ['method' => 'post|options']],
        'connect'   => ['equip/Inquiry/inquiryConnect' , ['method' => 'post|options']],
        'screen'    => ['equip/Inquiry/inquiryScreen' , ['method' => 'patch|options']],
        'report'    => ['equip/Inquiry/inquiryReport' , ['method' => 'patch|post']],
        'hospital'  => ['equip/Inquiry/hospitalList' , ['method' => 'get|options']],
        'answer'    => ['equip/Inquiry/answer' , ['method' => 'get|post|options']],
        'outstatus' => ['equip/Inquiry/outStatus' , ['method' => 'post|options']],
        'puterror'  => ['equip/Inquiry/putError' , ['method' => 'post|options']],
        'getstatus'    => ['equip/Inquiry/callStatus' , ['method' => 'post|options']],
        'orderstatus'    => ['equip/Inquiry/orderStatus' , ['method' => 'post|options']],
    ],
    '[EmtAi]'     => [
        'getAiSession'      => ['equip/EmtAi/createAiSession' , ['method' => 'get|post']],
        'searchAiBusness'      => ['equip/EmtAi/searchAiBusinessSm' , ['method' => 'get|post']],
        'addVoiceSelect'      => ['equip/EmtAi/addVoiceSelect' , ['method' => 'get|post']],
        'addUserSelect'      => ['equip/EmtAi/addUserSelect' , ['method' => 'get|post']],
        'deleteUserSelected'      => ['equip/EmtAi/deleteUserSelected' , ['method' => 'get|post']],
        'getBaseAnswer'      => ['equip/EmtAi/getBaseAnswer' , ['method' => 'get|post']],
        'testPregfunction'      => ['equip/EmtAi/testPregfunction' , ['method' => 'get|post']],
    ],

    '[EmtAiV2]'     => [
        'getAiSession'      => ['equip/EmtAiV2/createAiSession' , ['method' => 'get|post']],
        'searchAiBusness'      => ['equip/EmtAiV2/searchAiBusinessSm' , ['method' => 'get|post']],
        'addVoiceSelect'      => ['equip/EmtAiV2/addVoiceSelect' , ['method' => 'get|post']],
        'addUserSelect'      => ['equip/EmtAiV2/addUserSelect' , ['method' => 'get|post']],
        'deleteUserSelected'      => ['equip/EmtAiV2/deleteUserSelected' , ['method' => 'get|post']],
        'getBaseAnswer'      => ['equip/EmtAiV2/getBaseAnswer' , ['method' => 'get|post']],
        'updateUserSelectedSchedul'      => ['equip/EmtAiV2/updateUserSelectedSchedul' , ['method' => 'get|post']],
        'createAiDirectSession'      => ['equip/EmtAiV2/createAiDirectSession' , ['method' => 'get|post']],
        'testPregfunction'      => ['equip/EmtAiV2/testPregfunction' , ['method' => 'get|post']],
    ],

    '[list]'        => [
        'drug'      => ['equip/ListSearch/drug' , ['method' => 'get|options']],
        'diagnosis' => ['equip/ListSearch/diagnosis' , ['method' => 'get|options']],
    ],
    '[inpatient]'   => [
        'list'      => ['equip/Inpatient/inpatientList' , ['method' => 'get|options']],
        'type'      => ['equip/Inpatient/inpatientType' , ['method' => 'get|options']],
        'detail'    => ['equip/Inpatient/inpatientDetail' , ['method' => 'get|options']],
    ],

    '[payment]'     => [
        'outpatientlist'=> ['equip/Payment/outpatientList' , ['method' => 'get|options']],
        'outpatient'    => ['equip/Payment/outpatient' , ['method' => 'get|options']],
        'inpatient'     => ['equip/Payment/inpatient' , ['method' => 'get|options']],
        'order/outpatient'  => ['equip/Payment/outpatientOrder' , ['method' => 'post|options']],
        'order/inpatient'   => ['equip/Payment/inpatientOrder' , ['method' => 'post|options']],
        'getpay'   => ['equip/Payment/getPay' , ['method' => 'post|options']],
    ],
    '[report]'      => [
        'list'          => ['equip/Report/reportList' , ['method' => 'get|options']],
        'detail'        => ['equip/Report/reportDetail' , ['method' => 'get|options']],
        'print'         => ['equip/Report/reportPrint' , ['method' => 'patch|options']],
        'bar'           => ['equip/Report/barCode' , ['method' => 'get|options']],
        'barprint'      => ['equip/Report/barCodePrint' , ['method' => 'patch|options']],
    ],

    '[order]'       => [
        'qr/wechat' => ['equip/Order/wechatQR' , ['method' => 'get|options']],
        'qr/alipay' => ['equip/Order/alipayQR' , ['method' => 'get|options']],
        'print'     => ['equip/Order/markPrint' , ['method' => 'get|patch|options']],
        'dinner'    => ['equip/Order/addDinner' , ['method' => 'post|patch|options']],
        'setStatus'    => ['equip/Order/setStatus' , ['method' => 'post|patch|options']],
    ],
    
    '[query]'       => [
        'registration'  => ['equip/Registration/registrationQuery' , ['method' => 'get|options']],
        'video'         => ['equip/Video/videoQuery' , ['method' => 'get|options']],
        'consultant'    => ['equip/Inquiry/inquiryQuery' , ['method' => 'get|options']],
        'fetch'         => ['equip/Registration/fetchQuery' , ['method' => 'get|options']],
        'inpatient'     => ['equip/Payment/inpatientQuery' , ['method' => 'get|options']],
        'outpatient'    => ['equip/Payment/outpatientQuery' , ['method' => 'get|options']],
        'order/patient' => ['equip/Order/searchByCard' , ['method' => 'get|options']],
        'order/number'  => ['equip/Order/searchByOrder' , ['method' => 'get|options']],
        'order/link'    => ['equip/Order/searchByLink' , ['method' => 'get|options']],
        'order/treat'   => ['equip/Order/searchByTreat' , ['method' => 'get|options']],
        'order/detail'  => ['equip/Order/detail' , ['method' => 'get|options']],
    ],
    '[common]'       => [
        'getarealist'    => ['equip/Common/getAreaList' , ['method' => 'get|options']],

    ],
    '[propaganda]'      => [
        'hospitalDes'   => ['equip/propaganda/hospitalDes' , ['method' => 'get|options']],
        'deptDes'       => ['equip/propaganda/deptDes' , ['method' => 'get|options']],
        'notice'        => ['equip/propaganda/notice' , ['method' => 'get|options']],
        'evaluate'      => ['equip/propaganda/evaluate' , ['method' => 'get|post|options']],
    ],
    '[inHospitalRecord]'       => [
        'searchInHospital'  => ['equip/InHospitalRecord/searchInHospital' , ['method' => 'get|options']],
        'getSelectList'  => ['equip/InHospitalRecord/getSelectList' , ['method' => 'get|options']],
        'submitInHospital'  => ['equip/InHospitalRecord/submitInHospital' , ['method' => 'post|options']],
        'sendCode'  => ['equip/InHospitalRecord/sendCode' , ['method' => 'post|options']],
        'checkCode'  => ['equip/InHospitalRecord/checkCode' , ['method' => 'post|options']],
    ],
    '[outHospital]'       => [
        'search'  => ['equip/OutHospital/search' , ['method' => 'get|options']],
        'detail'  => ['equip/OutHospital/detail' , ['method' => 'get|options']],
        'submit'  => ['equip/OutHospital/submit' , ['method' => 'post|options']],
    ],
    '[hospital]'       => [
        'district'  => ['equip/Registration/getHospitalDistrict' , ['method' => 'get|post|options']],

    ],

    'business/callback' => ['equip/Business/index' , ['method' => 'post']],
    'business/windowRefund' => ['equip/Business/windowRefund' , ['method' => 'post']],
    'pay/callback'  => ['equip/Pay/index' , ['method' => 'post']],
    'pay/unionPayCallBack'  => ['equip/Pay/callBackUnionPay' , ['method' => 'post']],

    '[timing]'      => [
        'business/execute'      => ['timing/Business/executeBusiness' , ['method' => 'get']],
        'cancel/registration'   => ['timing/Order/cancelRegistration' , ['method' => 'get']],
        'cancel/order'          => ['timing/Order/cancelOrder' , ['method' => 'get']],
        'refund/order'          => ['timing/Order/refundOrder' , ['method' => 'get']],
        'checkOrder'            => ['timing/Order/checkOrder' , ['method' => 'get']],
        'refundOrder'           => ['timing/Order/refundOrderByRedis' , ['method' => 'get']],
        'delid'                 => ['timing/Little/delId' , ['method' => 'get']],
        'Accesstokenget'        => ['timing/Accesstokenget/getAccessToken' , ['method' => 'get']],
        'echoAccessToken'       => ['timing/Accesstokenget/echoAccessToken' , ['method' => 'get']],
        'huaxi2/refundset'      => ['timing/Business/getHuaxiRefundStatus' , ['method' => 'get']],
        'refundInquiryOrder'    => ['timing/Order/refundInquiryOrder' , ['method' => 'get']],
        'cacheDoctorSchedel'    => ['timing/Business/cacheDoctorSchedel' , ['method' => 'get']],

        'network'          => ['timing/Network/index' , ['method' => 'get']],
        'test/test1'          => ['timing/Business/test1' , ['method' => 'get']],
    ],
    '[face]'       => [
        'addface'    => ['equip/Face/addFace' , ['method' => 'post']],
        'searchface'  => ['equip/Face/searchFace' , ['method' => 'post']],
        'addperson'   => ['equip/Face/addPerson' , ['method' => 'post|options']],
        'searchperson'   => ['equip/Face/searchPerson' , ['method' => 'get']],
        'uppic'   => ['equip/Face/upPic' , ['method' => 'post|get']]
    ],

    '[test]'       => [
        'testVideoUrl'    => ['equip/Test/testVideoUrl' , ['method' => 'get']],
        'testSendMsg'    => ['equip/Test/testSend' , ['method' => 'get']],
        'syncOrder'    => ['equip/Pay/syncOrder' , ['method' => 'post']],

    ],

    '__miss__'      => ['index/Index/_empty' , ['method' => 'post|put|get|delete|patch']],
];
