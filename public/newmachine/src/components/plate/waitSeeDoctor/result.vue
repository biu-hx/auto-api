<template :ready>
    <div v-cloak>
        <Loading v-if="unLoaded" :title="loadingText"></Loading>
        <Dialog v-if="infoBomb" :text="infoNotice" :buttonconfig="['返回']" @hideBox="hideBox"></Dialog>

        <div class="content">
            <div class="title">
                <p>候诊查询结果</p>
            </div>
            
            <div class="resultDetail" v-if="waitInfoItem && !noData">
                
                <div class="info">
                    <ul>
                        <li>
                            <span>姓名：</span>
                            <strong>{{waitInfoItem.username}}</strong>
                        </li>
                        <li>
                            <span>卡号：</span>
                            <strong>{{waitInfoItem.CardNo}}</strong>
                        </li>
                        <li>
                            <span>就诊科室：</span>
                            <strong>{{waitInfoItem.dept}}</strong>
                        </li>
                        <li>
                            <span>医生：</span>
                            <strong>{{waitInfoItem.doctor}}</strong>
                        </li>
                    </ul>

                    <ul class="states">
                        <li>
                            <span>就诊序号：</span>
                            <strong>{{waitInfoItem.myNum}}</strong>
                        </li>
                        <li>
                            <span>就诊状态：</span>
                            <strong>{{waitInfoItem.beingNum == '' ? "未开始就诊" : (waitInfoItem.beingNum == waitInfoItem.myNum ? '正在就诊' : "等待就诊")}}</strong>
                        </li>
                    </ul>
                </div>

                <div class="graphic">
                    <template v-if="waitInfoItem.beingNum == ''">
                        <div class="noBegin">
                            <img src="../../../static/img/overtime-info-icon.png"/>
                            <p class="msg">未开始就诊</p>
                            <p class="text" v-if="waitInfoItem.HouZhen && waitInfoItem.HouZhen=='1'">请在就诊时段来医院签到，签到成功后可查询候诊信息。</p>
                        </div>
                    </template>

                    <template v-else>
                        <ul class="tag">
                            <li><i class="me"></i><span>我</span></li>
                            <li style="padding-right:40px"><i class="ing"></i><span>正在就诊</span></li>
                            <li><i class="wait"></i><span>排队中</span></li>
                            <li><i class="see"></i><span>已看诊</span></li>
                            <li><i class="late"></i><span>未到达</span></li>
                        </ul>
                        <div class="block">
                            <span class="icon">...</span>

                            <span v-for="item in waitNumItem" :class="getCss(item)">{{item}}</span>

                            <span class="icon">...</span>
                        </div>
                        <div class="num">
                            <span>当前就诊号：{{waitInfoItem.beingNum}}</span>
                            <span>等待人数：{{waitInfoItem.myNum - waitInfoItem.beingNum}}</span>
                            <span>预计等待时长：{{(waitInfoItem.myNum - waitInfoItem.beingNum)*5}}分钟</span>
                        </div>
                    </template>
                    
                </div>

            </div>
            
        </div>
    </div>
</template>
<script>
    import { getWaitInfos } from '../../api/api'
    let timeInterval;
    export default {
        data() {
            return {
                unLoaded: true,
                loadingText: '数据加载中...',
                infoBomb: false,
                infoNotice: "",
                waitInfoItem: undefined,

                waitNumItem: [],
                noData: false
            };
        },
        created: function () {
            this.getWaitInfo();
        },
        methods: {
            getWaitInfo: function(){
                getWaitInfos({
                    cardId: JSON.parse(localStorage.getItem('userInfo')).cardId
                }).then(res => {
                    if(res.code == 10000){
                        this.waitInfoItem = res.data;
                        if(this.waitInfoItem.length==0){
                            this.infoNotice = "无挂号记录";
                            this.infoBomb = true;
                            this.noData = true;
                            return;
                        }
                        this.drawTable();
                    }else {
                        if (this.filterASCII(res.msg)) {
                            this.infoNotice = this.filterASCII(res.msg);
                        } else {
                            this.infoNotice = res.msg || "暂无候诊信息";
                        }
                        this.infoBomb = true;
                    }
                    this.unLoaded = false
                })

            },
            drawTable: function(){
                let beingNum = this.waitInfoItem.beingNum;
                let myNum = this.waitInfoItem.myNum;
                let totalNum = this.waitInfoItem.totalNum;
                
                let num = beingNum;
                for(let m=0; m < myNum-beingNum && m < 6; m++){
                    if(m==5 && num < myNum-1){
                        num = "...";
                    }
                    this.waitNumItem.push(num);
                    num ++;
                }
                
                this.waitNumItem.push(myNum);

                if(myNum-beingNum < 6){
                    let my = myNum + 1;
                    for(let y=0; y < 6 - (myNum-beingNum) && my <= totalNum; y++){
                        this.waitNumItem.push(my++);
                    }
                }
            },
            getCss: function(item){
                let beingNum = this.waitInfoItem.beingNum;
                let myNum = this.waitInfoItem.myNum;
                
                if(item == myNum){
                    return "me"
                }else if(item == beingNum){
                    return "ing"
                }else if(item == "..."){
                    return "icon"
                }else{
                    return "wait"
                }
            },
            hideBox: function () {
                this.infoBomb = false;
                this.$router.push({
                    name: 'home'
                })
            }
        },
        destroyed: function () {
            clearInterval(timeInterval);
        }
    };
</script>
<style scoped>
    .resultDetail{
        padding:40px;
        height: 610px;
        padding-top: 90px;
        box-sizing: border-box;
    }
    .info{float: left; width: 380px; height: 380px; border-right: #ccc dotted 1px }
    .graphic{float:right; width: 660px; height: 380px}

    .info li{display: flex; padding:10px 0; font-size: 22px; align-items: center;}
    .info li span{width:110px; height: 24px; line-height: 24px; display: block; text-align: justify; margin-right: 10px; overflow: hidden;}
    .info li span:after {
      content:'';
      width: 100%;
      display: inline-block;
      overflow: hidden;
      height: 0;
    }
    .info li strong{font-weight: normal;}
    .info ul.states{margin-top: 50px}
    .info ul.states strong{color: #f30; font-size: 28px}
    
    .graphic .tag{padding-bottom: 25px; padding-top: 10px; border-bottom: #ccc 1px solid; display: flex;}
    .graphic .tag li{display: block; flex: 1; font-size: 18px;}
    .graphic .tag li i,
    .graphic .tag li span{display: inline-block; vertical-align: middle;}
    .graphic .tag li i{height: 18px; width:18px; border-radius: 3px; margin-right:10px; box-sizing: border-box;}
    .graphic .block{padding:100px 0; text-align: center;}
    .graphic .block span{display: inline-block; font-size: 24px; width:50px; height: 50px; line-height: 50px; text-align: center; color:#fff; margin:0 10px; border-radius: 3px}
    .graphic .block span.icon{color: #666; width:30px; position: relative; top:-5px;}
    .graphic .num{display: flex;}
    .graphic .num span{flex: 1; font-size: 20px}

    .me{background-color: #E54145}
    .ing{background-color: #F39700}
    .wait{background-color: #0595E7}
    .see{background-color: #fff; border:#ddd 1px solid;}
    .late{background-color: #ccc;}

    .graphic .block .ing{width:60px; height: 60px; line-height: 60px}

    .noBegin{margin-top: 80px; text-align: center;}
    .noBegin .msg{color: #FD2727; font-size: 50px; margin-top: 20px}
    .noBegin .text{font-size: 22px; margin-top: 20px}
</style>