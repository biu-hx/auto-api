<template :ready>
    <div v-cloak>
        <Loading v-if="unLoaded" :title="loadingText"></Loading>
        <Dialog v-if="infoBomb" :text="infoNotice" :buttonconfig="['返回']" @hideBox="hideBox"></Dialog>
        <div class="content">
            <div class="title">
                <p>请选择您想要挂号的科室
                    <span>(周末及节假日不预约)</span>
                </p>
            </div>
            <div class="dept-list" v-if="deptList.length>0">
                <div class="dept-item" v-for="(item,index) in deptList" @click="selectDept(item.deptId)">
                    <p>{{item.name}}</p>
                </div>
            </div>
            <div class="page-controll" v-if="showList.length>pageNum">
                <div class="prev-page" :class="{'unflip':!ablePrev}" @click="prevPage">上一页</div>
                <div class="page-num">{{pageIndex+1}}/{{Math.ceil(showList.length/pageNum)}}</div>
                <div class="next-page" :class="{'unflip':!ableNext}" @click="nextPage">下一页</div>
            </div>
            <div class="for-position" v-if="deptList.length>0">
                <div class="statement-box">
                    <p class="statement-box-title">温馨提示</p>
                    <div class="index-information">
                        <p class="info-content-sp" v-if="hospitalInfo.hospital_id=='10000'">挂号时间：当日号7：30开始挂号；预约号20：00开始挂号；节假日和周末不预约，只能挂当天号。</p>
                        <p class="info-content-sp" v-if="hospitalInfo.hospital_id=='61754'">挂号时间：当日号挂号时间为上午7:00-12:00，下午12:40-15:50；</p>
                    </div>
                </div>
            </div>
            <div class="demonstration-box" v-if="deptList.length===0">
                <p class="no-record-icon">
                    <img src="../../../static/img/no-data.png" alt="" class="no-data-img">
                </p>
                <p class="no-record-text">暂无科室信息！</p>
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
                unLoaded: true,
                loadingText: '数据加载中...',
                infoBomb: false,
                infoNotice: "",
                
                deptList: {},
                showList: [],
                pageIndex: 0, //页面索引
                pageNum: 12, //每页数量
                ablePrev: false,
                ableNext: true,
                districtId: undefined,
                hospitalInfo: undefined
            };
        },
        created: function () {
            let districtInfo = localStorage.getItem('districtInfo');
            this.districtId = districtInfo ? JSON.parse(districtInfo).districtId : '';
            this.hospitalInfo = JSON.parse(localStorage.getItem('hospitalInfo'));

            this.getDeptList();
        },
        methods: {
            //重新查询
            reSearch: function () {
                this.getDeptList();
            },
            //选择科室进入选择医生页面
            selectDept: function (deptId) {
                this.$router.push({
                    name: "selectTime",
                    params: {
                        deptId: deptId
                    }
                });
            },
            //隐藏弹框
            hideBox: function () {
                this.infoBomb = false;
                if(this.infoNotice != ""){
                    history.back(-10);
                }
            },
            //获取科室列表
            getDeptList: function () {
                this.loadingText = '数据加载中...';
                this.unLoaded = true;
                this.$http.get(this.publicUrl + '/registration/dept', {
                    params: {
                        districtId: this.districtId,
                        timeStr: new Date().getTime()
                    },
                    headers: this.config(this.hospitalInfo.number),
                    timeout: 10000
                }).then((res) => {
                    this.unLoaded = false;
                    if (res.data.code == 10000) {
                        this.showList = res.data.data;
                        this.deptList = this.showList.slice(0, this.pageNum);
                        //播放选择科室语音

                        this.$audioPlay(16);

                    } else {
                        if (this.filterASCII(res.data.msg)) {
                            this.infoNotice = this.filterASCII(res.data.msg);
                        } else {
                            this.infoNotice = res.data.msg || "暂无科室信息";
                        }
                        this.infoBomb = true;
                    }
                }).catch((err) => {

                    this.$audioPlay(21);

                    this.dealError();
                })
            },
            //页面显示控制函数
            pageShowControll: function () {
                if (this.showList.length - this.pageNum * this.pageIndex > this.pageNum) {
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
                    this.deptList = this.showList.slice(this.pageIndex * this.pageNum, this.pageIndex * this.pageNum +
                        this.pageNum);
                }
                this.pageShowControll();
            },
            //下一页
            nextPage: function () {
                if (this.showList.length - this.pageNum * this.pageIndex > this.pageNum) {
                    this.pageIndex += 1;
                    this.deptList = this.showList.slice(this.pageIndex * this.pageNum, this.pageIndex * this.pageNum +
                        this.pageNum);
                }
                this.pageShowControll();
            }
        }
    };
</script>
<style scoped>
    .title span {
        color: #0b95db;
        font-size: 26px;
        font-weight: 300;
        margin-top: 10px;
    }

    .dept-list {
        width: 1153px;
        height: 440px;
        overflow: hidden;
        margin-left: 35px;
    }

    .dept-item {
        float: left;
        width: 260px;
        height: 110px;
        border:#0062A2 1px solid;
        box-sizing: border-box;
        background-color: #0399E0;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 32px;
        line-height: 34px;
        color: #fff;
        cursor: pointer;
        border-radius: 8px;
        margin-right: 30px;
        margin-bottom: 25px;
    }

    .dept-item:nth-child(4) {
        margin-right: 0;
    }

    .dept-item:nth-child(8) {
        margin-right: 0;
    }

    .dept-item:nth-child(12) {
        margin-right: 0;
    }

    .dept-item p {
        width: 230px;
        text-align: center;
    }

    .for-position {
        width: 1115px;
        position: absolute;
        bottom: 20px;
        left: 40px;
    }

    .statement-box {
        width: 1115px;
        height: 75px;
        background: url('../../../static/img/dept-border-bg.png')no-repeat;
        background-size: 100% 65px;
        background-position-y: bottom;
        position: relative;
        overflow: hidden;
    }

    .statement-box-title {
        font-size: 24px;
        position: absolute;
        left: 55px;
        top: 0;
    }

    .index-information {
        width: 1078px;
        margin-top: 40px;
        margin-left: 30px;
    }

    .info-content-sp {
        width: 100%;
        height: 20px;
        display: flex;
        align-items: center;
        font-size: 16px;
        color: #000;
        margin-top: 10px;
    }
</style>