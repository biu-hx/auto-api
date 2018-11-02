<template>
    <div class="FrameRegional" v-cloak @click="staticTimeReset">
        <router-view></router-view>
    </div>
</template>

<script>
    

    export default {
        data() {
            return {
                clock: "",
                staticTime: 180,    // 设备无任何操作的情况下, 强制回首页的时间间隔设定
                timing: undefined,

            }
        },
        created: function () { 
            if(this.$route.name == 'regional_home') return;
            this.goTiming();
        },
        methods: {

            // 静止(无任何操作)时间重置
            staticTimeReset: function () {
                this.staticTime = 180;
            },

            // 回到首页
            backIndex: function () {
                this.$router.push({
                    name: 'regional_home'
                })
            },

            // 倒计时
            goTiming: function(){
                this.timing = setInterval(() => {
                    if (this.staticTime > 0) {
                        this.staticTime--;
                    } else {
                        this.backIndex();
                    }
                }, 1000);
            }
        },
        destroyed: function () { },
        watch: {
            '$route': function(to, from) {
                
                // 设备3分钟无操作强制回首页
                if(to.name == "regional_home"){
                    clearInterval(this.timing);
                }else{
                    clearInterval(this.timing);
                    this.goTiming();
                }
            }
        }
    }
</script>

<style lang="less">
    .FrameRegional{width:1280px; height: 1024px; position: relative; background: url(./static/images/body_bg.png) no-repeat; background-size: cover; margin:0 auto;}

    .btnGradient{
        box-sizing: border-box;
        display: inline-block;
        padding: 0!important;
        text-align: center;
        font-size: 24px;
        color: #fff;
        background: -webkit-linear-gradient(left,#0093fe,#0352e2);
        background: -o-linear-gradient(left,#0093fe,#0352e2);
        background: -moz-linear-gradient(left,#0093fe,#0352e2);
        background: -ms-linear-gradient(left,#0093fe,#0352e2);
        filter:progid:DXImageTransform.Microsoft.Gradient(gradientType=0,startColorStr=#0093fe,endColorStr=#0352e2);
    }
    .chooseHosital_box{
        width: 100%;
        height: 604px;
        background: rgba(214,242,255,.2);
        box-sizing: border-box;
    }
    .chooseHosital_box.else{
        background: url("static/images/chooseHospital_bg.png");
    }
    .btnCommon(){
        display: inline-block;
        width: 200px;
        height: 66px;
        font-size: 30px;
        line-height: 66px;
        border-radius: 66px;
        color: white;
        box-shadow: 0px 0px 4px 0px #d8dfee,
        0px 0px 4px 0px #d8dfee,
        0px 0px 4px 0px #d8dfee,
        0px 0px 4px 0px #d8dfee;
        margin: 0 70px;
    }
    .btn-grey{
        .btnCommon;
        background: grey;
    }
    .btn-gradient{
        .btnCommon;
        background: -webkit-linear-gradient(left,#0093fe,#0352e2);
        background: -o-linear-gradient(left,#0093fe,#0352e2);
        background: -moz-linear-gradient(left,#0093fe,#0352e2);
        background: -ms-linear-gradient(left,#0093fe,#0352e2);
        filter:progid:DXImageTransform.Microsoft.Gradient(gradientType=0,startColorStr=#0093fe,endColorStr=#0352e2);
    }
    .btn-red{
        .btnCommon;
        background: #f30000;
    }
</style>