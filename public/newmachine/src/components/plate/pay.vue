<template>
    <div class="content" v-cloak>
        <Loading v-if="unLoaded" :title="loadingText"></Loading>
        <Dialog v-if="infoBomb" :text="infoNotice" :buttonconfig="['取消','确定']" @hideBox="hideBox"></Dialog>
        <!-- 微信支付 -->
        <div v-if="payway=='wechatpay'">
            <div class="title">
                <p v-if="payStatus=='unpay'">微信扫码支付</p>
                <p v-if="payStatus!='unpay'">微信扫码支付-结果提示</p>
                <div class="count-time" v-if="countTime!=''">{{countTime}}</div>
                <div class="count-time" v-if="reversionTime!=''">{{reversionTime}}</div>
            </div>
            <div class="demonstration-box">
                <!-- 未支付 -->
                <div class="pay-box" v-if="payStatus=='unpay'">
                    <div class="pay-box-item">
                        <div class="pay-box-item-header">
                            <p class="step">1</p>
                            <div class="info">
                                <p>登录微信</p>
                                <p>打开扫一扫</p>
                            </div>
                        </div>
                        <img src="../../static/img/wechat-open.png" alt="" class="demonstration-img">
                    </div>
                    <div class="pay-box-item">
                        <div class="pay-box-item-header">
                            <p class="step">2</p>
                            <div class="info">
                                <p>点击"扫一扫"</p>
                            </div>
                        </div>
                        <img src="../../static/img/wechat-scan.png" alt="" class="demonstration-img">
                    </div>
                    <div class="pay-box-item-qrcode">
                        <div class="pay-box-item-header-qrcode">
                            请扫描二维码
                        </div>
                        <img :src="orderInfo.qr" class="qrcode-img" v-if="orderInfo.qr">
                        <img src="../../static/img/loading.gif" class="qrcode-img-loading" v-if="!orderInfo.qr">
                    </div>
                </div>

                <!-- 支付超时 -->
                <div v-if="payStatus=='overtime'">
                    <p class="icon-box">
                        <img src="../../static/img/overtime-info-icon.png" alt="" class="info-icon">
                    </p>
                    <p class="information">抱歉，超时未支付</p>
                    <div class="repay-box">
                        <p class="repay-btn" @click="rePay">重新支付</p>
                    </div>
                </div>

                <!-- 支付成功 -->
                <div v-if="payStatus=='paySuccess'">
                    <p class="success-box">
                        <img src="../../static/img/paysuccess-info-icon.png" alt="" class="info-icon">
                    </p>
                    <p class="success-info">支付成功</p>
                    <p class="auto-deal">系统正在处理...</p>
                </div>

                <!-- 交易成功 -->
                <div v-if="payStatus=='reversionSuccess'">
                    <p class="success-box">
                        <img src="../../static/img/paysuccess-info-icon.png" alt="" class="info-icon">
                    </p>
                    <p class="success-info">交易成功</p>
                </div>
                
                <!-- 交易失败 -->
                <div v-if="payStatus=='reversionFailed'">
                    <p class="icon-box">
                        <img src="../../static/img/overtime-info-icon.png" alt="" class="info-icon">
                    </p>
                    <p class="information">抱歉，交易失败</p>
                    <!-- <p class="auto-deal">失败原因：取号失败</p> -->
                    <!-- <div class="repay-box-sp">
                        <p class="repay-btn" @click="rePay">重新支付</p>
                    </div> -->
                </div>
            </div>
        </div>
        <!-- 支付宝支付 -->
        <div v-if="payway=='alipay'">
            <div class="title">
                <p v-if="payStatus=='unpay'">支付宝扫码支付</p>
                <p v-if="payStatus!='unpay'">支付宝扫码支付-结果提示</p>
                <div class="count-time" v-if="countTime!=''">{{countTime}}</div>
                <div class="count-time" v-if="reversionTime!=''">{{reversionTime}}</div>
            </div>
            <div class="demonstration-box">
                <!-- 未支付 -->
                <div class="pay-box" v-if="payStatus=='unpay'">
                    <div class="pay-box-item">
                        <div class="pay-box-item-header">
                            <p class="step">1</p>
                            <div class="info">
                                <p>打开支付宝</p>
                            </div>
                        </div>
                        <img src="../../static/img/ali-open.png" alt="" class="demonstration-img">
                    </div>
                    <div class="pay-box-item">
                        <div class="pay-box-item-header">
                            <p class="step">2</p>
                            <div class="info">
                                <p>点击"扫一扫"</p>
                            </div>
                        </div>
                        <img src="../../static/img/ali-scan.png" alt="" class="demonstration-img">
                    </div>
                    <div class="pay-box-item-qrcode">
                        <div class="pay-box-item-header-qrcode">
                            请扫描二维码
                        </div>
                        <img :src="orderInfo.qr" class="qrcode-img" v-if="orderInfo.qr">
                        <img src="../../static/img/loading.gif" class="qrcode-img-loading" v-if="!orderInfo.qr">
                    </div>
                </div>
                <!-- 支付超时 -->
                <div v-if="payStatus=='overtime'">
                    <p class="icon-box">
                        <img src="../../static/img/overtime-info-icon.png" alt="" class="info-icon">
                    </p>
                    <p class="information">抱歉，超时未支付</p>
                    <div class="repay-box">
                        <p class="repay-btn" @click="rePay">重新支付</p>
                    </div>
                </div>
                <!-- 支付成功 -->
                <!-- <div v-if="payStatus=='paySuccess'">
                    <p class="success-box">
                        <img src="../../static/img/paysuccess-info-icon.png" alt="" class="info-icon">
                    </p>
                    <p class="success-info">支付成功</p>
                    <p class="auto-deal">系统正在处理...</p>
                </div> -->
                <!-- 交易成功 -->
                <!-- <div v-if="payStatus=='reversionSuccess'">
                    <p class="success-box">
                        <img src="../../static/img/paysuccess-info-icon.png" alt="" class="info-icon">
                    </p>
                    <p class="success-info">交易成功</p>
                </div> -->
                <!-- 交易失败 -->
                <div v-if="payStatus=='reversionFailed'">
                    <p class="icon-box">
                        <img src="../../static/img/overtime-info-icon.png" alt="" class="info-icon">
                    </p>
                    <p class="information">抱歉，交易失败</p>
                    <!-- <p class="auto-deal">失败原因：取号失败</p> -->
                    <!-- <div class="repay-box-sp">
                        <p class="repay-btn" @click="rePay">重新支付</p>
                    </div> -->
                </div>
            </div>
        </div>
        <!-- 银行卡支付 -->
        <div v-if="payway=='bankpay'">
            <div class="title">
                <p>银联支付</p>
                <div class="count-time">{{countTime}}</div>
            </div>
            <div class="demonstration-box">
                <p class="demonstration-title">请插入您的银行卡</p>
                <p class="demonstration-title-img">
                    <img src="../../static/img/read-bank-card.png" alt="" class="bank-img">
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
                infoNotice: '',
                unLoaded: false,
                loadingText: "数据加载中...",
                districtInfo: undefined,
                payway: '', //支付方式
                payData: '', //下单的数据
                orderInfo: '', //下单后的订单信息
                payStatus: 'unpay', //支付状态
                countTime: '',
                lostTime: '',
                reversionTime: '',
                fromName: '',
                payFunction: '',
                qrcodeInfo: '',
                isLockFail: false, //是否锁号失败

                userInfo: undefined,
                hospitalInfo: undefined,

                // 可支付时间配置
                paytimeConfig:{
                    lockTime: '',
                    payTime:''
                }

            }
        },
        created: function () {
            this.payway = this.$route.params.payway;
            this.fromName = this.$route.params.fromName;
            this.payData = JSON.parse(this.$route.params.payData);
            this.districtInfo = JSON.parse(localStorage.getItem('districtInfo'));
            this.userInfo = JSON.parse(localStorage.getItem('userInfo'));
            this.hospitalInfo = JSON.parse(localStorage.getItem('hospitalInfo'))
            console.log(this.districtInfo)

            // 可支付时间配置
            this.paytimeConfig = {
                lockTime: parseInt(this.hospitalInfo.serviceConf.registration.DaiZhiFuShiChang),
                payTime: parseInt(this.hospitalInfo.serviceConf.registration.KeZhiFuShiChang)
            }


            console.log(this.payData)
            //调用下单函数
            this.orderOrigin(this.fromName, this.payData, this.payway);
            //支付倒计时
            this.timer = setInterval(() => {
                if (this.countTime != '' && this.lostTime != '') {
                    this.countTime--;
                    this.lostTime--;
                    if (this.countTime > 0) {
                        this.payStatus = 'unpay';
                        if (this.countTime % 3 === 0) {
                            this.searchOrigin(this.fromName, this.orderInfo.orderNum);
                        }
                    } else if (this.countTime === 0) {
                        this.countTime = 0;
                        this.payStatus = 'overtime';
                    } else if (this.countTime < 0 && this.lostTime > 0) {
                        this.payStatus = 'overtime';
                        this.countTime = 0;
                    } else if (this.lostTime <= 0) {
                        this.lostTime = 0;
                        this.countTime = 0;
                        this.payStatus = 'overtime';
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
                        this.payStatus = 'reversionFailed';
                        setTimeout(() => {
                            this.gotoPrint();
                        }, 3000)
                    }
                }
            }, 1000)
        },

        methods: {
            // 去打印
            gotoPrint: function(){
                this.$router.push({
                    name: 'printPage',
                    params: {
                        orderNum: this.orderInfo.orderNum,
                        num: 0
                    }
                })
            },

            //挂号下单
            placeRegOrder: function (payData, payway) {
                this.unLoaded = true;
                this.loadingText = '订单生成中，请稍候...';
                let obj = {
                    cardId: this.userInfo.cardId,
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

                if (this.hospitalInfo.hospital_id === "61756") {
                    obj.userName= this.userInfo.cardName;
                    obj.phone=this.userInfo.phone;
                }

                if(this.userInfo.UserIdKey){
                    obj.uniqueId = this.userInfo.UserIdKey
                }

                this.postData(this.hospitalInfo.number, obj,
                    '/registration/lock', (res) => {
                        this.unLoaded = false;
                        if (res.data.code == 10000) {
                            //播放锁号成功，提醒支付语音

                            this.$audioPlay(4);

                            this.getQrcode(payway, res.data.data.orderNum, 'register');
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
            publicPlaceOrder: function (payData, obj, payway, url) {
                this.unLoaded = true;
                this.loadingText = '订单生成中，请稍候...';
                let _obj = obj;
                _obj.cardId = this.userInfo.cardId;
                _obj.payType = this.$route.params.payType;
                
                if(this.userInfo.UserIdKey){
                    _obj.uniqueId = this.userInfo.UserIdKey
                }
                this.postData(this.hospitalInfo.number, _obj,
                    url, (res) => {
                        this.unLoaded = false;
                        if (res.data.code == 10000) {
                            //播放锁号成功，提醒支付语音

                            this.$audioPlay(4);

                            this.getQrcode(payway, res.data.data.orderNum, 'other');

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
            //取号下单
            placeRevOrder: function (payData, payway) {
                let obj = {
                    scheduleCode: payData.scheduleCode
                }
                this.publicPlaceOrder(payData, obj, payway, '/registration/order/fetch');
            },
            //门诊缴费下单
            placeOutOrder: function (payData, payway) {
                let obj = {
                    recipeId: payData.recipeId,
                    orderNumber: payData.orderNumber
                }
                this.publicPlaceOrder(payData, obj, payway, '/payment/order/outpatient');
            },
            //住院预缴下单
            placeHosOrder: function (payData, payway) {
                let obj = {
                    treatNo: payData.treatNo,
                    fee: payData.fee
                }
                this.publicPlaceOrder(payData, obj, payway, '/payment/order/inpatient');
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
                            //显示LOADING: "系统正在处理，请耐心等待…"
                            this.unLoaded = true;
                            this.loadingText = '系统正在处理，请耐心等待…';

                            this.payStatus = 'paySuccess';
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
                            this.payStatus = 'reversionSuccess';
                            this.loadingText = '交易成功，将为您打印小票…';
                            clearInterval(this.timer);
                            setTimeout(() => {
                                this.unLoaded = false;
                                this.gotoPrint();
                            }, 3000)
                        } else if (res.data.data.payStatus >= 1) {
                            this.$audioPlay(34);
                            this.payStatus = 'reversionFailed';
                            this.loadingText = '交易失败，将为您打印小票…';
                            clearInterval(this.timer);
                            setTimeout(() => {
                                //播放取号失败语音
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
            rePay: function () {
                //判断原路由来源，重新支付后跳转到来源页面
                if (this.fromName == 'register') {
                    history.back(-2);
                    // this.$router.push({
                    //     name: 'selectDoctor',
                    //     params: {
                    //         deptId: this.payData.deptId
                    //     }
                    // })
                } else {
                    history.back(-1);
                }
            },
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
            //根据路由名称判断订单来源，调用不同的下单方法
            orderOrigin: function (fromName, payData, payway) {
                if (fromName == 'register') { //挂号下单
                    this.placeRegOrder(payData, payway);
                } else if (fromName == 'reversionNumList') { //取号下单
                    this.placeRevOrder(payData, payway);
                } else if (fromName == 'outpatient') { //门诊缴费下单
                    this.placeOutOrder(payData, payway);
                } else if (fromName == 'hospitalizationDetail') { //住院预缴下单
                    this.placeHosOrder(payData, payway);
                }
            },
            //根据路由名称判断订单来源，调用不同的支付状态查询方法
            searchOrigin: function (fromName, orderNum) {
                if (fromName == 'register') {
                    this.publicSearchPayStatus(orderNum, '/query/registration');
                } else if (fromName == 'reversionNumList') {
                    this.publicSearchPayStatus(orderNum, '/query/fetch');
                } else if (fromName == 'outpatient') {
                    this.publicSearchPayStatus(orderNum, '/query/outpatient');
                } else if (fromName == 'hospitalizationDetail') {
                    this.publicSearchPayStatus(orderNum, '/query/inpatient');
                }
            },
            //隐藏提醒弹框
            hideBox: function (selector) {
                this.infoBomb = false;
                // 挂号锁号失败
                if(this.isLockFail){
                    history.back(-3);
                    // this.$router.push({
                    //     name: "selectDoctor",
                    //     params: {
                    //         deptId: this.payData.deptId,
                    //         dateTime: this.payData.date,
                    //         period: this.payData.period
                    //     }
                    // });
                }
                
            },
            //根据所选的支付方式调用支付宝或微信二维码
            getQrcode: function (payway, orderNum, type) {
                if (payway == 'wechatpay') {
                    this.publicGetQrcode(orderNum, '/order/qr/wechat', type);
                } else if (payway == 'alipay') {
                    this.publicGetQrcode(orderNum, '/order/qr/alipay', type);
                }
            },
            //获取微信或支付宝支付二维码
            publicGetQrcode: function (orderNum, url, type) {

                // let curSecond = parseInt(new Date().getTime() / 1000);
                
                this.$http.get(this.publicUrl + url, {
                    params: {
                        orderNum: orderNum,
                        timeStr: new Date().getTime()
                    },
                    headers: this.config(this.hospitalInfo.number),
                    timeout: 10000
                }).then((res) => {
                    if (res.data.code == 10000) {
                        this.orderInfo = res.data.data;
                        var curSecond = this.orderInfo.now_time;
                        
                        //挂号的支付逻辑
                        if (type == 'register') {
                            
                            //判断是否锁号，若未锁号，则倒计时重置为150秒，已锁号，根据当前时间与锁号时间差确定显示时间
                            // console.log(parseInt(this.orderInfo.create_time))
                            // console.log(parseInt(this.orderInfo.create_time)+ 150)
                            // console.log(curSecond)
                            // console.log(parseInt(this.orderInfo.create_time) + 150 - curSecond);
                            
                            let createTime = parseInt(this.orderInfo.create_time);
                            if(this.hospitalInfo.hospital_id=='10000'){
                                this.countTime = createTime + this.paytimeConfig.payTime - curSecond >= this.paytimeConfig.payTime ? this.paytimeConfig.payTime : createTime + this.paytimeConfig.payTime - curSecond;
                            }
                            if(this.hospitalInfo.hospital_id=='61754' || this.hospitalInfo.hospital_id=='61756' || this.hospitalInfo.hospital_id=='61757'){
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

                    }
                }).catch((err) => {

                    this.$audioPlay(21);

                    this.dealError();
                })
            },
            

        },
        destroyed: function () {
            clearInterval(this.timer);
        }
    }
</script>
<style scoped>
    .demonstration-box {
        width: 1125px;
        height: 600px;
        border-radius: 10px;
        border: 1px solid #d2d5d5;
        margin-left: 35px;
        overflow: hidden;
    }

    .pay-box {
        width: 960px;
        height: 500px;
        margin: 47px 0 0 66px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .pay-box-item {
        width: 222px;
        height: 100%;
        overflow: hidden;
    }

    .pay-box-item-header {
        width: 100%;
        height: 95px;
        display: flex;
        align-items: center;
        justify-content: flex-start;
        font-size: 24px;
        color: #029b44;
    }

    .pay-box-item-qrcode {
        width: 270px;
        height: 100%;
        overflow: hidden;
    }

    .pay-box-item-header-qrcode {
        width: 100%;
        height: 95px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 32px;
        color: #029b44;
    }

    .step {
        width: 50px;
        height: 50px;
        border-radius: 50px;
        border: 2px solid #029b44;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 20px;
    }

    .demonstration-img {
        width: 100%;
        height: 373px;
        display: block;
        margin-top: 30px;
    }

    .qrcode-img {
        width: 270px;
        height: 270px;
        margin-top: 60px;
        display: block;
    }

    .qrcode-img-loading {
        width: 60px;
        height: 60px;
        margin-left: 105px;
        margin-top: 140px;
        display: block;
    }

    .demonstration-title {
        width: 100%;
        height: 170px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 38px;
        margin: 0;
    }

    .demonstration-title-img {
        width: 100%;
        text-align: center;
    }

    .bank-img {
        width: 360px;
        height: 390px;
    }

    .icon-box {
        width: 100%;
        text-align: center;
        margin-top: 115px;
        overflow: hidden;
    }

    .info-icon {
        width: 125px;
        height: 125px;
        border-radius: 100%;
    }

    .information {
        width: 100%;
        text-align: center;
        font-size: 46px;
        color: #fe0403;
        margin-top: 60px;
        overflow: hidden;
    }

    .repay-box {
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-top: 135px;
        overflow: hidden;
    }

    .repay-box-sp {
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-top: 70px;
        overflow: hidden;
    }

    .repay-btn {
        width: 165px;
        height: 54px;
        border-radius: 8px;
        background: #018ede;
        font-size: 26px;
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
    }

    .success-box {
        width: 100%;
        text-align: center;
        margin-top: 125px;
        overflow: hidden;
    }

    .success-info {
        width: 100%;
        text-align: center;
        font-size: 46px;
        color: #009942;
        margin-top: 55px;
    }

    .auto-deal {
        width: 100%;
        text-align: center;
        font-size: 34px;
        margin-top: 25px;
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

    .sp {
        background: #787f82;
    }
</style>