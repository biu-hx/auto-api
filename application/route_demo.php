<?php


return [

    '[demo]' 	=> [
    	'timing' 	 						    => ['demo/timing/index' , ['method' => 'get']],
        '/hospital/card'     					=> ['demo/Card/patient' , ['method' => 'get']],
        '/hospital/reg/duty_dept' 				=> ['demo/Registration/dept' , ['method' => 'get']],
        '/hospital/reg/duty_doctor'   			=> ['demo/Registration/duty' , ['method' => 'get']],
        '/hospital/reg/doctor_schedule'    		=> ['demo/Registration/schedule' , ['method' => 'get']],
       // '/hospital/reg/schedule_period'     	=> ['demo/Registration/basic' , ['method' => 'get']],
        '/hospital/reg/lock_schedule'       	=> ['demo/Registration/lock' , ['method' => 'post']],
        '/hospital/reg/add_reg_query'       	=> ['demo/Registration/fetch' , ['method' => 'get']],
        '/hospital/reg/get_reg_wait'       	    => ['demo/Registration/getWaitDetail' , ['method' => 'get']],
        '/hospital/reg/add_reg_order'     		=> ['demo/Registration/fetchOrder' , ['method' => 'post']],
        '/hospital/out_pat_pay/doctor_advice'   => ['demo/Payment/outpatientList' , ['method' => 'get']],
        '/hospital/out_pat_pay/prescription'    => ['demo/Payment/outpatient' , ['method' => 'get']],
        '/hospital/in_pat_pay/patient_info'    	=> ['demo/Payment/inpatient' , ['method' => 'get']],  
        '/hospital/in_pat_pay/order'   		 	=> ['demo/Payment/inpatientOrder' , ['method' => 'post']],
        //'/hospital/report/inspect'   			=> ['demo/Personal/account' , ['method' => 'get']],
        //'/hospital/report/examine'   			=> ['demo/Personal/account' , ['method' => 'get']],
        //'/hospital/report/inspect_detail'   	=> ['demo/Personal/account' , ['method' => 'get']],
        //'/hospital/report/examine_detail'   	=> ['demo/Personal/account' , ['method' => 'get']],
        '/hospital/transfer/index'   			=> ['demo/Transfer/index' , ['method' => 'get']],
        '/hospital/other/his/index'             => ['demo/His/index' , ['method' => 'get']],
    ],
];