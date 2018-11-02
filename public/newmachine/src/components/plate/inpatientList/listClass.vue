<template :ready>
    <div v-cloak>
        <Loading v-if="unLoaded" :title="loadingText"></Loading>
        <Dialog v-if="infoBomb" :text="infoNotice" :buttonconfig="['确定']" @hideBox="hideBox"></Dialog>
        <div class="content">
            <div class="title">
                <p>住院清单费用类别</p>
                <div class="total-price">
                    <p>合计：
                        <span>￥250</span>
                    </p>
                </div>
            </div>
            <div class="order-info">
                <div class="order-info-item">
                    <p class="sp1">就诊人：{{userInfo.cardName}}</p>
                    <p class="sp2">就诊卡号：{{userInfo.cardId}}</p>
                </div>
                <div class="order-info-item">
                    <p class="sp1">类型：住院</p>
                    <p class="sp2">住院号：{{info.treatNo}}</p>
                </div>
                <div class="order-info-item">
                    <p class="sp1">科室：{{info.dept}}</p>
                    <p class="sp2">床位号：{{info.bedDesc}}</p>
                </div>
                <div class="order-info-item">
                    <p class="sp1">入院时间：{{info.dateFrom}}</p>
                    <p class="sp2">出院时间：{{info.dateTo}}</p>
                </div>

            </div>
            <div class="table">
                <div class="class-list">
                    <div class="list-nav-header">
                        <ul class="list-nav">
                            <li>费用类别</li>
                            <li>消费金额(元)</li>
                        </ul>
                    </div>
                    <div class="item-box" v-for="(item,index) in pendingDetail.item " v-if="index<=5">
                        <ul class="list-nav">
                            <li>{{item.arcicDesc}}</li>
                            <li>{{item.sumFee}}</li>
                        </ul>
                    </div>
                </div>
                <div class="class-list">
                    <div class="list-nav-header">
                        <ul class="list-nav">
                            <li>费用类别</li>
                            <li>消费金额(元)</li>
                        </ul>
                    </div>
                    <div class="item-box" v-for="(item,index) in pendingDetail.item " v-if="index>5">
                        <ul class="list-nav">
                            <li>{{item.arcicDesc}}</li>
                            <li>{{item.sumFee}}</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="look-detail-box">
                <div class="look-detail-btn" @click="lookDetail">查看明细</div>
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
                showList: [],
                pageIndex: 0, //页面索引
                pageNum: 4, //每页数量
                ablePrev: false,
                ableNext: true,
                classParams: '',
                pendingDetail: '',
                info: ''
            };
        },
        created: function () {
            this.userInfo = JSON.parse(localStorage.getItem('userInfo'));
            this.classParams = this.$route.params;
            this.getClass(this.classParams.admId, this.classParams.arpbl);
        },
        methods: {
            //获取分类
            getClass: function (admId, arpbl) {
                this.$http.get(this.publicUrl + '/inpatient/type', {
                    params: {
                        cardId: JSON.parse(localStorage.getItem('userInfo')).cardId,
                        admId: admId,
                        arpbl: arpbl,
                        timeStr: new Date().getTime()
                    },
                    headers: this.config(JSON.parse(localStorage.getItem('hospitalInfo')).number),
                    timeout: 10000
                }).then((res) => {
                    this.unLoaded = false;
                    if (res.data.code === 10000) {
                        this.pendingDetail = res.data.data;
                        this.info = res.data.data.info;

                    } else {
                        if (this.filterASCII(res.data.msg)) {
                            this.infoNotice = this.filterASCII(res.data.msg);
                        } else {
                            this.infoNotice = res.data.msg;
                        }
                        this.infoBomb = true;
                    }
                }).catch((err) => {

                    this.$audioPlay(21);

                    this.dealError();
                })
            },
            //查看详情
            lookDetail:function(){
                this.$router.push({
                    name: 'listDetail',
                    params:{
                        arpbl:this.classParams.arpbl
                    }
                });
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
        height: 180px;
        margin-left: 30px;
        border: 1px solid #109de8;
        overflow: hidden;
    }

    .order-info-item {
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: flex-start;
        font-size: 28px;
        margin-top: 5px;
    }

    .order-info-item .sp1 {
        width: 60%;
        text-indent: 30px;
    }
    .order-info-item .sp2 {
        width: 40%;
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
        height: 340px;
        margin-left: 30px;
        margin-top: 10px;
        border: 1px solid #109de8;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .class-list {
        width: 50%;
        height: 100%;
        overflow: hidden;
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
        width: 50%;
        list-style: none;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100%;
        white-space: nowrap;
        text-overflow: ellipsis;
        overflow: hidden;
    }

    .item-box {
        width: 100%;
        height: 40px;
        font-size: 24px;
        overflow: hidden;
        border-right: 1px solid #109de8;
    }

    .item-box:nth-child(2) {
        margin-top: 20px;
    }

    .item-box:nth-child(7) {
        margin-bottom: 20px;
    }

    .look-detail-box {
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-top: 20px;
    }

    .look-detail-btn {
        width: 165px;
        height: 52px;
        background: #018ede;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 26px;
        border-radius: 10px;
        color: #fff;
        cursor: pointer;
    }
</style>