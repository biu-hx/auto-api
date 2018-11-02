<template>
    <div class="video_detail">
        <Loading v-if="isLoading" :title="loadingText"></Loading>
        <div class="video_detail_scroll">
            <div class="video_infor">
                <div class="video_img">
                    <img :src="videoDetail.logo" alt="">
                </div>
                <div class="video_text">
                    <div class="video_title">{{videoDetail.title}}</div>
                    <div class="video_time">视频时长：{{videoDetail.time}}</div>
                    <div class="video_price">视频资费：<span style="font-size: 28px">￥</span><span>{{videoDetail.price}}</span></div>
                    <p v-if="videoDetail.price && videoDetail.price > 0 && videoDetail.status == 0">(购买后订单<span>{{videoDetail.YouXiaoShiChang}}小时</span>内有效)</p>
                    <p v-if="videoDetail.status == 1">(您已购买，剩余订单有效时长<span>{{videoDetail.YouXiaoShiChang}}小时</span>)</p>
                </div>
            </div>
            <p v-html="'介绍：'+videoDetail.introduce"></p>
        </div>
        <div v-if="videoDetail.price && videoDetail.price > 0 && videoDetail.status != 1" class="callBtn" @click="handlePay">立即支付</div>
        <div v-else class="callBtn" @click="handlePlay">播放</div>
    </div>
</template>
<script>
    import { getVideoDetail } from '../api/sciedu'
    export default {
        name: "sciedu-detail",
        data() {
            return {
                isLoading: true,
                loadingText: '正在加载数据...',
                videoDetail: {
                },
                isShow: false
            }
        },
        created: async function () {
            let videoDetail = await getVideoDetail(this.$route.params)
            this.videoDetail = videoDetail.data
            this.isLoading = false
        },
        methods: {
            handlePay: function () {
                this.$router.push(
                    {
                        name:'sciedu-pay',
                        params: this.$route.params
                    }
                )
            },
            handlePlay: function () {
                this.$router.push(
                    {
                        name:'sciedu-play',
                        params: {
                          videoId: this.$route.params.videoId,
                          orderNum: this.videoDetail.orderNumber
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
    .video_detail{
        padding: 25px 0 0 0;
        box-sizing: border-box;
        div.video_detail_scroll{
            padding: 0 34px;
            height: 458px;
            overflow: auto;
        }
        div.video_infor{
            width: 100%;
            margin-bottom: 40px;
            .video_img{
                display: inline-block;
                width: 388px;
                height: 264px;
                border: 1px solid #ddd;
                background: white;
                text-align: center;
                img{
                    display: inline-block;
                    position: relative;
                    top: 50%;
                    transform: translateY(-50%);
                    max-width: 380px;
                    max-height: 256px;
                    border-radius: 50%;
                }
                margin-right: 60px;
            }
            div.video_text{
                max-width: 600px;
                color: #fff;
                display: inline-block;
                vertical-align: top;
                div{
                    color: #fff;
                    line-height: 1;
                    &.video_title{
                        margin-top: 22px;
                        font-size: 44px;
                    }
                    &.video_time{
                        margin-top: 48px;
                        font-size: 37px;
                        line-height: 37px;
                    }
                    &.video_price{
                        margin-top: 34px;
                        font-size: 37px;
                        line-height: 37px;
                        span{
                            font-size: 36px;
                            color: #fccb02;
                        }
                    }
                }
                p{
                    margin-top: 17px;
                    font-size: 28px;
                    line-height: 28px;
                    span{
                        font-size: 26px;
                        color: #fccb02;
                    }
                }
            }
        }
        p{
            font-size: 30px;
            line-height: 42px;
            color: #fff;
            margin-bottom: 10px;
        }
        div.callBtn{
            width: 168px;
            height: 56px;
            line-height: 56px;
            font-size: 24px;
            color: #333;
            background: white;
            border-radius: 56px;
            text-align: center;
            margin: 33px auto;
        }
    }
</style>