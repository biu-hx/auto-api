<template :ready>
    <div v-cloak>
        <Loading v-if="unLoaded" :title="loadingText"></Loading>
        <Dialog v-if="infoBomb" :text="infoNotice" :buttonconfig="['返回']" @hideBox="hideBox"></Dialog>
        <!--预约信息 确认-->
        <Dialog v-if="infoConfirm" :text="infoTips" :buttonconfig="[]" title="B超预约信息确认">
            <div class="info-con">
                <p>姓名：{{tipsName}}</p>
                <p>就诊卡号：{{tipsCardId}}</p>
                <p class="red-font">预约时间：{{tipsTime}}   {{tipsDateM}}</p>
                <div class="no-record-btn is-agree">
                    <div class="research-btn" @click="cancelOrder">取消</div>
                    <div class="research-btn" @click="confirmOrder(tipsTime,tipsDateM)">确认</div>
                </div>
            </div>
        </Dialog>
        <!--预约成功-->
        <div class="result" v-if="successLay">
            <div class="modal">
                <div class="success">
                    <img src="../../../static/img/paysuccess-info-icon.png"/>
                    <p class="msg">预约成功</p>
                </div>
            </div>
        </div>
        <!--预约失败-->
        <div class="result" v-if="errLay">
            <div class="modal">
                <div class="success">
                    <img src="../../../static/img/overtime-info-icon.png"/>
                    <p class="msg red-font">预约失败</p>
                    <p class="err-tips">请重新选择预约时间</p>
                </div>
            </div>
        </div>

        <div class="content">
            <div class="title">
                <p>选择检查时间 <span class="spa-tips">（仅展示有号的时间）</span></p>
            </div>
            <div class="func-list">
                <div v-for="item in calendarList">
                    <!--上午-->
                    <div class="func-item" @click="showTips(item.date,'上午')">
                        <div class="func-item-icon-box">
                            {{item.date}}  上午
                        </div>
                        <div class="func-item-title">星期{{item.week}}  余{{item.morning}}</div>
                    </div>
                    <!--下午-->
                    <div class="func-item" @click="showTips(item.date,'下午')">
                        <div class="func-item-icon-box">
                            {{item.date}}  下午
                        </div>
                        <div class="func-item-title">星期{{item.week}}  余{{item.afternoon}}</div>
                    </div>
                </div>
            </div>

            <div class="page-controll" v-if="rendercCaleList.length > pageNum">
                <div class="prev-page" :class="{'unflip':!ablePrev}" @click="prevPage">上一页</div>
                <div class="page-num">{{pageIndex+1}}/{{Math.ceil(rendercCaleList.length/pageNum)}}</div>
                <div class="next-page" :class="{'unflip':!ableNext}" @click="nextPage">下一页</div>
            </div>
        </div>

    </div>
