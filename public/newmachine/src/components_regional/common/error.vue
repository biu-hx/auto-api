<template>
    <div class="system-error">
        <dl>
            <dt>
                <img src="../static/images/error-notice.png"/>
                <span>{{msg}}</span>
            </dt>
            <dd>请返回上一步重新操作</dd>
        </dl>
    </div>
</template>
<script>
    export default {
        data() {
            return {
                msg: ''
            }
        },
        computed: {
        },
        methods: {
        },
        activated: function () {
            this.msg = this.$route.params.msg;
        }
    }
</script>
<style scoped>
    .system-error{
       display: flex;
       align-items: center;
       justify-content: center;
       height: 550px
    }
    .system-error dl{display: block; text-align: center; }
    .system-error dt{
        margin: 0;
        font-size: 36px;
        color: #fff;
        padding-bottom: 20px
    }
    .system-error dt img,
    .system-error dt span{display: block; margin:0 auto;}
    .system-error dt img{width:120px;}
    .system-error dd{
        margin: 0;
        font-size: 20px;
        padding: 15px 0;
        color: rgba(255,255,255,.6)
    }
</style>