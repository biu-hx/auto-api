<template>
    <div class="registerWrap" v-cloak>
        <Loading v-if="isLoading" :title="loadingText"></Loading>
        <div class="doctors_list" v-if="doctorData.length > 0">
            <div class="item" v-for="item in currentDoctorData" @click="handleDoctor(item)">
                <div class="basic">
                    <div class="head"><img :src="item.image"></div>
                    <div class="name">
                        <strong>{{item.doctorName}}</strong>
                        <p>{{item.title}}</p>
                    </div>
                    <em>余{{item.num}}</em>
                </div>
                <div class="info">
                    <p>挂号费：￥{{item.fee}}</p>
                    <p>就诊时间：{{date.date}}  {{item.period|returnPeriodName}}</p>
                </div>
            </div>
            
        </div>
        <div class="pageinationWrap">
            <pageination :currentPage="currentPage" :allPage="allPage" @changePage = "handlePage"></pageination>
        </div>
    </div>
</template>
<script>
    import { getDoctors } from '../api/register';
    import enumerate from '../../js/enumerate';
    export default {
        data() {
            return {
                isLoading: false,
                loadingText: '正在加载数据...',

                doctorData: [],
                currentPage: '1',
                allPage: undefined,

                // 路由参数
                paramsData: undefined,
                date: undefined
            }
        },
        created: function () {
            
        },
        activated:function(){
            this.doctorData = [];
            this.paramsData = this.$route.params;
            this.getDoctorsList();
        },
        filters: {
            returnPeriodName: function (value) {
                return enumerate.period[value];
            }  
        },
        computed: {
            currentDoctorData:function () {
                return this.doctorData.slice((Number(this.currentPage) - 1)*6, Number(this.currentPage)*6)
            }
        },
        methods: {
            getDoctorsList: function () {
                this.isLoading = true;
                this.date = JSON.parse(this.paramsData.date);
                getDoctors({
                    hospitalId: this.paramsData.hospitalId,
                    deptId: this.paramsData.deptId,
                    date: this.date.date,
                    period: this.date.period
                }).then(res => {
                    if(res.code == 10000){
                        this.doctorData = res.data;
                        this.allPage = String(Math.ceil(this.doctorData.length/6))
                    }
                    this.isLoading = false
                })
            },

            handlePage: function (res) {
                this.currentPage = res.toString()
            },

            handleDoctor(item){

                // 存储所选号源信息
                item.date = this.date.date;
                item.deptId = this.paramsData.deptId;
                item.hospitalId = this.paramsData.hospitalId;
                item.serviceType = '2';
                localStorage.setItem('currentOrderInfo', JSON.stringify(item));
                
                this.$router.push(
                    {
                        name: 'choose-card'
                    }
                )
            }
            
        },
        destroyed: function () {
        }
    }
</script>

<style lang="less" src='../static/less/register.less' scoped></style>