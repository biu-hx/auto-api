<template :ready>
    <div v-cloak>
        <Loading v-if="unLoaded" :title="loadingText"></Loading>
        <Dialog v-if="infoBomb" :text="infoNotice" :buttonconfig="['确定']" @hideBox="hideBox"></Dialog>
        <div class="content">
            <div class="title">
                <p>待缴费记录</p>
                <div class="count-time" v-if="poutList.length===0">{{noRecordTime}}</div>
                <div class="box-head" v-if="poutList.length>0">
                    <p class="head-title">{{userInfo.cardName}}，您有
                        <span>{{showList.length}}</span>笔待缴费记录</p>
                </div>
            </div>
            <div v-if="poutList.length>0" class="table">
                <div class="list-nav-header">
                    <ul class="list-nav">
                        <li class="payment-type">缴费类型</li>
                        <li class="payment-user">就诊人</li>
                        <li class="payment-time">卡号</li>
                        <li class="payment-time">开具时间</li>
                        <li class="payment-fee">缴费金额</li>
                        <li class="payment-operate">操作</li>
                    </ul>
                </div>
                <div class="item-box" v-for="(item,index) in poutList">
                    <ul class="list-nav">
                        <li class="payment-type">门诊缴费</li>
                        <li class="payment-user">{{userInfo.cardName}}</li>
                        <li class="payment-time">{{userInfo.cardId}}</li>
                        <li class="payment-time">{{item.payDate}}</li>
                        <li class="payment-fee">￥{{item.fee}}</li>
                        <li class="payment-operate-sp">
                            <div class="pay-btn-sp" @click="lookDetail(item.recipeId,item.payDate,item.orderNumber)">查看详情</div>
                            <div class="pay-btn" @click="immediatlyPay(item.recipeId,item.fee,item.orderNumber)">立即缴费</div>
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
                    <img src="../../../static/img/no-data.png" alt="" class="no-data-img">
                </p>
                <p class="no-record-text">暂无待缴费记录~</p>
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
                userInfo: {},
                unLoaded: true,
                loadingText: "数据加载中...",
                infoBomb: false,
                infoNotice: "",
                payInfo: {},
                noRecordTime: 30,
                isResearch: 1,
                showList: [],
                pageIndex: 0, //页面索引
                pageNum: 4, //每页数量
                ablePrev: false,
                ableNext: true,
            };
        },
        created: function () {
            this.userInfo = JSON.parse(localStorage.getItem('userInfo'));
            this.getPoutList();
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
            //立即缴费，将参数传递到选择支付方式页面
            immediatlyPay: function (recipeId, fee, orderNumber) {
                const obj = {
                    recipeId: recipeId,
                    fee: fee,
                    orderNumber: orderNumber
                }
                this.$router.push({
                    name: 'selectPayway',
                    params: {
                        registerInfo: JSON.stringify(obj)
                    }
                });
            },
            //查看详情
            lookDetail: function (recipeId, payDate, orderNumber) {
                let obj = {
                    recipeId: recipeId,
                    payDate: payDate,
                    orderNumber: orderNumber
                }
                this.$router.push({
                    name: 'paymentDetail',
                    params: {
                        registerInfo: JSON.stringify(obj)
                    }
                });
            },
            //门诊缴费列表查询
            getPoutList: function () {
                let obj = {
                    cardId: this.userInfo.cardId,
                    timeStr: new Date().getTime()
                }
                if(this.userInfo.UserIdKey){
                    obj.uniqueId = this.userInfo.UserIdKey
                }
                this.$http.get(this.publicUrl + '/payment/outpatientlist', {
                    params: obj,
                    headers: this.config(JSON.parse(localStorage.getItem('hospitalInfo')).number),
                    timeout: 10000
                }).then((res) => {
                    this.unLoaded = false;
                    this.isResearch += 1;
                    if (res.data.code === 10000) {
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
                        this.poutList = []
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
            //重新查询
            reSearch: function () {
                this.unLoaded = true;
                this.getPoutList();
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
            },
            hideBox:function(){
                this.infoBomb=false;
            }
        },
        destroyed: function () {
            clearInterval(this.timer);
        }
    };
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
        width: 150px;
    }

    .payment-user {
        width: 145px;
    }

    .payment-card {
        width: 200px;
    }

    .payment-time {
        width: 200px;
    }

    .payment-operate {
        width: 245px;
    }

    .payment-operate-sp {
        width: 245px;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: space-between !important;
    }

    .payment-fee {
        width: 140px;
    }

    .pay-btn {
        width: 112px;
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

    .pay-btn-sp {
        width: 112px;
        height: 45px;
        background: #018ede;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        border-radius: 5px;
        color: #fff;
        cursor: pointer;
    }

    .detail {
        background: #787f82;
    }
</style>