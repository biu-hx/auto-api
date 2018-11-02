<template>
    <div class="am-frame">
        <div class="am-frame-box">
            <span class="warning"></span>
            <div class="warning_title" v-html="title"></div>
            <div class="warning_text" v-html="text"></div>
            <span class="closeModal" @click="closeModal"></span>
        </div>
    </div>
</template>
<script>
    export default {
        data() {
            return {
                time: 60
            }
        },
        props: {
            title: {
                type: String,
                default: '提示'
            },
            text: {
                type: String,
                default: '温馨提示'
            }
        },
        methods: {
            closeModal: function () {
                this.$emit('hideModal');
            }
        }
    }
</script>
<style lang="less" scoped>
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
        width: 870px;
        min-height: 530px;
        border-radius: 10px;
        text-align: center;
        background: #fff;
        position: relative;
        overflow: hidden;
        .warning{
            display: inline-block;
            margin-top: 80px;
            margin-bottom: 68px;
            width: 124px;
            height: 124px;
            border-radius: 50%;
            background: url("../static/images/pay_error.png") center/cover;
        }
        .warning_title{
            font-size: 58px;
            line-height: 58px;
            color: #f50103;
            text-align: center;
            margin-bottom: 42px;
        }
        .warning_text{
            font-size: 40px;
            max-width: 670px;
            margin: auto;
            text-align: center;
            margin-bottom: 50px;
        }
        .closeModal{
            display: inline-block;
            position: absolute;
            right: 27px;
            top: 24px;
            width: 40px;
            height: 40px;
            background: url("../static/images/pay_close.png") center/cover;
        }
    }
</style>