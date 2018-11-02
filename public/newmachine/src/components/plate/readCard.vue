<template>

    <div class="content" v-cloak>

        <Loading v-if="unLoaded" :title="loadingText"></Loading>
        <Dialog v-if="infoBomb" :text="infoNotice" :buttonconfig="dialogBtn" @hideBox="hideBox"></Dialog>

        <div class="title">
            <p>{{cardTitle}}</p>
            <div class="count-time" v-if="agent=='Windows' && paramsType=='electronics'">{{lastTime}}</div>
        </div>

        <template v-if="agent=='Android'">

            <!-- 就诊卡操作示意图 -->
            <div class="demonstration-box" v-if="cardType=='solid'">
                <p class="demonstration-title">请在机器右侧刷您的就诊卡
                    <span>(磁条朝左)</span>
                </p>
                <p class="demonstration-img">
                    <img src="../../static/img/base-right.png" class="solid-card-img">
                    <img src="../../static/img/solid-animation-right.png" class="card-right">
                </p>
            </div>

            <!-- 电子就诊卡二维码操作示意图 -->
            <div class="demonstration-box" v-if="cardType=='electronics'">
                <div class="qrcode-box">
                    <p class="qrcode-box-title">请出示您的电子就诊卡二维码</p>
                    <p class="qrcode-box-info">
                        <span>打开"***医院公众号"</span>
                        <img src="../../static/img/next.png" class="next-img">
                        <span>点开"我的"</span>
                        <img src="../../static/img/next.png" class="next-img">
                        <span>点击就诊卡右上角的二维码</span>
                    </p>
                </div>
                <p class="demonstration-img">
                    <img src="../../static/img/ele-base.png" class="solid-card-img">
                    <img src="../../static/img/ele-card-animation.png" class="electronics-card">
                </p>
            </div>

            <!-- 电子居民健康卡二维码操作示意图 -->
            <div class="demonstration-box" v-if="cardType=='electronicsHealthy'">
                <div class="qrcode-box">
                    <p class="qrcode-box-title">请出示您的电子居民健康卡二维码</p>
                    <p class="qrcode-box-info">
                        <span>打开"***医院公众号"</span>
                        <img src="../../static/img/next.png" class="next-img">
                        <span>点开"我的"</span>
                        <img src="../../static/img/next.png" class="next-img">
                        <span>点击居民健康卡右上角的二维码</span>
                    </p>
                </div>
                <p class="demonstration-img">
                    <img src="../../static/img/ele-base.png" class="solid-card-img">
                    <img src="../../static/img/ele-card-animation.png" class="electronics-card">
                </p>
            </div>

            <!-- 银行卡操作示意图 -->
            <div class="demonstration-box" v-if="cardType=='bank'">
                <p class="demonstration-title">请插入您的银行卡
                    <span>(磁条朝上)</span>
                </p>
                <p class="demonstration-img">
                    <img src="../../static/img/base-insert.png" class="solid-card-img">
                    <img src="../../static/img/bank-animation.png" class="healthy-insert">
                </p>
            </div>

            <!-- 居民健康卡操作示意图 -->
            <div class="demonstration-box" v-if="cardType=='healthy'">
                <p class="demonstration-title">请插入您的居民健康卡
                    <span>(磁条朝下)</span>
                </p>
                <p class="demonstration-img">
                    <img src="../../static/img/base-insert.png" class="solid-card-img">
                    <img src="../../static/img/healthy-animation-insert.png" class="healthy-insert">
                </p>
            </div>

            <!-- 二代社保卡操作示意图 -->
            <div class="demonstration-box" v-if="cardType=='social'">
                <p class="demonstration-title">请插入您的二代社保卡
                    <span>(磁条朝下)</span>
                </p>
                <p class="demonstration-img">
                    <img src="../../static/img/base-insert.png" class="solid-card-img">
                    <img src="../../static/img/sociel-animation.png" class="healthy-insert">
                </p>
            </div>

            <!-- 人脸识别示意图 -->
            <div class="demonstration-box" v-if="cardType=='face'">
                <p class="demonstration-title">正在进行人脸识别</p>
                <p class="demonstration-img">
                    <img src="../../static/img/face_sb_icon.png">
                </p>
            </div>

        </template>


        <!-- 内江市第一医院新机型Windows版示意图 -->

        <template v-if="agent=='Windows'">

            <!-- 就诊卡操作示意图 -->
            <div class="demonstration-box" v-if="cardType=='solid'">
                <p class="demonstration-title">请插入您的{{cardTitle}}
                    <span v-if="hospitalInfo.hospital_id === '61757'">(磁条朝左)</span>
                    <span v-else>(磁条朝右下)</span>
                </p>
                <p class="demonstration-img">
                    <img src="../../static/img/img_win_baseCard_main.png" class="solid-card-img">
                    <img src="../../static/img/solid-animation-insert.png" class="animation-img animation-to-top">
                </p>
            </div>

            <!-- 电子就诊卡操作示意图 -->
            <div class="demonstration-box" v-if="cardType=='electronics' || cardType=='electronicsHealthy'">
                <div class="qrcode-box">
                    <p class="qrcode-box-title">请出示您的{{cardTitle}}二维码</p>
                    <p class="qrcode-box-info">
                        <span>打开"***医院公众号"</span>
                        <img src="../../static/img/next.png" class="next-img">
                        <span>点开"我的"</span>
                        <img src="../../static/img/next.png" class="next-img">
                        <span>点击{{cardTitle}}右上角的二维码</span>
                    </p>
                </div>
                <p class="demonstration-img">
                    <img src="../../static/img/img_eleCard_main.png" class="solid-card-img">
                    <img src="../../static/img/ele-card-animation.png" class="electronics-card">
                </p>
            </div>

        </template>


        <!-- 输入键盘面板 -->
        <div class="demonstration-box-keyboard" v-if="cardType=='input'">
            <div class="input-box">
                <!-- <input type="text" placeholder="请输入就诊卡卡号" class="input" v-model="inputContent"> -->
                <div class="input">{{inputContent}}</div>
                <div class="confirm-btn" @click="confirmSearch">确认</div>
            </div>
            <div class="keyboard-box" v-if="keyboardConfig == 'all'">
                <div class="keyboard-item" v-for="(item,index) in keyList" @click.self="selectKey(item,index)">
                    {{item}}
                </div>
            </div>
            <div class="keyboard-box-number" v-if="keyboardConfig == 'number'">
                <div class="keyboard-item-number" v-for="(item,index) in keyListNumber"
                     @click.self="selectKey(item,index)">{{item}}
                </div>
            </div>
        </div>

    </div>
