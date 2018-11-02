<template>
    <div v-cloak>
        <Loading v-if="unLoaded" :title="loadingText"></Loading>
        <Dialog v-if="infoBomb" :text="infoNotice" :buttonconfig="buttonconfig" @hideBox="hideBox"></Dialog>

        <div class="content">
            <div class="title">
                <p>就医评价</p>
            </div>
            <div class="rateContent">
                <p class="printStatus"><span class="hintPrintStatus">{{printTitle}}！</span>您可以对我们的服务进行评价...</p>
                <p class="rateDesc">服务星级评价：一星糟糕，两星不满意，三星一般，四星好，五星很棒</p>
                <div>
                    <div class="form-item">
                        <span class="form-title">服务星级：</span>
                        <div class="rate-wrap">
                            <em @click="selectRate(i)" class="rate-icon" :class="i>activeIndex?'':'active'"
                                v-for="i in 5"></em>
                        </div>
                    </div>
                    <div class="form-item">
                        <span class="form-title">意见建议：</span>
                        <textarea v-model="content" class="form-text"></textarea>
                    </div>
                    <div class="operation-btn">
                        <div class="submit-btn" @click="submitRate">提交</div>
                    </div>

                </div>
            </div>
            <div class="model-frame" v-if="rateStatus">
                <!-- 交易成功 -->
                <div class="box" style="width: 730px;">
                    <p class="success-box">
                        <img src="../../static/img/paysuccess-info-icon.png" alt="" class="info-icon">
                    </p>
                    <p class="success-info">提交成功</p>
                    <p class="rate-success-info">感谢您的宝贵意见</p>
                </div>

            </div>
        </div>
    </div>

</template>

<script>
    export default {
        name: "doctorRate",
        data() {
            return {
                unLoaded: false,
                loadingText: "正在检测打印机...",
                infoBomb: false,
                infoNotice: "",
                buttonconfig: ['知道了'],

                printTitle: '正在打印小票',
                printStatus: "printing",
                orderDetail: {},

                rateStatus: false,
                activeIndex: false,
                content: '',

                orderId: '',//订单ID
                orderNum: '', //订单号
                num: undefined, //打印次数
                printDeviceStatus: '',//安卓打印机状态

                agent: this.$agent(),
                hospitalInfo: undefined,
            }
        },
        created() {
            this.unLoaded = true;
            // 路由参数
            this.orderNum = this.$route.params.orderNum;
            this.orderId = this.$route.params.orderId;
            this.num = this.$route.params.num;

            this.hospitalInfo = JSON.parse(localStorage.getItem('hospitalInfo'));
            //不同机型 调用不同打印设备
            if (this.agent == "Windows") {
                this.$router.replace({
                    name: 'doctorRate_win',
                    params: {
                        orderNum: this.orderNum,
                        orderId: this.orderId,
                        num: this.num
                    }
                })
            } else {
                let servicePhone = this.hospitalInfo.servicePhone ? this.hospitalInfo.servicePhone : '--';
                // 获取安卓打印机状态
                this.printDeviceStatus = this.dsBridge.call("getPrintStatus", {msg: 'getPrintStatus'});
                if (this.printDeviceStatus == '0') {
                    // 正常
                    this.loadingText = '正在加载数据...',

                        // 播放打印小票语音

                        this.$audioPlay(7);

                    // 查询数据
                    this.searchDetailOrder(this.orderNum);

                } else if (this.printDeviceStatus == '-1') {

                    //缺纸
                    this.unLoaded = false;

                    this.infoBomb = true;
                    this.infoNotice = '<p style="text-align:left; font-size:46px;">打印机缺纸，无法打印小票，请于其他机器上补打小票；<p>'
                        + '<p style="text-align:left; font-size:32px;">流程：其他壁挂式缴费机点击<span style="color:red">【补打小票】</span>-刷就诊卡-选择交易记录-补打小票；<br/>现场客服电话：' + servicePhone + '<p>';

                } else if (this.printDeviceStatus == '-2') {

                    //异常
                    this.unLoaded = false;

                    this.infoBomb = true;
                    this.infoNotice = '<p style="text-align:left; font-size:46px;">打印机设备异常，无法打印小票，请于其他机器上补打小票；<p>'
                        + '<p style="text-align:left; font-size:32px;">流程：其他壁挂式缴费机点击<span style="color:red">【补打小票】</span>-刷就诊卡-选择交易记录-补打小票；<br/>现场客服电话：' + servicePhone + '<p>';

                }
            }
        },
        methods: {
            // 选择评分
            selectRate(index) {
                this.activeIndex = index;
            },
            // 提交评价
            submitRate() {
                const obj = {
                    orderId: this.orderId,
                    stars: this.activeIndex,
                    content: this.content,
                };
                this.postData(this.hospitalInfo.number, obj,
                    '/propaganda/evaluate', (res) => {
                        if (res.data.code === 10000) {
                            this.rateStatus = true;
                            setTimeout(() => {
                                this.rateStatus = false;
                                this.$router.push({
                                    name: 'home'
                                });
                            }, 3000)
                        }
                    })

            },
            //根据订单号查询交易详情
            searchDetailOrder: function (orderNum) {
                this.$http.get(this.publicUrl + '/query/order/detail', {
                    params: {
                        orderNum: orderNum,
                        timeStr: new Date().getTime()
                    },
                    headers: this.config(this.hospitalInfo.number),
                    timeout: 10000
                }).then((res) => {
                    this.unLoaded = false;
                    // 数据处理
                    if (res.data.code == 10000) {

                        this.orderDetail = res.data.data;
                        this.printTimes(orderNum);

                        //根据订单开头字段判断订单类型并将订单查询的内容传递给安卓打印
                        if (!this.isPc()) {
                            if (res.data.data.type == 2 || res.data.data.type == 3) {

                                this.dsBridge.call('printGuaHao', {msg: this.orderDetail, num: this.num});

                            } else if (res.data.data.type == 4) {

                                this.dsBridge.call('printJiaofei', {msg: this.orderDetail, num: this.num});

                            } else if (res.data.data.type == 5) {

                                this.dsBridge.call('printZhuYuan', {msg: this.orderDetail, num: this.num});

                            }
                        }

                        setTimeout(() => {
                            //6秒后，播放打印小票完成语音

                            this.$audioPlay(3);

                            //将打印状态变为已打印
                            this.printTitle = '打印完成，请取走小票';
                            this.printStatus = "printed";

                            // 交易失败
                            if (this.orderDetail.status == 2) {
                                var modName = "";
                                if (this.orderDetail.type == 2) {
                                    modName = "预约挂号"
                                }
                                else if (this.orderDetail.type == 4) {
                                    modName = "门诊缴费"
                                }
                                else if (this.orderDetail.type == 5) {
                                    modName = "住院缴费"
                                }

                                this.infoBomb = true;
                                this.infoNotice = '<p><span style="color:red">' + modName + '失败！</span>为了不影响您就诊，请重新交易；</p>'
                                    + '<p>交易失败的款项系统会在7个工作日内退回您的付款账户，请不必担心！</p>';
                            }

                        }, 6000);

                    } else {

                        if (this.filterASCII(res.data.data)) {
                            this.infoNotice = this.filterASCII(res.data.data);
                        } else {
                            this.infoNotice = res.data.data;
                        }
                        this.infoBomb = true;
                    }

                }).catch((err) => {
                    this.unLoaded = false;

                    this.$audioPlay(21);

                    this.dealError();
                })
            },

            //打印时调用打印小票的次数
            printTimes: function (orderNum) {
                this.$http.get(this.publicUrl + '/order/print', {
                    params: {
                        orderNum: orderNum,
                        timeStr: new Date().getTime()
                    },
                    headers: this.config(JSON.parse(localStorage.getItem('hospitalInfo')).number),
                    timeout: 10000
                }).then((res) => {

                }).catch((err) => {

                    this.$audioPlay(21);

                    this.dealError();
                })
            },
            // 关闭模态窗
            hideBox: function () {
                this.infoBomb = false;
            }
        },
        destroyed() {

        }

    }
