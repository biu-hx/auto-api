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
                    <img src="../../static/img/printing-ticket-default.png" class="printing-ticket"
                         v-if="printStatus=='printing'">
                    <img src="../../static/img/printed-ticket-default.png" class="printing-ticket"
                         v-if="printStatus=='printed'">
                </div>
                <p class="info">
                    <span>提示：</span>一次交易最多可打印2次小票，请妥善保管！</p>
            </div>
        </div>

    </div>
</template>
<script>
    import enumerate from '../../js/enumerate';

    export default {
        data() {
            return {
                unLoaded: false,
                loadingText: "正在检测打印机...",

                infoBomb: false,
                infoNotice: "",
                buttonconfig: ['知道了'],

                orderNum: "",
                num: undefined,

                printTitle: '正在打印小票',
                orderDetail: {},

                noRecordTime: 30,
                printStatus: "printing",
                orderType: "register",
                hospitalInfo: {},
                printDeviceStatus: '',
                fromName: undefined,

                intError: false,
                timeRound: undefined,

                agent: this.$agent()
            }
        },
        created: function () {
            this.unLoaded = true;

            // 路由参数
            this.orderNum = this.$route.params.orderNum;
            this.num = this.$route.params.num;

            // 取配置信息
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

                    //无数据页面30秒倒计时
                    this.timeRound = setInterval(() => {
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
                let headerStr = this.hospitalInfo.hospital_id == '61754' || this.hospitalInfo.hospital_id == '61756' || this.hospitalInfo.hospital_id == '61757' || this.hospitalInfo.hospital_id == '61759' || this.hospitalInfo.hospital_id == '61760' ? '<header name="医院名称"><![CDATA[' + hospitalName + ']]></header>' : '';


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
                            // let dzInfo = data.successInfo.TimeInfo + '(' + data.successInfo.LocInfo + data.successInfo.address + ')'||'';
                            let dzInfo = `${data.successInfo.TimeInfo || ''}${data.successInfo.LocInfo || ''}${data.successInfo.address || ''}`;
                            let queueNo = data.successInfo.queueNo || '';
                            template += '<lookinfo name="导诊信息"><![CDATA[' + dzInfo + ']]></lookinfo>'
                                + '<orderNum name="就诊序号"><![CDATA[' + queueNo + ']]></orderNum>'
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
                        //自贡&德阳妇幼 住院号查询 缴费不显示 卡号
                        let cardId = '';
                        if (this.hospitalInfo.hospital_id == '61757' || this.hospitalInfo.hospital_id == '61759' || this.hospitalInfo.hospital_id == '61760') {
                            cardId = data.cardId != data.businessInfo.treat_no ? '<cardNum name="卡号"><![CDATA[\' + data.cardId + \']]></cardNum>' : '';
                        } else {
                            cardId = '<cardNum name="卡号"><![CDATA[' + data.cardId + ']]></cardNum>';
                        }
                        template = '<printer name="住院预缴">'
                            + headerStr
                            + '<tradeId name="交易单号"><![CDATA[' + data.orderNum + ']]></tradeId>'
                            + '<patientName name="姓名"><![CDATA[' + data.cardName + ']]></patientName>'
                            + cardId
                            + '<status name="缴费状态"><![CDATA[' + status + ']]></status>'

                            + '<inhosptalNum name="住院号"><![CDATA[' + data.businessInfo.treat_no + ']]></inhosptalNum>'
                            + '<dept name="科室"><![CDATA[' + data.businessInfo.dept_name + ']]></dept>'

                        // 成功
                        if (status == 0) {
                            let balance = (this.hospitalInfo.hospital_id == '61757' || this.hospitalInfo.hospital_id == '61759' || this.hospitalInfo.hospital_id == '61760') ? '' : '<paymentBalance name="缴费后余额"><![CDATA[' + data.successInfo.arrears_fee + ']]></paymentBalance>';
                            template += '<contributionAmount name="缴费金额"><![CDATA[' + data.price + ']]></contributionAmount>'
                                + balance
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

            hideBox: function () {
                this.infoBomb = false;
                if (this.printDeviceStatus != "打印机正常" || this.intError) {
                    if (this.num == 0) {
                        this.$router.push({
                            name: 'home'
                        })
                    } else {
                        history.back();
                    }

                } else {
                    history.back();
                }
            }
        },
        destroyed() {
            clearInterval(this.timeRound);
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