<template :ready>
    <div v-cloak>
        <Loading v-if="unLoaded" :title="loadingText"></Loading>
        <Dialog v-if="infoBomb" :text="infoNotice" :buttonconfig="infoBtn" @hideBox="hideBox"></Dialog>
        <div class="content" v-if="!infoBomb">
            <div class="title">
                <p>挂号订单详情</p>
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
                <div class="order-item" v-if="orderDetail.businessInfo">
                    <p>医生：{{orderDetail.businessInfo.doctorName}}</p>
                    <p>就诊序号：{{orderDetail.successInfo.queueNo}}</p>
                </div>
                <div class="order-item" v-if="orderDetail.businessInfo">
                    <p>科室：{{orderDetail.businessInfo.deptName}}</p>
                    <p>挂号费：￥{{orderDetail.price}}</p>
                </div>
                <div class="line"></div>
                <div class="order-item" v-if="orderDetail.successInfo">
                    就诊地点：{{orderDetail.successInfo.LocInfo}}
                </div>
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
                    挂号时间：{{orderDetail.pay_time}}
                </div>
            </div>
            <div class="pay-box">
                <div class="pay-btn" @click="printTicket" v-if="orderDetail.print!=0 && orderDetail.status==='1'">补打小票</div>
                <div class="pay-btn detail" @click="noPrint" v-if="orderDetail.print==0 && orderDetail.status==='1'">补打小票</div>
                <div class="pay-btn pay-item-gy" @click="refund" v-if="orderDetail.successInfo && orderDetail.successInfo.refund==1">退号退费</div>
            </div>
        </div>

        <div v-if="refendResultShow != ''" class="refundResultBox">
            <div class="innerBox">
                <dl>
                    <dt>{{refendResultShow == "1" ? "提交成功" : "提交失败"}}</dt>
                    <dd v-if="refendResultShow == '1'">
                        款项会在7个工作日内返回到你的支付账号内，请注意查收！
                    </dd>
                    <dd v-else>
                        {{refendResultShow}}
                    </dd>
                </dl>
                <div>
                    <a href="javascript:;" @click="goHome">确定</a>
                </div>
            </div>
        </div>

    </div>
