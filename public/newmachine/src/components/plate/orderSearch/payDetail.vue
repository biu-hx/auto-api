<template :ready>
    <div v-cloak>
        <Loading v-if="unLoaded" :title="loadingText"></Loading>
        <Dialog v-if="infoBomb" :text="infoNotice" :buttonconfig="['确定']" @hideBox="hideBox"></Dialog>
        <div class="content">
            <div class="title">
                <p>{{showItems?'缴费详情 - 项目明细':'门诊缴费 - 缴费详情'}}</p>
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

            <template v-if="!showItems">
                <div class="order-info">
                    <div class="order-info-item">
                        <p>交易单号：{{orderDetail.orderNum}}</p>
                        <p>就诊人：{{orderDetail.cardName}}</p>
                    </div>
                    <div class="order-info-item">
                        <p>就诊卡号：{{orderDetail.cardId}}</p>
                        <p>缴费时间：{{orderDetail.pay_time}}</p>
                    </div>
                    <div class="order-info-item">
                        <p>支付方式：{{getpayType(orderDetail.pay_type)}}</p>
                        <p>支付终端：{{orderDetail.number}}</p>
                    </div>
                </div>
                <div class="guidance">
                    <h3>导诊信息</h3>
                    <div class="items" v-if="orderDetail.successInfo">
                        <template v-if="hospitalInfo.hospital_id=='10000'">
                            <div class="items-item" v-if="orderDetail.successInfo.direct" v-for="(item,index) in orderDetail.successInfo.direct">
                                <h4>{{index+1}}、{{item.title}}</h4>
                                <p v-for="(itemsub,indexsub) in item.items">{{indexsub+1}}、{{itemsub.title}}</p>
                            </div>
                            <div class="noguidance" v-if="!orderDetail.successInfo.direct">无导诊信息~</div>
                        </template>
                        <template v-if="hospitalInfo.hospital_id=='61754'">
                            <div class="noguidance" v-if="orderDetail.successInfo.remark">{{orderDetail.successInfo.remark}}</div>
                        </template>
                    </div>
                </div>
            </template>

            <template v-if="showItems">
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
                            <div v-if="orderDetail.businessInfo" v-for="(item,index) in orderDetail.businessInfo">
                                <ul class="detail-list" v-for="(childitem,childIndex) in item.items">
                                    <li class="detail-item-title">{{item.cateName}}</li>
                                    <li class="detail-item-type">{{childitem.itemName}}</li>
                                    <li class="detail-item-num">{{childitem.quantity}}</li>
                                    <li class="detail-item-price">￥{{childitem.unit}}</li>
                                    <li class="detail-item-total-price">￥{{childitem.fee}}</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="order-price">
                        <p>合计：
                            <span>￥{{orderDetail.price}}</span>
                        </p>
                    </div>
                </div>
            </template>
            <div class="pay-box">
                <div class="pay-btn" @click="printTicket">补打小票</div>
                <div class="pay-btn" @click="showPayList(0)" :class="!showItems?'pay-item':'pay-item-gy'">缴费详情</div>
                <div class="pay-btn" @click="showPayList(1)" :class="showItems?'pay-item':'pay-item-gy'">项目明细</div>
            </div>
        </div>
    </div>
</template>
<script>
    export default {
        data() {
            return {
                orderNum:'',
                orderDetail: {},
                unLoaded: true,
                loadingText: '数据加载中...',
                selectedList: [],
                qrcode: '',
                hospitalInfo: {},
                fee: '',
                infoBomb: false,
                infoNotice: '',
                printedInfo: false,
                showItems: false
            }
        },
        created: function () {
            this.orderNum = this.$route.params.orderNum;
            this.getDetail(this.orderNum);
            this.hospitalInfo = JSON.parse(localStorage.getItem('hospitalInfo'));
        },
        methods: {
            //根据订单号查询交易详情
            getDetail: function (orderNum) {
                this.$http.get(this.publicUrl + '/query/order/detail', {
                    params: {
                        orderNum: orderNum,
                        timeStr: new Date().getTime()
                    },
                    headers: this.config(JSON.parse(localStorage.getItem('hospitalInfo')).number),
                    timeout: 10000
                }).then((res) => {
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
                if(this.orderDetail.print!=0){
                    this.$router.push({
                        name: "printPage",
                        params: {
                            orderNum: this.orderNum,
                            num: 1
                        }
                    });
                }else if(this.orderDetail.print==0){
                    this.infoNotice='为了节约医疗资源，最多可打印2次小票，谢谢配合！';
                    this.infoBomb=true;
                }
                
            },
            showPayList: function(num){
                this.showItems = (num == 0 ? false : true);
            },
            hideBox: function () {
                this.infoBomb = false;
            },
            getpayType: function(type){
                switch(type){
                    case "1":
                      return "微信扫码支付"
                      break;
                    case "2":
                      return "支付宝扫码支付"
                      break;
                    case "3":
                      return "银联支付"
                      break;
                }
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
        height: 150px;
        margin-left: 30px;
        border: 1px solid #109de8;
        overflow: hidden;
        padding:10px 0;
        box-sizing: border-box;
    }

    .order-info-item {
        width: 100%;
        height: 33.3%;
        display: flex;
        align-items: center;
        justify-content: flex-start;
        font-size: 28px;
    }

    .order-info-item p {
        width: 50%;
        text-indent: 30px;
    }
    .guidance{
        width: 1152px;
        height: 360px;
        margin-left: 30px;
        border: 1px solid #109de8;
        overflow: hidden;
        margin-top: 20px;
        box-sizing: border-box;
    }
    .guidance{line-height: 40px; padding:15px 30px}
    .guidance h3{font-size: 30px; font-weight: normal; margin:0; padding:0 0 10px 0;}
    .guidance .items{
        height: 280px;
        overflow-y: scroll;
    }
    .guidance .items-item h4, 
    .guidance .items-item p{font-size: 26px; font-weight: normal; margin:0; padding:0; }
    .guidance .items-item p{padding-left:40px}
    .noguidance{height: 260px; line-height: 200px; text-align: center; color: #888; font-size: 24px; font-weight: normal;}
    .table {
        width: 1152px;
        height: 528px;
        margin-left: 30px;
        border: 1px solid #109de8;
        overflow: hidden;
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
        height: 50px;
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
        height: 390px;
        overflow: hidden;
    }

    .detail-list-box {
        width: 1140px;
        height: 390px;
        overflow-y: scroll;
    }

    .pay-box {
        width: 100%;
        margin-top: 18px;
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
    .pay-item {background-color: #109de8; margin-left: 50px}
    .pay-item-gy {background-color: #fff; color: #666; margin-left: 50px}

    .detail {
        background: #787f82;
    }

    .order-price {
        width: 100%;
        height: 75px;
        display: flex;
        align-items: center;
        justify-content: flex-end;
        font-size: 28px;
    }

    .order-price p span {
        font-size: 30px;
        color: #e80300;
        font-weight: normal;
        margin-right: 50px;
    }
</style>