<template>
    <div class="content" v-cloak>
        <Loading v-if="unLoaded" :title="loadingText"></Loading>
        <Dialog v-if="infoBomb" :text="infoNotice" :buttonconfig="infoBtn" @hideBox="hideBox"></Dialog>
        
        <!-- 银行卡支付 -->
        <div>
            <div class="title">
                <p>银联支付</p>
                <div class="count-time">{{countTime}}</div>
            </div>
            <!-- 提示插卡 -->
            <div class="demonstration-box" v-if="guide==1">
                <p class="demonstration-title">请插入您的银行卡
                    <span>(芯片朝上)</span>
                </p>
                <p class="demonstration-img">
                    <img src="../../static/img/img_win_baseCard_main.png" class="solid-card-img">
                    <img src="../../static/img/bank-animation.png" class="animation-img animation-to-top">
                </p>
            </div>
            <!-- 提示输入密码 -->
            <div class="demonstration-box" v-if="guide==2">
                <p class="demonstration-title">请输入您的银行卡密码</p>
                <p class="bank_password_input">
                    <input type="password" v-model="bank_password" disabled="true">
                </p>
                <p class="demonstration-img">
                    <img src="../../static/img/input_bankpassword.png" class="solid-card-img">
                </p>
            </div>
        </div>

        <div class="am-frame" v-if="isFrequentOperation">
            <div class="am-frame-box">
                <div class="close-icon">
                    <p>{{lostTime}} 秒</p>
                </div>
                <div class="frame-detail-box">
                    <img src="../../static/img/friend-info-icon.png" alt="" class="friend-icon">
                    <div class="frame-detail-content">
                        <p class="am-frame-box-title">提示</p>
                        <p class="am-frame-box-info">操作频繁，请等待倒计时结束后再操作！</p>
                    </div>
                </div>
                <div class="frame-detail-operate">
                    <p class="repay-btn" @click="hideFrequentOperationBox" v-if="lostTime==0">确定</p>
                    <p class="repay-btn sp" v-if="lostTime>0">确定</p>
                </div>
            </div>
        </div>

    </div>
