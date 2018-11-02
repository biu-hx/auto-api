<template>
    <div class="hospitalAndDept">
        <Loading v-if="isLoading" :title="loadingText"></Loading>
        <div class="chooseHosital_sidebar">
            <ul class="siderbar_list mb30">
                <li :class="currentId ? '' : 'checked'" @click="getHospitalData(undefined)">全部医院</li>
            </ul>
            <ul class="siderbar_list" v-if="deptPage>1">
                <li class="mb30" style="transform: rotate(180deg)" @click="changeModal(0)">
                    <img src="../static/images/chooseHospital_arrow.png" alt="">
                </li>
            </ul>
            <ul class="siderbar_list dept_list">
                <li :class="item.deptId === currentId ? 'checked' : ''" v-for="item in currentDeparData" @click="getHospitalData(item.deptId)">{{item.deptName}}</li>
            </ul>
            <ul class="siderbar_list" v-if="deptPage != deptAllPage">
                <li class="mt30" @click="changeModal(1)">
                    <img src="../static/images/chooseHospital_arrow.png" alt="">
                </li>
            </ul>
        </div>
        <div class="chooseHosital_right">
            <div class="hospital_list" v-if="currentHosipitalData.length!=0">
                <div class="hospital_item" v-for="item in currentHosipitalData" @click="handleHospital(item.id)">
                    <div class="hospital_logo">
                        <img :src="item.logo" alt="">
                    </div>
                    <span>{{item.name}}</span>
                </div>
            </div>
            <div class="nodata_img" v-if="currentHosipitalData.length==0">
                <img src="../static/images/hospitall_nodata.png" alt="">
                <p>暂无医生</p>
            </div>
            <div style="width: 714px;position: absolute;bottom: 27px;"  v-if="hosipitalData.length>4">
                <pageination :currentPage="currentPage" :allPage="allPage" @changePage = "handlePage"></pageination>
            </div>
        </div>
        <div class="dialog_modal" v-show="isShow">
            <div class="dialog_departments">
                <img class="close_depar" src="../static/images/pay_close.png" @click="isShow=false" alt="">
                <p class="dialog_title">请选择科室：</p>
                <ul>
                    <li v-for="item in deparData" :class="item.deptId === currentId ? 'isCheck' : ''" @click="handleDept(item.deptId)">
                        {{item.deptName}}
                    </li>
                </ul>
            </div>
        </div>
    </div>
</template>
<script>
    import { getHospitalData, getDeptData } from '../api/consultant'
    export default {
        name: "consult-hospital",
        data() {
            return {
                isLoading: true,
                loadingText: '正在加载数据...',
                isShow: false,
                currentId: undefined,
                hospitalId: undefined,
                deparData: [],
                alldeparData: [],
                hosipitalData: [],
                currentPage: '1',
                allPage: undefined,
                currentDeptId: undefined,
                deptPage: undefined,
                deptAllPage: undefined
            }
        },
        created: function () {
            this.getHospitalData()
            this.getDeptData()
        },
        computed: {
            currentHosipitalData:function () {
                return this.hosipitalData.slice((Number(this.currentPage) - 1)*4,Number(this.currentPage)*4)
            },
            currentDeparData:function () {
                if(this.deptPage == 1){
                    return this.alldeparData.slice(0, 6)
                }else if(this.deptPage == this.deptAllPage){
                    return this.alldeparData.slice(this.deptPage * 5 - 4, this.deptPage*5 + 2)
                }else if(this.deptPage < this.deptAllPage){
                    return this.alldeparData.slice(this.deptPage * 5 - 4, this.deptPage*5 + 1)
                }
            }
        },
        methods: {
            handlePage: function (res) {
                this.currentPage = res.toString()
            },
            changeModal: function (obj) {
                if(obj == 0) {
                    this.deptPage--
                }else {
                    this.deptPage++
                }
            },
            handleDept: async function (res) {
                this.currentId = res
                await this.getHospitalData(this.currentId)
                this.isShow = true
                this.handleOk()
            },
            handleHospital: function (res) {
                this.hospitalId = res
                if(this.currentId){
                    this.$router.push(
                        {
                            path: `consultDoctor/${this.currentId}/${this.hospitalId}`
                        }
                    )
                }else {
                    this.getDeptData(res)
                    this.isShow = true
                }
            },
            getHospitalData: async function (id) {
                this.currentId = id
                this.isLoading = true
                let hospitalData = await getHospitalData({
                    deptId: id
                })
                this.hosipitalData = hospitalData.data
                this.allPage = String(Math.ceil(this.hosipitalData.length/4))
                this.isLoading = false
            },
            getDeptData: async function (id) {
                this.isLoading = true
                let deptData = await getDeptData({
                    hospitalId: id
                })
                if(id == undefined){
                    this.alldeparData = deptData.data
                    if(this.alldeparData.length<=6){
                        this.deptAllPage = 1
                        this.deptPage = 1
                    }else if(this.alldeparData.length>6&&this.alldeparData.length<=12){
                        this.deptAllPage = 2
                        this.deptPage = 1
                    }else {
                        this.deptAllPage = Math.ceil((this.alldeparData.length - 12)/5)+2
                        this.deptPage = 1
                    }
                }
                this.deparData = deptData.data
                this.isLoading = false
            },
            handleOk: function () {
                this.isShow = false
                if(this.currentId && this.hospitalId){
                    this.$router.push(
                        {
                            name: 'consult-doctor',
                            params: {
                                deptId: this.currentId,
                                hospitalId: this.hospitalId
                            }
                        }
                    )
                }
            }
        },
        destroyed: function () {

        }
    }
</script>

<style lang="less" src='../static/less/chooseHosital.less' scoped></style>