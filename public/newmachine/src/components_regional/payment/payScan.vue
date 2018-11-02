<template>
    <div class="registerWrap" v-cloak>
        <Loading v-if="isLoading" :title="loadingText"></Loading>
        <Dialog v-if="infoBomb" :text="infoNotice" :buttonconfig="['知道了']" @hideBox="hideBox"></Dialog>
        <div class="paymentScan">
            <ul class="step">

                <!-- 引导提示 -->
                <li>
                    <div>
                        <i>1</i>
                        <strong v-if="payMode=='1'">登录微信<br>打开扫一扫</strong>
                        <strong v-if="payMode=='2'">打开支付宝</strong>
                    </div>
                    <div>
                        <img v-if="payMode=='1'" src="../static/images/create_step_1.png">
                        <img v-if="payMode=='2'" src="../static/images/alipay_step_1.png">
                    </div>
                </li>
                <li>
                    <div>
                        <i>2</i>
                        <strong v-if="payMode=='1'">扫二维码<br>进行支付</strong>
                        <strong v-if="payMode=='2'">点击“扫一扫”</strong>
                    </div>
                    <div>
                        <img v-if="payMode=='1'" src="../static/images/create_step_2.png">
                        <img v-if="payMode=='2'" src="../static/images/alipay_step_2.png">
                    </div>
                </li>

                <!-- 支付码 -->
                <li>
                    <div style="display:flex; align-items: center; justify-content: center;">
                        <strong>{{paywayConfig[payMode]}}</strong>
                    </div>
                    <div>
                        <img v-if="orderQrInfo" :src="orderQrInfo.qr">
                        <img v-else src="../../static/img/loading.gif" style="width:60px; height:60px; margin-top:120px">
                    </div>
                </li>
            </ul>
        </div>
    </div>
</template>

