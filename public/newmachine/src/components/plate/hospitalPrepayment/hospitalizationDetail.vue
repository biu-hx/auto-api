<template :ready>
    <div v-cloak>
        <Loading v-if="unLoaded" :title="loadingText"></Loading>
        <Dialog v-if="infoBomb" :text="infoNotice" :buttonconfig="['重新输入']" @hideBox="hideBox"></Dialog>
        <div class="content">
            <div class="title">
                <p>请输入您想要预缴的金额</p>
                <div class="count-time" v-if="hosInfo.length===0">{{noRecordTime}}</div>
            </div>
            <div class="detail-content" v-if="hosInfo!=''">
                <div class="detail">
                    <p class="detail-info">住院信息</p>
                    <div class="detail-list">
                        <p class="name">
                            <span class="name-title">就诊人：</span>
                            <span class="name-content">{{hosInfo.patientName}}</span>
                        </p>
                        <p class="name">
                            <span class="model-title">住院号：</span>
                            <span class="model-content">{{hosInfo.treatNo}}</span>
                        </p>
                        <p class="name">
                            <span class="model-title">入院时间：</span>
                            <span class="model-content">{{hosInfo.inDate}}</span>
                        </p>
                        <p class="name">
                            <span class="model-title-sp">住院科室：</span>
                            <span class="model-content-sp">{{hosInfo.deptName}}</span>
                        </p>
                        <p class="name">
                            <span class="model-title">总费用：</span>
                            <span class="model-content-price">￥{{hosInfo.totalFee}}</span>
                        </p>
                        <p class="name">
                            <span class="model-title">已缴金额：</span>
                            <span class="model-content-price">￥{{hosInfo.prePayFee}}</span>
                        </p>
                        <p class="name">
                            <span class="model-title">余额：</span>
                            <span class="model-content-price">￥{{hosInfo.arrearsFee}}</span>
                        </p>
                    </div>
                </div>
                <div class="money-box">
                    <div v-show="!isShowKeyboard">
                        <p class="detail-info">请输入预缴金额</p>
                        <div class="money-input-box">
                            <p class="input-box">
                                <input type="text" placeholder="请输入或选择预缴金额" class="input-number" v-model="valueNum"
                                       @input="inputNum" @focus="showKeyboard">
                            </p>
                            <div class="operation-btn">
                                <div class="btn" v-bind:class="isEmpty ? 'able':''" @click="clear">更正</div>
                                <div class="btn" @click="confirmPay">确定</div>
                            </div>
                            <div class="price-box">
                                <p class="price-item" v-for="(item,index) in priceList"
                                   v-bind:class="{'cur-select' : index===curIndex}" @click="selectAmount(item,index)">
                                    {{item}}元</p>
                                <!--<p class="price-item" v-if="hospitalInfo.hospital_id==='61754'?index<4:'false'" v-for="(item,index) in priceList"-->
                                   <!--v-bind:class="{'cur-select' : index===curIndex}" @click="selectAmount(item,index)">-->
                                    <!--{{item}}元</p>-->
                            </div>
                        </div>
                    </div>
                    <div v-show="isShowKeyboard">
                        <div class="money-input-box">
                            <p class="input-box-keyboard">
                                <input type="text" placeholder="请输入100-20000的整数" class="input-number" v-model="valueNum"
                                       @input="inputNum">
                            </p>
                            <div class="operation-btn">
                                <div class="back-btn" @click="hideKeyboard">返回</div>
                                <div class="btn" @click="confirmPay">确定</div>
                            </div>
                            <div class="keyboard">
                                <div class="keyboard-item" v-for="(item,index) in keyNumList" :key="item"
                                     @click="selectKey(item,index)" :class="{'press-key':index==curKeyIndex}">{{item}}
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <div class="demonstration-box" v-if="hosInfo.length===0">
                <p class="no-record-icon">
                    <img src="../../../static/img/no-data.png" alt="" class="no-data-img">
                </p>
                <p class="no-record-text">您暂无住院缴费记录！</p>
                <div class="no-record-btn">
                    <div class="research-btn" @click="reSearch">重新查询</div>
                </div>
            </div>
            <!--内江限额提示-->
            <!--<div v-if="hospitalInfo.hospital_id==='61754'">-->
                <!--温馨提示：单笔最大住院预交金额1万元；-->
            <!--</div>-->
        </div>
    </div>
