<template :ready>
    <div v-cloak>
        <Loading v-if="unLoaded" :title="loadingText"></Loading>
        <Dialog v-if="infoBomb" :text="infoNotice" :buttonconfig="['返回','重试']" @hideBox="hideBox"></Dialog>
        <div class="content">
            <div class="title">
                <p>请选择院区</p>
            </div>
            <div class="districts">
                <div class="item" 
                    v-if="districtList" 
                    v-for="item in districtList" 
                    @click="selectDistrict(item.districtId,item.name)">
                    <p>
                        <strong>{{item.name}}</strong>
                        <span>地址：{{item.address}}</span>
                    </p>
                </div>
            </div>
            <!-- <div class="districts" v-if="districtList && districtList.length>0">
                <div class="item" v-for="(item,index) in districtList" @click="selectDoctor(item.deptId)">
                    <p>{{item.name}}</p>
                </div>
            </div> -->
        </div>
    </div>
</template>
<script>
    export default {
        data() {
            return {
                unLoaded: false,
                loadingText: '数据加载中...',
                infoBomb: false,
                infoNotice: "",
                
                districtList: undefined
            };
        },
        created: function () {
            localStorage.removeItem('districtInfo');
            this.getDistrict();
        },
        methods: {
            //重新查询
            reSearch: function () {
                this.getDistrict();
            },
            //选择科室进入选择医生页面
            selectDistrict: function (districtId, districtName, type) {
                try {
                    localStorage.setItem('districtInfo', JSON.stringify({districtId:districtId, districtName:districtName}));
                }catch(err) {

                }
                
                if(type && type=="replace"){
                    this.$router.replace({
                        name: "selectDept"
                    });
                }else{
                    this.$router.push({
                        name: "selectDept"
                    });
                }
                
            },
            
            //隐藏弹框
            // hideBox: function () {
            //     this.infoBomb = false;
            //     if(this.infoNotice != ""){
            //         history.back(-2);
            //     }
            // },
            
            //获取院区列表
            getDistrict: function () {
                this.$http.get(this.publicUrl + '/hospital/district', {
                    params: {timeStr: new Date().getTime()},
                    headers: this.config(JSON.parse(localStorage.getItem('hospitalInfo')).number),
                    timeout: 10000
                }).then((res) => {
                    this.unLoaded = false;
                    if (res.data.code == 10000) {
                        this.districtList = res.data.data;
                        if(this.districtList.length < 2){
                            let item = this.districtList[0];
                            this.selectDistrict(item.districtId, item.name, "replace");
                        }
                    } else {
                        if (this.filterASCII(res.data.msg)) {
                            this.infoNotice = this.filterASCII(res.data.msg);
                        } else {
                            this.infoNotice = res.data.msg || "暂无院区";
                        }
                        this.infoBomb = true;
                    }
                }).catch((err) => {
                    this.unLoaded = false;

                    this.$audioPlay(21);

                    this.dealError();
                })
            }
        
        }
    };
</script>
<style scoped>
    
    .districts {
        width: 1153px;
        height: 600px;
        overflow: hidden;
        margin-left: 35px;
        text-align: center
    }
    .districts .item {
        width: 48%;
        height: 120px;
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
        padding:0 40px;
        margin:0 auto;
        margin-top: 55px;
        text-align: left
    }
    .districts .item p{flex: 1}
    .districts .item strong,
    .districts .item span{display: block;}
    .districts .item strong{
        font-size: 36px;
        font-weight: 400
    }
    .districts .item span{
        font-size: 28px;
        margin-top: 8px;
        color: rgba(255,255,255,.9);
    }
</style>