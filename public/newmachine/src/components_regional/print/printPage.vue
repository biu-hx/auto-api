<template :ready>
    <div class="registerWrap" v-cloak>
        
        <Loading v-if="isLoading" :title="loadingText"></Loading>
        <Dialog v-if="infoBomb" :text="infoNotice" :buttonconfig="['知道了']" @hideBox="hideBox"></Dialog>
        
        <div class="count-time">{{noRecordTime}}</div>
        <div class="demonstration">
            <p class="demonstration-title">{{printTitle}}</p>
            <p class="demonstration-info"><span>提示：</span>一次交易最多可打印2次小票，请妥善保管！</p>
            <div class="demonstration-img">
                <img src="../../static/img/printing-ticket.png" class="printing-ticket" v-if="printStatus=='printing'">
                <img src="../../static/img/printed-ticket.png" class="printing-ticket" v-if="printStatus=='printed'">
            </div>
            
        </div>
    </div>
</template>

<script>
    import { queryOrderDetail, setPrintCount } from '../../components_regional/api/common'
    export default {
        data() {
            return {
                isLoading: false,
                loadingText: "正在检测打印机...",
                infoBomb: false,
                infoNotice: "",

                printTitle: '正在打印小票',
                orderDetail: {},
                noRecordTime: 30,
                printStatus: "printing",    // 打印进度
                
                buttonconfig: ['知道了'],
                printDeviceStatus: '',      // 打印机设备状态

                timing: undefined
            }
        },
        created: function () {
            // this.isLoading = true;
            
            let hospitalInfo = JSON.parse(localStorage.getItem('hospitalInfo'));
            let servicePhone = hospitalInfo.servicePhone ? hospitalInfo.servicePhone : '--'

            // 获取打印机设备工作状态
            // this.printDeviceStatus = this.$machineApi.getMachine_printStatus();
            
            if (this.printDeviceStatus == '0') {
                // 正常
                this.loadingText='正在加载数据...',
                
                // 播放打印小票语音
                this.player.src = this.audioSrc[7];
                if(this.$agent()=="Android"){
                    this.player.play();
                }

                // 获取打印数据
                this.queryOrderDetail(this.$route.params.orderNum);

            } else if (this.printDeviceStatus == '-1') {

                //缺纸
                this.isLoading = false;

                this.infoBomb = true;
                this.infoNotice  = '<p style="text-align:left; font-size:46px;">打印机缺纸，无法打印小票，请于其他机器上补打小票；<p>'
                                 + '<p style="text-align:left; font-size:32px;">流程：其他壁挂式缴费机点击<span style="color:red">【补打小票】</span>-刷就诊卡-选择交易记录-补打小票；<br/>现场客服电话：'+servicePhone+'<p>';

            } else if (this.printDeviceStatus == '-2') {

                //异常
                this.isLoading = false;

                this.infoBomb = true;
                this.infoNotice  = '<p style="text-align:left; font-size:46px;">打印机设备异常，无法打印小票，请于其他机器上补打小票；<p>'
                                 + '<p style="text-align:left; font-size:32px;">流程：其他壁挂式缴费机点击<span style="color:red">【补打小票】</span>-刷就诊卡-选择交易记录-补打小票；<br/>现场客服电话：'+servicePhone+'<p>';

            }
        },
        methods: {

            //根据订单号查询交易详情
            queryOrderDetail: function (orderNum) {
                let params = { orderNum: orderNum };

                queryOrderDetail(params).then(res => {
                    
                    // 无数据页面30秒倒计时
                    this.timing = setInterval(() => {
                        if (this.noRecordTime > 0) {
                            this.noRecordTime--;
                        } else {
                            // 回到首页
                            this.backHome();
                        }
                    }, 1000);

                    // 数据处理
                    if (res.code == 10000) {

                        this.orderDetail = res.data;
                        
                        // 计录打印次数
                        this.printCount(orderNum);

                        let orderType = this.orderDetail.type;

                        // 调用安卓/执行打印操作
                        if (!this.isPc()) {
                            
                            let printService = ''
                            if (orderType == 2 || orderType == 3) {

                                printService = 'printGuaHao';

                            } else if (orderType == 4) {

                                printService = 'printJiaofei';

                            } else if (orderType == 5) {

                                printService = 'printZhuYuan';

                            }
                            // 打印小票
                            this.$machineApi.printOrderReceipt(printService, this.orderDetail);
                        }

                        // 6秒后
                        setTimeout(() => {
                            //播放打印小票完成语音
                            this.player.src = this.audioSrc[3];
                            if(this.$agent()=="Android"){
                                this.player.play();
                            }

                            //将打印状态变为已打印
                            this.printTitle = '打印完成，请取走小票';
                            this.printStatus = "printed";

                            // 交易失败
                            if(this.orderDetail.status == 2){
                                var modName = "";
                                
                                if(orderType == 2)     { modName = "预约挂号" }
                                else if(orderType == 4){ modName = "门诊缴费" }
                                else if(orderType == 5){ modName = "住院缴费" }

                                this.infoBomb = true;
                                this.infoNotice = '<p><span style="color:red">'+modName+'失败！</span>为了不影响您就诊，请重新交易；</p>'
                                    +'<p>交易失败的款项系统会在7个工作日内退回您的付款账户，请不必担心！</p>';
                            } 

                        }, 6000);

                    } 

                    // 数据异常
                    else {

                        if (this.filterASCII(res.data)) {
                            this.infoNotice = this.filterASCII(res.data);
                        } else {
                            this.infoNotice = res.data;
                        }
                        this.infoBomb = true;
                    }

                    this.isLoading = false
                })
            },

            // 打印时调用打印小票的次数
            printCount: function (orderNum) {
                let params = { orderNum: orderNum };
                setPrintCount(params).then(res => {
                    // 发送成功
                })
            },

            // 关闭Dailog
            hideBox: function () {
                this.infoBomb = false;
                if(this.printDeviceStatus=="-1" || this.printDeviceStatus=="-2"){
                    this.backHome();
                }
            },

            // 回到首页
            backHome: function(){
                this.$router.push({
                    name: 'regional_home'
                })
            }
        },
        destroyed() {
            clearInterval(this.timing);
        }
    };
</script>

<style scoped>
    .registerWrap{
      padding:35px; 
      height: 604px; 
      box-sizing: border-box; 
      position: relative;
    }
    .demonstration {
        height: 600px;
    }
    .demonstration-title {
        width: 100%;
        height: 85px;
        padding-top: 30px;
        text-align: center;
        font-size: 50px;
        color: #fff;
    }
    .demonstration-info{font-size: 28px; text-align: center; color: #fff;}
    .demonstration-img {
        height: 380px;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
    }
    .printing-ticket {
        width: 260px;
        height: 260px;
    }
    .count-time{position: absolute; right:-20px; top:-86px;}
</style>