<script>
    import { createRegOrder, createPublicOrder, createPayQrcode, checkPayStatus } from '../../components_regional/api/register';
    import enumerate from '../../js/enumerate';
    export default {
        data() {
            return {
                isLoading: false,
                loadingText: '正在加载数据...',
                infoBomb: false,
                infoNotice: '',

                orderData: '',      // 挂号/下单信息
                serviceType: '',   // 当前服务类型, 参照接口文档"功能模块枚举值"

                orderQrInfo: undefined, // 生成订单(锁号)后的订单信息
                
                // 支付方式静态配置
                paywayConfig:{
                    '1':'请扫描二维码',
                    '2':'请扫描二维码',
                    '3':'刷银行卡支付'
                },

                time: '',       // 支付计时
                takeTime: '',   // 取号计时
                timing: undefined,
                payStatus: 'pay',    // 支付状态: pay-支付中, payTimeOut-支付超时, paySuccess-支付成功, takeSuccess-取号成功, takeFail-取号失败

                // 路由参数
                paramsData: undefined,  
                payMode: '', // 当前支付方式
            }
        },
        created: function () { },
        activated:function(){
            this.paramsData = this.$route.params;
            this.payMode = this.paramsData.payMode;
            this.$route.meta.title = enumerate.payMode[this.payMode];
            
            this.orderData = JSON.parse(localStorage.getItem('currentOrderInfo'));
            this.serviceType = this.orderData.serviceType;

            this.checkServiceType();
        },
        computed: {},
        methods: {
            // 判断服务类型, 生成相应的订单信息
            checkServiceType: function(){
                let params = {};

                // serviceType对应接口文档的"功能模块枚举值"
                switch(this.serviceType){
                    case '2': 
                        // 预约挂号
                        this.createRegOrder();
                        break;
                    case '3':
                        // 加号取号
                        params = {
                            scheduleCode: this.orderData.scheduleCode,
                            serviceType: this.serviceType
                        }
                        break;
                    case '4':
                        // 门诊缴费
                        params = {
                            recipeId: this.orderData.recipeId,
                            serviceType: this.serviceType
                        }
                        break;
                    case '5':
                        // 住院缴费
                        params = {
                            treatNo: this.orderData.treatNo,
                            fee: this.orderData.fee,
                            serviceType: this.serviceType
                        }
                        break;
                }

                // 除"预约挂号"业务外的其它业务生成订单方式
                if(this.serviceType != '2'){
                    this.createPublicOrder(params)
                }
                
            },

            // 生成挂号订单
            createRegOrder: function () {
                this.isLoaded = true;
                this.loadingText = '订单生成中，请稍候...';
                let params = {
                    hospitalId: this.orderData.hospitalId,
                    cardId: JSON.parse(localStorage.getItem('userInfo')).cardId,
                    deptId: this.orderData.deptId,
                    doctorId: this.orderData.doctorId,
                    scheduleId: this.orderData.scheduleId,
                    date: this.orderData.date,
                    period: this.orderData.period,
                    payType: this.paramsData.payType
                }

                createRegOrder(params).then(res => {
                    if (res && res.code == 10000) {
                        //播放锁号成功，提醒支付语音
                        this.player.src = this.audioSrc[4];
                        if(this.$agent()=="Android"){
                            this.player.play();
                        }
                        this.getQrcode(res.data.orderNum);
                    }
                    this.isLoading = false
                })
            },

            // 生成非挂号类订单
            createPublicOrder: function (params) {
                this.isLoaded = true;
                this.loadingText = '订单生成中，请稍候...';

                createPublicOrder(params).then(res => {
                    if (res && res.code == 10000) {
                        //播放锁号成功，提醒支付语音
                        this.player.src = this.audioSrc[4];
                        if(this.$agent()=="Android"){
                            this.player.play();
                        }
                        this.getQrcode(res.data.orderNum);

                    } else {
                        if (this.filterASCII(res.msg)) {
                            this.infoNotice = this.filterASCII(res.msg);
                        } else {
                            this.infoNotice = res.msg;
                        }
                        this.infoBomb = true;
                    }
                    this.isLoading = false
                })
            },

            // 生成订单支付二维码 _ 调用API
            getQrcode: function(orderNum){
                let params = {
                    orderNum: orderNum,
                    payMode: this.payMode
                }
                createPayQrcode(params).then(res => {
                    if (res && res.code == 10000) {
                        this.orderQrInfo = res.data;
                        var curSecond = this.orderQrInfo.now_time;  // 服务器当前时间,秒
                        
                        // 获取二维码后，设置倒计时时间
                        if (this.serviceType == '2') {
                            // 判断是否锁号，若未锁号，则倒计时重置为150秒，已锁号，根据当前时间与锁号时间差确定显示时间
                            
                            let orderCreateTime = parseInt(this.orderQrInfo.create_time); // 订单创建时间, 秒
                            this.time = orderCreateTime + 150 - curSecond > 150 ? 150 : orderCreateTime + 150 - curSecond;

                        } else {
                            // 非挂号的支付逻辑
                            this.time = 120;
                        }

                        // 开始倒计时
                        this.timing = setInterval(() => {
                            this.timingSet();
                        }, 1000)

                    }
                })
            },

            // 查询支付状态 
            getPaySatus: function(){
               let params = {
                    orderNum: this.orderQrInfo.orderNum,
                    serviceType: this.serviceType
                }
                checkPayStatus(params).then(res => {
                    if (res && res.code == 10000) {
                        //获取支付状态成功后，初始化取号倒计时
                        if (res.data.payStatus == 1 && res.data.status == 0) {
                            
                            //显示LOADING: "系统正在处理，请耐心等待…"
                            this.isLoaded = true;
                            this.loadingText = '支付成功，系统正在处理…';

                            this.payStatus = 'paySuccess';

                            // 将时间重置: 取号计时时间为空、取号时间为100
                            this.time = '';    
                            this.takeTime = 100 ;

                            //判断是否已初始化倒计时,仅挂号需要取号
                            if (this.serviceType == '2') {
                                //播放正在取号语音
                                this.player.src = this.audioSrc[17];
                                if(this.$agent()=="Android"){
                                    this.player.play();
                                }
                            } else {
                                //播放系统正在处理语音
                                this.player.src = this.audioSrc[27];
                                if(this.$agent()=="Android"){
                                    this.player.play();
                                }
                            }
                            

                        } else if (res.data.payStatus == 1 && res.data.status == 1) {
                            this.payStatus = 'takeSuccess';
                            this.loadingText = '交易成功，即将为您打印小票…';
                            
                            this.clearTiming();
                            
                            setTimeout(() => {
                                this.goPrintPage(); // 打印小票
                            }, 3000)

                        } else if (res.data.payStatus >= 1) {
                            this.payStatus = 'takeFailed';
                            this.loadingText = '交易失败，即将为您打印小票…';
                            
                            this.clearTiming();

                            setTimeout(() => {
                                //播放取号失败语音
                                this.player.src = this.audioSrc[11];
                                if(this.$agent()=="Android"){
                                    this.player.play();
                                }

                                this.goPrintPage(); // 打印小票

                            }, 3000)
                        }
                    }
                }) 
            },

            // 计时
            timingSet: function(){
                
                // 支付计时
                if(this.time != '' && this.time > 0){
                    this.time--;
                    if (this.time > 0) {
                        this.payStatus = 'pay';
                        if (this.time % 2 === 0) {
                            // 继续查询支付状态
                            this.getPaySatus(); 
                        }
                    } else if (this.time <= 0) {
                        this.time = 0;
                        this.payStatus = 'payTimeOut';
                        this.clearTiming();
                    }
                }
                

                //取号倒计时
                if (this.takeTime != '' && this.takeTime > 0) {
                    this.takeTime--;
                    if (this.takeTime > 0) {
                        if (this.takeTime % 2 === 0) {
                            // 继续查询支付状态
                            this.getPaySatus(); 
                        }
                    } else {
                        this.payStatus = 'takeFailed';
                        setTimeout(() => {
                            this.goPrintPage(); // 打印小票
                        }, 3000)
                    }
                }
            },

            // 去打印小票
            goPrintPage: function(){
                if(this.isLoading) {
                    this.isLoading = false;
                }
                this.$router.push({
                    name: 'printPage',
                    params: {
                        orderNum: this.orderInfo.orderNum
                    }
                })
            },

            // Clear
            clearTiming: function(){
                clearInterval(this.timing);
            },

            // 重新支付
            rePay: function () {
                // 判断原路由来源，重新支付后跳转到来源页面
                if (this.fromName == 'register') {
                    history.back(-2);
                } else {
                    history.back(-1);
                }
            },
            
        },
        destroyed: function () {
            this.clearTiming()
        }
    }
</script>

<style lang="less" src='../static/less/payment.less' scoped></style>