</template>
<script>
    export default {
        data() {
            return {
                orderNum: '',
                defaultValue: '----',
                orderDetail:{},
                unLoaded: true,
                loadingText: '数据加载中...',
                infoBomb: false,
                infoNotice: '',
                infoBtn: ['知道了'],
                hospitalInfo: {},
                printedInfo: false,
                isRefund:false,
                refendResultShow: ''
            }
        },
        created: function () {
            this.orderNum = this.$route.params.orderNum;
            this.hospitalInfo = JSON.parse(localStorage.getItem('hospitalInfo'));
            this.userInfo = JSON.parse(localStorage.getItem('userInfo'));
            this.getDetail(this.orderNum);
        },
        methods: {
            //不能打印小票
            noPrint: function () {
                this.infoNotice = '为了节约医疗资源，最多可打印2次小票，谢谢配合！';
                this.infoBomb = true;
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
            //根据订单号查询交易详情
            getDetail: function (orderNum) {
                this.$http.get(this.publicUrl + '/query/order/detail', {
                    params: {
                        orderNum: orderNum,
                        timeStr: new Date().getTime()
                    },
                    headers: this.config(this.hospitalInfo.number),
                    timeout: 10000
                }).then((res) => {
                    this.unLoaded = false;
                    if (res.data.code == 10000) {
                        var res_data = res.data.data;
                        if(res_data.length == 0){
                            this.infoBomb = true;
                            this.infoNotice = "查询订单信息失败";
                        }else{
                            this.orderDetail = res_data;
                        }
                        
                    } else {
                        this.infoBomb = true;
                        this.infoNotice = res.data.data;
                    }
                }).catch((err) => {

                    this.$audioPlay(21);

                    this.dealError();
                })
            },
            // 退号退费
            refund: function(){
                this.infoBomb = true;
                this.isRefund = true;
                
                if(this.orderDetail.hospital_id == "61754" || this.orderDetail.hospital_id == "61756" || this.orderDetail.hospital_id == "61759" || this.orderDetail.hospital_id == "61757" || this.orderDetail.hospital_id=='61760'){
                    this.infoNotice = '<p style="font-size:28px">当日号不能退号退费，预约号在就诊时间24小时之前才可退号退费。</p>'
                                + '<p style="font-size:34px; padding-top:10px">确定要退号退费？</p>'
                    this.infoBtn = ["暂不","确定"]

                }else{
                    this.infoNotice = '<p style="font-size:28px">当日号不能退号退费，预约号在就诊时间24小时之前才可退号退费。</p>'
                                + '<p style="font-size:34px; padding-top:10px">打开手机微信“扫一扫”二维码进行退费！</p>'
                                + '<p style="margin-top:20px"><img src="./dist/qr_Refund.png" width="220"></p>';
                }
            },

            // 内江医院退号流程
            refund_hosLJ: function(){
                this.unLoaded = true;
                
                this.loadingText = '正在提交请求...';
                
                this.$http.get(this.publicUrl + '/Registration/cancel', {
                    params: {
                        orderNum: this.orderDetail.orderNum,
                        uniqueId: this.userInfo.UserIdKey,
                        timeStr: new Date().getTime()
                    },
                    headers: this.config(this.hospitalInfo.number),
                    timeout: 10000
                }).then((res) => {
                    
                    this.unLoaded = false;
                    if (res.data.code == 10000) {
                        // 提交成功
                        this.refendResultShow = "1";
                    } else {
                        // 提交失败
                        this.refendResultShow = res.data.msg;
                    }
                    
                }).catch((err) => {

                    this.$audioPlay(21);

                    this.dealError();
                })
            },

            hideBox: function (n) {
                this.infoBomb = false;
                if(this.isRefund){
                    if(this.orderDetail.hospital_id == "61754" || this.orderDetail.hospital_id == "61756" || this.orderDetail.hospital_id == "61759" || this.orderDetail.hospital_id == "61757" || this.orderDetail.hospital_id=='61760'){
                        if(n==1){
                            this.refund_hosLJ();
                        }
                    }else{
                        this.$router.push({
                            name: 'home'
                        })
                    } 
                }else{
                    history.back(-1);   
                } 
                
            },

            goHome: function(){
                this.refendResultShow = "";
                this.$router.push({
                    name: 'home'
                })
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
        height: 520px;
        margin-left: 30px;
        border: 1px solid #109de8;
        overflow: hidden;
    }

    .order-item {
        width: 1100px;
        margin-left: 25px;
        display: flex;
        justify-content: flex-start;
        margin-top: 20px;
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

    .pay-box {
        width: 100%;
        margin-top: 20px;
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

    .pay-item-gy {background-color: #fff; color: #666; margin-left: 50px}

    .detail {
        background: #787f82;
    }

    .refundResultBox{display: flex; position: fixed; z-index: 1000; top:0; left: 0; right:0; bottom:0; background-color: rgba(0,0,0,0.5); align-items: center; justify-content: center;}

    .refundResultBox .innerBox{
        width: 985px;
        padding:70px 0;
        border-radius: 15px;
        background: #fff;
        text-align: center;
    }

    .refundResultBox dt{
        font-size: 50px;
        color: #ff4039;
        font-weight: 600;
    }

    .refundResultBox dd{
        margin-top: 40px;
        font-size: 36px;
        min-height: 140px;
    }

    .refundResultBox .innerBox div{
        display: block;
        text-align: center;
    }

    .refundResultBox .innerBox div a{
        display: block;
        margin:0 auto;
        width: 180px;
        height: 60px;
        line-height: 60px;
        border-radius: 8px;
        background: #018ede;
        font-size: 26px;
        color: #fff;
    }

    

</style>