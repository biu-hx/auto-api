<template :ready>
    <div v-cloak>
        <Loading v-if="unLoaded" :title="loadingText"></Loading>
        <Dialog v-if="infoBomb" :text="infoNotice" :buttonconfig="['确定']" @hideBox="hideBox"></Dialog>
        <div class="content">
            <div class="title">
                <p>{{typeTitle}}</p>
                <div class="count-time" v-if="poutList.length===0">{{noRecordTime}}</div>
            </div>
            <div class="table" v-if="byType=='treatment'&&poutList.length>0">
                <div class="list-nav-header">
                    <ul class="list-nav">
                        <li class="payment-brand">名称</li>
                        <li class="payment-num">价格</li>
                        <li class="payment-type">类型</li>
                        <li class="payment-num">单位</li>
                        <li class="payment-brand">品牌</li>
                    </ul>
                </div>
                <div class="item-box" v-for="item in poutList">
                    <ul class="list-nav">
                        <li class="payment-brand">{{item.inciDesc}}</li>
                        <li class="payment-num">{{item.inciPrice}}</li>
                        <li class="payment-type">{{item.incCat}}</li>
                        <li class="payment-num">{{item.incUom}}</li>
                        <li class="payment-brand">{{item.incManf}}</li>
                    </ul>
                </div>
            </div>
            <div class="table" v-if="byType=='drugs'&&poutList.length>0">
                <div class="list-nav-header">
                    <ul class="list-nav">
                        <li class="payment-num">序号</li>
                        <li class="payment-name">项目名称</li>
                        <li class="payment-num">单价</li>
                        <li class="payment-num">数量</li>
                        <li class="payment-price">金额</li>
                        <li class="payment-type">医保类别</li>
                        <li class="payment-name">分类</li>
                    </ul>
                </div>
                <div class="item-box" v-for="(item,index) in poutList">
                    <ul class="list-nav">
                        <li class="payment-num" v-html="item.number"></li>
                        <li class="payment-name">{{item.ordName}}</li>
                        <li class="payment-num">{{item.ordPrice}}</li>
                        <li class="payment-num">{{item.ordQty}}</li>
                        <li class="payment-price">{{item.ordAmt}}</li>
                        <li class="payment-type">{{item.ordInsur}}</li>
                        <li class="payment-name">{{item.ordFeeCat}}</li>
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
                <p class="no-record-text">无记录！</p>
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
                unLoaded: false,
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
                searchParams: '',
                byType: '',
                typeTitle:''
            };
        },
        created: function () {
            this.searchParams = this.$route.params;
            this.searchPrice(this.searchParams.value, this.searchParams.url);
            //根据接口参数判断查询方式，渲染不同列表
            if (this.searchParams.url.indexOf('drug') > 0) {
                this.byType = 'drugs';
                this.typeTitle='药品费物价表';
            } else {
                this.byType = 'treatment';
                this.typeTitle='诊疗费用物价表';
            }
        },
        methods: {
            //查询物价
            searchPrice: function (value, url) {
                this.unLoaded = true;
                this.$http.get(this.publicUrl + url, {
                    params: {
                        search: value,
                        timeStr: new Date().getTime()
                    },
                    headers: this.config(JSON.parse(localStorage.getItem('hospitalInfo')).number),
                    timeout: 10000
                }).then((
                    res) => {
                    this.unLoaded = false;
                    this.isResearch += 1;
                    if (res.data.code == 10000) {
                        this.showList = res.data.data;
                        this.poutList = this.showList.slice(0, this.pageNum);
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
                    this.unLoaded = false;
                    this.$audioPlay(21);
                    this.dealError();
                })
            },
            hideBox: function () {
                this.infoBomb = false;
            },
            //重新查询
            reSearch: function () {
                this.unLoaded = true;
                this.searchPrice(this.searchParams.value, this.searchParams.url);
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
    .payment-num{
        width: 80px;
    }
    .payment-name{
        width:300px;
    }
    .payment-price{
        width: 100px;
    }
    .payment-type {
        width: 180px;
    }
    .payment-brand{
        width: 380px;
    }
</style>