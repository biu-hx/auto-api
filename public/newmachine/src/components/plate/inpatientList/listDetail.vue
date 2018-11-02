<template :ready>
    <div v-cloak>
        <Loading v-if="unLoaded" :title="loadingText"></Loading>
        <Dialog v-if="infoBomb" :text="infoNotice" :buttonconfig="['确定']" @hideBox="hideBox"></Dialog>
        <div class="content">
            <div class="title">
                <p>住院清单明细</p>
                <!-- <div class="total-price">
                    <p>合计：
                        <span>￥250</span>
                    </p>
                </div> -->
            </div>
            <div class="order-info">
                <div class="order-info-item">
                    <p>就诊人：{{userInfo.cardName}}</p>
                    <p>就诊卡号：{{userInfo.cardId}}</p>
                </div>
            </div>
            <div class="table" v-if="poutList.length>0">
                <div class="list-nav-header">
                    <ul class="list-nav">
                        <li class="payment-time">时间</li>
                        <li class="payment-type">项目</li>
                        <li class="payment-name">名称</li>
                        <li class="payment-unit">单位</li>
                        <li class="payment-price">单价(元)</li>
                        <li class="payment-num">数量</li>
                        <li class="payment-ishos">医保</li>
                        <li class="payment-total-price">合计(元)</li>
                    </ul>
                </div>
                <div class="item-box" v-for="(item,index) in poutList">
                    <ul class="list-nav">
                        <li class="payment-time">{{item.date}}</li>
                        <li class="payment-type">{{item.cat}}</li>
                        <li class="payment-name">{{item.name}}</li>
                        <li class="payment-unit">{{item.uom}}</li>
                        <li class="payment-price">{{item.price}}</li>
                        <li class="payment-num">{{item.qty}}</li>
                        <li class="payment-ishos" v-if="item.yb=='医保'">√</li>
                        <li class="payment-ishos" v-if="item.yb!='医保'"></li>
                        <li class="payment-total-price">{{item.amt}}</li>
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
                <p class="no-record-text">您暂无待缴费记录！</p>
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
                isPay: false,
                poutList: {},
                unLoaded: true,
                loadingText: "数据加载中...",
                infoBomb: false,
                infoBombText: "",
                userInfo: {},
                noRecordTime: 30,
                isResearch: 1,
                showList: [],
                pageIndex: 0, //页面索引
                pageNum: 5, //每页数量
                ablePrev: false,
                ableNext: true,
                pendingDetail:'',
                hospitalInfo: ''
            };
        },
        created: function () {
            this.userInfo = JSON.parse(localStorage.getItem('userInfo'));
            this.hospitalInfo = JSON.parse(localStorage.getItem('hospitalInfo'));
            this.getDetail(this.$route.params.arpbl, this.$route.params.dateFrom, this.$route.params.admId);
        },
        methods: {
            //获取详情
            getDetail:function(arpbl, dateFrom,admId){
                let obj = {
                    cardId: this.userInfo.cardId,
                    arpbl:arpbl,
                    timeStr: new Date().getTime()
                };

                if(this.hospitalInfo.hospital_id==='61760'){
                   obj.inpatientId=admId;
                }
                if(this.userInfo.UserIdKey){
                    obj.uniqueId = this.userInfo.UserIdKey
                }
                if(this.hospitalInfo.hospital_id != "10000"){
                    obj.startTime = dateFrom
                }
                this.$http.get(this.publicUrl + '/inpatient/detail', {
                    params: obj,
                    headers: this.config(this.hospitalInfo.number),
                    timeout: 10000
                }).then((res) => {
                    this.unLoaded = false;
                    if (res.data.code === 10000) {
                        this.showList = res.data.data;
                        this.poutList = this.showList ? this.showList.slice(0, this.pageNum) : [];
                    } else {
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
    .total-price {
        height: 100%;
        display: flex;
        align-items: center;
        margin-right: 10px;
        font-size: 28px;
        font-weight: normal;
    }

    .total-price p span {
        font-size: 30px;
        color: #e80300;
    }

    .order-info {
        width: 1152px;
        height: 65px;
        margin-left: 30px;
        border: 1px solid #109de8;
        overflow: hidden;
    }

    .order-info-item {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: flex-start;
        font-size: 28px;
    }

    .order-info-item p {
        width: 50%;
        text-indent: 30px;
    }

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
        height: 410px;
        margin-left: 30px;
        margin-top: 10px;
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
        height:70px;
        font-size: 20px;
        overflow: hidden;
    }

    .item-box:not(:last-child) {
        border-bottom: 1px solid #109de8;
    }

    .payment-time {
        width: 180px;
    }

    .payment-type {
        width: 130px;
    }
    .payment-name{
        width: 250px;
    }
    .payment-unit{
        width: 100px;
    }
    .payment-price{
        width: 100px;
    }
    .payment-num{
        width: 100px;
    }
    .payment-ishos{
        width: 100px;
    }
    .payment-total-price{
        width: 100px;
    }
</style>