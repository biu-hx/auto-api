<template>
    <div class="registerWrap" v-cloak>
        <Loading v-if="isLoading" :title="loadingText"></Loading>
        <div class="createEleCard">
            <ul class="step">
                <li>
                    <div>
                        <i>1</i>
                        <strong>登录微信<br>打开扫一扫</strong>
                    </div>
                    <div>
                        <img src="../static/images/create_step_1.png">
                    </div>
                </li>
                <li>
                    <div>
                        <i>2</i>
                        <strong>扫二维码<br>进行办卡</strong>
                    </div>
                    <div>
                        <img src="../static/images/create_step_2.png">
                    </div>
                </li>
                <li>
                    <div style="display:flex; align-items: center; justify-content: center;">
                        <strong>请扫二维码办卡</strong>
                    </div>
                    <div>
                        <img class="qr_img" src="http://static.motherchildren.com/images/huaxi/weixinQR.png">
                    </div>
                </li>
            </ul>
        </div>
    </div>
</template>
<script>
    export default {
        data() {
            return {
                isLoading: false,
                loadingText: '正在加载数据...',
            }
        },
        created: function () {
            
        },
        computed: {},
        methods: {
            
            
        },
        destroyed: function () {}
    }
</script>

<style lang="less" src='../static/less/payment.less' scoped></style>