<template>
    <div class="doctor_order">
        <Loading v-if="isLoading" :title="loadingText"></Loading>
        <span class="countDown">{{time}}s</span>
        <div class="order_left_div">
            <p class="order_infor">缴费信息</p>
            <p class="order_dept">视频名称：{{videoDetail.title}}</p>
            <p class="order_title">视频时长：{{videoDetail.time}}</p>
            <p class="order_price">缴费金额：<span style="font-size: 28px">￥</span><span>{{videoDetail.price}}</span></p>
        </div>
        <div class="order_div">
            <p class="order_infor">{{payName}}</p>
            <img :src="qr" alt="">
        </div>
        <Dialog v-if="isShow" :isShow="canvasOn" title="恭喜您，支付成功" text="请收好交易纸质小票，即将为您播放视频..." buttonconfig="['我知道了']"  @callError="handleCallError"></Dialog>
        <DialogError v-if="errorIsShow" :title="errorTitle" :text="errorReason"  @hideModal="closeModal"></DialogError>
        <specialPrintPage v-if="printShow" :orderNum="orderNum" :printType="'printPlay'" @printOver="handleOver"></specialPrintPage>
    </div>
</template>
<script>
    import { getVideoOrder, getVideoPayType } from '../api/sciedu'
    import { getPayType, getWerXinQr, getAlipayQr } from '../api/common'
    import specialPrintPage from '../print/specialPrintPage.vue'
    export default {
        name: "sciedu-pay",
        data() {
            return {
                isLoading: true,
                loadingText: '正在加载数据...',
                videoDetail: {
                    title: undefined,
                    time: undefined,
                    price: undefined
                },
                payType: undefined,
                qr: undefined,
                payName: undefined,
                isShow: false,
                errorIsShow: false,
                canvasOn: true,
                time: 180,
                currentTime: 60,
                countDown: true,
                errorTitle: undefined,
                errorReason: undefined,
                orderNum: undefined,
                pushUrl: undefined, //推送地址
                playUrl: undefined, //播放地址
                printShow: false
            }
        },
        components: {
            specialPrintPage
        },
        created: async function () {
            this.isLoading = true
            let payType = await getPayType({business: 99})
            this.payName = payType.data[0].name
            let videoDetail = await getVideoOrder({
                videoId: this.$route.params.videoId,
                payType: payType.data[0].id
            })
            this.videoDetail = videoDetail.data
            this.orderNum = videoDetail.data.orderNum
            if(payType.data[0].pay_type == '1'){
                let werXinQr = await getWerXinQr({
                    orderNum: this.orderNum
                })
                this.qr = werXinQr.data.qr
            }else if(payType.data[0].pay_type == '2'){
                let werXinQr = await getAlipayQr({
                    orderNum: this.orderNum
                })
                this.qr = werXinQr.data.qr
            }
            var timeInter = setInterval(()=>{
                if(this.time>0){
                    this.time -= 1
                }
                else {
                    clearInterval(timeInter)
                }
            },1000)
            this.isLoading = false
            //查询是否支付
            // this.orderNum = '2018051798G894080'
            let payTypeInter = setInterval(async ()=>{
                //获取支付状态
                let videoType = await getVideoPayType({orderNum: this.orderNum})
                this.payType = videoType.data.payStatus
                if(this.time == 0 && this.payType != '1' && this.payType != '-2'){
                    clearInterval(payTypeInter)
                    this.$router.push({
                        name: 'regional_home'
                    })
                }
                if(this.payType == '-2'){
                    clearInterval(timeInter)
                    this.errorTitle = '超时未支付'
                    this.errorReason = '支付未完成'
                    this.errorIsShow = true
                    clearInterval(payTypeInter)
                }else if(this.payType == '1' && videoType.data.status == 1){
                    clearInterval(timeInter)
                    clearInterval(payTypeInter)
                    //处理支付成功之后的接口，播放和打印小票
                    console.log('打印小票')
                    this.printShow = true
                }
            },1000)
        },
        methods: {
            handleCallError: function (obj) {

            },
            closeModal: function () {
                this.errorIsShow = false
                history.back(-1);
            },
            callDoctor: function () {

            },
            handleOver: function () {
                this.printShow = false
                this.isShow = true
                this.$router.push(
                    {
                        name:'sciedu-play',
                        params: {
                            videoId: this.$route.params.videoId,
                            orderNum: this.orderNum
                        }
                    }
                )
            }
        },
        destroyed: function () {

        }
    }
</script>

<style lang="less" scoped>
    .doctor_order{
        padding: 76px 0 0 97px;
        box-sizing: border-box;
        div.order_left_div{
            width: 432px;
            height: 445px;
            border-right: 1px dashed #4395d3;
            display: inline-block;
            vertical-align: top;
            p{
                font-size: 28px;
                line-height: 28px;
                color: #fff;
                &.order_infor{
                    font-size: 32px;
                    line-height: 32px;
                    margin-bottom: 100px;
                }
                &.order_dept{
                    margin-bottom: 50px;
                }
                &.order_title{
                    margin-bottom: 54px;
                }
                &.order_price{
                    span{
                        font-size: 40px;
                        color: #fccb02;
                    }
                }
            }
        }

        div.order_div{
            display: inline-block;
            padding-left: 170px;
            border: none;
            vertical-align: top;
            p.order_infor{
                color: #fff;
                font-size: 32px;
                line-height: 32px;
                margin: 0 17px 83px;
                text-align: center;
            }
            img{
                width: 316px;
                height: 316px;
            }
        }
        span.countDown{
            position: absolute;
            right: 78px;
            top: 188px;
            font-size: 35px;
            color: #ff4039;
        }
    }
</style>