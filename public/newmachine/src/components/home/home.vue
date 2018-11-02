<template>
    <div class="frame" v-cloak>
        <Loading v-if="unLoaded" :title="loadingText"></Loading>

        <header>
            <div class="hospital-name">
                <div class="hospital-logo-name">
                    <img :src="hospitalInfo.logoAll" alt="" class="project-logo">
                </div>
                <div class="hospital-investorLogo">
                    <img :src="hospitalInfo.bankFullIcon" alt="" class="ad-logo">
                </div>
            </div>
            <div class="base-show">
                <div class="current-time" v-html="time"></div>
            </div>
        </header>
        <div class="body">
            <div class="home-content">
                <div class="main-content">
                    <!-- 系统提示 -->
                    <div class="notice-box" v-if="isAbnormal">
                        <div class="notice-info" >
                            <img src="../../static/img/notice-img.png" alt="" class="notice-img">
                            <p>{{printInfo}}</p>
                        </div>
                    </div>

                    <div class="func-list" :style="isAbnormal?'padding-bottom:10px':''">

                        <!-- 功能菜单 -->
                        <div v-if="!showDefaultPage" class="func-item" v-for="(item,index) in hospitalInfo.item" @click="selectFunc(item.important, item.id)">
                            <div class="func-item-icon-box">
                                <img :src="item.logo" alt="" class="func-item-icon">
                            </div>
                            <div class="func-item-title">{{item.name}}</div>
                        </div>

                        <!-- 设备无法使用 -->
                        <div v-if="showDefaultPage" class="defaultPage">
                            <img src="../../static/img/overtime-info-icon.png" >
                            <strong>当前设备异常</strong>
                            <p>{{defaultMsg}}</p>
                        </div>
                    </div>
                </div>

                <div class="statement-box">
                    <p class="statement-box-title">温馨提示</p>

                    <div class="index-information" v-if="hospitalInfo.hospital_id=='10000'">
                        <div class="info-list-box">
                            <p class="info-content">1、本自助机支持微信“扫码支付”</p>
                            <p class="info-content">2、本自助机支持华西二院实体就诊卡、电子就诊卡和电子居民健康卡</p>
                        </div>
                        <div class="info-list-box">
                            <p class="info-content">3、自助机挂号成功，一律不退号</p>
                            <p class="info-content">4、若您需要交易发票，请到门诊缴费窗口打印</p>
                        </div>
                        <p class="info-content-sp">5、若支付成功后，自助机提示缴费失败，请重新支付订单，原有订单会在7个工作日内返回您的支付账户</p>
                    </div>

                    <div class="index-information" v-if="hospitalInfo.hospital_id=='61754' || hospitalInfo.hospital_id=='61757' || hospitalInfo.hospital_id=='61759' || hospitalInfo.hospital_id=='61760'">
                        <div class="info-list-box">
                            <p class="info-content">1、本自助机支持微信支付</p>
                            <p class="info-content">2、本机器支持实体就诊卡及电子就诊卡</p>
                        </div>
                        <p class="info-content-sp">3、自助机上仅支持退预约号，其他项目退费，请先在人工窗口补打交易发票再办理退费</p>
                        <p class="info-content-sp">4、交易失败的订单款项会在7个工作日内退回到您的支付账户</p>
                        
                    </div>

                    <div class="index-information" v-if="hospitalInfo.hospital_id=='61756'">
                        <div class="info-list-box">
                            <p class="info-content">1、本自助机支持微信和支付宝扫码支付、银联支付</p>
                        </div>
                        <p class="info-content-sp">2、自助机上仅支持退预约号，其他项目退费，请先在人工窗口补打交易发票再办理退费</p>
                        <p class="info-content-sp">3、交易失败的订单款项会在7个工作日内退回到您的支付账户</p>

                    </div>
                </div>
            </div>
        </div>
        <footer>
            <div class="system-box">
                <div class="system-info">
                    <p>终端编号：{{hospitalInfo.number}}</p>
                    <p>系统版本：{{version}}</p>
                </div>
            </div>
            <div class="contact-service">
                <img src="../../static/img/service-icon.png" alt="" class="call-logo">
                <div class="service-box">
                    <p class="service-phone">{{hospitalInfo.servicePhone}}</p>
                    <p class="service-title">客服电话</p>
                </div>
            </div>
        </footer>
    </div>