</script>

<style scoped>
    .rateContent {
        width: 750px;
        margin: auto;
    }

    .printStatus {
        font-size: 36px;
        color: #000000;
    }

    .printStatus .hintPrintStatus {
        color: #f00202;
    }

    .rate-wrap {
        display: flex;
        align-items: center;
        justify-content: flex-start;
    }

    .rate-icon {
        width: 54px;
        height: 51px;
        margin-right: 30px;
        background: url("../../static/img/rate-icon.png") no-repeat 0 0;
    }

    .rate-icon.active {
        background-position: -56px 0;
    }

    .rate-icon:last-child {
        margin-right: 0;
    }

    .rateDesc {
        font-size: 24px;
        color: #888888;
        padding: 20px 0 50px;
    }

    .form-item {
        display: flex;
        justify-content: flex-start;
        align-items: flex-start;
        margin-bottom: 55px;
    }

    .form-title {
        width: 190px;
        font-size: 32px;
        color: #000000;
    }

    .form-text {
        font-size: 16px;
        outline: none;
        width: 510px;
        height: 195px;
        resize: none;
        box-sizing: border-box;
        padding: 10px;
        border-radius: 6px;
        border: 1px solid #d8d5d5;
        font-family: "Microsoft YaHei" !important;
        background: rgba(255, 255, 255, 0.7);
    }

    .operation-btn {
        width: 100%;
        height: 110px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .submit-btn {
        width: 165px;
        height: 50px;
        border-radius: 10px;
        background: #018ede;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 26px;
        color: #fff;
        cursor: pointer;
    }

    .success-box {
        width: 100%;
        text-align: center;
        margin: 80px 0 60px;
        overflow: hidden;
    }

    .success-info {
        width: 100%;
        text-align: center;
        font-size: 58px;
        color: #009942;
        margin-bottom: 30px;
    }

    .rate-success-info {
        color: #010101;
        width: 100%;
        text-align: center;
        font-size: 40px;
        margin-bottom: 80px;
    }
</style>