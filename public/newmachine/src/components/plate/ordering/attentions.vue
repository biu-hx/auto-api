<template :ready>
    <div v-cloak>
        <div class="content">
            <div class="title">
                <p>住院订餐注意事项</p>
            </div>
            
            <div class="attentions">
                <div class="needs">
                    <dl class="sendAddr">
                       <dt>送餐地址：</dt>
                       <dd>通过<span><就诊卡或登记号></span>获取,请您带住院患者就诊卡号确认您已知道患者的登记号再开始订餐,登记号在患者手环和入院证上。</dd> 
                    </dl>
                    <div class="arrow">
                        
                    </div>
                    <dl class="orderingTips">
                       <dt>请仔细阅读订餐注意事项</dt>
                       <dd>
                       1、本机器仅限住院患者及其家属点餐；</br>
                       2、点餐支持微信支付；</br>
                       3、若您需要一次性餐盒，请订餐时选择并支付费用；</br>
                       4、需要治疗餐的患者请先去挂营养科的号，医生为您开具；</br>
                       5、预订后，食堂工作人员免费为您配送。</dd> 
                    </dl>

                </div>
                <div class="desc">
                    <h2>订餐和送餐时间说明</h2>
                    <dl>
                       <dt>早餐</dt>
                       <dd>
                       预订时间：前一天10:00 - 前一天15:30</br>
                       预计当日送达时间：07:15 - 07:45
                       </dd>
                       <dt>午餐</dt>
                       <dd>
                       预订时间：前一天17:00 - 当天10:00</br>
                       预计当日送达时间：11:00 - 11:30
                       </dd> 
                       <dt>晚餐</dt>
                       <dd>
                       预订时间：前一天20:00 - 当天15:30</br>
                       预计当日送达时间：17:00 - 17:20
                       </dd>
                       <dt>晚餐加汤</dt>
                       <dd>
                       预订时间：当天13:30 - 当天18:00</br>
                       预计当日送达时间：19:00 - 20:00
                       </dd>   
                    </dl>
                </div>
            </div>
            
            <div style="margin-top:20px; text-align:center">
                <button type="button" class="button_default" @click="nextPage">我知道了</button>
            </div>

        </div>
    </div>
</template>
<script>
    export default {
        data() {
            return {
            };
        },
        created: function () {

        },
        methods: {
            nextPage: function(){
                this.$router.push({
                    name: 'ordering-choose'
                })
            }
        },
        destroyed: function () {
        }
    };
</script>
<style lang="less" src='../../static/less/ordering.less' scoped></style>