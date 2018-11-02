<template>
    <div class="registerWrap" v-cloak>
        <Loading v-if="isLoading" :title="loadingText"></Loading>
        <Dialog v-if="infoBomb" :text="infoNotice" :buttonconfig="['知道了']" @hideBox="hideBox"></Dialog>
        <div class="orderAndPaymode">
            <dl class="orderInfo">
                <dt>缴费信息</dt>
                <template v-if="userInfo">
                <dd><span>姓名：</span><strong>{{userInfo.cardName}}</strong></dd>
                <dd><span>就诊卡号：</span><strong>{{userInfo.cardId}}</strong></dd>
                </template>
                <template v-if="orderInfo">
                <dd><span>医院：</span><strong>成都市第一人民医院</strong></dd>
                <dd><span>科室：</span><strong>{{orderInfo.title}}</strong></dd>
                <dd><span>医生：</span><strong>{{orderInfo.doctorName}}</strong></dd>
                <dd><span>就诊时间：</span><strong>{{orderInfo.date}}  {{orderInfo.period|returnPeriodName}}</strong></dd>
                <dd><span>缴费金额：</span><strong>¥ <em>{{orderInfo.fee}}</em></strong></dd>
                </template>
            </dl>
            <dl class="payMode">
                <dt>选择支付方式</dt>
                <dd v-for="(item,index) in paytypeArray" @click="selectThisPayMod(item.pay_type, item.id)" >
                    <div class="payModeItem" :class="{'action': index==0}">
                        <img :src="item.icon" class="icon">
                        <div class="item-box">
                            <p class="item-title">{{item.name}}</p>
                            <p class="item-info">{{paywayConfig[item.pay_type]}}</p>
                        </div>
                    </div>
                </dd>
                <dd v-if="paytypeArray && paytypeArray.length == 0" style="text-align:center; padding-top:50px;color:rgba(255,255,255,.6); font-size:18px; margin:0">~暂未开通支付~</dd>
            </dl>
        </div>
    </div>
</template>
<script>
    import { getPayType } from '../../components_regional/api/common';
    import enumerate from '../../js/enumerate';
    export default {
        data() {
            return {
                isLoading: false,
                loadingText: '正在加载数据...',
                infoBomb: false,
                infoNotice: '',

                userInfo: undefined,
                orderInfo: undefined, 
                paytypeArray: undefined,
                
                // 支付方式静态配置
                paywayConfig:{
                    '1':"使用手机微信APP扫码支付",
                    '2':"使用支付宝APP扫码支付",
                    '3':"使用银行卡余额进行支付"
                }
            }
        },
        created: function () { },
        activated:function(){
            this.userInfo = JSON.parse(localStorage.getItem('userInfo'));
            this.orderInfo = JSON.parse(localStorage.getItem('currentOrderInfo'));

            // 获取支付方式
            this.getPayWayData();
        },
        filters: {
            returnPeriodName: function (value) {  
                return enumerate.period[value];
            }  
        },
        computed: {},
        methods: {
            // 获取支付方式
            getPayWayData: function(){
                // this.isLoading = true;
                getPayType({
                    business: this.orderInfo.serviceType,      // 业务类型
                    hospitalId: this.orderInfo.hospitalId      // 医院
                }).then(res => {
                    if(res.code == 10000){
                        this.paytypeArray = res.data;

                        if(this.paytypeArray.length > 1){
                            // 请选择支付方式语音
                            this.player.src = this.audioSrc[23];
                            if(this.$agent()=="Android"){
                                this.player.play();
                            }
                        }else if(this.paytypeArray.length == 1){
                            let mod = this.paytypeArray[0].pay_type;
                            let id = this.paytypeArray[0].id;
                            this.$router.replace({
                                name: 'pay-muster',
                                params: {
                                    payMode: mod,
                                    payType: id
                                }
                            })
                        }else{

                        }
                    }else {
                        this.dealError("获取支付方式失败");
                    }
                    // this.isLoading = false
                })
            },

            // 选择当前支付方式
            selectThisPayMod: function(mod, id){

                if (!this.isPc()) {

                    // 获取打印机状态
                    this.printDeviceStatus = this.$machineApi.getMachine_printStatus();

                    if (this.printDeviceStatus == '0') {
                        // 正常
                        this.gotoPay(mod, id);

                    } else if (this.printDeviceStatus == '-1' || this.printDeviceStatus == '-2') {
                        // 缺纸 或 异常
                        this.showDailogModel(this.printDeviceStatus);
                    }
                    
                }else{
                    // 测试逻辑
                    this.gotoPay(mod, id);
                }
            },

            // 去支付
            gotoPay: function(mod, id){

                /** 
                 * 支付方式枚举值:
                 * 1: 微信扫码支付
                 * 2: 支付宝扫码支付
                 * 3: 银联支付
                 */
                this.$router.push({
                    name: 'pay-scan',
                    params: {
                        payMode: mod,
                        payType: id
                    }
                })
            },

            // 提示打印机异常
            showDailogModel: function(status){
                let servicePhone = this.hospitalInfo.servicePhone ? this.hospitalInfo.servicePhone : '--';
                let msgText = (status=='-1') ? "本机纸张已用完啦" : "本机打印机设备异常";

                this.infoBomb = true;
                this.infoNotice  = '<p style="text-align:left; font-size:46px;">'+msgText+'，暂停交易，请前往其它自助机上缴费！<p>'
                                 + '<p style="text-align:left; font-size:32px;">或等换纸后在本自助机上补打交易小票<br/>现场客服电话：'+servicePhone+'<p>';
            },
            
        },
        destroyed: function () {}
    }
</script>

<style lang="less" src='../static/less/payment.less' scoped></style>