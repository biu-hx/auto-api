<template :ready>
    <div v-cloak>
        <Loading v-if="unLoaded" :title="loadingText"></Loading>
        <Dialog v-if="infoBomb" :text="infoNotice" :buttonconfig="['知道了']" @hideBox="hideBox"></Dialog>
        <div class="content">
            <div class="title">
                <p>
                请选择您想要挂号的日期
                <span style="color:red; font-size:26px; font-weight: normal">(仅显示有号源的排班时间)</span>
                </p>
            </div>
            <div class="date-list" v-if="curSelectItem=='date'&&timeList.length>0">
                <div class="date-item" v-for="(item,index) in timeList" @click="selectTime(item.date,item.period)">
                    <strong>
                        {{item.date}}
                        <span>{{getweekday(item.date)}}</span>
                    </strong>
                </div>
            </div>
            <div class="am-frame" v-if="curSelectItem=='period'">
                <div class="am-frame-box">
                    <img src="../../../static/img/close-icon.png" alt="" class="close-icon" @click="closeAmBox">
                    <p class="am-frame-box-title">提示</p>
                    <p class="am-frame-box-info">请选择您要挂号的时间段...</p>
                    <div class="am-list">
                        <div class="am-item"  v-for="(item,index) in amList" @click="selectAm(item)">
                            <span v-if="item=='am'">上午</span>
                            <span v-if="item=='pm'">下午</span>
                            <span v-if="item=='npm'">夜间</span>
                            <span v-if="item=='all'">全天</span>
                            <span v-if="item=='day'">白天</span>
                            <span v-if="item=='sam'">早间门诊</span>
                            <span v-if="item=='amd'">上午延时</span>
                            <span v-if="item=='md'">午间门诊</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="demonstration-box" v-if="timeList.length===0">
                <p class="no-record-icon">
                    <img src="../../../static/img/no-data.png" alt="" class="no-data-img">
                </p>
                <p class="no-record-text">暂无医生排班信息！</p>
                <div class="no-record-btn">
                    <div class="research-btn" @click="reSearch">重新查询</div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
    export default {
        data() {
            return {
                curSelectItem: 'date',
                deptId: "",
                dateTime: '',
                period: '',
                amList: [],
                timeList: {},
                unLoaded: true,
                loadingText: "数据加载中...",
                countTime: '',
                infoBomb: false,
                infoNotice: '',
                isNoTime:false,
                weekDay: ["星期天", "星期一", "星期二", "星期三", "星期四", "星期五", "星期六"],
                hospitalInfo: ''
            };
        },
        created: function () {
            let districtInfo = localStorage.getItem('districtInfo');
            this.districtId = districtInfo ? JSON.parse(districtInfo).districtId : '';

            this.deptId = this.$route.params.deptId;
            this.userInfo = JSON.parse(localStorage.getItem('userInfo'));
            this.hospitalInfo = JSON.parse(localStorage.getItem('hospitalInfo'));
            this.getTimeList(this.deptId);
        },
        methods: {
            getweekday: function(dayNum){
                var wk = new Date(Date.parse(dayNum.replace(/-/g, "/")));
                return this.weekDay[wk.getDay()]
            },
            //关闭上下午选择框
            closeAmBox: function () {
                this.curSelectItem = 'date';
            },
            //选择日期后，生成可以选择的时段数组
            selectTime: function (date, period) {
                // 根据系统配置，是否选择“上午/下午”
                if(this.hospitalInfo.serviceConf.registration.XuanZeShangXiaWu == "1"){
                    if(period.length == 0){
                        this.infoBomb = true;
                        this.infoNotice = "当前日期已无号源";
                        return
                    }
                    this.curSelectItem = 'period';
                    this.dateTime = date;
                    this.amList = period;
                }else{
                    this.dateTime = date;
                    this.$router.push({
                        name: "selectDoctor",
                        params: {
                            deptId: this.deptId,
                            dateTime: this.dateTime,
                            period: "all"
                        }
                    });
                }
                
            },
            //选择上下午后，生成医生数组
            selectAm: function (period) {
                this.period = period;
                this.$router.push({
                    name: "selectDoctor",
                    params: {
                        deptId: this.deptId,
                        dateTime: this.dateTime,
                        period: period
                    }
                });
                // this.getDoctorList(this.deptId, this.dateTime, period);
            },
            //根据科室id获取排班日期表
            getTimeList: function (deptId) {
                this.loadingText = "数据加载中...";
                this.unLoaded = true;
                this.$http.get(this.publicUrl + '/Registration/date', {
                    params: {
                        deptId: deptId,
                        districtId: this.districtId,
                        timeStr: new Date().getTime()
                    },
                    headers: this.config(this.hospitalInfo.number),
                    timeout: 10000
                }).then((res) => {
                    this.unLoaded = false;
                    //播放选择时间语音

                    this.$audioPlay(10);

                    if (res.data.code == 10000) {
                        this.timeList = res.data.data;
                    } else {
                        this.isNoTime = true;
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
            //隐藏提醒框
            hideBox: function () {
                this.infoBomb = false;
                if(this.isNoTime){
                    history.back();
                }
            },
            //重新查询
            reSearch: function () {
                this.getTimeList(this.deptId);
            }
        },
        destroyed: function () {
            clearInterval(this.timer);
        }
    };
</script>
<style scoped>
    .date-list {
        width: 100%;
        margin-top: 30px;
        display: flex;
        flex-wrap: wrap;
        overflow: hidden;
    }

    .date-item {
        width: 255px;
        height: 110px;
        border-radius: 8px;
        margin-left: 35px;
        margin-bottom: 35px;
        margin-right: 0px;
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: center;
        font-size: 30px;
        color: #fff;
        border:#0062A2 1px solid;
        box-sizing: border-box;
        background-color: #0399E0;
        cursor: pointer;
        line-height: 40px;
    }
    .date-item strong{display: block; text-align: center;}
    .date-item span{display: block; font-size: 26px; letter-spacing: 2px;}

    .am-frame {
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 2, 0.6);
        position: fixed;
        top: 0;
        left: 0;
        overflow: hidden;
        z-index: 9999;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .am-frame-box {
        width: 985px;
        height: 585px;
        border-radius: 15px;
        background: #fff;
        position: relative;
        overflow: hidden;
    }

    .close-icon {
        width: 40px;
        height: 40px;
        position: absolute;
        right: 20px;
        top: 20px;
        cursor: pointer;
    }

    .am-frame-box-title {
        width: 785px;
        margin-left: 200px;
        margin-top: 115px;
        font-size: 50px;
        font-weight: 600;
        color: #ff4039;
    }

    .am-frame-box-info {
        width: 785px;
        margin-left: 200px;
        margin-top: 35px;
        font-size: 38px;
    }

    .am-list {
        width: 785px;
        margin-left: 200px;
        margin-top: 85px;
        display: flex;
        flex-wrap: wrap;
        overflow: hidden;
    }

    .am-item {
        width: 180px;
        height: 65px;
        border: 1px solid #008de8;
        color: #008de8;
        font-size: 36px;
        border-radius: 5px;
        margin-right: 70px;
        margin-bottom: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
    }
    .am-item:active{
        background-color: #E0F0F5;
    }
</style>