<template>
    <div id="scieduplay">
        <Loading v-if="isLoading" :title="loadingText"></Loading>
        <my-video :sources="video.sources" :options="video.options" :name="video.name" @backPrev="backPrev"></my-video>
    </div>
</template>
<script>
    import myVideo from './myVideo.vue'
    import { getVideoUrl } from '../api/sciedu'
    export default {
        name: "sciedu-play",
        data() {
            return {
                isLoading: false,
                loadingText: '正在加载数据...',
                video: {
                    sources: undefined,
                    options: undefined,
                    name: undefined
                }
            }
        },
        computed: {
        },
        components: {
            myVideo
        },
        created: async function () {
            this.isLoading = false
            let videoDetail = await getVideoUrl(this.$route.params)
            this.video = {
                sources: [{
                    src: videoDetail.data.url,
                    type: 'video/mp4'
                }],
                options: {
                    autoplay: false,
                    poster: videoDetail.data.logo
                },
                name: videoDetail.data.title
            }
        },
        methods: {
            backPrev() {
                history.back(-1)
            }
        },
        destroyed: function () {

        }
    }
</script>
<style lang="less" scoped>
    #scieduplay{
        position: fixed;
        left: 0;
        top: 0;
        width: 100vw;
        height: 100vh;
        background: black;
        #videoPlay{
            position: relative;
            top: 0;
            right: 0;
            bottom: 0;
            left: 0;
            margin: auto;
            width: 100%;
            height: 100%;
            max-width: 100%;
            max-height: 100%;
            object-fit: cover;
        }
        .videoPlayControl{
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            right: 0;
            margin: auto;
            background: url("http://auto-1253714281.cosgz.myqcloud.com/source/114deff32f93960e72eef45d6ba96bfb.png") center/contain no-repeat;
        }
    }
</style>
