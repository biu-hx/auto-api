<template>
    <!-- <div class="model-frame">
        <div class="box">
            <div class="box-header">
                <p class="box-info">{{title}}</p>
                <img src="../../static/images/close.png" alt="" class="close-btn" @click="cancelPay">
            </div>
            <div class="box-content">
                <div class="box-content-entry">
                    <p class="start-pay">{{payTitle}}</p>
                    <p class="pay-info-type">就诊人：{{userName}}</p>
                    <p class="pay-info-type">就诊卡号：{{cardId}}</p>
                    <p class="pay-info-type" v-if="isRegister">医生：{{docName}}</p>
                    <p class="pay-info-type" v-if="isRegister">科室：{{deptName}}</p>
                    <p class="pay-info-type" v-if="isRegister">就诊时间：{{date}}
                        <span v-if="period=='am'">上午</span>
                        <span v-if="period=='pm'">下午</span>
                        <span v-if="period=='npm'">晚间</span>
                        <span v-if="period=='all'">全天</span>
                    </p>
                    <p class="pay-info-type">缴费金额：
                        <span>￥{{fee}}</span>
                    </p>
                </div>
                <div class="box-content-qrcode">
                    <div class="unpay" v-if="payStatus=='unpay'">
                        <p class="scan">请使用"扫一扫"付款</p>
                        <img :src="qrcode" alt="" class="pay-qrcode">
                        <p class="pay-count-time">请在
                            <span>{{countTime}}秒</span>内支付</p>
                    </div>
                    <div class="payed-success" v-if="payStatus=='paySuccess'">
                        <img src="../../static/images/success.png" alt="" class="other-payed-img">
                        <p class="payed-success-info">支付成功</p>
                        <p class="payed-waiting"><span>请耐心等待...</span></p>
                    </div>
                    <div class="payed-fail" v-if="payStatus=='payFailed'">
                        <img src="../../static/images/fail.png" alt="" class="other-payed-img">
                        <p class="payed-fail-info">很遗憾，缴费失败！</p>
                    </div>
                </div>
            </div>
        </div>
    </div> -->
</template>
<script>
    export default {
        data () {
            return {
                
            }
        },
        props:{
            title:{
                type:String,
                default:'门诊缴费'
            },
            payTitle:{
                type:String,
                default:'缴费信息'
            },
            userName:{
                type:String,
                default:''
            },
            cardId:{
                type:String,
                default:''
            },
            isRegister:{
                type:Boolean,
                default:false
            },
            docName:{
                type:String,
                default:''
            },
            deptName:{
                type:String,
                default:''
            },
            date:{
                type:String,
                default:''
            },
            period:{
                type:String,
                default:''
            },
            fee:{
                type:String,
                default:''
            },
            payStatus:{
                type:String,
                default:'unpay'
            },
            qrcode:{
                type:String,
                default:''
            },
            countTime:{
                type:Number,
                default:120
            }
        },
        methods: {
            cancelPay:function(){
                this.$emit('cancelPay');
            }
        }
    }
</script>
