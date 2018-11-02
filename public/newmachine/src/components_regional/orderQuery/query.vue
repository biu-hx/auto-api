<template>
    <div class="registerWrap" v-cloak>
        <div class="systemNotice">
        开发中,敬请期待 ......
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

<style lang="less">
    .systemNotice{
        color:rgba(255,255,255,.7);
        font-size:24px;
        text-align:center;
        padding-top:25%
    }
</style>