</template>
<script>
    import qs from 'qs'
    import enumerate from "../../js/enumerate";

    export default {
        data() {
            return {
                unLoaded: false,
                loadingText: "",
                infoBomb: false,
                infoNotice: "当前输入框内容均不能为空！",
                cardTitle: '',
                cardType: '',
                keyList: [1, 2, 3, 4, 5, 6, 7, 8, 9, 0, '删除', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', '清空'],
                keyListNumber: [1, 2, 3, 4, 5, 6, 7, 8, '清空', 9, 0, '删除'],
                curIndex: -1,
                inputContent: '',
                inputList: [],
                canSearch: true,
                fromName: '',
                keyboardConfig: "number",    // 键盘配置 默认number为数字键盘
                dialogBtn: ['重新输入'],

                // 系统环境
                agent: this.$agent(),
                machineError: false,

                lastTime: 30,
                timeRound: undefined,
                paramsType: '',
                hospitalInfo: {},

                audios_new: undefined
            }
        },

        created: function () {

            let paramsType = this.$route.params.type;
            this.paramsType = paramsType;
            this.hospitalInfo = JSON.parse(localStorage.getItem('hospitalInfo'));
            //请按照图示方式刷卡
            switch (paramsType) {
                case 'solid':
                    this.$audioPlay(9);
                    break;
                case 'electronics':
                    this.$audioPlay(31);
                    break;
                case 'input':
                    this.$audioPlay(39);
                    break;
            }

            this.fromName = this.$route.params.fromName;
            this.nameControll();
            // 电子卡和扫描二维码 页面30秒倒计时
            if (paramsType == "electronics") {
                this.timeRound = setInterval(() => {
                    if (this.lastTime > 0) {
                        this.lastTime--;
                    } else {
                        history.back();
                    }
                }, 1000);
            }


            // 监听刷卡动作
            if (paramsType != 'input' && paramsType != "face") {

                // windows环境下刷卡设备接口初始化
                if (this.agent == "Windows") {
                    let result = '';
                    if (this.hospitalInfo.hospital_id === '61757') {
                        result = paramsType == "electronics" || paramsType == "electronicsHealthy" ? this.$machineApi.win_intScan() : this.$machineApi.zgSettingCardCOM();

                    } else {
                        result = paramsType == "electronics" || paramsType == "electronicsHealthy" ? this.$machineApi.win_intScan() : this.$machineApi.settingCardCOM();
                    }
                    if (result == 1 || result == "error") {
                        this.infoBomb = true;
                        this.machineError = true;
                        this.infoNotice = "读卡设备初始化失败";
                        this.dialogBtn = ["返回"]
                        return;
                    }

                }

                console.log("--------开始监听刷卡---------")
                console.log("当前环境：" + this.agent)

                this.timer = setInterval(() => {

                    if (!this.canSearch) return;

                    let str = '';

                    if (this.agent == "Android") {
                        str = this.$machineApi.watchInput();
                    } else if (this.agent == "Windows") {
                        if (this.hospitalInfo.hospital_id === '61757') {
                            str = paramsType == "electronics" || paramsType == "electronicsHealthy" ? this.$machineApi.win_getScanData() : this.$machineApi.zgReadCardNumberCom();
                            if (str) {
                                for (var i = 0; i < enumerate.readCardError.length; i++) {
                                    if (str === enumerate.readCardError[i]) {
                                        str = "读卡错误";
                                        break;
                                    }
                                }
                                str = (str == "读卡错误") ? str : this.returnStr(str.split("|")[1]);

                            } else {
                                str == "读卡错误";
                            }

                        } else {
                            str = paramsType == "electronics" || paramsType == "electronicsHealthy" ? this.$machineApi.win_getScanData() : this.$machineApi.readCardNumberCOM();

                        }


                    }

                    console.log("刷卡值:" + str)
                    // alert("刷卡值:" + str);

                    if (str && str != "error" && str != "1" && str != "读卡错误") {

                        // console.log("Yes! 监听到刷卡值:" + str);
                        // alert(`Yes! 监听到刷卡值:${str}&准备查询`)

                        //非输入界面才调用与安卓通信方法
                        if (paramsType == 'electronicsHealthy') {
                            //居民健康卡查询接口
                            this.searchDetail('healthCard', str, '/card/elehealth');
                        } else {
                            //普通就诊卡查询接口
                            let cardStr = '';
                            if (this.hospitalInfo.hospital_id === '61757') {
                                cardStr = str;
                            } else {
                                cardStr = this.dealCard(str);
                            }
                            if (this.agent == "Android" && cardStr.length < 10) {
                                this.unLoaded = true;
                                this.loadingText = '读卡失败,请重新刷卡...';
                                setTimeout(() => {
                                    this.unLoaded = false;
                                }, 2000)
                                return;
                            } else {
                                if (paramsType === 'electronics' && (this.hospitalInfo.hospital_id === '61754' || this.hospitalInfo.hospital_id === '61756' || this.hospitalInfo.hospital_id === '61757')) {

                                    if (this.fromName === 'printTicket' || this.fromName === 'queryOrder') {
                                        this.searchDetail('cardId', cardStr, '/card/patient');
                                    } else {
                                        this.searchDetail('uniqueId', cardStr, '/card/getCardInfo', '122');
                                    }

                                } else {
                                    this.searchDetail('cardId', cardStr, '/card/patient');
                                }
                            }

                        }
                        this.str = '';

                    }

                    if (localStorage.getItem('getfocus')) {

                        if (this.agent == "Android") {
                            this.$machineApi.setInputFocus();
                        }

                        localStorage.removeItem('getfocus');
                    }
                }, 1000);

            } else if (paramsType == "face") {
                // 开启人脸识别
                if (this.agent == "Android") {
                    this.$machineApi.faceRec();
                }
            }
        },
        methods: {
            returnStr: function(val){
                let str = val.replace(/\u0000/g, "");
                    str = str.replace(/(^\s*)|(\s*$)/g, "");
                return str;
            },
            //路由名称函数
            nameControll: function () {
                const type = this.$route.params.type;
                this.cardType = type;
                switch (type) {
                    case 'solid':
                        this.cardTitle = '读取就诊卡';
                        break;
                    case 'electronics':
                        this.cardTitle = '读取电子就诊卡';
                        break;
                    case 'electronicsHealthy':
                        this.cardTitle = '电子居民健康卡';
                        break;
                    case 'bank':
                        this.cardTitle = '读取银行卡';
                        break;
                    case 'healthy':
                        this.cardTitle = '读取居民健康卡';
                        break;
                    case 'social':
                        this.cardTitle = '读取社保卡(二代)';
                        break;
                    case 'input':
                        this.cardTitle = '输入就诊卡卡号';
                        break;
                    case 'face':
                        this.cardTitle = '人脸识别';
                        break;
                }
            },
            //路由控制函数,根据参数确定录入信息后需要跳转的路由
            routerControll: function (fromName) {

                switch (fromName) {
                    case "register":
                        // 根据配置,是否选择院区
                        let hospitalInfo = localStorage.getItem('hospitalInfo');
                        if (hospitalInfo && JSON.parse(hospitalInfo).serviceConf.registration.XuanZeYuanQu == "1") {
                            return "selectDistrict";
                        } else {
                            return "selectDept";
                        }
                        break;
                    case "outpatient":
                        return "paymentList";
                        break;
                    case "reversionNumList":
                        return "reversionNumList";
                        break;
                    case "hospitalizationDetail":
                        return "hospitalizationDetail";
                        break;
                    case "queryOrder":
                        return "printList";
                        break;
                    case "printTicket":
                        return "printList";
                        break;
                    case "inpatientList":
                        return "inpatientList";
                        break;
                    case "sign":
                        return "signIn";
                        break;
                    case "waiting":
                        return "waitSeeDoctor";
                        break;
                    case "typeb":
                        return "needs";
                        break;
                }
            },
            //键盘按键
            selectKey: function (item, index) {
                this.curIndex = index;

                // 全键盘
                if (this.keyboardConfig == "all") {
                    //退格键
                    if (index === 10) {
                        this.inputList.pop();
                    } else if (index === 37) {
                        //清除键
                        this.inputList = [];
                    } else {
                        //其他正常按键
                        this.inputList.push(item);
                    }
                }

                // 数字键盘
                if (this.keyboardConfig == "number") {
                    //退格键
                    if (index === 11) {
                        this.inputList.pop();
                    } else if (index === 8) {
                        //清除键
                        this.inputList = [];
                    } else {
                        //其他正常按键
                        this.inputList.push(item);
                    }
                }

                this.inputContent = this.inputList.join('');
                // this.timerOut = setTimeout(() => {
                //     this.curIndex = -1;
                // }, 200)
            },
            //截取刷卡卡号
            dealCard: function (str) {
                //根据刷卡后含有的字符串判断就诊卡类型
                if (str.search(/=/) > 0) {
                    //四川省人民医院
                    if (str.split('=')[0].search(/:/) < 0) {
                        return str.split('=')[0].replace(/[^0-9]/ig, "");
                    } else {
                        //社保卡
                        return str.split('=')[1].replace(/[^0-9]/ig, "");
                    }
                } else {
                    //其他类型就诊卡
                    return str.split('?')[0].replace(/[^0-9]/ig, "");
                }
            },

            // 输入内容时确定搜索
            confirmSearch: function () {

                // 测试人员专用

                // 关闭应用
                if (this.inputContent == '010101') {
                    if (this.agent == "Android") {
                        this.$machineApi.FinishApp();
                    } else {
                        this.$machineApi.win_closeApp();
                    }
                    return;
                }
                // 清除缓存
                else if (this.inputContent == '020202') {
                    localStorage.clear();
                    this.$router.push({
                        name: 'home'
                    })
                    return;
                }

                if (this.inputContent != '') {
                    this.searchDetail('cardId', this.inputContent, '/card/patient');
                    this.inputContent = '';
                    this.inputList = [];
                } else {
                    this.infoBomb = true;
                }
            },

            //通用查询就诊人信息函数 cardId 就诊卡卡号 url 通用接口或居民健康卡专用查询接口
            searchDetail: function (cardType, cardId, url, hospitalId) {
                this.canSearch = false;
                this.unLoaded = true;
                this.loadingText = '查询中，请稍候...';
                let data = {
                    [cardType]: cardId,
                    timeStr: new Date().getTime()
                };
                // alert(`查询数据：${data}&`)
                if (hospitalId) {
                    data.hospitalId = hospitalId;
                }

                this.$http.get(this.publicUrl + url, {
                    params: data,
                    headers: this.config(JSON.parse(localStorage.getItem('hospitalInfo')).number),
                    timeout: 10000
                }).then((res) => {

                    if (this.agent == "Android") {
                        this.$machineApi.clearWatchInput();
                    }

                    //执行搜索后禁用搜索，等搜索完成后重置搜索状态

                    if (res.data.code == 10000) {

                        //播放查询成功语音

                        this.$audioPlay(5);

                        this.loadingText = '查询成功，请稍候...';

                        if (this.agent == "Windows" && this.$route.params.type == "solid") {
                            if (this.hospitalInfo.hospital_id === '61757') {
                                this.$machineApi.zgRejectcardCOM();
                            } else {
                                this.$machineApi.rejectcardCOM();
                            }
                        }

                        setTimeout(() => {
                            this.unLoaded = false;
                            this.$router.push({
                                name: this.routerControll(this.fromName)
                            });
                        }, 2000)

                        if (hospitalId) {
                            localStorage.setItem('userInfo', JSON.stringify(res.data.data[0]));
                        } else {
                            localStorage.setItem('userInfo', JSON.stringify(res.data.data));
                        }

                    } else {

                        //播放查询失败语音

                        switch (this.$route.params.type) {
                            case 'solid':
                                this.$audioPlay(6);
                                break;
                            case 'electronics':
                                this.$audioPlay(32);
                                break;
                        }

                        this.$audioPlay(6);

                        this.loadingText = res.data.code == 30000 ? res.data.msg : '查询失败,请重新录入...';
                        this.cardId = '';

                        setTimeout(() => {

                            if (this.agent == "Android") {
                                this.canSearch = true;
                                this.unLoaded = false;
                                this.$machineApi.setInputFocus();
                            }

                            if (this.agent == "Windows") {
                                if (this.$route.params.type == "solid") {
                                    if (this.hospitalInfo.hospital_id === '61757') {
                                        // this.$machineApi.zgRejectcardCOM();
                                        this.canSearch = true;
                                        this.unLoaded = false;
                                    } else {
                                        this.$machineApi.rejectcardCOM();
                                        history.back();
                                    }

                                } else if (this.$route.params.type == "electronics" || this.$route.params.type == "electronicsHealthy") {

                                    this.canSearch = true;
                                    this.unLoaded = false;

                                    // 重新初始化读卡扫描设备
                                    let result = this.$machineApi.win_intScan();
                                    if (result == 1 || result == "error") {
                                        this.infoBomb = true;
                                        this.machineError = true;
                                        this.infoNotice = "读卡设备初始化失败";
                                        this.dialogBtn = ["返回"]
                                        return;
                                    }

                                } else {
                                    this.canSearch = true;
                                    this.unLoaded = false;
                                }
                            }

                        }, 3000)
                    }

                }).catch((err) => {
                    // alert("接口调用失败或超时")
                    this.unLoaded = false;

                    this.$audioPlay(21);

                    this.dealError();
                })
            },
            //隐藏弹框
            hideBox: function () {
                this.infoBomb = false;
                if (this.machineError) {
                    history.back();
                }
            }
        },
        destroyed: function () {
            clearInterval(this.timer);

            if (this.paramsType == "solid" && this.agent == "Windows") {
                let res = '';
                if (this.hospitalInfo.hospital_id === '61757') {
                    res = this.$machineApi.zgRejectcardCOM();
                } else {
                    res = this.$machineApi.rejectcardCOM();
                }

            }

            clearInterval(this.timeRound);
        }
    }
</script>
<style scoped>
    .animation-img {
        position: absolute;
        right: 270px;
        top: 210px;
    }

    /*动画*/
    .animation-to-top {
        animation-name: toTop;
        animation-duration: 3.5s;
        animation-timing-function: linear;
        animation-delay: 0s;
        animation-iteration-count: infinite;
        animation-direction: normal;
        animation-play-state: running;
    }

    @keyframes toTop {
        0% {
            top: 300px;
        }
        50% {
            top: 210px;
        }
        100% {
            top: 300px;
        }
    }

    .demonstration-box {
        width: 1125px;
        height: 600px;
        border-radius: 10px;
        border: 1px solid #d2d5d5;
        margin-left: 35px;
        overflow: hidden;
    }

    .demonstration-title {
        width: 100%;
        height: 120px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 38px;
        margin: 0;
    }

    .demonstration-title span {
        color: #f70c0c;
    }

    .demonstration-img {
        width: 100%;
        position: relative;
        display: flex;
        justify-content: center;
    }

    .card-right {
        position: absolute;
        right: 265px;
        top: 5px;
        animation-name: cardRight;
        animation-duration: 2.5s;
        animation-timing-function: linear;
        animation-delay: 0s;
        animation-iteration-count: infinite;
        animation-direction: normal;
        animation-play-state: running;
    }

    @keyframes cardRight {
        0% {
            top: 5px;
        }
        25% {
            top: 35px;
        }
        50% {
            top: 65px;
        }
        75% {
            top: 100px;
        }
        100% {
            top: 130px;
        }
    }

    .electronics-card {
        position: absolute;
        right: 150px;
        top: 30px;
        animation-name: eleCard;
        animation-duration: 3.5s;
        animation-timing-function: linear;
        animation-delay: 0.4s;
        animation-iteration-count: infinite;
        animation-direction: normal;
        animation-play-state: running;
    }

    @keyframes eleCard {
        0% {
            right: 150px;
            top: 30px;
        }
        50% {
            right: 240px;
            top: 80px;
        }

        100% {
            right: 150px;
            top: 30px;
        }
    }

    .healthy-insert {
        position: absolute;
        right: 260px;
        top: 150px;
        animation-name: cardInsert;
        animation-duration: 2.5s;
        animation-timing-function: linear;
        animation-delay: 0s;
        animation-iteration-count: infinite;
        animation-direction: normal;
        animation-play-state: running;
    }

    @keyframes cardInsert {
        0% {
            top: 220px;
        }
        25% {

            top: 202px;
        }
        50% {

            top: 184px;
        }
        75% {
            top: 168px;
        }
        100% {
            top: 150px;
        }
    }

    .qrcode-box {
        width: 100%;
        height: 190px;
        overflow: hidden;
    }

    .qrcode-box-title {
        width: 100%;
        margin-top: 50px;
        font-size: 38px;
        text-align: center;
    }

    .qrcode-box-info {
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 30px;
        color: #838383;
        margin-top: 15px;
    }

    .next-img {
        width: 14px;
        height: 20px;
        margin: 0 15px;
    }

    .input-box {
        width: 100%;
        height: 130px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .input {
        width: 780px;
        height: 80px;
        line-height: 80px;
        border-radius: 5px 0 0 5px;
        border: 1px solid #9d9d9d;
        border-right: none;
        font-size: 38px;
        outline: none;
        text-indent: 20px;
        box-sizing: border-box;
        overflow: hidden;
    }

    .confirm-btn {
        width: 220px;
        height: 80px;
        border-radius: 0 5px 5px 0;
        background: #018ede;
        font-size: 34px;
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
    }

    .keyboard-box {
        width: 100%;
        margin-top: 20px;
        display: flex;
        flex-wrap: wrap;
        overflow: hidden;
        -webkit-tap-highlight-color: transparent;
        -webkit-user-select: none;
    }

    .keyboard-item {
        width: 88px;
        height: 88px;
        background: #1491e8;
        font-size: 42px;
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 20px;
        margin-bottom: 30px;
        border-radius: 5px;
        cursor: pointer;
    }

    .keyboard-item:nth-child(1) {
        margin-left: 20px;
    }

    .keyboard-item:nth-child(11) {
        background: #e7a100;
        margin-right: 0;
        font-size: 36px;
    }

    .keyboard-item:nth-child(12) {
        margin-left: 70px;
    }

    .keyboard-item:nth-child(22) {
        margin-left: 120px;
    }

    .keyboard-item:nth-child(30) {
        margin-right: 80px;
    }

    .keyboard-item:nth-child(31) {
        margin-left: 170px;
    }

    .keyboard-item:nth-child(38) {
        background: #e7a100;
        font-size: 36px;
    }

    .keyboard-box-number {
        width: 100%;
        margin-top: 20px;
        display: flex;
        flex-wrap: wrap;
        overflow: hidden;
        justify-content: space-between;
        padding: 0 8%;
        box-sizing: border-box;
        -webkit-tap-highlight-color: transparent;
        -webkit-user-select: none;
    }

    .keyboard-item-number {
        width: 22%;
        height: 110px;
        background: #1491e8;
        font-size: 48px;
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 30px;
        border-radius: 5px;
        cursor: pointer;
        box-shadow: 1px 1px 4px rgba(0, 0, 0, .3)
    }

    .keyboard-item-number:nth-child(9),
    .keyboard-item-number:nth-child(12) {
        background: #e7a100;
        font-size: 42px;
    }

    .keyboard-item-number:active,
    .keyboard-item:active {
        background: #4ae15b;
    }
</style>