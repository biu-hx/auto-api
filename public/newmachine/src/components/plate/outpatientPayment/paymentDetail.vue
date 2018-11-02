<template :ready>
    <div v-cloak>
        <Loading v-if="unLoaded" :title="loadingText"></Loading>
        <Dialog v-if="infoBomb" :text="infoNotice" :buttonconfig="['确定']" @hideBox="hideBox"></Dialog>
        <div class="content">
            <div class="title">
                <p>查看待缴费详情</p>
                <div class="total-price">
                    <p>合计：
                        <span>￥{{pendingDetail.fee}}</span>
                    </p>
                </div>
            </div>
            <div class="order-info">
                <div class="order-info-item">
                    <!-- <p>交易单号：{{pendingDetail.out_trade_no}}</p> -->
                    <p>就诊卡号：{{userInfo.cardId}}</p>
                    <p>就诊人：{{userInfo.cardName}}</p>
                </div>
        </div>
        <div class="table">
            <ul class="detail-title">
                <li class="detail-item-title">类型</li>
                <li class="detail-item-type">名称</li>
                <li class="detail-item-num">数量</li>
                <li class="detail-item-price">单价</li>
                <li class="detail-item-total-price">价格</li>
            </ul>
            <div class="detail-list-scroll">
                <div class="detail-list-box">
                    <ul class="detail-list" v-for="(item,index) in pendingDetail.outpatient">
                        <li class="detail-item-title">{{item.cateName}}</li>
                        <li class="detail-item-type">{{item.itemName}}</li>
                        <li class="detail-item-num">{{item.quantity}}</li>
                        <li class="detail-item-price">￥{{item.price}}/{{item.unit}}</li>
                        <li class="detail-item-total-price">￥{{item.fee}}</li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="pay-box">
            <div class="pay-btn" @click="immediatlyPay">立即缴费</div>
        </div>
    </div>
    </div>
</template>
<script>
    export default {
        data() {
            return {
                isPay: false,
                isShowDoc: false,
                userInfo: {},
                unLoaded: true,
                loadingText: '数据加载中...',
                infoBomb: false,
                infoNotice: '',
                detailParams: {},
                pendingDetail: {},
                payInfo: {},
                countTime: 120,
                payStatus: 'unpay',
                reversionTime: ''
            }
        },
        created: function () {
            this.detailParams = JSON.parse(this.$route.params.registerInfo);
            this.userInfo = JSON.parse(localStorage.getItem('userInfo'));
            this.getDetail(this.detailParams.recipeId);
        },
        methods: {
            //立即缴费,将参数传递到选择支付方式页面
            immediatlyPay: function () {
                const obj = {
                    recipeId: this.detailParams.recipeId,
                    fee: this.pendingDetail.fee,
                    orderNumber: this.detailParams.orderNumber
                }
                this.$router.push({
                    name: 'selectPayway',
                    params: {
                        registerInfo: JSON.stringify(obj)
                    }
                });
            },
            //获取缴费详情
            getDetail: function (recipeId) {
                let obj = {
                    cardId: this.userInfo.cardId,
                    recipeId: recipeId,
                    orderNumber: this.detailParams.orderNumber,
                    timeStr: new Date().getTime()
                }
                console.log(this.userInfo.UserIdKey)
                if(this.userInfo.UserIdKey){
                    obj.uniqueId = this.userInfo.UserIdKey
                }
                this.$http.get(this.publicUrl + '/payment/outpatient', {
                    params: obj,
                    headers: this.config(JSON.parse(localStorage.getItem('hospitalInfo')).number),
                    timeout: 10000
                }).then((res) => {
                    this.unLoaded = false;
                    if (res.data.code === 10000) {
                        this.pendingDetail = res.data.data;
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
            hideBox:function(){
                this.infoBomb=false;
                history.back();
            }
        },
        destroyed: function () {
            clearInterval(this.timer);
        }
    }
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

    .table {
        width: 1152px;
        height: 450px;
        margin-left: 30px;
        border: 1px solid #109de8;
        overflow: hidden;
        margin-top: 20px;
    }

    .detail-title {
        width: 100%;
        height: 60px;
        background: #109de8;
        color: #fff;
        display: flex;
        align-items: center;
        font-size: 26px;
    }

    .detail-title li {
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .detail-item-title {
        width: 220px;
    }

    .detail-item-type {
        width: 470px;
    }

    .detail-item-num {
        width: 110px;
    }

    .detail-item-price {
        width: 190px;
    }

    .detail-item-total-price {
        width: 150px;
    }

    .detail-list {
        width: 100%;
        height: 75px;
        display: flex;
        align-items: center;
        font-size: 18px;
        color: #000;
    }

    .detail-list li {
        text-align: center;
    }

    .detail-list-scroll {
        width: 1140px;
        height: 330px;
        overflow: hidden;
    }

    .detail-list-box {
        width: 1160px;
        height: 330px;
        overflow-y: scroll;
    }

    .pay-box {
        width: 100%;
        margin-top: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .pay-btn {
        width: 165px;
        height: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 26px;
        color: #fff;
        background: #ffae00;
        border-radius: 8px;
        border: 1px solid #c78802;
        cursor: pointer;
    }
</style>