</template>
<script>
    import axios from "axios";
    export default {
        data() {
            return {
                isFrequentOperation: false, //是否操作频繁
                infoBomb: false,
                infoBtn: ['知道了'],
                infoNotice: '',
                
                unLoaded: false,
                loadingText: "处理中,请等待...",
                
                districtInfo: undefined,

                payData: '', //下单的数据
                orderInfo: '', //下单后的订单信息

                payStatus: '', //支付状态
                payNum: 0,
                
                countTime: '',
                lostTime: '',
                reversionTime: '',
                
                fromName: '',
                isLockFail: false, //是否锁号失败

                hospitalInfo: '',
                intError: false,

                // 可支付时间配置
                paytimeConfig:{
                    lockTime: '',
                    payTime:''
                },

                guide: 1,    // 1:插卡前 2: 输入密码
                bank_password: ""

            }
        },
        created: function () {
            // 首先检查硬件设备支付环境是否正常
            this.unLoaded = true;

            // 打开灯条
            let ligthOn_status = this.$machineApi.win_lightOn();
            if(ligthOn_status == 1 || ligthOn_status == 'error'){
                // 显示设备支付环境异常
                this.machinaErrorTip("支付设备异常(打开灯条失败)");
                return;
            }else{
                // 初始化
                let initUmps = this.$machineApi.win_initUmps();
                if(initUmps == 1 || initUmps == 'error'){
                    this.machinaErrorTip("支付设备异常(初始化失败)");
                    return;
                }
            }

            this.fromName = this.$route.params.fromName;
            this.payData = JSON.parse(this.$route.params.payData);
            this.districtInfo = JSON.parse(localStorage.getItem('districtInfo'));
            this.hospitalInfo = JSON.parse(localStorage.getItem('hospitalInfo'));

            // 可支付时间配置
            this.paytimeConfig = {
                lockTime: parseInt(this.hospitalInfo.serviceConf.registration.DaiZhiFuShiChang),
                payTime: parseInt(this.hospitalInfo.serviceConf.registration.KeZhiFuShiChang)
            }
            
            //调用下单函数
            this.orderOrigin(this.fromName, this.payData);
            
            //支付倒计时
            this.timer = setInterval(() => {
                if (this.countTime != '' && this.lostTime != '') {
                    this.countTime--;
                    this.lostTime--;
                    if (this.countTime > 0) {
                        if (this.countTime % 3 === 0) {
                            this.searchOrigin(this.fromName, this.orderInfo.orderNum);
                        }
                    } else if (this.countTime === 0) {
                        this.countTime = 0;
                    } else if (this.countTime < 0 && this.lostTime > 0) {
                        this.countTime = 0;
                    } else if (this.lostTime <= 0) {
                        this.lostTime = 0;
                        this.countTime = 0;
                        clearInterval(this.timer);
                    }
                }

                //取号倒计时
                if (this.reversionTime != '') {
                    this.reversionTime--;
                    if (this.reversionTime > 0) {
                        if (this.reversionTime % 3 === 0) {
                            this.searchOrigin(this.fromName, this.orderInfo.orderNum);
                        }
                    } else {
                        setTimeout(() => {
                            this.gotoPrint();
                        }, 3000)
                    }
                }
            }, 1000)
        },

        methods: {
            machinaErrorTip: function(msg){
                this.unLoaded = false;
                this.infoBomb = true;
                this.infoNotice = msg;
                this.intError = true;
            },

            leavePayment: function(){
                // 离开前先退卡
                let res = this.$machineApi.win_rejectBankCard();    // 退卡
                if(res == 0){
                    this.$machineApi.win_lightOff();    // 关闭灯条
                }
            },
            // 去打印
            gotoPrint: function(){

                this.leavePayment(); // 退卡和关闭灯条

                this.$router.push({
                    name: 'printPage',
                    params: {
                        orderNum: this.orderInfo.orderNum,
                        num: 0
                    }
                })
            },

            //根据路由名称判断订单来源，调用不同的下单方法
            orderOrigin: function (fromName, payData) {
                switch(fromName){
                    case 'register':
                        //挂号下单
                        this.placeRegOrder(payData);
                        break;
                    case 'reversionNumList':
                        //取号下单 
                        this.publicPlaceOrder(
                            payData, 
                            {scheduleCode: payData.scheduleCode}, 
                            '/registration/order/fetch');
                        break;
                    case 'outpatient':
                        //门诊缴费下单
                        this.publicPlaceOrder(
                            payData, 
                            {
                                recipeId: payData.recipeId,
                                orderNumber: payData.orderNumber
                            }, 
                            '/payment/order/outpatient');
                        break;
                    case 'hospitalizationDetail':
                        //住院预缴下单
                        this.publicPlaceOrder(
                            payData, 
                            {
                                treatNo: payData.treatNo,
                                fee: payData.fee
                            }, 
                            '/payment/order/inpatient');
                        break;
                }

            },

            //根据路由名称判断订单来源，调用不同的支付状态查询方法
            searchOrigin: function (fromName, orderNum) {
                let apiUrl = "";
                switch(fromName){
                    case 'register': //挂号下单
                        apiUrl = '/query/registration';
                        break;
                    case 'reversionNumList': //取号下单
                        apiUrl = '/query/fetch';
                        break;
                    case 'outpatient': //门诊缴费下单
                        apiUrl = '/query/outpatient';
                        break;
                    case 'hospitalizationDetail': //住院预缴下单
                        apiUrl = '/query/inpatient';
                        break;
                }

                this.publicSearchPayStatus(orderNum, apiUrl);

            },

            //挂号下单
            placeRegOrder: function (payData) {
                this.loadingText = '订单生成中，请稍候...';
                let user_info = JSON.parse(localStorage.getItem('userInfo'));

                let obj = {
                    cardId: user_info.cardId,
                    deptId: payData.deptId,
                    doctorId: payData.doctorId,
                    scheduleId: payData.scheduleId,
                    date: payData.date,
                    period: payData.period,
                    payType: this.$route.params.payType,
                    districtId: this.districtInfo.districtId,
                    districtName: this.districtInfo.districtName
                }
                
                if(payData.beginTime && payData.endTime){
                    obj.beginTime = payData.beginTime;
                    obj.endTime = payData.endTime;
                }

                if(user_info.UserIdKey){
                    obj.uniqueId = user_info.UserIdKey
                }

                this.postData(this.hospitalInfo.number, obj,
                    '/registration/lock', (res) => {
                    this.unLoaded = false;
                    
                    if (res.data.code == 10000) {
                        //播放锁号成功，提醒支付语音
                        this.$audioPlay(4);

                        this.orderInfo = res.data.data;

                        this.setTimeDown();

                        this.payNextStep("register");
                    } else {
                        this.isLockFail = true;
                        if (this.filterASCII(res.data.msg)) {
                            this.infoNotice = this.filterASCII(res.data.msg);
                        } else {
                            this.infoNotice = res.data.msg;
                        }
                        this.infoBomb = true;
                    }
                })
            },

            //非挂号类统一下单函数，参数说明:payData用于页面显示和下单参数，obj 下单传递给后端参数 payway支付方式 url下单接口
            publicPlaceOrder: function (payData, obj, url) {
                this.loadingText = '订单生成中，请稍候...';
                let user_info = JSON.parse(localStorage.getItem('userInfo'));
                let _obj = obj;
                
                _obj.cardId = user_info.cardId;
                _obj.payType = this.$route.params.payType;
                
                if(user_info.UserIdKey){
                    _obj.uniqueId = user_info.UserIdKey
                }

                this.postData(this.hospitalInfo.number, _obj,
                    url, (res) => {
                        this.unLoaded = false;
                        if (res.data.code == 10000) {
                            //播放锁号成功，提醒支付语音

                            this.$audioPlay(4);

                            this.orderInfo = res.data.data;
                            this.setTimeDown();

                            this.payNextStep();
                        } else {
                            if (this.filterASCII(res.data.msg)) {
                                this.infoNotice = this.filterASCII(res.data.msg);
                            } else {
                                this.infoNotice = res.data.msg;
                            }
                            this.infoBomb = true;
                        }
                    })
            },

            setTimeDown: function(){
                
                //挂号的支付逻辑
                if (this.fromName == 'register') {
                    let curSecond = this.orderInfo.now_time;
                    //判断是否锁号，若未锁号，则倒计时重置为150秒，已锁号，根据当前时间与锁号时间差确定显示时间
                    let createTime = parseInt(this.orderInfo.create_time);

                    if(this.hospitalInfo.hospital_id=='10000'){
                        this.countTime = createTime + this.paytimeConfig.payTime - curSecond >= this.paytimeConfig.payTime ? this.paytimeConfig.payTime : createTime + this.paytimeConfig.payTime - curSecond;
                    }
                    if(this.hospitalInfo.hospital_id=='61754'||this.hospitalInfo.hospital_id=='61756' || this.hospitalInfo.hospital_id === '61757'){
                        this.countTime = createTime + this.paytimeConfig.payTime - curSecond >= 120 ? 120 : createTime + this.paytimeConfig.payTime - curSecond;
                    }

                    this.lostTime = createTime + this.paytimeConfig.lockTime - curSecond; 

                    if (this.lostTime > 0 && this.lostTime < 30) {
                        this.isFrequentOperation = true;
                    }

                } else {
                    //非挂号的支付逻辑
                    this.countTime = 120;
                    this.lostTime = 120;
                }
            },

            // 设置
            payNextStep: function(type){

                this.$audioPlay(35);

                // alert("交易参数:"+ this.orderInfo.price)

                // 设置交易参数
                let setTradeParams = this.$machineApi.win_settingTradeParams(0.01);
                if(setTradeParams == 1){
                    this.machinaErrorTip("设置交易参数失败");
                    return;
                }

                // alert("准备设置读卡器进卡")

                // 设置读卡器进卡
                let setCardIn = this.$machineApi.win_settingCardIn();
                if(setCardIn == 1){
                    this.machinaErrorTip("设置读卡器进卡失败");
                    return;
                }

                // alert("准备开始读卡")

                // alert("next:"+this.countTime +"|"+ this.lostTime +"|"+ curSecond)
                
                // 读卡
                this.readBankcardNum();

            },

            // 读银行卡号
            readBankcardNum: function(){
                // 开始读卡
                let timing = setInterval(() => {
                    // 读卡
                    let resultCard = this.$machineApi.win_readerBankCard();
                    if(resultCard!="0"){
                        clearInterval(timing);

                        this.$audioPlay(37);

                        // 输入密码进行交易
                        this.payPassConsume();
                    }else{
                        this.$audioPlay(36);
                    }
                },1000);
            },

            // 输入密码
            payPassConsume: function(){

                // 开启密码键盘
                let startBankPin = this.$machineApi.win_startBankPin();
                if(startBankPin == 1){
                    this.machinaErrorTip("开启密码键盘失败");
                    return;
                }

                
                this.guide = 2;

                // 监听键盘输入
                let timing = setInterval(() => {
                    //操作结果+“|”+“键值类型” 操作结果：0 成功 其他：失败
                    var keyVal = this.$machineApi.win_getkeyValue();
                    var arr = keyVal.split("|");
                    if(arr[0]=="0"){
                       if(arr[1]=="3"){
                            // 输入中
                            this.bank_password+="#";
                       }else if(arr[1]=="4"){
                            clearInterval(timing);
                            console.log("用户取消交易");
                       }else if(arr[1]=="2"){
                            console.log("用户在退格");
                            this.bank_password=this.bank_password.substr(0,this.bank_password.length-1);
                       }else if(arr[1]=="1"){
                            console.log("输入超时");
                       }else if(arr[1]=="5"){
                            clearInterval(timing);
                            console.log("输入完成");

                            this.bank_password="######"

                            // 开始交易
                            this.startConsume();

                       }else if(arr[1]=="6"){
                            //等待用户输入
                       }
                    }else{
                        clearInterval(timing);
                        this.machinaErrorTip("获取键值失败");
                    }
                },1000);
            },

            // 交易
            startConsume: function(){
                
                let consumeStatus = this.$machineApi.win_startConsume(0.01, this.orderInfo.orderNum);
                if(consumeStatus){
                    consumeStatus = JSON.parse(consumeStatus);

                    this.payNum ++;

                    if(consumeStatus.plan.strRespCode == "00"){
                        // 交易成功
                        // 显示LOADING: "系统正在处理，请耐心等待…"
                        this.unLoaded = true;
                        this.loadingText = '系统正在处理，请耐心等待…';
                        this.payStatus == "success";

                        
                        // 支付结果告诉后端, 无需回调
                        // alert("回调密文：" + consumeStatus.cipher)
                        this.postData(
                            this.hospitalInfo.number, 
                            {
                                signData: consumeStatus.cipher,
                                'rand': new Date().getTime()
                            },
                            '/pay/unionPayCallBack', 
                            (res) => {
                                // 接口回调
                                // alert("unionPayCallBack：" + JSON.stringify(res.data))
                            })

                    }else{
                        this.infoBomb = true;

                        this.$audioPlay(38);

                        let msg = this.payNum >= 2 ? "<span style='color:red'>你输入密码错误已"+this.payNum+"次，以防银行卡被锁，</span>" : ""
                        this.infoNotice = "支付失败，失败原因:" + consumeStatus.plan.strRespInfo + "，" + msg + "是否重新输入密码？";
                        this.infoBtn = ["取消","重新输入"];
                        this.payStatus = "fail";
                    }
                }

            },


            //支付状态查询统一函数参数说明 orderNum 订单号 url 支付状态查询接口
            publicSearchPayStatus: function (orderNum, url) {
                this.$http.get(this.publicUrl + url, {
                    params: {
                        orderNum: orderNum,
                        timeStr: new Date().getTime()
                    },
                    headers: this.config(this.hospitalInfo.number),
                    timeout: 10000
                }).then((res) => {
                    if (res.data.code == 10000) {
                        //获取支付状态成功后，初始化取号倒计时
                        if (res.data.data.payStatus == 1 && res.data.data.status == 0) {
                            
                            this.countTime = '';
                            this.lostTime = '';
                            
                            //判断是否已初始化倒计时,仅挂号需要取号
                            if (this.reversionTime == '') {
                                this.reversionTime = 100;
                                if (this.fromName == 'register') {
                                    //播放正在取号语音
                                    this.$audioPlay(17);
                                } else {
                                    //播放系统正在处理语音
                                    this.$audioPlay(27);
                                }
                            }
                            

                        } else if (res.data.data.payStatus == 1 && res.data.data.status == 1) {
                            this.$audioPlay(33);
                            this.loadingText = '交易成功，将为您打印小票…';
                            clearInterval(this.timer);
                            setTimeout(() => {
                                this.unLoaded = false;
                                this.gotoPrint();
                            }, 3000)
                        } else if (res.data.data.payStatus >= 1) {
                            this.$audioPlay(34);
                            this.loadingText = '交易失败，将为您打印小票…';
                            clearInterval(this.timer);
                            setTimeout(() => {

                                this.unLoaded = false;
                                this.gotoPrint();
                            }, 3000)
                        }
                    }
                }).catch((err) => {

                    this.$audioPlay(21);

                    this.dealError();
                })
            },

            //重新支付
            // rePay: function () {
            //     //判断原路由来源，重新支付后跳转到来源页面
            //     if (this.fromName == 'register') {
            //         history.back(-2);
            //     } else {
            //         history.back(-1);
            //     }
            // },

            //隐藏操作频繁提示框
            hideFrequentOperationBox: function () {
                this.isFrequentOperation = false;
                this.$router.push({
                    name: "selectDoctor",
                    params: {
                        deptId: this.payData.deptId,
                        dateTime: this.payData.date,
                        period: this.payData.period
                    }
                });
            },
            
            //隐藏提醒弹框
            hideBox: function (selector) {
                this.infoBomb = false;
                
                // 挂号锁号失败
                
                if(this.isLockFail){
                    history.back(-3);
                }else if(selector == 1 && this.payStatus == "fail"){    // 支付失败后的重试
                    // 重新读卡
                    this.readBankcardNum();
                }else if(selector == 1 || this.intError){
                    history.back();
                }
                
            }

        },
        destroyed: function () {
            clearInterval(this.timer);
            this.leavePayment(); // 退卡和关闭灯条
        }
    }
