<template :ready>
    <div v-cloak>
        <Loading v-if="unLoaded" :title="loadingText"></Loading>
        <Dialog v-if="infoBomb" :text="infoNotice" @hideBox="hideBox"></Dialog>
        <div class="content">
            <div v-if="poutList.length>0">
                <div class="title">
                    <p>缴费记录</p>
                    <div class="count-time" v-if="poutList.length===0">{{noRecordTime}}</div>
                    <div class="box-head" v-if="poutList.length>0">
                        <p class="head-title">{{userInfo.cardName}}，您有
                            <span>{{showList.length}}</span>笔缴费记录</p>
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
                            <li class="payment-type" v-if="item.type=='2'">挂号</li>
                            <li class="payment-type" v-if="item.type=='3'">取号</li>
                            <li class="payment-type" v-if="item.type=='4'">门诊缴费</li>
                            <li class="payment-type" v-if="item.type=='5'">住院预缴</li>
                            <li class="payment-user">{{item.cardName}}</li>
                            <li class="payment-time">{{item.cardId}}</li>
                            <li class="payment-time">{{item.pay_time}}</li>
                            <li class="payment-fee">￥{{item.price}}</li>
                            <li class="payment-operate-sp">
                                <div class="pay-btn" @click="lookDetail(item.orderNum,item.type)">查看详情</div>
                                <div class="pay-btn" v-if="item.print!=0 && item.status==='1'"
                                     @click="printTicket(item.orderNum)">打印小票
                                </div>
                                <div class="pay-btn detail" v-if="item.print==0 && item.status==='1'" @click="noPrint">
                                    打印小票
                                </div>
                            </li>
                        </ul>
                    </div>
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
                <p class="no-record-text">您暂无缴费记录！</p>
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
                userInfo: "",
                unLoaded: true,
                loadingText: "数据加载中...",
                infoBomb: false,
                infoNotice: "",
                noRecordTime: 30,
                isResearch: 1,
                showList: [],
                pageIndex: 0, //页面索引
                pageNum: 4, //每页数量
                ablePrev: false,
                ableNext: true,
                hospitalInfo: '',
            };
        },
        created: function () {
            this.userInfo = JSON.parse(localStorage.getItem("userInfo"));
            this.hospitalInfo = JSON.parse(localStorage.getItem('hospitalInfo'));
            if (this.hospitalInfo.hospital_id === "61757" || this.hospitalInfo.hospital_id === "61759" || this.hospitalInfo.hospital_id === "61760") {
                let res = this.userInfo.orderList;
                if (res) {
                    this.unLoaded = false;
                    this.isResearch += 1;
                    this.showList = res;
                    this.poutList = this.showList.slice(0, this.pageNum);
                    if (this.isResearch >= 3 && this.poutList.length === 0) {
                        history.back(1);
                    }

                } else {
                    this.searchListOrder();
                }

            } else {
                this.searchListOrder();
            }
            this.timer = setInterval(() => {
                //无数据页面30秒倒计时
                if (this.poutList.length === 0) {
                    if (this.noRecordTime > 0) {
                        this.noRecordTime--;
                    } else {
                        this.$router.push({
                            name: "home"
                        });
                    }
                }
            }, 1000);
        },
        methods: {
            //打印小票
            printTicket: function (orderNum) {
                this.$router.push({
                    name: "printPage",
                    params: {
                        orderNum: orderNum,
                        num: 1
                    }
                });
            },
            noPrint: function () {
                this.infoNotice = '为了节约医疗资源，最多可打印2次小票，谢谢配合！';
                this.infoBomb = true;
            },
            //查看详情
            lookDetail: function (orderNum, type) {
                if (type == 2 || type == 3) {
                    this.$router.push({
                        name: "registerDetail",
                        params: {
                            orderNum: orderNum
                        }
                    });
                } else if (type == 4) {
                    this.$router.push({
                        name: "payDetail",
                        params: {
                            orderNum: orderNum
                        }
                    });
                } else if (type == 5) {
                    this.$router.push({
                        name: "prepaymentDetail",
                        params: {
                            orderNum: orderNum
                        }
                    });
                }
            },
            searchListOrder: function () {
                this.$http.get(this.publicUrl + '/query/order/patient', {
                    params: {
                        cardId: JSON.parse(localStorage.getItem("userInfo")).cardId,
                        timeStr: new Date().getTime()
                    },
                    headers: this.config(JSON.parse(localStorage.getItem('hospitalInfo')).number),
                    timeout: 10000
                }).then((res) => {
                    this.unLoaded = false;
                    this.isResearch += 1;
                    if (res.data.code === 10000) {
                        this.showList = res.data.data;
                        this.poutList = this.showList.slice(0, this.pageNum);
                        if (this.isResearch >= 3 && this.poutList.length === 0) {
                            history.back(1);
                        }
                    } else {
                        this.poutList = [];
                        if (this.filterASCII(res.data.data)) {
                            this.infoNotice = this.filterASCII(res.data.data);
                        } else {
                            this.infoNotice = res.data.data;
                        }
                        // this.infoBomb = true;

                    }
                }).catch((err) => {
                    this.unLoaded = false;

                    this.$audioPlay(21);

                    this.dealError();
                })
            },
            //重新查询
            reSearch: function () {
                this.unLoaded = true;
                this.searchListOrder();
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
        destroyed() {
            clearInterval(this.timer);
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
        font-size: 26px;
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
        white-space: nowrap;
        text-overflow: ellipsis;
        overflow: hidden;
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
        border: 1px solid #a7acab;
    }

    .detail {
        background: #787f82;
    }

    .no-record-icon {
        width: 100%;
        text-align: center;
        margin-top: 70px;
        overflow: hidden;
    }

    .no-record-text {
        width: 100%;
        text-align: center;
        font-size: 34px;
        margin-top: 60px;
        color: #666;
    }

    .no-record-btn {
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-top: 85px;
    }

    .research-btn {
        width: 165px;
        height: 52px;
        background: #018ede;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
        border-radius: 10px;
        color: #fff;
        cursor: pointer;
    }

    .no-data-img {
        width: 328px;
        height: 215px;
    }
</style>