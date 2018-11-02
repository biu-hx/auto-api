<template>
    <div class="registerWrap" v-cloak>
        <Loading v-if="isLoading" :title="loadingText"></Loading>
        <div class="hospital_list">
            <div class="hospital_item" v-for="item in currentHosipitalData" @click="handleHospital(item.hospitalId)">
                <div class="hospital_logo">
                    <img :src="item.logo">
                </div>
                <span>{{item.hospitalName}}</span>
            </div>
        </div>
        <div class="pageinationWrap">
            <pageination :currentPage="currentPage" :allPage="allPage" @changePage = "handlePage"></pageination>
        </div>
    </div>
</template>
<script>
    import { getHospitalData } from '../api/register'
    export default {
        data() {
            return {
                isLoading: false,
                loadingText: '正在加载数据...',

                hosipitalData: [],
                currentPage: '1',
                allPage: undefined
            }
        },
        created: function () {
            this.getHospitalData();
        },
        computed: {
            currentHosipitalData:function () {
                return this.hosipitalData.slice((Number(this.currentPage) - 1)*6, Number(this.currentPage)*6)
            }
        },
        methods: {
            handlePage: function (res) {
                this.currentPage = res.toString()
            },
            handleHospital: function (res) {
                this.$router.push(
                    {
                        name: 'reg-department',
                        params: {
                            hospitalId: res
                        }
                    }
                )
            },
            getHospitalData: function (id) {
                this.currentId = id
                this.isLoading = true
                getHospitalData({
                    deptId: id
                }).then(res => {
                    if(res.code == 10000){
                        this.hosipitalData = res.data
                        this.allPage = String(Math.ceil(this.hosipitalData.length/6))
                    }else {
                        this.dealError();
                    }
                    this.isLoading = false
                })
            },

        },
        destroyed: function () {
        }
    }
</script>

<style lang="less" src='../static/less/register.less' scoped></style>