<template>
    <div class="content" v-cloak>
        <Loading v-if="unLoaded" :title="loadingText"></Loading>
        <Dialog v-if="infoBomb" :text="infoNotice" :buttonconfig="buttonconfig" @hideBox="hideBox"></Dialog>
        <div class="title">
            <p>请支付订单</p>
            <div class="count-time" v-if="countTime!=''">{{countTime}}</div>
            <div class="count-time" v-if="reversionTime!=''">{{reversionTime}}</div>
        </div>
        <div class="register-info">
            <div class="detail-info">
                <p class="info-title">{{curTitle}}</p>
                <img src="../../static/img/left-line.png" class="left-line">
                <div class="userinfo-box" v-if="routerName=='register'">
                    <div class="userinfo-item">
                        <p class="userinfo-item-title">
                            <span>就</span>
                            <span>诊</span>
                            <span>人</span>
                            <span>：</span>
                        </p>
                        <p>{{userInfo.cardName}}</p>
                    </div>
                    <div class="userinfo-item">
                        <p class="userinfo-item-title">
                            <span>就</span>
                            <span>诊</span>
                            <span>卡</span>
                            <span>号</span>
                            <span>：</span>
                        </p>
                        <p>{{userInfo.cardId}}</p>
                    </div>
                    <div class="userinfo-item"
                         v-if="districtInfo && hospitalInfo.hospital_id != '61757' && hospitalInfo.hospital_id != '61759' && hospitalInfo.hospital_id != '61760'">
                        <p class="userinfo-item-title">
                            <span>院</span>
                            <span>区</span>
                            <span>：</span>
                        </p>
                        <p>{{districtInfo?districtInfo.districtName:''}}</p>
                    </div>
                    <div class="userinfo-item">
                        <p class="userinfo-item-title">
                            <span>医</span>
                            <span>生</span>
                            <span>：</span>
                        </p>
                        <p>{{registerInfo.doctorName}}</p>
                    </div>
                    <div class="userinfo-item">
                        <p class="userinfo-item-title">
                            <span>科</span>
                            <span>室</span>
                            <span>：</span>
                        </p>
                        <p>{{registerInfo.deptName}}</p>
                    </div>
                    <div class="userinfo-item">
                        <p class="userinfo-item-title">
                            <span>就</span>
                            <span>诊</span>
                            <span>时</span>
                            <span>间</span>
                            <span>：</span>
                        </p>
                        <p>{{registerInfo.date}}
                            <span v-if="registerInfo.period=='am'">上午</span>
                            <span v-if="registerInfo.period=='pm'">下午</span>
                            <span v-if="registerInfo.period=='npm'">晚间</span>
                            <span v-if="registerInfo.period=='all'">全天</span>
                        </p>
                    </div>
                    <div class="userinfo-item">
                        <p class="userinfo-item-title">
                            <span>缴</span>
                            <span>费</span>
                            <span>金</span>
                            <span>额</span>
                            <span>：</span>
                        </p>
                        <p class="sp">￥{{registerInfo.fee}}</p>
                    </div>
                </div>
                <div class="userinfo-box" v-if="routerName=='outpatient'">
                    <div class="userinfo-item-sp">
                        <p class="userinfo-item-title">
                            <span>就</span>
                            <span>诊</span>
                            <span>人</span>
                            <span>：</span>
                        </p>
                        <p>{{userInfo.cardName}}</p>
                    </div>
                    <div class="userinfo-item-sp">
                        <p class="userinfo-item-title">
                            <span>就</span>
                            <span>诊</span>
                            <span>卡</span>
                            <span>号</span>
                            <span>：</span>
                        </p>
                        <p>{{userInfo.cardId}}</p>
                    </div>
                    <div class="userinfo-item-sp">
                        <p class="userinfo-item-title">
                            <span>缴</span>
                            <span>费</span>
                            <span>金</span>
                            <span>额</span>
                            <span>：</span>
                        </p>
                        <p class="sp">￥{{registerInfo.fee}}</p>
                    </div>
                </div>
                <div class="userinfo-box" v-if="routerName=='hospitalizationDetail'">
                    <div class="userinfo-item-sp">
                        <p class="userinfo-item-title">
                            <span>就</span>
                            <span>诊</span>
                            <span>人</span>
                            <span>：</span>
                        </p>
                        <p>{{userInfo.cardName}}</p>
                    </div>
                    <div class="userinfo-item-sp">
                        <p class="userinfo-item-title">
                            <span>就</span>
                            <span>诊</span>
                            <span>卡</span>
                            <span>号</span>
                            <span>：</span>
                        </p>
                        <p>{{userInfo.cardId}}</p>
                    </div>
                    <div class="userinfo-item-sp">
                        <p class="userinfo-item-title">
                            <span>缴</span>
                            <span>费</span>
                            <span>金</span>
                            <span>额</span>
                            <span>：</span>
                        </p>
                        <p class="sp">￥{{registerInfo.fee}}</p>
                    </div>
                </div>
            </div>
            <div class="detail-info">
                <p class="info-title">{{paywayConfig[payway]}}</p>
                <img src="../../static/img/right-line.png" class="right-line">
                <div class="payway-box">
                    <img :src="orderInfo.qr" class="qrcode-img" v-if="orderInfo.qr">
                    <img src="../../static/img/loading.gif" class="qrcode-img-loading" v-if="!orderInfo.qr">
                </div>
            </div>
        </div>
        <!-- 支付超时 -->
        <div class="payTimeOut_box" v-if="payStatus=='overtime'">
            <div>
                <p class="icon-box">
                    <img src="../../static/img/overtime-info-icon.png" class="info-icon">
                </p>
                <p class="information">抱歉，超时未支付</p>
                <div class="repay-box">
                    <p class="repay-btn" @click="rePay">重新支付</p>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
    export default {
        data() {
            return {
                unLoaded: false,
                loadingText: "数据加载中...",
                userInfo: {},
                registerInfo: {},
                districtInfo: undefined,
                curTitle: '',
                fromName: '',
                routerName: 'register',
                infoBomb: false,
                infoNotice: '',
                hospitalInfo: {},
                payway: '', //支付方式
                payData: '', //下单的数据
                orderInfo: '', //下单后的订单信息
                payStatus: 'unpay', //支付状态
                countTime: '',
                lostTime: '',
                reversionTime: '',
                payFunction: '',
                qrcodeInfo: '',
                orderId: '',
                isLockFail: false, //是否锁号失败
                buttonconfig: ['知道了'],

                // 支付方式静态配置
                paywayConfig: {
                    '1': '微信“扫一扫”支付',
                    '2': '支付宝“扫一扫”支付',
                    '3': '刷银行卡支付'
                },

                // 可支付时间配置
                paytimeConfig: {
                    lockTime: '',
                    payTime: ''
                }

            }
        },
        created: function () {
            let _params = this.$route.params;
            this.payway = _params.payway;
            this.fromName = _params.fromName;
            this.payData = JSON.parse(_params.payData);
            this.registerInfo = this.payData;
            this.routerNameControll(this.fromName);

            this.userInfo = JSON.parse(localStorage.getItem('userInfo'));
            this.hospitalInfo = JSON.parse(localStorage.getItem('hospitalInfo'));
            this.districtInfo = JSON.parse(localStorage.getItem('districtInfo'));

            // 可支付时间配置
            this.paytimeConfig = {
                lockTime: parseInt(this.hospitalInfo.serviceConf.registration.DaiZhiFuShiChang),
                payTime: parseInt(this.hospitalInfo.serviceConf.registration.KeZhiFuShiChang)
            }

            this.orderOrigin(this.fromName, this.payData, this.getPayWayTitle(this.payway));

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
            gotoPrint: function () {
                this.$router.push({
                    name: 'printPage',
                    params: {
                        orderNum: this.orderInfo.orderNum,
                        num: 0
                    }
                })
            },

            // 去支付
            getPayWayTitle: function (mod) {

                /**
                 * 支付方式枚举值:
                 * 1: 微信扫码支付
                 * 2: 支付宝扫码支付
                 * 3: 银联支付
                 */

                let paywayTitle = '';

                switch (mod) {
                    case '1':
                        paywayTitle = 'wechatpay';
                        break;
                    case '2':
                        paywayTitle = 'alipay';
                        break;
                    case '3':
                        paywayTitle = 'bankpay';
                        break;
                }

                return paywayTitle;
            },

            //根据页面来源显示不同的页面标题
            routerNameControll: function (fromName) {
                switch (fromName) {
                    case 'register':
                        this.curTitle = '挂号信息';
                        this.routerName = 'register';
                        break;
                    case 'reversionNumList':
                        this.curTitle = '取号信息';
                        this.routerName = 'register';
                        break;
                    case 'outpatient':
                        this.curTitle = '门诊缴费信息';
                        this.routerName = 'outpatient';
                        break;
                    case 'hospitalizationDetail':
                        this.curTitle = '住院预缴信息';
                        this.routerName = 'hospitalizationDetail';
                }
            },

            //隐藏提醒弹框
            hideBox: function (selector) {
                this.infoBomb = false;
                // 挂号锁号失败
                if (this.isLockFail) {
                    history.back(-3);
                } else if (selector == 1 && this.payStatus === 'reversionSuccess') {
                    clearInterval(this.timer);
                    setTimeout(() => {
                        this.unLoaded = false;
                        clearInterval(this.timer);
                        this.$router.push({
                            name: 'doctorRate',
                            params: {
                                orderId: this.orderId,
                                orderNum: this.orderInfo.orderNum,
                                num: 0
                            }
                        })
                    }, 3000)

                } else if (selector === 0 && this.payStatus === 'reversionSuccess') {
                    this.loadingText = '交易成功，将为您打印小票…';
                    clearInterval(this.timer);
                    setTimeout(() => {
                        this.unLoaded = false;
                        this.gotoPrint();
                    }, 3000)
                }
            },

            //挂号下单
            placeRegOrder: function (payData, payway) {
                this.unLoaded = true;
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
                    districtId: this.districtInfo ? this.districtInfo.districtId : '',
                    districtName: this.districtInfo ? this.districtInfo.districtName : ''
                }

                if (payData.beginTime && payData.endTime) {
                    obj.beginTime = payData.beginTime;
                    obj.endTime = payData.endTime;
                }
                if (this.hospitalInfo.hospital_id === "61757") {
                    obj.periodId = payData.periodId;
                }
                if (this.hospitalInfo.hospital_id === "61756") {
                    obj.userName = user_info.cardName;
                    obj.phone = user_info.phone;
                    obj.sqno = payData.sqno;
                }

                if (user_info.UserIdKey) {
                    obj.uniqueId = user_info.UserIdKey
                }
                this.postData(JSON.parse(localStorage.getItem('hospitalInfo')).number, obj,
                    '/registration/lock', (res) => {
                        this.unLoaded = false;
                        if (res.data.code == 10000) {
                            //播放锁号成功，提醒支付语音

                            this.$audioPlay(4);
                            this.orderId = res.data.data.orderId;
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
                _obj.cardId = JSON.parse(localStorage.getItem('userInfo')).cardId;
                _obj.payType = this.$route.params.payType;
                if (this.userInfo.UserIdKey) {
                    _obj.uniqueId = this.userInfo.UserIdKey
                }
                this.postData(JSON.parse(localStorage.getItem('hospitalInfo')).number, _obj,
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
                    headers: this.config(JSON.parse(localStorage.getItem('hospitalInfo')).number),
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
                            // 就医评价逻辑
                            // this.$audioPlay(33);
                            // this.payStatus = 'reversionSuccess';
                            // this.infoBomb = true;
                            // this.infoNotice = '是否就医评价';
                            // this.buttonconfig = ['取消', '确定'];
                        } else if (res.data.data.payStatus >= 1) {
                            this.$audioPlay(34);
                            this.payStatus = 'reversionFailed';
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
            rePay: function () {
                //判断原路由来源，重新支付后跳转到来源页面
                if (this.fromName == 'register') {
                    history.back(-2);
                } else {
                    history.back(-1);
                }
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

            //获取微信或支付宝支付二维码
            publicGetQrcode: function (orderNum, url, type) {
                // let curSecond = parseInt(new Date().getTime() / 1000);

                this.$http.get(this.publicUrl + url, {
                    params: {
                        orderNum: orderNum,
                        timeStr: new Date().getTime()
                    },
                    headers: this.config(JSON.parse(localStorage.getItem('hospitalInfo')).number),
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

                            if (this.hospitalInfo.hospital_id == '10000') {
                                this.countTime = createTime + this.paytimeConfig.payTime - curSecond >= this.paytimeConfig.payTime ? this.paytimeConfig.payTime : createTime + this.paytimeConfig.payTime - curSecond;
                            }
                            if (this.hospitalInfo.hospital_id == '61754' || this.hospitalInfo.hospital_id == '61756' || this.hospitalInfo.hospital_id == '61757' || this.hospitalInfo.hospital_id == '61759' || this.hospitalInfo.hospital_id == '61760') {
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
            //根据所选的支付方式调用支付宝或微信二维码
            getQrcode: function (payway, orderNum, type) {
                if (payway == 'wechatpay') {
                    this.publicGetQrcode(orderNum, '/order/qr/wechat', type);
                } else if (payway == 'alipay') {
                    this.publicGetQrcode(orderNum, '/order/qr/alipay', type);
                }
            }

        },
        destroyed: function () {
            clearInterval(this.timer)
        }
    }
</script>
<style scoped>

    .register-info {
        width: 1135px;
        height: 590px;
        margin-left: 35px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: url('../../static/img/payway-bg.png') no-repeat;
        background-size: 100% 100%;
    }

    .detail-info {
        width: 50%;
        height: 100%;
        overflow: hidden;
    }

    .info-title {
        width: 100%;
        height: 100px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 34px;
    }

    .left-line {
        width: 494px;
        height: 6px;
        display: block;
    }

    .userinfo-box {
        width: 450px;
        margin-left: 45px;
        font-size: 30px;
        overflow: hidden;
    }

    .userinfo-item {
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: flex-start;
        margin-top: 10px;
    }

    .userinfo-item-sp {
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: flex-start;
        margin-top: 90px;
    }

    .userinfo-item-title {
        width: 150px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .sp {
        color: #e20104;
    }

    .right-line {
        width: 538px;
        height: 6px;
        display: block;
    }

    .payway-box {
        display: flex;
        height: 420px;
        align-items: center;
        justify-content: center;
    }

    .qrcode-img-loading {
        width: 60px;
    }

    .qrcode-img {
        width: 270px;
        height: 270px;
    }

    .payTimeOut_box {
        position: fixed;
        background-color: rgba(0, 0, 0, .4);
        z-index: 1000;
        width: 100%;
        height: 100%;
        top: 0;
        left: 0;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .payTimeOut_box > div {
        background-color: #fff;
        border-radius: 6px;
        width: 760px;
        padding: 60px 0;
    }

    .icon-box {
        width: 100%;
        text-align: center;
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
        margin-top: 30px;
        overflow: hidden;
    }

    .repay-box {
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-top: 100px;
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


</style>