<template :ready>
    <div v-cloak>
        <div class="content">
            <div class="title">
                <p>签到结果</p>
            </div>
            
            <div class="result">
                <div class="modal">
                    <div class="success">
                        <img src="../../../static/img/paysuccess-info-icon.png"/>
                        <p class="msg">签到成功</p>
                    </div>
                    <div class="time" @click="goback"><strong>返回</strong> {{time}}S</div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
    let timeInterval;
    export default {
        data() {
            return {
                result: undefined,
                time: 8
            };
        },
        created: function () {
            this.result = true;
            this.timing();
        },
        methods: {
            // 倒计时
            timing:function(){
                let _this = this;

                timeInterval = setInterval(function(){
                    _this.time--;
                    if (_this.time<=0){
                        clearInterval(timeInterval);
                        _this.goback();
                        return;
                    }
                }, 1000)

            },
            goback: function(){
                this.$router.push({
                    name: 'home'
                })
            }
        },
        destroyed: function () {
            clearInterval(timeInterval);
        }
    };
</script>
<style scoped>
    .result{position: fixed; width:100%; height: 100%; z-index: 10; top:0; left: 0; background-color: rgba(0,0,0,.6); display: flex; align-items: center; justify-content: center;}
    .result .modal{background-color: #fff; width:700px; height: 460px; border-radius: 6px; margin-top: 150px; text-align: center; position: relative;}
    .result .modal img{margin:0 auto;}
    .result .modal .msg{font-size: 50px; margin-top: 30px; font-weight: bold;}
    .result .modal .success{margin-top: 110px}
    .result .modal .success .msg{color: #009843}
    .result .modal .time{position: absolute; right:10px; top:8px; height: 50px; line-height: 50px; width:100px; font-size: 16px; color: #f30}
    .result .modal .time strong{font-size: 20px; font-weight: normal;}
</style>