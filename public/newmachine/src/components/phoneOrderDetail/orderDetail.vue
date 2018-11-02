<template>
    <div v-cloak>
        <!-- 挂号订单详情 -->
        <div v-if="orderType=='register'">
            <header>
                <p>挂号详情</p>
            </header>
            <div class="order-box">
                <div class="order-detail">
                    <p class="order-detail-title">订单号</p>
                    <p class="order-detail-content">{{orderDetail.order_sn}}</p>
                </div>
                <div class="order-detail">
                    <p class="order-detail-title">姓名</p>
                    <p class="order-detail-content">{{orderDetail.userName}}</p>
                </div>
                <div class="order-detail">
                    <p class="order-detail-title">看诊时间</p>
                    <p class="order-detail-content">{{orderDetail.date}}</p>
                </div>
                <div class="order-detail">
                    <p class="order-detail-title">科室</p>
                    <p class="order-detail-content">{{orderDetail.deptName}}</p>
                </div>
                <div class="order-detail">
                    <p class="order-detail-title">医生</p>
                    <p class="order-detail-content">{{orderDetail.doctorName}}</p>
                </div>
                <div class="order-detail">
                    <p class="order-detail-title">就诊地点</p>
                    <p class="order-detail-content">{{orderDetail.duty_hos_address}}</p>
                </div>
                <div class="order-detail">
                    <p class="order-detail-title">就诊序号</p>
                    <p class="order-detail-content">{{orderDetail.duty_doc_id}}</p>
                </div>
                <div class="order-detail">
                    <p class="order-detail-title">备注</p>
                    <p class="order-detail-content">{{orderDetail.remark}}</p>
                </div>
            </div>
        </div>
        <!-- 住院预缴订单详情 -->
        <div v-if="orderType=='prepayment'">
            <header>
                <p>住院预缴详情</p>
            </header>
            <div class="order-box">
                <div class="order-detail">
                    <p class="order-detail-title">姓名</p>
                    <p class="order-detail-content">{{orderDetail.userName}}</p>
                </div>
                <div class="order-detail">
                    <p class="order-detail-title">住院号</p>
                    <p class="order-detail-content">{{orderDetail.inhospital_treat_no}}</p>
                </div>
                <div class="order-detail">
                    <p class="order-detail-title">交易时间</p>
                    <p class="order-detail-content">{{orderDetail.addtime}}</p>
                </div>
                <div class="order-detail">
                    <p class="order-detail-title">床号</p>
                    <p class="order-detail-content">{{orderDetail.inhospital_patient_id}}</p>
                </div>
            </div>
            <div class="frame">
                <div class="pre-info">
                    <p>出院中心：打印发票和出院证盖章</p>
                    <p>住院清单在自助机上打印</p>
                </div>
                <div class="drugs-box">
                    <div class="drugs-title">
                        <p>项目明细</p>
                    </div>
                    <div class="price-list">
                        <div class="price-item">
                            <p>总费用</p>
                            <p>￥{{orderDetail.inhospital_fee}}</p>
                        </div>
                        <div class="price-item">
                            <p>预缴金总额</p>
                            <p>￥{{orderDetail.inhospital_fee}}</p>
                        </div>
                        <div class="price-item">
                            <p>退还金额</p>
                            <p>￥{{orderDetail.inhospital_fee}}</p>
                        </div>
                    </div>
                </div>
                <div class="refund-info">
                    <p>应退还金额会在24小时内退回您的银行卡，请注意查收！</p>
                </div>
            </div>
        </div>
        <!-- 门诊缴费订单详情 -->
        <div v-if="orderType=='outpatient'">
            <header>
                <p>门诊缴费详情</p>
            </header>
            <div class="patient-box">
                <div class="patient-info">
                    <div class="patient-content">
                        <p>姓名：{{orderDetail.userName}}</p>
                        <p>卡号：{{orderDetail.cardId}}</p>
                        <p>登记号：{{orderDetail.order_sn}}</p>
                    </div>
                    <img :src="orderDetail.order_sn_qrcode" alt="" class="qrcode">
                </div>
            </div>
            <p class="info">注：就诊时须出示此页面</p>
            <div class="frame">
                <div class="guide-box">
                    <p class="guide-box-title">导诊信息</p>
                    <p class="guide-box-title">{{orderDetail.remark}}</p>
                </div>
                <div class="drugs-box">
                    <div class="drugs-title">
                        <p>项目明细</p>
                        <p class="drugs-title-price">合计：<span>￥{{orderDetail.advice_fee}}</span></p>
                    </div>
                    <ul class="drugs-list">
                        <li class="drugs-list-name">名称</li>
                        <li class="drugs-list-num">数量</li>
                        <li class="drugs-list-price">价格</li>
                    </ul>
                    <ul class="list-item" v-for="item in drugsList">
                        <li class="drugs-list-name">{{item.itemName}}</li>
                        <li class="drugs-list-num">{{item.quantity}}</li>
                        <li class="drugs-list-price">￥{{item.price}}</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
    export default {
        data() {
            return {
                orderSn: '',
                orderDetail: {},
                orderType: 'register',
                drugsList: []
            }
        },
        created: function() {
            this.orderSn = this.$route.params.orderSn;
            this.getDetail(this.orderSn);
            //根据订单前2位判断订单类型，显示不同的订单详情
            let str = this.orderSn.slice(0, 2);
            if (str == 'RE') {
                this.orderType = 'register';
            } else if (str == 'AD') {
                this.orderType = 'outpatient';
            } else if (str == 'HO') {
                this.orderType = 'prepayment';
            }
        },
        methods: {
            //根据订单号查询交易详情
            getDetail: function(orderSn) {
                let obj = {
                    orderSn: orderSn
                };
                this.publicHttp(obj, "post", "1", "/Machine/OrderPQuery", (res) => {
                    if (res.data.code == 10000) {
                        this.orderDetail = res.data.data;
                        if (res.data.data) {
                            this.drugsList = this.orderDetail.advice.categories;
                        }
                    }
                });
            },
        }
    }
