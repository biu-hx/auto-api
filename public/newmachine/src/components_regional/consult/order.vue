<template>
    <div class="doctor_order">
        <Loading v-if="isLoading" :title="loadingText"></Loading>
        <span class="countDown">{{time}}s</span>
        <div class="order_left_div">
            <p class="order_infor">缴费信息</p>
            <p class="order_dept">问诊科室：{{orderDetail.deptName}}</p>
            <p class="order_dept">问诊医生：{{orderDetail.doctorName}}</p>
            <p class="order_title">医生职称：{{orderDetail.title}}</p>
            <p class="order_price">缴费金额：<span style="font-size: 28px">￥</span><span>{{orderDetail.price}}</span></p>
        </div>
        <div class="order_div">
            <p class="order_infor">{{payName}}</p>
            <img :src="qr" alt="">
        </div>
        <Dialog v-if="isShow" :time="currentTime" :countDown="countDown" :isShow="canvasOn" title="恭喜您，支付成功" text="请收好交易纸质小票，正在为您呼叫医生..." buttonconfig="['我知道了']" @hideBox="hideBox" ></Dialog>
        <DialogError v-if="errorIsShow" :title="errorTitle" :text="errorReason"  @hideModal="closeModal"></DialogError>
        <specialPrintPage v-if="printShow" :orderNum="orderNum" :printType="'printVideo'" @printOver="handleOver"></specialPrintPage>
    </div>
</template>
<script>
    import { getOrderDetail, getConsultantType, connectConsultant, isConsultantAnswer, isTest } from '../api/consultant'
    import { getPayType, getWerXinQr, getAlipayQr } from '../api/common'
    import specialPrintPage from '../print/specialPrintPage.vue'
    export default {
        name: "consult-order",
        data() {
            return {
                isLoading: true,
                loadingText: '正在加载数据...',
                orderDetail: {
                    deptName: undefined,
                    doctorName: undefined,
                    title: undefined,
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
            let payType = await getPayType({business: 98})
            this.payName = payType.data[0].name
            let OrderDetail = await getOrderDetail({
                doctorId: this.$route.params.doctorId,
                payType: payType.data[0].id
            })
            this.orderDetail = OrderDetail.data
            this.orderNum = OrderDetail.data.orderNum
            if(payType.data[0].pay_type == '1'){
                let werXinQr = await getWerXinQr({
                    orderNum: OrderDetail.data.orderNum
                })
                this.qr = werXinQr.data.qr
            }else if(payType.data[0].pay_type == '2'){
                let werXinQr = await getAlipayQr({
                    orderNum: OrderDetail.data.orderNum
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
                let consultantType = await getConsultantType({orderNum: this.orderNum})
                this.payType = consultantType.data.payStatus
                // this.payType = '1'
                if(this.time == 0 && this.payType != '1' && this.payType != '-2'){
                    clearInterval(payTypeInter)
                    clearInterval(timeInter)
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
                }else if(this.payType == '1'){
                    clearInterval(timeInter)
                    clearInterval(payTypeInter)
                    //处理小票和接听
                    console.log('打印小票')
                    this.printShow = true
                }
            },1000)
        },
        methods: {
            handleCallError: function (obj,title) {
                this.isShow = false
                this.errorTitle = title ? title : '呼叫失败'
                this.errorReason = obj? obj : '医生正在服务其他患者，请您耐心等待医生在线时呼叫!您支付的款项会在7个工作日内退回您的支付账户!'
                this.errorIsShow = true
            },
            closeModal: function () {
                this.errorIsShow = false
                history.back(-1);
            },
            hideBox: function () {
                this.isShow = false
                this.$router.push({
                    name: 'regional_home'
                })
            },
            callDoctor: async function () {
                //发起视频咨询
                let timeDown = setInterval(async ()=>{
                    if(this.currentTime >0){
                        this.currentTime -= 1
                    }else {
                        this.handleCallError('医生繁忙，无法接听您的问诊，您支付的款项会在7个工作日内退回您的支付账户!','呼叫失败')
                        clearInterval(timeDown)
                    }},1000)
                let connectconsultant = await connectConsultant({orderNum: this.orderNum})
              console.log('连接，获取推流')
              console.log('connectconsultant')
              console.log(connectconsultant)
                if(connectconsultant.code == 10000){
                    //开始呼叫
                    this.pushUrl = connectconsultant.data.pushUrl
                    this.playUrl = connectconsultant.data.playUrl
                    //医生是否接听
                    let isAnswer = setInterval(async ()=>{
                        let isconsultantAnswer = await isConsultantAnswer({
                            callId: connectconsultant.data.callId
                        })
                      console.log('是否接听')
                      console.log('isconsultantAnswer')
                      console.log(isconsultantAnswer)
                        if(isconsultantAnswer.code == 10000){
                            // 处理接听
                            clearInterval(isAnswer)
                            clearInterval(timeDown)
                          var sUserAgent = navigator.userAgent;
                          var bIsAndroid = sUserAgent.toLowerCase().match(/android/i) == "android";
                          this.isShow = false
                          this.$nextTick(()=> {
                              if(bIsAndroid){
                                  var msg = this.dsBridge.call('startVideo', {
                                      msg: {
                                          "pushUrl":connectconsultant.data.pushUrl,
                                          "playUrl":connectconsultant.data.playUrl,
                                          "callId": connectconsultant.data.callId,
                                          "orderNum": this.orderNum
                                      }
                                  });
                              }else {
                                  window.external.CallMe(JSON.stringify({
                                      "pushUrl":connectconsultant.data.pushUrl,
                                      "playUrl":connectconsultant.data.playUrl,
                                      "callId": connectconsultant.data.callId,
                                      "orderNum": this.orderNum
                                  }))
                              }
                              this.$router.push({
                                  name: 'regional_home'
                              })
                          })
                        }
                        else {
                            console.log('尚未接听')
                        }
                    },1000)
                }else if(connectconsultant.code == 91025){
                    this.handleCallError('医生繁忙，无法接听您的问诊，您支付的款项会在7个工作日内退回您的支付账户!','呼叫失败')
                    // this.handleCallError(connectconsultant.msg + '!您支付的款项会在7个工作日内退回您的支付账户!')
                }
            },
            handleOver: function () {
              this.printShow = false
              this.isShow = true
              this.callDoctor()
            }
        },
        destroyed: function () {
            clearInterval(timeInter);
            clearInterval(payTypeInter)
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