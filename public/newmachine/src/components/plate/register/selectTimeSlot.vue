<template :ready>
    <div v-cloak>
        <Loading v-if="unLoaded" :title="loadingText"></Loading>
        <Dialog v-if="infoBomb" :text="infoNotice" :buttonconfig="['知道了']" @hideBox="hideBox"></Dialog>

        <div class="content">
            <div class="title">
                <p>
                    请选择挂号的时段
                    <span style="color:red; font-size:26px; font-weight: normal">(仅显示有号源的时段)</span>
                </p>
            </div>
            <div class="date-list" v-if="timeSlotArray && timeSlotArray.length>0">
                <div class="date-item" v-for="item in showList"
                     @click="confirmTime(item.beginTime, item.endTime,item.periodId,item.sqno)">
                    <strong>
                        {{item.beginTime}}-{{item.endTime}}
                        <span>余{{item.leaveCount||'1'}}</span>
                    </strong>
                </div>
            </div>
            <!--分页-->
            <div class="page-controll" v-if="timeSlotArray.length>pageNum">
                <div class="prev-page" :class="{'unflip':!ablePrev}" @click="prevPage">上一页</div>
                <div class="page-num">{{pageIndex+1}}/{{Math.ceil(timeSlotArray.length/pageNum)}}</div>
                <div class="next-page" :class="{'unflip':!ableNext}" @click="nextPage">下一页</div>
            </div>
        </div>

    </div>
</template>

<script>

    import {getDoctorTimeSlot} from '../../api/api';

    export default {
        data() {
            return {
                timeSlotArray: undefined,
                showList: [],
                pageIndex: 0, //页面索引
                pageNum: 16, //每页数量
                ablePrev: false,
                ableNext: true,

                unLoaded: true,
                loadingText: "数据加载中...",

                infoBomb: false,
                infoNotice: '',

                hospitalInfo: '',
                registerInfo: ''
            };
        },
        created: function () {
            this.registerInfo = JSON.parse(this.$route.params.registerInfo);
            this.hospitalInfo = JSON.parse(localStorage.getItem('hospitalInfo'));

            this.getTimeSlot();
        },
        methods: {

            // 获取时段数据
            getTimeSlot: function (deptId) {
                this.loadingText = "数据加载中...";
                this.unLoaded = true;

                getDoctorTimeSlot({
                    hospitalId: "",
                    scheduleId: this.registerInfo.scheduleId,
                    period: this.registerInfo.period
                }).then((res) => {
                    this.unLoaded = false;
                    //播放选择时间语音

                    if (res.code == 10000) {
                        if (res.data.length == 0) {
                            // 去支付
                            this.$router.replace({
                                name: 'selectPayway',
                                params: {
                                    registerInfo: JSON.stringify(this.registerInfo)
                                }
                            })
                        } else {
                            this.$audioPlay(10);
                            this.timeSlotArray = res.data.periodList;
                            if (this.timeSlotArray.length > this.pageNum) {
                                this.showList = this.timeSlotArray.slice(0, this.pageNum);
                            } else {
                                this.showList = res.data.periodList;
                            }
                        }

                    } else {
                        if (this.filterASCII(res.msg)) {
                            this.infoNotice = this.filterASCII(res.msg);
                        } else {
                            this.infoNotice = res.msg;
                        }
                        this.infoBomb = true;
                    }
                })

            },

            // 选择时段,确认挂号
            confirmTime: function (beginTime, endTime, periodId, sqno) {
                // 追加时段参数
                this.registerInfo.beginTime = beginTime;
                this.registerInfo.endTime = endTime;
                this.registerInfo.periodId = periodId;
                this.registerInfo.sqno = sqno;

                // 去支付
                this.$router.push({
                    name: 'selectPayway',
                    params: {
                        registerInfo: JSON.stringify(this.registerInfo)
                    }
                })
            },
            //页面显示控制函数
            pageShowControll: function () {
                if (this.timeSlotArray.length - this.pageNum * this.pageIndex > this.pageNum) {
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
                    this.showList = this.timeSlotArray.slice(this.pageIndex * this.pageNum, this.pageIndex * this.pageNum +
                        this.pageNum);
                }
                this.pageShowControll();
            },
            //下一页
            nextPage: function () {
                if (this.timeSlotArray.length - this.pageNum * this.pageIndex > this.pageNum) {
                    this.pageIndex += 1;
                    this.showList = this.timeSlotArray.slice(this.pageIndex * this.pageNum, this.pageIndex * this.pageNum +
                        this.pageNum);
                }
                this.pageShowControll();
            },

            //隐藏提醒框
            hideBox: function () {
                this.infoBomb = false;
                if (!this.timeSlotArray) {
                    history.back();
                }
            }
        },
        destroyed: function () {
        }
    };
</script>
<style scoped>
    .date-list {
        width: 100%;
        display: flex;
        height: 545px;
        flex-wrap: wrap;
        overflow: hidden;
    }

    .date-item {
        width: 255px;
        height: 110px;
        border-radius: 8px;
        margin-left: 35px;
        margin-bottom: 26px;
        margin-right: 0px;
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: center;
        font-size: 26px;
        color: #fff;
        border: #0062A2 1px solid;
        box-sizing: border-box;
        background-color: #0399E0;
        cursor: pointer;
        line-height: 40px;
    }

    .date-item strong {
        display: block;
        text-align: center;
    }

    .date-item span {
        display: block;
        font-size: 26px;
        letter-spacing: 2px;
    }

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

    .am-item:active {
        background-color: #E0F0F5;
    }
</style>