</template>
<script>

    export default {
        data() {
            return {
                unLoaded: false,
                loadingText: "正在加载... ",
                hospitalInfo: {},
                macAddress: '',
                time: '',
                servicePhone: '',
                lackPaper: true,
                isPay: true,
                payStatus: 'paySuccess',
                funcList: [],
                number: '',//终端编号
                version: '',//软件版本号
                isAbnormal: false,//设备是否异常
                printInfo: '',//打印机状态提示,默认
                showDefaultPage: false,
                defaultMsg: "",

                // 系统环境
                agent: this.$agent()
            }
        },
        created: function () {
            /**
             * 双系统兼容优化
             */
            let machinaMac = this.$getMacAddress();
            if(machinaMac){
                this.macAddress = machinaMac.mac;
                this.version = machinaMac.version;
                localStorage.setItem('version', this.version);
            }

            //判断是否有缓存，若存在缓存则先显示缓存内容，再通过接口更新缓存及界面
            if (localStorage.getItem('hospitalInfo')) {
                this.hospitalInfo = JSON.parse(localStorage.getItem('hospitalInfo'));
            }
            //调用医院基本配置信息函数
            this.gethospitalInfo(this.macAddress);

            // Windows
            if(this.agent=="Windows"){
                
                // 初始化打印机
                let intSatus = this.$machineApi.win_printerInt();
                if(intSatus!=0 || intSatus == "error"){
                    this.isAbnormal = true;
                    this.printInfo = '打印机设备初始化失败, 请联系管理员进行维修！';
                    return;
                }

            }
            
            this.checkPrintStatus();

            //时间显示函数和登录状态监听,在当前页时，执行当前页的定时器，否则清除定时器函数。
            this.timer = setInterval(() => {
                this.showTime();
            }, 1000);

            localStorage.removeItem('userInfo');
        },

        methods: {

            //获取医院基本配置信息
            gethospitalInfo: function (macAddress) {
                
                this.$getMachineInfo('/equip/number', macAddress, (res)=>{
                    if (res.code === 10000) {
                        this.hospitalInfo = res.data;
                        localStorage.setItem('hospitalInfo', JSON.stringify(this.hospitalInfo));
                        
                        // 开启设备监控
                        if(this.agent=="Windows"){ this.$machineApi.win_startMoniter(); }

                        // 获取语音
                        this.getAudioArray(res.data.voiceVersion, ()=>{
                            this.$audioPlay(18);
                        });

                    }else if(res.code === 20001) {

                        // 设备异常
                        this.showDefaultPage = true;
                        this.defaultMsg = res.data.msg
                    }
                })
            },

            // 在线获取全部语音，把语音存起来
            getAudioArray: function(newVersion, call){
                
                // 本地缓存
                let audios_new = localStorage.getItem('audios_new');
                let audios_version = localStorage.getItem('audios_version');

                // 匹配
                if(audios_new && audios_version && audios_version == newVersion) {
                    call();
                    return
                }

                // 更新服务器数据
                this.$http.get(this.publicUrl + "/equip/voiceList", {
                    params: {'rand': new Date().getTime() },
                    headers: this.config(JSON.parse(localStorage.getItem('hospitalInfo')).number),
                    timeout: 10000
                }).then((res) => {
                    if (res.data.code == 10000) {
                        localStorage.setItem('audios_new', JSON.stringify(res.data.data));
                        localStorage.setItem('audios_version', newVersion);
                    }

                    call();
                }).catch((err) => {

                })
            },

            //选择功能
            selectFunc: function (routerName,id) {
                let pushName = "istinguishCard";

                if (routerName == 'printTicket' || routerName == 'queryOrder') {
                    pushName = 'selectQueryMethod';
                } 
                else if (routerName == 'selectType') {
                    pushName = 'selectType';
                } 
                else if (routerName == 'dinner') {
                    pushName = 'ordering-attentions';
                } 
                else {
                    pushName = 'istinguishCard';
                }

                // 关闭设备监控
                if(this.agent=="Windows"){ this.$machineApi.win_stopMoniter(); }

                // 跳转
                this.$router.push({
                    name: pushName,
                    params: {
                        fromName: routerName
                    }
                })

            },

            //生成当前时间
            showTime: function () {
                let year = new Date().getFullYear();
                let month = ((new Date().getMonth() + 1) < 10) ? ('0' + (new Date().getMonth() + 1)) : (new Date().getMonth() +
                    1);
                let date = new Date().getDate() < 10 ? ('0' + new Date().getDate()) : new Date().getDate();
                let day = year + '年' + month + '月' + date + '日';
                let week = this.translateWeek(new Date().getDay());
                let hours = (new Date().getHours() < 10) ? ('0' + new Date().getHours()) : (new Date().getHours());
                let minutes = (new Date().getMinutes() < 10) ? ('0' + new Date().getMinutes()) : (new Date().getMinutes());
                let second = new Date().getSeconds();
                this.time = day + '&nbsp;&nbsp;' + week + '&nbsp;&nbsp;' + hours + ':' + minutes;
                
                //每小时调用一次检查网络接口
                if (minutes == 30 && second == 0) {
                    this.checkNetwork(this.macAddress);
                    this.checkPrintStatus();
                }

            },

            checkPrintStatus: function(){
                //获取打印机状态
                    let printStatus = "";

                    if(this.agent == "Android") {
                        printStatus = this.$machineApi.getMachine_printStatus();
                    }else if(this.agent == "Windows") {
                        // 获取打印机状态
                        printStatus = this.$machineApi.win_printerStatus();

                        if (printStatus == '打印机正常') {
                            // 正常
                            printStatus='0';

                        } else {
                            // 缺纸 或 异常
                            printStatus = '-2';
                        }
                    }

                    if (printStatus == '0') {
                        //正常
                        this.isAbnormal = false;
                    } else if (printStatus == '-1') {
                        //缺纸
                        this.isAbnormal = true;
                        this.printInfo = '打印机缺纸，请联系管理员换纸！';
                    } else if (printStatus == '-2') {
                        //异常
                        this.isAbnormal = true;
                        this.printInfo = '该设备打印机异常，请联系管理员进行维修！'
                    }else{
                        this.printInfo = '欢迎使用自助医疗设备！';
                    }
            },

            //访问后台接口，报告设备网络情况
            checkNetwork: function (macAddress) {
                this.$http.get(this.publicUrl + '/equip/network', {
                    params: {
                        mac: macAddress
                    },
                    headers: this.config(macAddress)
                }).then((res) => {
                    
                }).catch((err) => {
                    this.dealError('网络错误，请网络正常后重试!');
                })
            },

            //将数字星期转换为中文星期函数
            translateWeek: function (w) {
                let weekArray = ['星期日','星期一','星期二','星期三','星期四','星期五','星期六']
                return weekArray[w];
            }
        },
        destroyed: function () {
            clearInterval(this.timer);
        }
    }
