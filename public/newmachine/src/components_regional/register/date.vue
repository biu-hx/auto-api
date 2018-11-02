<template>
    <div class="registerWrap" v-cloak>
        <Loading v-if="isLoading" :title="loadingText"></Loading>
        <div class="dateList">
            <ul>
                <li v-for="item in currentDateData" @click="handleDate(item)">
                    <strong>{{item.date}}</strong>
                    <small>{{item.date|getWeek}}</small>
                </li>
            </ul>
        </div>
        <div class="pageinationWrap">
            <pageination :currentPage="currentPage" :allPage="allPage" @changePage = "handlePage"></pageination>
        </div>

        <!-- 请选择时段 -->
        <div class="period_modal" v-show="showPeriodModal">
            <dl>
                <dt>请选择就诊时段</dt>
                <dd>
                    <p v-for="item in currentDate.period" @click="handlePeriod(item)">{{item|returnPeriodName}}</p>
                </dd>
                <dd class="close" @click="showPeriodModal = false"></dd>
            </dl>
        </div>
    </div>
</template>
<script>
    import { getRegDate } from '../api/register';
    import enumerate from '../../js/enumerate';
    export default {
        data() {
            return {
                isLoading: false,
                loadingText: '正在加载数据...',

                dateList: [],
                currentPage: '1',
                allPage: undefined,

                showPeriodModal: false,
                currentDate: {
                    date: "",
                    period: []
                },

                // 路由参数
                hospitalId: undefined,
                deptId: undefined,
            }
        },
        created: function () {},
        activated:function(){
            this.dateList = [];
            this.hospitalId = this.$route.params.hospitalId;
            this.deptId = this.$route.params.deptId;
            this.getDateList();
        },
        filters: {
            getWeek: function(value){
                let weekDay = ["星期天", "星期一", "星期二", "星期三", "星期四", "星期五", "星期六"];
                let wk = new Date(Date.parse(value.replace(/-/g, "/")));
                return weekDay[wk.getDay()]
            },
            returnPeriodName: function (value) {  
                return enumerate.period[value];
            }  
        },  
        computed: {
            currentDateData:function () {
                return this.dateList.slice((Number(this.currentPage) - 1)*6, Number(this.currentPage)*6)
            }
        },
        methods: {
            getDateList: function () {
                this.isLoading = true;
                getRegDate({
                    hospitalId: this.hospitalId,
                    deptId: this.deptId
                }).then(res => {
                    console.log(res)
                    if(res && res.code == 10000){
                        this.dateList = res.data
                        this.allPage = String(Math.ceil(this.dateList.length/6))
                    }
                    this.isLoading = false
                })
            },
            handlePage: function (res) {
                this.currentPage = res.toString()
            },
            handleDate: function (item) {
                this.currentDate = {
                    date: item.date,
                    period: item.period
                }
                this.showPeriodModal = true
            },
            handlePeriod: function(res){
                let currentPeriod = res;
                this.showPeriodModal = false;
                this.$router.push(
                    {
                        name: 'reg-doctor',
                        params: {
                            hospitalId: this.hospitalId,
                            deptId: this.deptId,
                            date: JSON.stringify({
                               date: this.currentDate.date,
                               period: currentPeriod
                            })
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