</script>
<style scoped>
    .animation-img{
        position: absolute;
        right: 270px;
        top: 210px;
    }
    /*动画*/
    .animation-to-top{
        animation-name: toTop;
        animation-duration: 3.5s;
        animation-timing-function: linear;
        animation-delay: 0s;
        animation-iteration-count: infinite;
        animation-direction: normal;
        animation-play-state: running;
    }

    @keyframes toTop {
        0% {
            top: 300px;
        }
        50% {
            top: 210px;
        }
        100% {
            top: 300px;
        }
    }

    .demonstration-box {
        width: 1125px;
        height: 600px;
        border-radius: 10px;
        border: 1px solid #d2d5d5;
        margin-left: 35px;
        overflow: hidden;
    }

    .demonstration-title {
        width: 100%;
        height: 120px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 38px;
        margin: 0;
    }

    .demonstration-title span {
        color: #f70c0c;
    }

    .demonstration-img {
        width: 100%;
        position: relative;
        display: flex;
        justify-content: center;
    }

    .am-frame {
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 2, 0.6);
        position: fixed;
        top: 0;
        left: 0;
        overflow: hidden;
        z-index: 9999;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .am-frame-box {
        width: 985px;
        height: 585px;
        border-radius: 15px;
        background: #fff;
        position: relative;
        overflow: hidden;
    }

    .close-icon {
        width: 100%;
        height: 77px;
        display: flex;
        align-items: center;
        justify-content: flex-end;
    }

    .close-icon p {
        margin-right: 35px;
        font-size: 34px;
        color: #fe0403;
    }

    .frame-detail-box {
        width: 100%;
        height: 315px;
        display: flex;
        align-items: center;
        justify-content: flex-start;
    }

    .friend-icon {
        width: 232px;
        height: 100%;
        margin-left: 105px;
    }

    .frame-detail-content {
        width: 510px;
        height: 100%;
        margin-left: 78px;
        overflow: hidden;
    }

    .am-frame-box-title {
        width: 100%;
        font-size: 50px;
        color: #ff4039;
        font-weight: 600;
        margin-top: 80px;
    }

    .am-frame-box-info {
        width: 100%;
        margin-top: 40px;
        font-size: 36px;
    }

    .frame-detail-operate {
        width: 100%;
        margin-top: 75px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .bank_password_input{
        text-align: center;
        padding-bottom: 60px
    }
    .bank_password_input input{
        width:500px;
        height: 62px;
        border:#999 2px solid;
        border-radius: 5px;
        font-size: 60px;
        text-align: center;
        letter-spacing: 10px
    }
</style>