</script>
<style scoped>
    header {
        width: 100%;
        height: 45px;
        display: flex;
        align-items: center;
        justify-content: flex-start;
        border-bottom: 1px solid #eaeaea;
        font-size: 18px;
        color: #333;
    }
    header p {
        margin-left: 3%;
    }
    .order-box {
        width: 100%;
        overflow: hidden;
        border-bottom: 1px solid #eaeaea;
    }
    .order-detail {
        width: 94%;
        height: 35px;
        margin-left: 3%;
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-size: 18px;
    }
    .order-detail-title {
        color: #666;
    }
    .order-detail-content {
        color: #333;
    }
    .patient-box {
        width: 100%;
        overflow: hidden;
        border-bottom: 1px solid #eaeaea;
    }
    .patient-info {
        width: 94%;
        margin-left: 3%;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .patient-content {
        height: 80px;
        margin: 20px 0;
    }
    .patient-content p {
        width: 100%;
        height: 25px;
        display: flex;
        align-items: center;
        justify-content: flex-start;
        font-size: 16px;
    }
    .qrcode {
        width: 80px;
        height: 80px;
        margin: 20px 0;
    }
    .info {
        width: 100%;
        height: 50px;
        font-size: 18px;
        color: #00824f;
        display: flex;
        align-items: center;
        justify-content: flex-start;
        text-indent: 3%;
        border-bottom: 1px solid #eaeaea;
    }
    .frame {
        width: 100%;
        overflow: hidden;
        background: #f5f5f5;
    }
    .guide-box {
        width: 100%;
        background: #fff;
        overflow: hidden;
        margin: 10px 0;
    }
    .guide-box-title {
        width: 97%;
        margin-left: 3%;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: flex-start;
        font-size: 16px;
        color: #333;
    }
    .guide-box-title:first-child {
        border-bottom: 1px solid #eaeaea;
    }
    .drugs-box {
        width: 100%;
        background: #fff;
        overflow: hidden;
    }
    .drugs-title {
        width: 97%;
        margin-left: 3%;
        height: 40px;
        border-bottom: 1px solid #eaeaea;
        font-size: 16px;
        color: #333;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .drugs-title-price {
        margin-right: 3%;
    }
    .drugs-title-price span {
        color: #ed0200;
    }
    .drugs-list {
        width: 94%;
        height: 35px;
        margin-left: 3%;
        display: flex;
        align-items: center;
        justify-content: flex-start;
        background: #f7f7f7;
        font-size: 16px;
        color: #333;
    }
    .drugs-list-name {
        width: 53%;
        margin-left: 3%;
    }
    .drugs-list-num {
        width: 18%;
        text-align: center;
    }
    .drugs-list-price {
        width: 23%;
        text-align: right;
        margin-right: 3%;
    }
    .list-item {
        width: 94%;
        min-height: 35px;
        margin-left: 3%;
        display: flex;
        align-items: center;
        justify-content: flex-start;
        font-size: 16px;
        color: #333;
    }
    .pre-info {
        width: 100%;
        overflow: hidden;
        background: #fff;
        margin: 10px 0;
    }
    .pre-info p {
        margin-left: 3%;
        font-size: 16px;
        padding: 5px 0;
    }
    .pre-info p:first-child {
        color: #ed0200;
    }
    .price-list {
        width: 97%;
        margin-left: 3%;
        overflow: hidden;
        border-bottom: 1px solid #eaeaea;
    }
    .price-item {
        width: 97%;
        margin-right: 3%;
        height: 35px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-size: 16px;
        color: #333;
    }
    .refund-info {
        width: 100%;
        font-size: 16px;
        color: #00824f;
        display: flex;
        align-items: center;
        justify-content: flex-start;
        background: #fff;
    }
    .refund-info p {
        width: 94%;
        margin: 10px 0 10px 3%
    }
</style>

