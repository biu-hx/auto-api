<template :ready>
    <div v-cloak>
        <Loading v-if="unLoaded" :title="loadingText"></Loading>
        <Dialog v-if="infoBomb" :text="infoNotice" :buttonconfig="['知道了']" @hideBox="hideBox"></Dialog>
        <div class="content">
            <div class="title">
                <p v-if="curSelectItem=='doctor'">请选择您想要挂号的医生</p>
            </div>
            <div class="doctor-list" v-if="curSelectItem=='doctor'&&doctorList.length>0">
                <div class="doctor-item" v-for="(item,index) in doctorList" @click="selectDoctor(item.doctorName,item.title,item.doctorId,item.scheduleId,item.fee,item.period)">
                    <div class="doctor-item-header">
                        <img :src="item.image" alt="" class="doctor-avatar">
                        <div class="doctor-detail">
                            <p class="doctor-name">{{item.doctorName}}
                                <span v-if="hospitalInfo.serviceConf.registration.XianShiYuHao=='1'">(余号{{item.num}})</span>
                            </p>
                            <p class="doctor-title">{{item.title}}</p>
                        </div>
                    </div>
                    <div class="doctor-item-footer">
                        <p class="doctor-fee">挂号费：
                            <span>￥{{item.fee}}</span>
                        </p>
                        <p class="doctor-time">就诊时间：{{dateTime}}
                            <span v-if="item.period=='am'">上午</span>
                            <span v-if="item.period=='pm'">下午</span>
                            <span v-if="item.period=='npm'">晚间</span>
                            <span v-if="item.period=='all'">全天</span>
                        </p>
                    </div>
                </div>
            </div>
            <div class="page-controll" v-if="renderDocList.length>pageNum">
                <div class="prev-page" :class="{'unflip':!ablePrev}" @click="prevPage">上一页</div>
                <div class="page-num">{{pageIndex+1}}/{{Math.ceil(renderDocList.length/pageNum)}}</div>
                <div class="next-page" :class="{'unflip':!ableNext}" @click="nextPage">下一页</div>
            </div>
        </div>
    </div>
