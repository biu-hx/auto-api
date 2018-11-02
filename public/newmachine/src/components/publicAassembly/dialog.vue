<template>
    <div class="am-frame">
        <div class="am-frame-box">
            <div class="frame-detail-box">
                <div class="frame-detail-content">
                    <p class="am-frame-box-title" v-html="title"></p>
                    <p class="am-frame-box-info" v-html="text"></p>
                </div>
            </div>
            <canvas id="canvas" width="600" height="69" style="overflow:hidden"></canvas>
            <div class="frame-detail-operate">
                <template v-if="buttonconfig.length == 2">
                    <p class="repay-btn sp" @click="hideBox(0)">{{buttonconfig[0]}}</p>
                    <p class="repay-btn" @click="hideBox(1)">{{buttonconfig[1]}}</p>
                </template>
                <template v-if="buttonconfig.length == 1">
                    <p class="repay-btn" @click="hideBox(1)">{{buttonconfig[0]}}</p>
                </template>
                <slot></slot>
            </div>
            <span v-if="countDown" class="time">{{time}}s</span>
        </div>
    </div>
</template>
<script>
    export default {
        data() {
            return {
            }
        },
        props: {
            title: {
                type: String,
                default: '提示'
            },
            time: {
                default: '60'
            },
            countDown: {
                default: false
            },
            text: {
                type: String,
                default: '温馨提示'
            },
            isShow: {
                default: false
            },
            buttonconfig:{
                default: ['取消','确定']
            }
        },
        mounted() {
            if(this.isShow){
                this.drawProgress();
            }
        },
        methods: {
            hideBox: function (type) {
                this.$emit('hideBox', type);
            },
            drawProgress: function() {
                let cav = document.getElementById('canvas');
                let ctx = cav.getContext('2d');
                let cw = cav.width;
                let ch = cav.height;
                let x = 0;
                function draw() {
                    //绘制背景矩形
                    ctx.lineJoin = "round";
                    ctx.lineWidth = 18;
                    ctx.fillStyle = '#009df7';
                    ctx.fillRect(0, 0, cw, ch);
                    //绘制进度
                    ctx.lineWidth = 2;
                    ctx.strokeStyle = '#00d258';
                    ctx.moveTo(x, 0);
                    ctx.lineTo(x, ch);
                }
                this.drawTimer = setInterval(() => {

                    if (x <= cw) {
                        draw();
                        ctx.stroke();
                    } else {
                        ctx.clearRect(0, 0, cw, ch);
                        ctx.beginPath();
                        x=0;
                        draw();
                        ctx.stroke();
                    }
                    x += ctx.lineWidth;
                },30)
            }
        },
        destroyed: function () {
            clearInterval(this.drawTimer)
        }
    }
</script>
<style scoped>
    .am-frame {
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 2, 0.6);
        position: fixed;
        top: 0;
        left: 0;
        overflow: hidden;
        z-index: 9999;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .am-frame-box {
        width: 985px;
        padding:70px 0;
        border-radius: 15px;
        background: #fff;
        position: relative;
        overflow: hidden;
        text-align: center;
    }


    .frame-detail-box {
        width: 100%;
        height: auto;
        display: flex;
        align-items: center;
        justify-content: flex-start;
    }

    .friend-icon {
        width: 232px;
        height: 100%;
        margin-left: 105px;
    }

    .frame-detail-content {
        width:100%;
        height: 100%;
        padding:0 70px;
        box-sizing: border-box;
        overflow: hidden;
        text-align: center;
    }

    .am-frame-box-title {
        width: 100%;
        font-size: 50px;
        color: #ff4039;
        font-weight: 600;
    }

    .am-frame-box-info {
        width: 100%;
        margin-top: 40px;
        font-size: 36px;
        min-height: 140px;
    }

    .frame-detail-operate {
        width: 100%;
        margin-top: 75px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .repay-btn {
        width: 180px;
        height: 60px;
        border-radius: 8px;
        background: #018ede;
        font-size: 26px;
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
    }
    .sp {
        background: #787f82;
    }
    .repay-btn{
        margin: 0 50px;
    }
    #canvas{
        width: 600px;
        height: 69px;
        border-radius: 69px;
        margin: auto;
    }
    .time{
        display: inline-block;
        position: absolute;
        right: 0;
        top: 0;
        padding: 29px;
        font-size: 35px;
        color: #ff4039;
    }
</style>