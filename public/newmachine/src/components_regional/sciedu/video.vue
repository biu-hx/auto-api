<template>
    <div class="choose_doctor">
        <Loading v-if="isLoading" :title="loadingText"></Loading>
        <ul>
            <li v-for="item in currentVideoData" @click="goVideoDetail(item.videoId)">
                <img :src="item.logo" alt="">
                <p>{{item.title}}</p>
                <div :class="(item.price && item.price > 0)? 'busy' : 'free'">
                    <span>{{(item.price && item.price > 0)? '付费' : '免费'}}</span>
                </div>
            </li>
        </ul>
        <pageination v-if="videoData.length>8" class="pageination" :currentPage="currentPage" :allPage="allPage" @changePage = "handlePage"></pageination>
    </div>
</template>
<script>
    import { getVideoList } from '../api/sciedu'
    export default {
        name: "sciedu-video",
        data() {
            return {
                isLoading: true,
                loadingText: '正在加载数据...',
                videoData: [],
                online: [
                    ['在线','free'],
                    ['忙碌','busy']
                ],
                currentPage: '1',
                allPage: undefined
            }
        },
        computed: {
            currentVideoData:function () {
                return this.videoData.slice((Number(this.currentPage) - 1)*8,Number(this.currentPage)*8)
            }
        },
        created: async function () {
            let videoData =  await getVideoList()
            this.videoData = videoData.data
            this.allPage = Math.ceil(this.videoData.length/8).toString()
            this.isLoading = false
        },
        methods: {
            handlePage: function (res) {
                this.currentPage = res.toString()
                console.log(this.currentPage)
                console.log(this.currentVideoData)
            },
            goVideoDetail: function (id) {
                this.$router.push(
                    {
                        name:'sciedu-detail',
                        params: {
                            videoId: id
                        }
                    }
                )
            }
        },
        destroyed: function () {

        }
    }
</script>
<style lang="less" scoped>
    .choose_doctor{
        padding: 42px 0 0 23px;
        height: 100%;
        box-sizing: border-box;
        font-size: 0;
        position: relative;
        ul{
            font-size: 0;
            li{
                display: inline-block;
                width: 260px;
                height: 190px;
                margin: 0 22px 46px 0px;
                border-radius: 2px;
                background: white;
                box-sizing: border-box;
                position: relative;
                img{
                    width: 100%;
                    height: 152px;
                    background: gray;
                }
                p{
                    width: 100%;
                    text-align: center;
                    background: #eaeaea;
                    font-size: 20px;
                    color: #000;
                    line-height: 38px;
                    overflow: hidden;
                    text-overflow:ellipsis;
                    white-space: nowrap;
                }
                div{
                    position: absolute;
                    right: -34px;
                    top: -34px;
                    width: 0;
                    height: 0;
                    border: 34px solid;
                    font-size: 16px;
                    line-height: 44px;
                    color: white;
                    white-space: nowrap;
                    transform: rotate(45deg);
                    text-align: center;
                    span{
                        display: inline-block;
                        transform: translateX(-50%);
                    }
                    &.busy{
                        border-color: transparent transparent #f30000 transparent;
                    }
                    &.free{
                        border-color: transparent transparent #02a915 transparent;
                    }
                }
            }
        }
        .pageination{
            position: absolute;
            left: 50%;
            bottom: 36px;
            transform: translateX(-50%);
        }
    }
</style>
