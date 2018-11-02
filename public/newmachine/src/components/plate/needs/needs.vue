<template :ready>
    <div v-cloak>
        <Loading v-if="unLoaded" :title="loadingText"></Loading>
        <Dialog v-if="infoBomb" :text="infoNotice" :buttonconfig="['返回']" @hideBox="hideBox"></Dialog>
        <div class="content">
            <div class="title">
                <p>B超预约-注意事项</p>
            </div>
            <div class="caution-details-box">
                <div class="caution-details" v-for="item in fomartCheckCautionData">
                    <p>{{item}}；</p>
                </div>
            </div>
            <div class="spacial-tips caution-details">
                特别提醒：该检查一旦预约成功，概不退号、不换号、不退费，您是否同意预约？
            </div>
            <div class="no-record-btn is-agree">
                <div class="research-btn" @click="backHome">我不同意</div>
                <div class="research-btn" @click="getCheckTime">我同意</div>
            </div>
        </div>

    </div>
</template>

<script>
    import { getCheckCaution } from '../../api/api'
    export default {
        name: "needs",
        data(){
            return{
                unLoaded: true,
                loadingText: '数据加载中...',
                infoBomb: false,
                infoNotice: '',
                CheckCautionData:'',
                fomartCheckCautionData:[],
                myre:''
            }

        },
        created:function () {
            this.getCheckCaution();
        },
        methods:{
            //获取注意事项信息
            getCheckCaution:function () {
                getCheckCaution({
                    cardId: JSON.parse(localStorage.getItem('userInfo')).cardId
                }).then(res => {
                    if(res.code == 10000){
                        this.CheckCautionData = res.data;
                        this.fomartCheckCautionData = this.CheckCautionData.split("；");
                    }
                    this.unLoaded = false;
                })
            },
            hideBox: function () {
                this.infoBomb = false;
                this.backHome();
            },
            //回到首页
            backHome: function () {
                this.$router.push({
                    name: 'home'
                })
            },
            //跳转到 可预约时间页面
            getCheckTime:function () {
                this.$router.push({
                    name: 'getCheckTime'
                })
            }

        },
        destroyed:{}

    }
</script>

<style scoped>
    .caution-details{
        width: 100%;
        align-items: center;
        justify-content: space-between;
        font-size: 24px;
        color: #000;
        margin-left: 35px;
    }
    .caution-details p{
        margin-bottom: 26px;
        line-height: 34px;
    }
    .caution-details-box .caution-details:last-child {
        display: none;
    }
    .spacial-tips{
        color: red;
        margin-top: 10px;
    }
    .is-agree .research-btn{
        margin: 0 40px;
    }

</style>