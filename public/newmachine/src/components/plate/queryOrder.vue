<template>
    <div>
        <Loading v-if="unLoaded" :title="loadingText"></Loading>
        <Dialog v-if="infoBomb" :text="infoNotice" :buttonconfig="dialogBtn" @hideBox="hideBox"></Dialog>
        <div class="content">
            <div class="title">
                <p>{{searchTitle}}</p>
                <div class="count-time" v-if="agent=='Windows' && searchMethod=='scanQrcode'">{{lastTime}}</div>
            </div>
            <div class="demonstration-box" v-if="searchMethod=='scanQrcode'">
                <div class="qrcode-box">
                    <p class="qrcode-box-title">请出示您的小票二维码</p>
                    <p class="qrcode-box-info">
                        <span>拿出交易小票</span>
                        <img src="../../static/img/next.png" alt="" class="next-img">
                        <span>右上角二维码对准二维码扫码器</span>
                    </p>
                </div>
                <p class="demonstration-img">
                    <img v-if="agent=='Android'" src="../../static/img/order-qrcode-read.png" alt=""
                         class="solid-card-img">
                    <img v-if="agent=='Windows'" src="../../static/img/order-qrcode-read-win.png" alt=""
                         class="solid-card-img">
                </p>
            </div>
            <div class="demonstration-box-keyboard" v-if="searchMethod=='inputOrderSn'||searchMethod=='inputTreatNo'">
                <div class="input-box">
                    <div class="input">{{inputContent}}</div>
                    <div class="confirm-btn" @click.self="confirmSearch">确认</div>
                </div>
                <div class="keyboard-box">
                    <div class="keyboard-item" v-for="(item,index) in keyList" @click.self="selectKey(item,index)">
                        {{item}}
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
    export default {
        data() {
            return {
                searchTitle: '',
                searchMethod: '',
                unLoaded: false,
                loadingText: "",
                infoBomb: false,
                infoNotice: "当前输入框内容均不能为空！",
                dialogBtn: ['返回'],
                keyList: [1, 2, 3, 4, 5, 6, 7, 8, 9, 0, '删除', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K',
                    'L', 'M',
                    'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', '清空'
                ],
                curIndex: -1,
                inputContent: '',
                inputList: [],
                canSearch: true,
                fromName: '',

                agent: this.$agent(),
                lastTime: 30,
                timeRound: undefined,
                testNum: '',
                hospitalInfo: '',
            }
        },
        created: function () {
            this.nameControll();
            localStorage.removeItem('userInfo');
            this.hospitalInfo = JSON.parse(localStorage.getItem('hospitalInfo'));
            //播放语音
            if (this.searchMethod == 'scanQrcode') {
                this.$audioPlay(1);
            } else if (this.searchMethod == 'inputOrderSn') {
                this.$audioPlay(25);
            } else if (this.searchMethod == 'inputTreatNo') {
                this.$audioPlay(26);
            }


            if (this.searchMethod == 'scanQrcode') {

                //判断是否为本地环境和线上环境,调用聚焦方法
                if (this.agent == "Android") {
                    this.dsBridge.call('setF', {msg: '11'});
                }

                if (this.agent == "Windows") {
                    let result = this.$machineApi.win_intScan();
                    if (result == 1 || result == "error") {
                        this.infoBomb = true;
                        this.infoNotice = "读卡设备初始化失败";
                        this.dialogBtn = ["返回"]
                        return;
                    }

                }

                this.timer = setInterval(() => {

                    // 电子卡和扫描二维码 页面30秒倒计时
                    if (this.agent == "Windows") {
                        if (this.lastTime > 0) {
                            this.lastTime--;
                        } else {
                            history.back();
                        }
                    }

                    if (this.unLoaded) return;

                    //调用安卓提供的方法获取其他输入设备的值,并在查询失败后继续聚焦文本框

                    let content = this.agent == "Android" ? this.dsBridge.call("getInput", {msg: '11'}) : this.$machineApi.win_getScanData();

                    if (content && content != "error" && content != "1") {
                        content = this.dealCard(content);
                        this.searchListOrder('cardId', content, '/card/patient');
                        content = '';
                    }
                    if (this.agent == "Android" && localStorage.getItem('getfocus')) {
                        this.dsBridge.call('setF', {
                            msg: '11'
                        });
                        localStorage.removeItem('getfocus');
                    }
                }, 1000)

            }
        },
        methods: {
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
            //键盘按键
            selectKey: function (item, index) {
                this.curIndex = index;
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
                this.inputContent = this.inputList.join('');
                this.timerOut = setTimeout(() => {
                    this.curIndex = -1;
                }, 300)
            },
            //输入内容时确定搜索
            confirmSearch: function () {
                if (!this.isPc()) {
                    //在输入框中输入隐藏密码，关闭应用
                    if (this.inputContent == 'IWANTEXIT' || this.inputContent == 'HELLOYIHUAN' || this.inputContent ==
                        'YPZUIMEI') {
                        this.dsBridge.call('finishApp', {
                            msg: '1111'
                        })
                    }
                }
                if (this.inputContent != '') {
                    if (this.searchMethod == 'inputOrderSn') {
                        this.searchDetailOrder(this.inputContent);
                    } else if (this.searchMethod == 'inputTreatNo') {
                        //通过住院清单进入
                        if (this.$route.params.fromName === "inpatientList") {
                            if (this.hospitalInfo.hospital_id === "61757" || this.hospitalInfo.hospital_id === "61759" || this.hospitalInfo.hospital_id === "61760") {
                                this.searchListOrder('cardId', this.inputContent, '/inpatient/list');
                            }
                        }
                        //通过住院缴费进入
                        else if (this.$route.params.fromName === "hospitalizationDetail") {
                            if (this.hospitalInfo.hospital_id === "61757" || this.hospitalInfo.hospital_id === "61759" || this.hospitalInfo.hospital_id === "61760") {
                                this.searchListOrder('inpatientId', this.inputContent, '/payment/inpatient');
                            }
                        } else {
                            this.searchListOrder('treatNo', this.inputContent, '/query/order/treat');
                        }
                    }
                    this.inputContent = '';
                    this.inputList = [];
                } else {
                    this.infoBomb = true;
                }
            },
            //隐藏弹框
            hideBox: function () {
                this.infoBomb = false;
                history.back();
            },
            //路由名称函数
            nameControll: function () {
                const method = this.$route.params.method;
                this.searchMethod = method;
                switch (method) {
                    case 'scanQrcode':
                        this.searchTitle = '扫描订单二维码';
                        break;
                    case 'inputOrderSn':
                        this.searchTitle = '输入订单编号';
                        break;
                    case 'inputTreatNo':
                        this.searchTitle = '输入住院号';
                        break;
                }
            },
            //订单详情查询函数，查询成功后跳转到订单详情页 orderNum 订单编号
            searchDetailOrder: function (orderNum) {
                this.unLoaded = true;
                this.loadingText = '查询中，请稍候...';
                this.$http.get(this.publicUrl + '/query/order/detail', {
                    params: {
                        orderNum: orderNum,
                        timeStr: new Date().getTime()
                    },
                    headers: this.config(JSON.parse(localStorage.getItem('hospitalInfo')).number),
                    timeout: 10000
                }).then((res) => {
                    if (!this.isPc()) {
                        this.dsBridge.call('clearInput', {
                            msg: 'clearInput'
                        });
                    }

                    if (res.data.code == 10000) {
                        this.loadingText = '查询成功，请稍候...';
                        this.userInfo = {
                            cardName: res.data.data.cardName,
                            cardId: res.data.data.cardId
                        }
                        localStorage.setItem('userInfo', JSON.stringify(this.userInfo));
                        //根据type类型判断订单类型 2 挂号 3 取号 4门诊缴费 5住院预缴
                        if (res.data.data.type == 2 || res.data.data.type == 3) {
                            setTimeout(() => {
                                this.$router.push({
                                    name: 'registerDetail',
                                    params: {
                                        orderNum: res.data.data.orderNum
                                    }
                                })
                            }, 2000)
                        } else if (res.data.data.type == 4) {
                            setTimeout(() => {
                                this.$router.push({
                                    name: 'payDetail',
                                    params: {
                                        orderNum: res.data.data.orderNum
                                    }
                                })
                            }, 2000)
                        } else if (res.data.data.type == 5) {
                            setTimeout(() => {
                                this.$router.push({
                                    name: 'prepaymentDetail',
                                    params: {
                                        orderNum: res.data.data.orderNum
                                    }
                                })
                            }, 2000)
                        } else {
                            this.searchFail();
                        }
                    } else {
                        //播放查询失败语音

                        this.$audioPlay(2);
                        this.searchFail();
                    }
                }).catch((err) => {
                    this.$audioPlay(21);
                    this.dealError();
                })
            },

            searchFail: function () {
                this.loadingText = '查询失败，请重新录入订单号信息';
                setTimeout(() => {
                    this.unLoaded = false;
                    if (this.agent == "Android") {
                        this.dsBridge.call('setF', {
                            msg: '11'
                        });
                    } else {
                        let result = this.$machineApi.win_intScan();
                        if (result == 1 || result == "error") {
                            this.infoBomb = true;
                            this.infoNotice = "读卡设备初始化失败";
                            this.dialogBtn = ["返回"]
                            return;
                        }
                    }
                }, 2000)
            },

            //订单列表查询函数， 查询成功后跳转到订单列表页(通过住院号或扫描订单二维码查询方式)
            searchListOrder: function (byType, content, url) {
                this.unLoaded = true;
                this.loadingText = '查询中，请稍候...';
                this.$http.get(this.publicUrl + url, {
                    params: {
                        [byType]: content,
                        timeStr: new Date().getTime()
                    },
                    headers: this.config(JSON.parse(localStorage.getItem('hospitalInfo')).number),
                    timeout: 10000
                }).then((res) => {
                    if (!this.isPc()) {
                        this.dsBridge.call('clearInput', {
                            msg: '111'
                        });
                    }

                    if (res.data.code == 10000) {
                        if (res.data.data.length > 0 || res.data.data) {
                            this.loadingText = '查询成功，请稍候...';
                            //通过住院缴费&住院清单进入 输入住院号 查询
                            if (this.searchMethod === "inputTreatNo") {
                                if (byType === 'inpatientId' || byType === 'cardId') {
                                    if (this.$route.params.fromName === "hospitalizationDetail") {
                                        this.userInfo = {
                                            cardName: res.data.data.patientName,
                                            cardId: res.data.data.treatNo
                                        }
                                    }
                                    if (this.$route.params.fromName === "inpatientList") {
                                        if (res.data.data.length > 0) {
                                            this.userInfo = {
                                                cardName: res.data.data[0].patName,
                                                cardId: res.data.data[0].admId
                                            }
                                        } else {
                                            this.$audioPlay(2);
                                            this.searchFail();
                                            return;
                                        }

                                    }
                                    localStorage.setItem('userInfo', JSON.stringify(this.userInfo));
                                    this.$router.push({
                                        name: this.$route.params.fromName,
                                        params: {
                                            treatData: res.data.data
                                        }
                                    });
                                    return;
                                }
                                else if (byType == 'treatNo') {
                                    //自贡妇幼通过订单进入 住院号 查询
                                    if (res.data.data.length > 0) {
                                        if (this.hospitalInfo.hospital_id === "61757" || this.hospitalInfo.hospital_id === "61759" || this.hospitalInfo.hospital_id === "61760") {
                                            this.userInfo = {
                                                cardName: res.data.data[0].cardName,
                                                cardId: res.data.data[0].cardId,
                                                orderList: res.data.data
                                            }
                                        } else {
                                            this.userInfo = {
                                                cardName: res.data.data[0].cardName,
                                                cardId: res.data.data[0].cardId
                                            }
                                        }
                                    } else {
                                        this.$audioPlay(2);
                                        this.searchFail();
                                        return;
                                    }
                                    // if (this.hospitalInfo.hospital_id === "61757" || this.hospitalInfo.hospital_id === "61759") {
                                    //     this.userInfo = {
                                    //         cardName: res.data.data[0].cardName,
                                    //         cardId: res.data.data[0].cardId,
                                    //         orderList: res.data.data
                                    //     }
                                    // } else {
                                    //     this.userInfo = {
                                    //         cardName: res.data.data[0].cardName,
                                    //         cardId: res.data.data[0].cardId
                                    //     }
                                    // }
                                }
                            } else {
                                this.userInfo = {
                                    cardName: res.data.data.cardName,
                                    cardId: res.data.data.cardId
                                }
                            }
                            setTimeout(() => {
                                this.unLoaded = false;
                                this.$router.push({
                                    name: 'printList'
                                });
                            }, 2000)
                            localStorage.setItem('userInfo', JSON.stringify(this.userInfo));
                        } else {
                            //播放查询失败语音

                            this.$audioPlay(2);

                            this.searchFail();
                        }

                    } else {
                        //播放查询失败语音

                        this.$audioPlay(2);
                        this.searchFail();
                    }
                }).catch((err) => {
                    this.$audioPlay(21);

                    this.dealError();
                })
            }
        },
        destroyed: function () {
            clearInterval(this.timer);
            clearInterval(this.timeRound);
        }
    }
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

    .demonstration-title {
        width: 100%;
        height: 170px;
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
        text-align: center;
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
        margin-top: 25px;
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
        width: 750px;
        height: 80px;
        line-height: 80px;
        border-radius: 5px;
        border: 1px solid #9d9d9d;
        font-size: 38px;
        outline: none;
        text-indent: 20px;
        box-sizing: border-box;
        overflow: hidden;
    }

    .confirm-btn {
        width: 225px;
        height: 80px;
        border-radius: 10px;
        background: #018ede;
        font-size: 34px;
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-left: 25px;
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

    .keyboard-item:active {
        background: #4ae15b;
    }

    .press-key {
        background: #4ae15b;
    }
</style>