</template>
<script>
    export default {
        data() {
            return {
                valueNum: '',
                isEmpty: true,
                priceList: ['1000', '2000', '5000', '10000', '15000', '20000'],
                keyNumList: [1, 2, 3, 4, 5, 6, 7, 8, '清空', 9, 0, '删除'],//数字键盘
                hosInfo: false,
                unLoaded: true,
                loadingText: '数据加载中...',
                infoBomb: false,
                infoNotice: '请输入或选择预缴金额！',
                index: 0,
                curIndex: -1,
                hospitalInfo: {},
                userInfo: {},
                noRecordTime: 30,
                isResearch: 1,
                isShowKeyboard: false,
                curKeyIndex: -1,
                inputList: [],
            }
        },
        created: function () {
            this.hospitalInfo = JSON.parse(localStorage.getItem('hospitalInfo'));
            this.userInfo = JSON.parse(localStorage.getItem('userInfo'));
            if (this.hospitalInfo.hospital_id === "61757" || this.hospitalInfo.hospital_id === "61759" || this.hospitalInfo.hospital_id === "61760") {
                if (this.$route.params.treatData) {
                    this.hosInfo = this.$route.params.treatData;
                    this.unLoaded = false;
                } else {
                    this.getPatientInfo();
                }

            } else {
                this.getPatientInfo();
            }
            this.timer = setInterval(() => {
                //无数据页面30秒倒计时
                if (this.hosInfo.length === 0) {
                    if (this.noRecordTime > 0) {
                        this.noRecordTime--;
                    } else {
                        this.$router.push({
                            name: 'home'
                        });
                    }
                }
            }, 1000)
        },
        methods: {
            //输入金额
            inputNum: function () {
                if (this.valueNum !== '') {
                    this.isEmpty = false;
                } else {
                    this.isEmpty = true;
                }
            },
            //隐藏预缴信息弹框
            hideBomb: function () {
                this.isPay = false;
            },
            //清除填写内容
            clear: function () {
                this.valueNum = '';
                this.isEmpty = true;
            },
            //确定预缴，通过验证后跳转到选择支付方式页面
            confirmPay: function () {
                if (this.valueNum != '' && this.valueNum != 0) {
                    if (this.valueNum % 1 !== 0) {
                        this.infoNotice = '金额只能为100的整数倍';
                        this.infoBomb = true;
                    } else {
                        const obj = {
                            cardId: this.userInfo.cardId,
                            treatNo: this.hosInfo.treatNo,
                            fee: this.valueNum
                        }
                        this.$router.push({
                            name: 'selectPayway',
                            params: {
                                business: 5,
                                registerInfo: JSON.stringify(obj)
                            }
                        });

                    }
                } else {
                    this.infoNotice = '请输入或选择预缴金额！';
                    this.infoBomb = true;
                }
            },
            //关闭提醒弹框
            hideBox: function () {
                this.infoBomb = false;
            },
            //选择金额
            selectAmount: function (item, index) {
                this.valueNum = item;
                this.isEmpty = false;
                this.curIndex = index;
            },
            //重新查询
            reSearch: function () {
                this.unLoaded = true;
                this.getPatientInfo();
            },
            //通过就诊卡号查询用户信息
            getPatientInfo: function () {
                let obj = {
                    cardId: this.userInfo.cardId,
                    timeStr: new Date().getTime()
                }
                if (this.userInfo.UserIdKey) {
                    obj.uniqueId = this.userInfo.UserIdKey
                }
                console.log(obj)
                this.$http.get(this.publicUrl + '/payment/inpatient', {
                    params: obj,
                    headers: this.config(JSON.parse(localStorage.getItem('hospitalInfo')).number),
                    timeout: 10000
                }).then((res) => {
                    this.unLoaded = false;
                    this.isResearch += 1;
                    if (res.data.code == 10000) {
                        this.hosInfo = res.data.data;
                        //播放输入金额提示语音
                        this.$audioPlay(13);
                    } else if (res.data.code == 30000) {
                        this.hosInfo = [];
                        //播放未查询到住院信息语音
                        this.$audioPlay(15);
                        if (this.isResearch >= 3 && this.hosInfo) {
                            history.back(1);
                        }
                    } else {
                        this.hosInfo = [];
                        if (this.filterASCII(res.data.msg)) {
                            this.infoNotice = this.filterASCII(res.data.msg);
                        } else {
                            this.infoNotice = res.data.msg;
                        }
                        // this.infoBomb = true;
                    }
                }).catch((err) => {

                    this.$audioPlay(21);

                    this.dealError();
                })
            },
            //输入框获得焦点时显示键盘
            showKeyboard: function () {
                this.isShowKeyboard = true;
            },
            //返回隐藏输入键盘
            hideKeyboard: function () {
                this.isShowKeyboard = false;
            },
            //键盘按键
            selectKey: function (item, index) {
                this.curKeyIndex = index;
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
                this.valueNum = this.inputList.join('');
                this.timerOut = setTimeout(() => {
                    this.curKeyIndex = -1;
                }, 300)
            },
        },
        destroyed: function () {
            clearInterval(this.timer);
        }
    }