</template>
<script>
    export default {
        data() {
            return {
                curSelectItem: 'doctor',
                deptId: "",
                dateTime: '',
                period: '',
                doctorList: [], //通过时间和上下午筛选后的医生列表
                unLoaded: true,
                loadingText: "数据加载中...",
                infoBomb: false,
                renderDocList: [],
                pageIndex: 0, //页面索引
                pageNum: 6, //每页数量
                ablePrev: false,
                ableNext: true,
                isNoTime:false,
                hospitalInfo: ''
            };
        },
        created: function () {
            let districtInfo = localStorage.getItem('districtInfo');
            this.districtId = districtInfo ? JSON.parse(districtInfo).districtId : '';
            
            this.deptId = this.$route.params.deptId;
            this.dateTime = this.$route.params.dateTime;
            this.period = this.$route.params.period;
            this.userInfo = JSON.parse(localStorage.getItem('userInfo'));
            this.hospitalInfo = JSON.parse(localStorage.getItem('hospitalInfo'));
            this.getDoctorList(this.deptId, this.dateTime, this.period);

        },
        methods: {
            //选择医生，将选择的参数传递到选择支付方式页面
            selectDoctor: function (doctorName, deptName, doctorId, scheduleId, fee, period) {
                const obj = {
                    doctorName: doctorName,
                    deptName: deptName,
                    deptId: this.deptId,
                    date: this.dateTime,
                    period: period,
                    doctorId: doctorId,
                    scheduleId: scheduleId,
                    fee: fee
                }

                if(this.hospitalInfo.serviceConf.registration.XuanZeShiDuan == "1"){ // 需要选择时段的医院
                    this.$router.push({
                        name: 'selectTimeSlot',
                        params: {
                            registerInfo: JSON.stringify(obj)
                        }
                    })
                }else{
                    this.$router.push({
                        name: 'selectPayway',
                        params: {
                            registerInfo: JSON.stringify(obj)
                        }
                    })
                }
                
                
            },
            //根据科室id、所选日期、上下午获取医生列表
            getDoctorList: function (deptId, date, period) {
                this.loadingText = "数据加载中...";
                this.unLoaded = true;
                this.$http.get(this.publicUrl + '/registration/schedule', {
                    params: {
                        districtId: this.districtId,
                        deptId: deptId,
                        date: date,
                        period: period,
                        timeStr: new Date().getTime()
                    },
                    headers: this.config(JSON.parse(localStorage.getItem('hospitalInfo')).number),
                    timeout: 10000
                }).then((res) => {
                    this.unLoaded = false;
                    if (res.data.code == 10000) {

                        var data_list = res.data.data;
                        
                        // 无数据
                        if(data_list.length <= 0){
                            this.isNoTime=true;
                            this.infoNotice = "没有找到您想要挂号的医生~";
                            this.infoBomb = true;
                            return;
                        }

                        this.renderDocList = data_list;
                        
                        //分页,查询出医生列表后默认渲染第一页医生数据
                        if (this.renderDocList.length > this.pageNum) {
                            this.doctorList = this.renderDocList.slice(0, this.pageNum);
                        } else {
                            this.doctorList = this.renderDocList;
                        }
                    } else {
                        this.isNoTime=true;
                        if (this.filterASCII(res.data.msg)) {
                            this.infoNotice = this.filterASCII(res.data.msg);
                        } else {
                            this.infoNotice = res.data.msg;
                        }
                        this.infoBomb = true;
                    }
                }).catch((err) => {
                    this.unLoaded = false;

                    this.$audioPlay(21);

                    this.dealError();
                })
            },
            //页面显示控制函数
            pageShowControll: function () {
                if (this.renderDocList.length - this.pageNum * this.pageIndex > this.pageNum) {
                    this.ableNext = true;
                } else {
                    this.ableNext = false;
                }
                if (this.pageIndex > 0) {
                    this.ablePrev = true;
                } else {
                    this.ablePrev = false;
                }
            },
            //上一页
            prevPage: function () {
                if (this.pageIndex > 0) {
                    this.pageIndex -= 1;
                    this.doctorList = this.renderDocList.slice(this.pageIndex * this.pageNum, this.pageIndex * this
                        .pageNum + this.pageNum);
                }
                this.pageShowControll();
            },
            //下一页
            nextPage: function () {
                if (this.renderDocList.length - this.pageNum * this.pageIndex > this.pageNum) {
                    this.pageIndex += 1;
                    this.doctorList = this.renderDocList.slice(this.pageIndex * this.pageNum, this.pageIndex * this
                        .pageNum + this.pageNum);
                }
                this.pageShowControll();
            },
            hideBox: function () {
                this.infoBomb = false;
                history.back();
            },
        },
        destroyed: function () {
            clearInterval(this.timer);
        }
    };
</script>
<style scoped>

    .am-item {
        width: 155px;
        height: 65px;
        border: 1px solid #008de8;
        color: #008de8;
        font-size: 30px;
        border-radius: 5px;
        margin-right: 70px;
        margin-bottom: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
    }

    .doctor-list {
        width: 100%;
        height: 480px;
        display: flex;
        flex-wrap: wrap;
        overflow: hidden;
        padding-top: 15px;
        padding-bottom: 15px
    }

    .doctor-item {
        width: 353px;
        height: 200px;
        border:#0062A2 1px solid;
        box-sizing: border-box;
        background-color: #0399E0;
        margin-left: 35px;
        margin-right: 0px;
        margin-bottom: 35px;
        border-radius: 10px;
        overflow: hidden;
        cursor: pointer;
    }

    .doctor-item-header {
        width: 100%;
        height: 107px;
        display: flex;
        align-items: center;
        justify-content: flex-start;
    }

    .doctor-avatar {
        width: 70px;
        height: 70px;
        border-radius: 70px;
        margin: 0 20px;
    }

    .doctor-detail {
        width: 200px;
        height: 70px;
        color: #fff;
        overflow: hidden;
    }

    .doctor-name {
        font-size: 30px;
        font-weight: bolder;
        line-height: 120%
    }

    .doctor-name span {
        padding-left: 10px;
        font-size: 22px;
        font-weight: 400;
    }

    .doctor-title {
        font-size: 22px;
        font-weight: 400;
    }

    .doctor-item-footer {
        width: 100%;
        border-top: 1px solid #6bc1ea;
        height: 90px;
        color: #fff;
        overflow: hidden;
    }

    .doctor-fee {
        width: 100%;
        text-indent: 20px;
        font-size: 22px;
        margin-top: 10px;
    }

    .doctor-fee span {
        font-size: 26px;
        color: #f0ff00;
    }

    .doctor-time {
        width: 100%;
        text-indent: 20px;
        font-size: 22px;
        margin-top: 5px;
    }
</style>