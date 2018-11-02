<template :ready>
    <div v-cloak>
        <div class="content">
            <div class="title">
                <p>查询订单-无结果</p>
                <div class="count-time">{{noRecordTime}}</div>
            </div>
            <div class="demonstration-box">
                <p class="no-record-icon">
                    <img src="../../../static/img/no-data.png" alt="" class="no-data-img">
                </p>
                <p class="no-record-text">您暂无交易记录！</p>
                <div class="no-record-btn">
                    <div class="research-btn" @click="reSearch">重新查询</div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
    export default {
        data() {
            return {
                noRecordTime: 30
            };
        },
        created: function () {
            this.timer = setInterval(() => {
                //无数据页面30秒倒计时
                if (this.noRecordTime > 0) {
                    this.noRecordTime--;
                } else {
                    this.$router.push({
                        name: "home"
                    });
                }
            }, 1000);
        },
        methods: {},
        destroyed() {
            clearInterval(this.timer);
        }
    };
</script>
<style scoped>
    .demonstration-box {
        width: 1125px;
        height: 600px;
        border-radius: 10px;
        border: 1px solid #d2d5d5;
        margin-left: 35px;
        overflow: hidden;
    }

    .no-record-icon {
        width: 100%;
        text-align: center;
        margin-top: 70px;
        overflow: hidden;
    }

    .no-record-text {
        width: 100%;
        text-align: center;
        font-size: 34px;
        margin-top: 60px;
        color: #666;
    }

    .no-record-btn {
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-top: 85px;
    }

    .research-btn {
        width: 165px;
        height: 52px;
        background: #018ede;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
        border-radius: 10px;
        color: #fff;
        cursor: pointer;
    }

    .no-data-img {
        width: 328px;
        height: 215px;
    }
</style>