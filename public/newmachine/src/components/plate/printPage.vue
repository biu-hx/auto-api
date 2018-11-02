<template :ready>
    <div v-cloak>
        <Loading v-if="unLoaded" :title="loadingText"></Loading>
        <Dialog v-if="infoBomb" :text="infoNotice" :buttonconfig="buttonconfig" @hideBox="hideBox"></Dialog>

        <div class="content">
            <div class="title">
                <p>打印小票</p>
                <div class="count-time">{{noRecordTime}}</div>
            </div>
            <div class="demonstration-box">
                <p class="demonstration-box-title">{{printTitle}}</p>
                <div class="demonstration-box-img">
                    <img src="../../static/img/printing-ticket.png" class="printing-ticket" v-if="printStatus=='printing'">
                    <img src="../../static/img/printed-ticket.png" class="printing-ticket" v-if="printStatus=='printed'">
                </div>
                <p class="info">
                    <span>提示：</span>一次交易最多可打印2次小票，请妥善保管！</p>
            </div>
        </div>

    </div>
</template>
<script>
    export default {
        data() {
            return {
                unLoaded: false,
                printTitle: '正在打印小票',
                orderDetail: {},
                loadingText: "正在检测打印机...",
                infoBomb: false,
                infoNotice: "",
                orderNum: "",
                num: undefined,
                noRecordTime: 30,
                printStatus: "printing",
                orderType: "register",
                buttonconfig: ['知道了'],
                hospitalInfo: {},
                printDeviceStatus: '',
                fromName: undefined,

                agent: this.$agent()
            }
        },
        created: function () {
            // 路由参数
            this.orderNum = this.$route.params.orderNum;
            this.num = this.$route.params.num;

            // 进入windows版流程
            if(this.agent == "Windows"){
                this.$router.replace({
                    name: 'printPage_win',
                    params: {
                        orderNum: this.orderNum,
                        num: this.num
                    }
                })
            }

            this.unLoaded = true;
            this.hospitalInfo = JSON.parse(localStorage.getItem('hospitalInfo'));
            let servicePhone = this.hospitalInfo.servicePhone ? this.hospitalInfo.servicePhone : '--'

            // 获取打印机状态
            this.printDeviceStatus = this.dsBridge.call("getPrintStatus", { msg: 'getPrintStatus' });
            
            if (this.printDeviceStatus == '0') {
                // 正常
                this.loadingText='正在加载数据...',
                
                // 播放打印小票语音

                this.$audioPlay(7);

                // 查询数据
                this.searchDetailOrder(this.orderNum);

            } else if (this.printDeviceStatus == '-1') {

                //缺纸
                this.unLoaded = false;

                this.infoBomb = true;
                this.infoNotice  = '<p style="text-align:left; font-size:46px;">打印机缺纸，无法打印小票，请于其他机器上补打小票；<p>'
                                 + '<p style="text-align:left; font-size:32px;">流程：其他壁挂式缴费机点击<span style="color:red">【补打小票】</span>-刷就诊卡-选择交易记录-补打小票；<br/>现场客服电话：'+servicePhone+'<p>';

            } else if (this.printDeviceStatus == '-2') {

                //异常
                this.unLoaded = false;

                this.infoBomb = true;
                this.infoNotice  = '<p style="text-align:left; font-size:46px;">打印机设备异常，无法打印小票，请于其他机器上补打小票；<p>'
                                 + '<p style="text-align:left; font-size:32px;">流程：其他壁挂式缴费机点击<span style="color:red">【补打小票】</span>-刷就诊卡-选择交易记录-补打小票；<br/>现场客服电话：'+servicePhone+'<p>';

            }
        },
        methods: {
            // //我知道了
            // knowNotice: function () {
            //     this.$router.push({
            //         name: "home"
            //     });
            // },
            // //重新挂号
            // reRegister: function () {
            //     this.$router.push({
            //         name: 'selectDept'
            //     })
            // },
            // //门诊重新缴费
            // rePayOutpatient: function () {
            //     this.$router.push({
            //         name: 'paymentList'
            //     })
            // },
            // //住院预缴重新缴费
            // rePayPrepayment: function () {
            //     this.$router.push({
            //         name: 'hospitalizationDetail'
            //     })
            // },
            //根据订单号查询交易详情
            searchDetailOrder: function (orderNum) {
                this.$http.get(this.publicUrl + '/query/order/detail', {
                    params: {
                        orderNum: orderNum,
                        timeStr: new Date().getTime()
                    },
                    headers: this.config(JSON.parse(localStorage.getItem('hospitalInfo')).number),
                    timeout: 10000
                }).then((res) => {
                    this.unLoaded = false;

                    //无数据页面30秒倒计时
                    this.timer = setInterval(() => {
                        if (this.noRecordTime > 0) {
                            this.noRecordTime--;
                        } else {
                            this.$router.push({
                                name: "home"
                            });
                        }
                    }, 1000);

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
                            if(this.orderDetail.status == 2){
                                var modName = "";
                                if(this.orderDetail.type == 2){modName="预约挂号"}
                                else if(this.orderDetail.type == 4){modName="门诊缴费"}
                                else if(this.orderDetail.type == 5){modName="住院缴费"}

                                this.infoBomb = true;
                                this.infoNotice = '<p><span style="color:red">'+modName+'失败！</span>为了不影响您就诊，请重新交易；</p>'
                                    +'<p>交易失败的款项系统会在7个工作日内退回您的付款账户，请不必担心！</p>';
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

            hideBox: function () {
                this.infoBomb = false;
                if(this.printDeviceStatus=="-1" || this.printDeviceStatus=="-2"){
                    this.$router.push({
                        name: 'home'
                    })
                }
            }
        },
        destroyed() {
            clearInterval(this.timer);
        }
    };
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

    .demonstration-box-title {
        width: 100%;
        height: 104px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 36px;
        font-weight: bold;
        color: #fd0307;
    }

    .demonstration-box-img {
        width: 520px;
        height: 380px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-top: 1px solid #d4d4d4;
        margin-left: 300px;
        overflow: hidden;
    }

    .printing-ticket {
        width: 260px;
        height: 260px;
    }

    .info {
        width: 100%;
        margin-top: 40px;
        text-align: center;
        font-size: 26px;
    }

    .info span {
        color: #fd0307;
    }
</style>