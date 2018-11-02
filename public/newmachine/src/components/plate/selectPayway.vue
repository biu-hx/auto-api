<template>
    <div class="content" v-cloak>
        <Loading v-if="isLoading" :title="loadingText"></Loading>
        <Dialog v-if="infoBomb" :text="infoNotice" :buttonconfig="['知道了']" @hideBox="hideBox"></Dialog>

        <div class="title">
            <p>请确认您的信息及选择支付方式</p>
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
                    <div class="userinfo-item" v-if="districtInfo">
                        <p class="userinfo-item-title">
                            <span>院</span>
                            <span>区</span>
                            <span>：</span>
                        </p>
                        <p>{{districtInfo.districtName}}</p>
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
                <p class="info-title">选择支付方式</p>
                <img src="../../static/img/right-line.png" class="right-line">
                <div class="payway-box" v-if="paytypeArray && paytypeArray.length > 0">
                    <div class="payway-item" v-for="item in paytypeArray"
                         @click="selectThisPayMod(item.pay_type, item.id)">
                        <img :src="item.icon" class="payway-icon">
                        <div class="payway-item-box">
                            <p class="payway-item-title">{{item.name}}</p>
                            <p class="payway-item-info">{{paywayConfig[item.pay_type]}}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
    import {getPayType} from '../../components_regional/api/common'

    export default {
        data() {
            return {
                isLoading: false,
                loadingText: "数据加载中...",
                userInfo: {},
                registerInfo: {},
                curTitle: '',
                fromName: '',
                routerName: 'register',
                infoBomb: false,
                infoNotice: '',
                hospitalInfo: {},
                printDeviceStatus: '',
                paytypeArray: undefined,
                districtInfo: undefined,

                // 支付方式静态配置
                paywayConfig: {
                    '1': "使用手机微信APP扫码支付",
                    '2': "使用支付宝APP扫码支付",
                    '3': "使用银行卡余额进行支付"
                },

                agent: this.$agent()
            }
        },
        created: function () {
            this.fromName = this.$route.params.fromName;
            this.userInfo = JSON.parse(localStorage.getItem('userInfo'));
            this.hospitalInfo = JSON.parse(localStorage.getItem('hospitalInfo'));
            this.districtInfo = JSON.parse(localStorage.getItem('districtInfo'));

            this.registerInfo = JSON.parse(this.$route.params.registerInfo);
            this.routerNameControll(this.fromName);


            if (this.agent == "Windows") {
                let intSatus = this.$machineApi.win_printerInt();
                if (intSatus != 0 || intSatus == "error") {
                    this.isLoading = false;
                    this.infoBomb = true;
                    this.infoNotice = "打印机设备初始化失败";
                    return;
                }
            }


            // 获取支付方式
            this.getPayWayData();

        },
        methods: {
            // 获取支付方式
            getPayWayData: function () {
                this.isLoading = true;

                getPayType({
                    business: this.$route.params.business ? this.$route.params.business : 2,
                }).then(res => {
                    if (res.code == 10000) {
                        this.paytypeArray = res.data;

                        if (this.paytypeArray.length > 1) {
                            // 请选择支付方式语音

                            this.$audioPlay(23);

                        } else {
                            if (this.checkPrintStatus()) {
                                this.$router.replace({
                                    name: 'confirmAndPay',
                                    params: {
                                        payway: this.paytypeArray[0].pay_type,
                                        payType: this.paytypeArray[0].id,
                                        payData: JSON.stringify(this.registerInfo)
                                    }
                                })
                            }
                        }
                    } else {
                        this.dealError("获取支付方式失败");
                    }
                    this.isLoading = false
                })
            },

            // 检测打印机状态
            checkPrintStatus: function () {
                if (this.agent == "Android") {

                    // 获取打印机状态
                    this.printDeviceStatus = this.$machineApi.getMachine_printStatus();

                    if (this.printDeviceStatus == '0') {
                        // 正常
                        return true;

                    } else if (this.printDeviceStatus == '-1' || this.printDeviceStatus == '-2') {
                        // 缺纸 或 异常
                        this.showDailogModel(this.printDeviceStatus);
                        return false;
                    }

                } else if (this.agent == "Windows") {

                    // 获取打印机状态

                    this.printDeviceStatus = this.$machineApi.win_printerStatus();

                    if (this.printDeviceStatus == '打印机正常') {
                        // 正常
                        return true;

                    } else {
                        // 缺纸 或 异常
                        this.showDailogModel("-1");
                        return false;
                    }

                } else {
                    return true;
                }
            },

            // 选择当前支付方式
            selectThisPayMod: function (mod, id) {

                if (this.checkPrintStatus()) {
                    this.gotoPay(mod, id);
                }

            },

            // 提示打印机异常
            showDailogModel: function (status) {
                let servicePhone = this.hospitalInfo.servicePhone ? this.hospitalInfo.servicePhone : '--';
                let msgText = (status == '-1') ? "本机纸张已用完啦" : "本机打印机设备异常";

                this.infoBomb = true;
                this.infoNotice = '<p style="text-align:left; font-size:46px;">' + msgText + '，暂停交易，请前往其它自助机上缴费！<p>'
                    + '<p style="text-align:left; font-size:32px;">或等换纸后在本自助机上补打交易小票<br/>现场客服电话：' + servicePhone + '<p>';
            },

            // 去支付
            gotoPay: function (mod, id) {

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
                if (mod == "3" && this.agent == "Windows") {
                    console.log(paywayTitle, id, JSON.stringify(this.registerInfo))
                    this.$router.push({
                        name: 'payBank_win',
                        params: {
                            payway: paywayTitle,
                            payType: id,
                            payData: JSON.stringify(this.registerInfo)
                        }
                    })
                } else {
                    this.$router.push({
                        name: 'pay',
                        params: {
                            payway: paywayTitle,
                            payType: id,
                            payData: JSON.stringify(this.registerInfo)
                        }
                    })
                }

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

                this.$router.push({
                    name: 'home'
                })

            },

        },
        destroyed: function () {

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
        margin-top: 20px;
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
        width: 460px;
        margin-left: 45px;
        font-size: 30px;
    }

    .payway-item {
        width: 99%;
        height: 120px;
        margin-top: 30px;
        border-radius: 5px;
        display: flex;
        align-items: center;
        justify-content: flex-start;
        cursor: pointer;
        border-width: 2px;
        border-style: solid;
        animation: myfirst 1.2s linear infinite;
        -webkit-animation: myfirst 1.2s linear infinite;
    }

    @keyframes myfirst {
        0% {
            background-color: #ffffff;
            border-color: #dddddd;
            box-shadow: 0 0 10px rgba(0, 0, 0, .3);
        }
        50% {
            background-color: #E0F0F5;
            border-color: #0893E7;
            box-shadow: 0 0 10px rgba(8, 147, 231, .5);
        }
        100% {
            background-color: #ffffff;
            border-color: #dddddd;
            box-shadow: 0 0 10px rgba(0, 0, 0, .3);
        }
    }

    @-webkit-keyframes myfirst {
        0% {
            background-color: #ffffff;
            border-color: #dddddd;
            box-shadow: 0 0 10px rgba(0, 0, 0, .3);
        }
        50% {
            background-color: #E0F0F5;
            border-color: #0893E7;
            box-shadow: 0 0 10px rgba(8, 147, 231, .5);
        }
        100% {
            background-color: #ffffff;
            border-color: #dddddd;
            box-shadow: 0 0 10px rgba(0, 0, 0, .3);
        }
    }

    .payway-icon {
        width: 88px;
        height: 88px;
        margin-left: 20px;
        margin-right: 25px;
    }

    .payway-item-box {
        overflow: hidden;
        height: 88px;
    }

    .payway-item-title {
        font-size: 34px;
        font-weight: bold;
    }

    .payway-item-info {
        font-size: 26px;
    }
</style>