</template>
<script>
    let timeInterval;
    import {getPatientInfo} from '../../api/api';
    // import { getCheckCalendar } from '../../api/api';
    export default {
        name: "getCheckTime",
        props: {
            title: {
                type: String,
                default: 'B超'
            },},
        data() {
            return {
                unLoaded: false,
                loadingText: '数据加载中...',
                infoBomb: false,
                infoNotice: '',
                patientId: '',//病人ID
                calendarList: '',
                infoConfirm:false,
                infoTips:'',
                tipsCardId:'',
                tipsName:'',
                tipsTime:'',
                tipsDateM:'',
                successLay:false,
                errLay:false,
                time:3,
                rendercCaleList:[],
                pageIndex:0,
                pageNum:8,
                ablePrev:false,
                ableNext: true,

                patientInfo: undefined,
                userInfo: undefined,

            }

        },
        created: function () {
            this.unLoaded = true;
            this.userInfo = JSON.parse(localStorage.getItem('userInfo'));
            this.hospitalNumber = JSON.parse(localStorage.getItem('hospitalInfo')).number;
            this.getPatientInfo();
        },
        methods: {
            //获取 病人信息>病人ID
            getPatientInfo: function () {
                getPatientInfo({
                    cardId: this.userInfo.cardId
                }).then(res => {
                    if (res.code == 10000) {
                        this.patientInfo = res.data;
                        this.getCheckCalendar();
                        // localStorage.setItem('patientInfo', JSON.stringify(res.data));
                    } else {
                        this.infoBomb = true;
                        this.infoNotice = res.msg || "暂无数据"
                        this.unLoaded = false;
                    }
                    
                })
            },
            //获取可预约检查时间
            getCheckCalendar: function () {
                this.tipsCardId = this.userInfo.cardId;
                this.tipsName = this.patientInfo.patient_name;
                let obj = {
                    cardId: this.userInfo.cardId,
                    patient_id: this.patientInfo.patient_id,
                };
                this.postData(this.hospitalNumber, obj, '/typeb/getCheckCalendar', (res) => {
                    if (res.data.code == 10000) {
                        this.calendarList = res.data.data.item;
                        for (var i in this.calendarList) {
                            this.calendarList[i].week = "日一二三四五六".split(/(?!\b)/)[this.calendarList[i].week];
                        }
                        //分页,查询出预约时间列表后默认渲染第一页列表数据
                        this.rendercCaleList = res.data.data.item;
                        if (this.rendercCaleList.length > this.pageNum) {
                            this.calendarList = this.rendercCaleList.slice(0, this.pageNum);
                        } else {
                            this.calendarList = this.rendercCaleList;
                        }
                    }
                    this.unLoaded = false;
                })
            },
            //确认是否预约
            showTips:function (date,dateM) {
                this.infoConfirm = true;
                this.tipsTime = date;
                this.tipsDateM = dateM;
            },
            //取消预约
            cancelOrder:function () {
                this.infoConfirm = false;
            },
            //确认预约
            confirmOrder:function (date,dateM) {
                if(dateM == '上午'){
                    dateM = 'am';
                }
                if(dateM == '下午'){
                    dateM = 'pm';
                }
                let obj = {
                    patient_id: this.patientInfo.patient_id,
                    appdate: date,
                    apptime: dateM
                };
                this.postData(this.hospitalNumber, obj, '/typeb/putConfirmCheck', (res) => {
                    if (res.data.code == 10000) {
                        //console.log("预约成功！");
                        //console.log(res.data.data);
                        this.successLay = true;
                        this.timing();
                    }else{
                        this.errLay = true;
                        this.timeReturn();
                    }
                });
                this.infoConfirm = false;
            },
            // 倒计时3秒后跳转到打印页面
            timing:function(){
                let _this = this;
                timeInterval = setInterval(function(){
                    _this.time--;
                    if (_this.time<=0){
                        clearInterval(timeInterval);
                        _this.goPrintPage();
                        return;
                    }
                }, 1000)
            },
            //3秒后重新选择预约时间
            timeReturn:function () {
                let _this = this;
                timeInterval = setInterval(function(){
                    _this.time--;
                    if (_this.time<=0){
                        clearInterval(timeInterval);
                        this.errLay = false;
                        return;
                    }
                }, 1000)
            },
            //跳转到打印页面
            goPrintPage:function () {
                this.$router.push({
                    name: 'printPage'
                });
            },
            hideBox: function () {
                this.infoBomb = false;
                this.$router.push({
                    name: 'home'
                })
            },

            //页面显示控制函数
            pageShowControll: function () {
                if (this.rendercCaleList.length - this.pageNum * this.pageIndex > this.pageNum) {
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
                    this.calendarList = this.rendercCaleList.slice(this.pageIndex * this.pageNum, this.pageIndex * this
                        .pageNum + this.pageNum);
                }
                this.pageShowControll();
            },
            //下一页
            nextPage: function () {
                if (this.rendercCaleList.length - this.pageNum * this.pageIndex > this.pageNum) {
                    this.pageIndex += 1;
                    this.calendarList = this.rendercCaleList.slice(this.pageIndex * this.pageNum, this.pageIndex * this
                        .pageNum + this.pageNum);
                }
                this.pageShowControll();
            },
        },
        destroyed: function () {
            clearInterval(timeInterval);
        }

    }
</script>

<style scoped>
    .spa-tips {
        color: red;
        font-size: 22px;
        font-weight: lighter;
    }
    .info-con .red-font{
        color: red;
    }

    .func-list {
        width: 100%;
        display: flex;
        flex-wrap: wrap;
        /*overflow: hidden;*/
    }
.am-frame-box-info p{
    margin: 10px;
}
    .func-item {
        width: 254px;
        height: 108px;
        background: url('../../../static/img/func-item-bg.png') no-repeat;
        background-size: 100% 100%;
        /*margin:15px 0 15px 30px;*/
        border-radius: 5px;
        margin-left: 35px;
        overflow: hidden;
        cursor: pointer;
        margin-bottom: 30px;
    }

    .func-item:active {
        opacity: .8
    }

    .func-item-icon-box {
        height: 60%;
        line-height: 82px;
    }

    .func-item-title {
        height: 40%;
    }

    .func-item-icon-box, .func-item-title {
        width: 100%;
        font-size: 22px;
        color: #fff;
        text-align: center;

    }
    .info-con{
        position: absolute;
        width: 530px;
        height: 300px;
        top: 50%;
        margin-top: -150px;
        text-align: left;
    }
    .info-con p{
        font-size: 34px;
        margin-top: 22px;
        margin-left: 72px;
        color: rgba(0,0,0,0.8);
    }
    .info-con p:first-child{
        margin-top: 58px;
    }

    .is-agree .research-btn{
        margin: 0 40px;
    }
    .is-agree div:first-child{
        background-color: #B5B5B5;
    }

    .result{position: fixed; width:100%; height: 100%; z-index: 10; top:0; left: 0; background-color: rgba(0,0,0,.6); display: flex; align-items: center; justify-content: center;}
    .result .modal{background-color: #fff; width:700px; height: 460px; border-radius: 6px; margin-top: 150px; text-align: center; position: relative;}
    .result .modal img{margin:0 auto;}
    .result .modal .msg{font-size: 50px; margin-top: 30px; font-weight: bold;}
    .result .modal .success{margin-top: 110px}
    .result .modal .success .msg{color: #009843}
    .result .modal .success .red-font{color: red}
    .result .modal .success .err-tips{color: #000000;font-size: 30px;margin-top: 20px}
    .result .modal .time{cursor:pointer;position: absolute; right:10px; top:8px; height: 50px; line-height: 50px; width:100px; font-size: 16px; color: #f30}
    .result .modal .time strong{font-size: 20px; font-weight: normal;}


</style>