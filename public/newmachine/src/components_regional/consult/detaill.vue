<template>
    <div class="doctor_detail">
        <Loading v-if="isLoading" :title="loadingText"></Loading>
        <div class="doctor_detail_scroll">
            <div class="doctor_infor">
                <img :src="doctorDetail.picture" alt="">
                <div class="doctor_text">
                    <div>
                        {{doctorDetail.doctorName}}
                        <span :class="online[doctorDetail.online][1]">{{online[doctorDetail.online][0]}}</span>
                    </div>
                    <p>{{doctorDetail.hospitalName}}<i></i>{{doctorDetail.title}}</p>
                    <p>问诊费：<span style="font-size: 28px">￥</span><span>{{doctorDetail.price}}</span></p>
                </div>
            </div>
            <p>
                医生在线时间：{{doctorDetail.uptime}}
            </p>
            <p>
                擅长：{{doctorDetail.disease}}
            </p>
            <p v-html="'介绍：'+doctorDetail.profile"></p>
        </div>
        <div v-if="doctorDetail.online == 1" class="callBtn" @click="handleCall">立即呼叫</div>
        <div v-else class="callBtn disabled"  @click="handleCallElse">立即呼叫</div>
        <Dialog v-if="isShow" :text="online[doctorDetail.online][2]" :buttonconfig="['我知道了']" @hideBox="closeDialog"></Dialog>
    </div>
</template>
<script>
    import { getDoctorDetail } from '../api/consultant'
    export default {
        name: "consult-detail",
        data() {
            return {
                isLoading: true,
                loadingText: '正在加载数据...',
                online: [
                    ['离线','leave','请在医生在线时，向医生进行视频问诊'],
                    ['在线','free'],
                    ['忙碌','busy','医生正在服务中，请耐心等待']
                ],
                doctorDetail: {
                    doctorId: undefined,
                    picture: undefined,
                    doctorName: undefined,
                    online: 1,
                    title: undefined,
                    hospitalName: undefined,
                    price: undefined,
                    uptime: undefined,
                    disease: undefined,
                    profile: undefined
                },
                isShow: false
            }
        },
        created: async function () {
            let doctorDetail = await getDoctorDetail(this.$route.params)
            this.doctorDetail = doctorDetail.data
            this.isLoading = false
        },
        methods: {
            handleCall: function () {
                this.$router.push(
                    {
                        name:'consult-order',
                        params: {
                            doctorId: this.doctorDetail.doctorId
                        }
                    }
                )
            },
            handleCallElse: function () {
                this.isShow = true
                setTimeout(()=>{
                    this.isShow = false
                },5000)
            },
            closeDialog: function () {
                this.isShow = false
            }
        },
        destroyed: function () {

        }
    }
</script>

<style lang="less" scoped>
    .doctor_detail{
        padding: 32px 0 0 0;
        box-sizing: border-box;
        div.doctor_detail_scroll{
            padding: 0 73px 0 61px ;
            height: 458px;
            overflow: auto;
        }
        div.doctor_infor{
            width: 100%;
            height: 210px;
            margin-bottom: 35px;
            img{
                display: inline-block;
                vertical-align: middle;
                width: 210px;
                height: 210px;
                border-radius: 50%;
                margin: 0 59px 0 11px;
                background: url("../static/images/default_img.png") center/contain;
            }
            div.doctor_text{
                max-width: 700px;
                display: inline-block;
                vertical-align: middle;
                color: #fff;
                div{
                    font-size: 50px;
                    line-height: 1;
                    margin-bottom: 37px;
                    span{
                        display: inline-block;
                        width: 80px;
                        height: 42px;
                        line-height: 42px;
                        border-radius: 5px;
                        margin-left: 23px;
                        font-size: 26px;
                        text-align: center;
                        vertical-align: bottom;
                        &.busy{
                            background: #f30000;
                        }
                        &.free{
                            background: #02a915;
                        }
                        &.leave{
                            background: #a8a8a8;
                        }
                    }
                }
                p{
                    font-size: 28px;
                    line-height: 28px;
                    i{
                        display: inline-block;
                        width: 2px;
                        height: 18px;
                        background: #6b98e0;
                        margin: 0 20px;
                    }
                    &:nth-child(2){
                        margin-bottom: 35px;
                    }
                    span{
                        font-size: 36px;
                        color: #fccb02;
                    }
                }
            }
        }
        p{
            font-size: 28px;
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
            &.disabled{
                background: #b1b1b1;
            }
        }
    }
</style>