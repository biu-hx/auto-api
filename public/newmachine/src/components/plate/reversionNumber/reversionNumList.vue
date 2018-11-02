<template :ready>
    <div v-cloak>
        <Loading v-if="unLoaded" :title="loadingText"></Loading>
        <Dialog v-if="infoBomb" :text="infoNotice" :buttonconfig="['确定']" @hideBox="hideBox"></Dialog>
        <div class="content">
            <div class="title">
                <p>待取号列表</p>
                <div class="count-time" v-if="poutList.length===0">{{noRecordTime}}</div>
                <div class="box-head" v-if="poutList.length>0">
                    <p class="head-title">{{userInfo.cardName}}，您有
                        <span>{{showList.length}}</span>笔待取号记录</p>
                </div>
            </div>
            <div v-if="poutList.length>0" class="table">
                <div class="list-nav-header">
                    <ul class="list-nav">
                        <li class="payment-type">缴费类型</li>
                        <li class="payment-user">就诊人</li>
                        <li class="payment-card">卡号</li>
                        <li class="payment-dept">科室</li>
                        <li class="payment-doctor">医生</li>
                        <li class="payment-time">就诊时间</li>
                        <li class="payment-fee">缴费金额</li>
                        <li class="payment-operate">操作</li>
                    </ul>
                </div>
                <div class="item-box" v-for="(item,index) in poutList">
                    <ul class="list-nav">
                        <li class="payment-type">门诊</li>
                        <li class="payment-user">{{userInfo.cardName}}</li>
                        <li class="payment-card">{{userInfo.cardId}}</li>
                        <li class="payment-dept sp">{{item.deptName}}</li>
                        <li class="payment-doctor">{{item.doctorName}}</li>
                        <li class="payment-time">{{item.date}}</li>
                        <li class="payment-fee">￥{{item.fee}}</li>
                        <li class="payment-operate">
                            <div class="pay-btn" @click="reversionNum(item.scheduleCode,item.fetchType,item.date,item.period,item.queueNo,item.fee,item.deptName,item.doctorName)">取号</div>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="page-controll" v-if="showList.length>pageNum">
                <div class="prev-page" :class="{'unflip':!ablePrev}" @click="prevPage">上一页</div>
                <div class="page-num">{{pageIndex+1}}/{{Math.ceil(showList.length/pageNum)}}</div>
                <div class="next-page" :class="{'unflip':!ableNext}" @click="nextPage">下一页</div>
            </div>
            <div class="demonstration-box" v-if="poutList.length===0">
                <p class="no-record-icon">
                    <img src="../../../static/img/no-data.png" class="no-data-img">
                </p>
                <p class="no-record-text">您暂无待取号记录！</p>
                <div class="no-record-btn">
                    <div class="research-btn" @click="reSearch">重新查询</div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
    export default {
        data() {
            return {
                poutList: {},
                loadingText: '数据加载中...',
                unLoaded: true,
                infoBomb: false,
                infoNotice: '',
                noRecordTime: 30,
                isResearch: 1,
                userInfo: {},
                showList: [],
                pageIndex: 0, //页面索引
                pageNum: 4, //每页数量
                ablePrev: false,
                ableNext: true,
            }
        },
        created: function () {
            this.getNumList();
            this.userInfo = JSON.parse(localStorage.getItem('userInfo'));
            this.timer = setInterval(() => {
                //无数据页面30秒倒计时
                if (this.poutList.length === 0) {
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
            //获取取号列表
            getNumList: function () {
                this.$http.get(this.publicUrl + '/registration/fetch', {
                    params: {
                        cardId: JSON.parse(localStorage.getItem('userInfo')).cardId,
                        timeStr: new Date().getTime()
                    },
                    headers: this.config(JSON.parse(localStorage.getItem('hospitalInfo')).number),
                    timeout: 10000
                }).then((res) => {
                    this.unLoaded = false;
                    this.isResearch += 1;
                    if (res.data.code == 10000) {
                        this.showList = res.data.data;
                        this.poutList = this.showList.slice(0, this.pageNum);
                        if (this.poutList.length === 0) {
                            //播放未查询到待缴费信息语音

                            this.$audioPlay(14);

                        }
                        if (this.isResearch >= 3 && this.poutList.length === 0) {
                            history.back(1);
                        }
                    } else {
                        this.poutList = [];
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
            //将参数传递到选择支付方式页面
            reversionNum: function (scheduleCode, fetchType, date, period, queueNo, fee, deptName, doctorName) {
                const obj = {
                    scheduleCode: scheduleCode,
                    fetchType: fetchType,
                    doctorName: doctorName,
                    deptName: deptName,
                    queueNo: queueNo,
                    date: date,
                    period: period,
                    fee: fee
                }
                this.$router.push({
                    name: 'selectPayway',
                    params: {
                        registerInfo: JSON.stringify(obj)
                    }
                });
            },
            //取消支付
            cancelPay: function () {
                this.isPay = false;
            },
            //重新查询
            reSearch: function () {
                this.unLoaded = true;
                this.getNumList();
            },
            //回到首页
            backHome: function () {
                this.$router.push({
                    name: 'home'
                })
            },
            //隐藏弹框
            hideBox: function () {
                this.infoBomb = false;
            },
            //页面显示控制函数
            pageShowControll: function () {
                if (this.showList.length - this.pageNum * this.pageIndex > this.pageNum) {
                    this.ableNext = true;
                } else {
                    this.ableNext = false;
                }
                if (this.pageIndex > 0) {
                    this.ablePrev = true;
                } else {
                    this.ablePrev = false;
                }
            },
            //上一页
            prevPage: function () {
                if (this.pageIndex > 0) {
                    this.pageIndex -= 1;
                    this.poutList = this.showList.slice(this.pageIndex * this.pageNum, this.pageIndex * this.pageNum +
                        this.pageNum);
                }
                this.pageShowControll();
            },
            //下一页
            nextPage: function () {
                if (this.showList.length - this.pageNum * this.pageIndex > this.pageNum) {
                    this.pageIndex += 1;
                    this.poutList = this.showList.slice(this.pageIndex * this.pageNum, this.pageIndex * this.pageNum +
                        this.pageNum);
                }
                this.pageShowControll();
            }
        },
        destroyed: function () {
            clearInterval(this.timer);
        }
    }
</script>
<style scoped>
    .table {
        width: 1152px;
        height: 460px;
        margin-left: 30px;
        border: 1px solid #109de8;
        overflow: hidden;
        margin-bottom: 40px;
    }

    .list-nav-header {
        width: 100%;
        height: 60px;
        background: #109de8;
        color: #fff;
        font-size: 24px;
        overflow: hidden;
    }

    .list-nav {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
    }

    .list-nav li {
        list-style: none;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100%;
    }

    .list-nav li:not(:last-child) {
        margin-right: 10px;
    }

    .item-box {
        width: 100%;
        height: 100px;
        font-size: 20px;
        overflow: hidden;
    }

    .item-box:not(:last-child) {
        border-bottom: 1px solid #109de8;
    }

    .payment-type {
        width: 110px;
    }

    .payment-user {
        width: 120px;
    }

    .payment-card {
        width: 150px;
    }

    .payment-dept {
        width: 150px;
    }

    .payment-doctor {
        width: 115px;
    }

    .payment-time {
        width: 160px;
    }

    .payment-operate {
        width: 135px;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .payment-fee {
        width: 140px;
    }

    .payment-fee-sp {
        width: 85px;
    }

    .pay-btn {
        width: 100px;
        height: 45px;
        background: #ffae00;
        color: #fff;
        font-size: 22px;
        border-radius: 5px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 1px solid #c78802;
    }
</style>