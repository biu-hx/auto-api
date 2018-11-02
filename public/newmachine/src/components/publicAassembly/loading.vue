<template>
    <div class="loading-box">
        <div class="loading-content">
            <p class="loading-text">{{title}}</p>
            <canvas id="canvas" width="270" height="36" style="border-radius:20px; overflow:hidden"></canvas>
        </div>
    </div>
</template>
<script>
    export default {
        data() {
            return {}
        },
        props: {
            title: {
                type: String,
                default: '数据加载中...'
            }
        },
        created: function() {},
        mounted() {
            this.drawProgress();
        },
        methods: {
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




