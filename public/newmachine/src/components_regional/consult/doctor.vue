<template>
    <div class="choose_doctor">
        <Loading v-if="isLoading" :title="loadingText"></Loading>
        <ul>
            <li v-for="item in currentDoctorData" @click="goDoctorDetail(item.doctorId)">
                <div class="doctor_infor">
                    <img :src="item.picture" alt="">
                    <span>
                        <p>{{item.doctorName}}</p>
                        <p>{{item.title}}</p>
                    </span>
                    <span :class="online[item.online][1]">
                        {{online[item.online][0]}}
                    </span>
                </div>
                <div class="doctor_desc">
                    {{item.disease}}
                </div>
            </li>
        </ul>
        <pageination v-if="doctorData.length>6" class="pageination_doctor" style="margin-top: 6px;" :currentPage="currentPage" :allPage="allPage" @changePage = "handlePage"></pageination>
    </div>
</template>
<script>
    import { getDoctorData } from '../api/consultant'
    export default {
        name: "consult-hospital",
        data() {
            return {
                isLoading: true,
                loadingText: '正在加载数据...',
                doctorData: [],
                online: [
                    ['离线','leave'],
                    ['在线','free'],
                    ['忙碌','busy']
                ],
                currentPage: '1',
                allPage: undefined
            }
        },
        computed: {
            currentDoctorData:function () {
                return this.doctorData.slice((Number(this.currentPage) - 1)*6,Number(this.currentPage)*6)
            }
        },
        created: async function () {
            let doctorData =  await getDoctorData(this.$route.params)
            this.doctorData = doctorData.data
            this.allPage = Math.ceil(this.doctorData.length/6).toString()
            this.isLoading = false
        },
        methods: {
            handlePage: function (res) {
                this.currentPage = res.toString()
            },
            goDoctorDetail: function (id) {
                this.$router.push(
                    {
                        name:'consult-detail',
                        params: {
                            doctorId: id
                        }
                    }
                )
            }
        },
        beforeRouteLeave(to,from,next){
            if(to.name == "consult-hospital"){  //这里写下你的条件
                this.$destroy();
            }
            next();
        },
        destroyed: function () {

        }
    }
</script>

<style lang="less" src='../static/less/chooseDoctor.less' scoped></style>
