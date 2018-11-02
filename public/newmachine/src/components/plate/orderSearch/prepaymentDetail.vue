<template :ready>
    <div v-cloak>
        <Loading v-if="unLoaded" :title="loadingText"></Loading>
        <Dialog v-if="infoBomb" :text="infoNotice" :buttonconfig="['确定']" @hideBox="hideBox"></Dialog>
        <div class="content">
            <div class="title">
                <p>住院预缴订单详情</p>
                <div class="total-price">
                    <p>
                        <span v-if="orderDetail.status=='-2'">已取消</span>
                        <span v-if="orderDetail.status=='0'">待支付</span>
                        <span v-if="orderDetail.status=='1'">已支付</span>
                        <span v-if="orderDetail.status=='2'">待退款</span>
                        <span v-if="orderDetail.status=='3'">退款中</span>
                        <span v-if="orderDetail.status=='4'">已退款</span>
                    </p>
                </div>
            </div>
            <div class="order-info">
                <div class="order-item">交易单号：{{orderDetail.orderNum}}</div>
                <div class="order-item">
                    <p>姓名：{{orderDetail.cardName}}</p>
                    <p>就诊卡号：{{orderDetail.cardId}}</p>
                </div>
                <div class="order-item">
                    <p>住院号：{{orderDetail.businessInfo.treat_no}}</p>
                    <p>入院科室：{{orderDetail.businessInfo.dept_name}}</p>
                </div>
                <div class="order-item">
                    缴费金额：<span class="fee">￥{{orderDetail.price}}</span>
                </div>
                <div class="line"></div>
                <div class="order-item">
                    支付方式：
                    <span v-if="orderDetail.pay_type=='1'">微信支付</span>
                    <span v-if="orderDetail.pay_type=='2'">支付宝支付</span>
                    <span v-if="orderDetail.pay_type=='3'">银联支付</span>
                </div>
                <div class="order-item">
                    终端编号：{{orderDetail.number}}
                </div>
                <div class="order-item">
                    交易时间：{{orderDetail.pay_time}}
                </div>
            </div>
            <div class="pay-box">
                <div class="pay-btn" @click="printTicket" v-if="orderDetail.print!=0">补打小票</div>
                <div class="pay-btn detail" @click="noPrint" v-if="orderDetail.print==0">补打小票</div>
            </div>
        </div>
    </div>
</template>
<script>
    export default {
        data() {
            return {
                orderNum: '',
                defaultValue: '————',
                orderDetail: {
                    businessInfo:'',
                    successInfo:''
                },
                unLoaded: true,
                loadingText: '数据加载中...',
                infoBomb: false,
                infoNotice: '',
                hospitalInfo: {},
                printedInfo: false
            }
        },
        created: function () {
            this.orderNum = this.$route.params.orderNum;
            this.getDetail(this.orderNum);
            this.hospitalInfo = JSON.parse(localStorage.getItem('hospitalInfo'));
        },
        methods: {
            //不能打印小票
            noPrint: function () {
                this.infoNotice='为了节约医疗资源，最多可打印2次小票，谢谢配合！';
                this.infoBomb=true;
            },
            //根据订单号查询交易详情
            getDetail: function (orderNum) {
                this.$http.get(this.publicUrl + '/query/order/detail', {
                    params: {
                        orderNum: orderNum,
                        timeStr: new Date().getTime()
                    },
                    headers: this.config(JSON.parse(localStorage.getItem('hospitalInfo')).number),
                    timeout: 10000
                }).then((
                    res) => {
                    this.unLoaded = false;
                    if (res.data.code == 10000) {
                        this.orderDetail = res.data.data;
                    } else {
                        this.infoBomb = true;
                        this.infoNotice = res.data.data;
                    }
                }).catch((err) => {

                    this.$audioPlay(21);

                    this.dealError();
                })
            },
            //补打小票
            printTicket: function () {
                this.$router.push({
                    name: "printPage",
                    params: {
                        orderNum: this.orderNum,
                        num: 1
                    }
                });
            },
            hideBox: function () {
                this.infoBomb = false;
            }
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
        height: 530px;
        margin-left: 30px;
        border: 1px solid #109de8;
        overflow: hidden;
    }

    .order-item {
        width: 1100px;
        margin-left: 25px;
        display: flex;
        justify-content: flex-start;
        margin-top: 30px;
        font-size: 28px;
    }

    .line {
        width: 1100px;
        margin-left: 25px;
        margin-top: 30px;
        border-top: 1px solid #109de8;
    }

    .order-item p {
        width: 50%;
    }
    .fee{
        color: #e80300;
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
        border: 1px solid #a7acab;
        cursor: pointer;
    }

    .detail {
        background: #787f82;
    }
</style>