</script>

<style scoped>
    .frame {
        width: 1280px;
        margin: 0 auto;
        padding: 0;
        overflow: hidden;
    }

    header {
        width: 100%;
        height: 146px;
        background: url('../../static/img/header-bg.jpg')no-repeat;
        background-size: 100% 100%;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .hospital-name {
        width: 970px;
        height: 100%;
        display: flex;
        align-items: center;
        padding-bottom: 5px;
    }

    .hospital-logo-name {
        
    }
    .hospital-investorLogo{
        margin-left: 35px;
    }
    .project-logo{
        width: auto;
        height: 90px;
        margin-left: 20px;
    }
    .ad-logo {
        width: auto;
        height: 90px;
    }

    .zn-name {
        font-size: 34px;
        font-weight: bolder;
        text-shadow: 2px 2px 2px #fff;
    }

    .en-name {
        margin-top: 10px;
        font-size: 12px;
        text-shadow: 2px 2px 2px #fff;
    }

    .welcome-user {
        height: 100%;
        display: flex;
        align-items: center;
        font-size: 26px;
        color: #8fdef8;
    }

    .user-icon {
        width: 69px;
        height: 72px;
        margin-right: 15px;
    }

    .bank-name {
        height: 72px;
        color: #000;
        margin-right: 35px;
    }

    .bank-zn-name {
        font-size: 30px;
        font-weight: bold;
    }

    .bank-en-name {
        font-size: 16px;
    }

    .base-show {
        height: 100%;
        display: flex;
        align-items: center;
        margin-right: 20px;
        font-size: 16px;
        color: rgba(255,255,255,.9);
    }

    .body {
        width: 100%;
        height: 801px;
        margin: 0;
        background: url('../../static/img/content-bg.jpg')no-repeat;
        background-size: 100% 100%;
        overflow: hidden;
    }

    .home-content {
        width: 1190px;
        height: 720px;
        margin: 40px 0 0 45px;
        overflow: hidden;
    }

    .main-content{height: 560px; display: flex; align-items: center; flex-direction: column; justify-content:center;}

    .notice-box {
        width: 100%;
        height: 90px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .notice-info {
        width: 1135px;
        height: 45px;
        border-radius: 5px;
        border: 1px solid #f5dfb1;
        background: #fdfaee;
        display: flex;
        align-items: center;
        justify-content: flex-start;
        color: #e9a41e;
        font-size: 20px
    }

    .notice-img {
        width: 28px;
        height: 28px;
        margin: 0 20px;
    }

    .func-list {
        width: 100%;
        height: 410px;
        display: flex;
        flex-wrap: wrap;
        overflow: hidden;
    }

    .func-item {
        width: 260px;
        height: 172px;
        background: url('../../static/img/func-item-bg.png')no-repeat;
        background-size: 100% 100%;
        margin:15px 0 15px 30px;
        border-radius: 5px;
        overflow: hidden;
        cursor: pointer;
    }
    .func-item:active{opacity: .8}

    .func-item-icon-box {
        width: 100%;
        height: 110px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .func-item-icon {
        width: 75px;
        height: 75px;
    }

    .func-item-title {
        width: 100%;
        text-align: center;
        font-size: 34px;
        color: #fff;
        /* text-shadow: 0 2px 1px #A0522D, 2px 0 1px #A0522D, -2px 0 1px #A0522D, 0 -2px 1px #A0522D; */
    }

    .statement-box {
        width: 1138px;
        height: 135px;
        background: url('../../static/img/index-border-bg.png')no-repeat;
        background-size: 100% 122px;
        background-position-y: bottom;
        margin-left: 28px;
        position: relative;
        overflow: hidden;
    }

    .statement-box-title {
        font-size: 24px;
        position: absolute;
        left: 55px;
        top: 0;
    }

    .index-information {
        width: 1078px;
        margin-top: 40px;
        margin-left: 30px;
    }

    .info-list-box {
        width: 100%;
        height: 20px;
        display: flex;
        align-items: center;
        margin-top: 10px;
    }

    .info-content {
        width: 50%;
        font-size: 16px;
        color: #000;
    }

    .info-content-sp {
        width: 100%;
        height: 20px;
        display: flex;
        align-items: center;
        font-size: 16px;
        color: #000;
        margin-top: 10px;
    }

    footer {
        width: 100%;
        height: 77px;
        background: url('../../static/img/index-footer-bg.png')no-repeat;
        background-size: 100% 100%;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .system-box {
        width: 910px;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: flex-start;
    }

    .system-info {
        height: 100%;
        font-size: 14px;
        color: #fff;
        margin-left: 20px;
    }

    .system-info p:first-child {
        margin-top: 15px;
    }

    .system-info p:last-child {
        margin-top: 10px;
    }

    .contact-service {
        width: 370px;
        height: 100%;
        margin-top: 1px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #2e7bba;
    }

    .call-logo {
        width: auto;
        height: 50px;
        margin-right: 10px;
    }

    .service-box {
        height: 100%;
        overflow: hidden;
        font-size: 20px;
        color: #fff;
    }

    .service-phone {
        margin-top: 10px;
        font-weight: 700;
    }

    .service-title {
        width: 150px;
        height: 30px;
        border-radius: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
        letter-spacing: 5px;
        color: #0995e7;
        background: #fff;
        font-weight: 600;
        font-size: 18px;
    }

    .defaultPage{text-align: center; width:100%; padding-top: 50px}
    .defaultPage img{display: inline-block; width:80px; margin:0 auto;}
    .defaultPage strong{display: block; font-size: 24px; color: #333; font-weight: normal; padding:10px;}
    .defaultPage p{display: block; font-size: 18px; color: #888 }
</style>