</script>
<style scoped>
    .detail-content {
        width: 1090px;
        height: 550px;
        margin-left: 55px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .detail {
        width: 372px;
        height: 100%;
        border-radius: 5px;
        overflow: hidden;
        border: 1px solid #2facfb;
    }

    .detail-info {
        width: 100%;
        height: 80px;
        font-size: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #0093f4;
    }

    .detail-list {
        width: 320px;
        margin-left: 25px;
        overflow: hidden;
        border-top: 1px solid #2facfb;
    }

    .name {
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: flex-start;
        font-size: 28px;
        margin-top: 15px;
    }

    .model-title-sp {
        display: block;
        width: 44%;
    }

    .model-content-sp {
        display: block;
        width: 56%;
    }

    .money-box {
        width: 620px;
        height: 100%;
        border-radius: 5px;
        overflow: hidden;
        border: 1px solid #2facfb;
    }

    .money-input-box {
        width: 560px;
        margin-left: 32px;
        overflow: hidden;
        border-top: 1px solid #2facfb;
    }

    .money-title {
        width: 100%;
        font-size: 38px;
        margin-top: 72px;
        text-align: center;
        color: #0081d5;
    }

    .input-box {
        width: 100%;
        margin-top: 50px;
        text-align: center;
    }

    .input-box-keyboard {
        width: 100%;
        margin-top: 20px;
        text-align: center;
    }

    .input-number {
        width: 99%;
        height: 74px;
        border: solid 1px #2facfb;
        border-radius: 5px;
        outline: none;
        font-size: 30px;
        text-indent: 20px;
        background: #fff;
    }

    .operation-btn {
        width: 100%;
        height: 110px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .btn {
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

    .back-btn {
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
        margin-right: 65px;
    }

    .able {
        background: #787f82;
    }

    .btn:first-child {
        margin-right: 65px;
    }

    .price-box {
        width: 100%;
        margin-top: 20px;
        display: flex;
        flex-wrap: wrap;
        overflow: hidden;
    }

    .price-item {
        width: 165px;
        height: 65px;
        border-radius: 5px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
        color: #108ce2;
        border: 1px solid #0081d5;
        margin-bottom: 30px;
        margin-right: 25px;
        cursor: pointer;
    }

    .price-item:nth-child(3) {
        margin-right: 0;
    }

    .price-item:nth-child(6) {
        margin-right: 0;
    }

    .cur-select {
        background: #108ce2;
        color: #fff;
    }

    .keyboard {
        width: 520px;
        margin-left: 20px;
        margin-top: 0;
        display: flex;
        flex-wrap: wrap;
        overflow: hidden;
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
        margin-right: 55px;
        margin-bottom: 30px;
        border-radius: 5px;
        cursor: pointer;
    }

    .keyboard-item:nth-child(4) {
        margin-right: 0;
    }

    .keyboard-item:nth-child(8) {
        margin-right: 0;
    }

    .keyboard-item:nth-child(9) {
        background: #e7a100;
        font-size: 36px;
    }

    .keyboard-item:nth-child(12) {
        margin-right: 0;
        background: #e7a100;
        font-size: 36px;
    }

    .press-key {
        background: #4ae15b;
    }
</style>