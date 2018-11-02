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
    import enumerate from '../../js/enumerate';

    export default {
        name: "doctorRate_win",
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

                intError: false,
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

            let servicePhone = this.hospitalInfo.servicePhone ? this.hospitalInfo.servicePhone : '--';
            // 初始化打印机
            let intSatus = this.$machineApi.win_printerInt();
            if (intSatus != 0 || intSatus == "error") {
                this.unLoaded = false;
                this.infoBomb = true;
                this.infoNotice = "打印机设备初始化失败";
                this.intError = true;
                return;
            }
            // 获取打印机状态
            this.printDeviceStatus = this.$machineApi.win_printerStatus();
            if (this.printDeviceStatus == '打印机正常') {
                // 正常
                this.loadingText = '正在加载数据...',

                    // 播放打印小票语音

                    this.$audioPlay(7);

                // 获取打印数据
                this.searchDetailOrder();

            } else {

                //缺纸
                this.infoBomb = true;
                this.infoNotice = '<p style="text-align:left; font-size:46px;">' + this.printDeviceStatus + '，无法打印小票，请于其他机器上补打小票；<p>'
                    + '<p style="text-align:left; font-size:32px;">流程：其他壁挂式缴费机点击<span style="color:red">【补打小票】</span>-刷就诊卡-选择交易记录-补打小票；<br/>现场客服电话：' + servicePhone + '<p>';

                this.unLoaded = false;
                this.intError = true;

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
            searchDetailOrder: function () {
                this.$http.get(this.publicUrl + '/query/order/detail', {
                    params: {
                        orderNum: this.orderNum,
                        timeStr: new Date().getTime()
                    },
                    headers: this.config(this.hospitalInfo.number),
                    timeout: 10000
                }).then((res) => {
                    this.unLoaded = false;

                    // 数据处理
                    if (res.data.code == 10000) {

                        this.orderDetail = res.data.data;
                        this.printTimes(this.orderNum);
                        this.setPrintMode();

                    } else {

                        if (this.filterASCII(res.data.data)) {
                            this.infoNotice = this.filterASCII(res.data.data);
                        } else {
                            this.infoNotice = res.data.data;
                        }

                        this.infoBomb = true;
                        this.intError = true;
                    }

                }).catch((err) => {
                    this.unLoaded = false;

                    this.$audioPlay(21);

                    this.dealError();
                })
            },
            // 打印
            setPrintMode: function () {

                //根据订单开头字段判断订单类型并将订单查询的内容传递给安卓打印
                let templateXML = this.getTemplateXML();

                if (!templateXML) return;

                this.$machineApi.win_printerIng(templateXML);

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

            },
            // 定制打印模板
            getTemplateXML: function () {
                let data = this.orderDetail;
                let status = data.status == "1" ? 0 : 1;    // 订单状态

                let template = '';
                let hospitalName = this.hospitalInfo.projectName;
                let headerStr = this.hospitalInfo.hospital_id == '61754' || this.hospitalInfo.hospital_id == '61756' || this.hospitalInfo.hospital_id == '61757' || this.hospitalInfo.hospital_id == '61759' ? '<header name="医院名称"><![CDATA[' + hospitalName + ']]></header>' : '';


                switch (data.type) {
                    case "2":

                        template = '<printer name="预约挂号">'
                            + headerStr
                            + '<tradeId name="交易单号"><![CDATA[' + data.orderNum + ']]></tradeId>'
                            + '<patientName name="姓名"><![CDATA[' + data.cardName + ']]></patientName>'
                            + '<cardNum name="卡号"><![CDATA[' + data.cardId + ']]></cardNum>'
                            + '<status name="挂号状态"><![CDATA[' + status + ']]></status>'

                            + '<doctor name="医生"><![CDATA[' + data.businessInfo.doctorName + ']]></doctor>'
                            + '<dept name="科室"><![CDATA[' + data.businessInfo.deptName + ']]></dept>'
                            + '<registerFee name="挂号费"><![CDATA[' + data.price + ']]></registerFee>'

                        // 成功
                        if (status == 0) {
                            let dzInfo = data.successInfo.TimeInfo + '(' + data.successInfo.LocInfo + data.successInfo.address + ')';
                            template += '<lookinfo name="导诊信息"><![CDATA[' + dzInfo + ']]></lookinfo>'
                                + '<orderNum name="就诊序号"><![CDATA[' + data.successInfo.queueNo + ']]></orderNum>'
                        }
                        break;

                    case "4":

                        template = '<printer name="门诊缴费">'
                            + headerStr
                            + '<tradeId name="交易单号"><![CDATA[' + data.orderNum + ']]></tradeId>'
                            + '<patientName name="姓名"><![CDATA[' + data.cardName + ']]></patientName>'
                            + '<cardNum name="卡号"><![CDATA[' + data.cardId + ']]></cardNum>'
                            + '<status name="缴费状态"><![CDATA[' + status + ']]></status>'

                        // 成功
                        if (status == 0) {
                            template += '<payfee name="支付金额"><![CDATA[' + data.price + ']]></payfee>'
                                + '<lookinfo name="导诊信息"><![CDATA[' + data.successInfo.remark + ']]></lookinfo>'
                        }

                        break;

                    case "5":

                        template = '<printer name="住院预缴">'
                            + headerStr
                            + '<tradeId name="交易单号"><![CDATA[' + data.orderNum + ']]></tradeId>'
                            + '<patientName name="姓名"><![CDATA[' + data.cardName + ']]></patientName>'
                            + '<cardNum name="卡号"><![CDATA[' + data.cardId + ']]></cardNum>'
                            + '<status name="缴费状态"><![CDATA[' + status + ']]></status>'

                            + '<inhosptalNum name="住院号"><![CDATA[' + data.businessInfo.treat_no + ']]></inhosptalNum>'
                            + '<dept name="科室"><![CDATA[' + data.businessInfo.dept_name + ']]></dept>'

                        // 成功
                        if (status == 0) {
                            template += '<contributionAmount name="缴费金额"><![CDATA[' + data.price + ']]></contributionAmount>'
                                + '<paymentBalance name="缴费后余额"><![CDATA[' + data.successInfo.arrears_fee + ']]></paymentBalance>'
                        }


                        break;
                }

                let payType = enumerate.payMode[data.pay_type];

                template += '<payWay name="支付方式"><![CDATA[' + payType + ']]></payWay>'
                    + '<payTime name="支付时间"><![CDATA[' + data.pay_time + ']]></payTime>'
                    + '<devNum name="终端编号"><![CDATA[' + data.number + ']]></devNum>'
                    + '</printer>'


                return template;
            },
            // 打印时调用打印小票的次数
            printTimes: function (orderNum) {
                this.$http.get(this.publicUrl + '/order/print', {
                    params: {
                        orderNum: orderNum,
                        timeStr: new Date().getTime()
                    },
                    headers: this.config(this.hospitalInfo.number),
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