<template>
    <div class="pageination" v-if="allPage > 1">
        <div class="page_btn" @click="pagePre">上一页</div>
        <span class="page_current">{{cur}}  ·  {{all}}</span>
        <div class="page_btn" @click="pageNext">下一页</div>
    </div>
</template>
<script>
    export default {
        data() {
            return {
                cur: undefined
            }
        },
        props: {
            currentPage: {
                type: String,
                default: '1'
            },
            allPage: {
                type: String,
                default: '1'
            }
        },
        computed: {
            all: function(){
                return this.allPage
            }
        },
        methods: {
            changePage: function () {
                this.$emit('changePage', this.cur);
            },
            pagePre: function () {
                this.cur = this.cur >1 ? Number(this.cur) - 1 : 1
                this.changePage()
            },
            pageNext: function () {
                this.cur = this.cur < this.all ? Number(this.cur) + 1 : Number(this.cur)
                this.changePage()
            }
        },
        created: function () {
            this.cur = this.currentPage
        }
    }
</script>
<style scoped>
    .pageination{
        font-size: 20px;
        text-align: center;
    }
    .page_btn{
        display: inline-block;
        width: 126px;
        height: 56px;
        line-height: 56px;
        color: #555;
        font-size: 24px;
        border-radius: 56px;
        background: white;
        text-align: center;
    }
    .page_current{
        line-height: 56px;
        font-size: 24px;
        color: white;
        margin: 0 27px;
    }
</style>