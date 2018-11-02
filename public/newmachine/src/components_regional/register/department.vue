<template>
    <div class="registerWrap" v-cloak>
        <Loading v-if="isLoading" :title="loadingText"></Loading>
        <div class="department">
            <ul>
                <li v-for="item in currentDepartmentData" @click="handleDepartment(item.deptId)">{{item.name}}</li>
            </ul>
        </div>
        <div class="pageinationWrap">
            <pageination :currentPage="currentPage" :allPage="allPage" @changePage="handlePage"></pageination>
        </div>
    </div>
</template>
<script>
    import { getDepartmentData } from '../api/register'
    export default {
        data() {
            return {
                isLoading: false,
                loadingText: '正在加载数据...',

                departmentArray: [],
                currentPage: '1',
                allPage: undefined,

                // 路由参数
                hospitalId: undefined
            }
        },
        created: function () { },
        activated:function(){
            this.departmentArray = [];
            this.hospitalId = this.$route.params.hospitalId;
            this.getDepartmentData();
        },
        computed: {
            currentDepartmentData:function () {
                return this.departmentArray.slice((Number(this.currentPage) - 1)*12, Number(this.currentPage)*12)
            }
        },
        methods: {
            getDepartmentData: function () {
                this.isLoading = true
                getDepartmentData({
                    hospitalId: this.hospitalId
                }).then(res => {
                    if(res.code == 10000){
                        this.departmentArray = res.data;
                        this.allPage = String(Math.ceil(this.departmentArray.length/12))
                    }else {
                        this.dealError();
                    }
                    this.isLoading = false
                })
            },
            handlePage: function (res) {
                this.currentPage = res.toString()
            },
            handleDepartment: function (res) {
                let departmentId = res;
                this.$router.push(
                    {
                        name: 'reg-date',
                        params: {
                            deptId: departmentId,
                            hospitalId: this.hospitalId
                        }
                    }
                )
            }
        },
        destroyed: function () {
        }
    }
</script>

<style lang="less" src='../static/less/register.less' scoped></style>