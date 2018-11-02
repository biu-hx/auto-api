<template>
    <div>
    <router-view>
    </router-view>
    
    <div v-if="closetimeNum" class="closeMachineTip">
        系统将在{{closetimeNum}}秒后关闭
    </div>
    </div>
</template>
<script>
    export default {
        data() {
            return {
                agent: this.$agent(),
                closeStatus:'',
                version: '',//软件版本号
                macAddress: '',
                hospitalInfo: {},
                closetimeNum: null,
                closeMachineTime: '18:00:00'    //关机时间
            }
        },
        created: function () {
            /**
             * 双系统兼容优化
             */
            let machinaMac = this.$getMacAddress();
            if(machinaMac){
                this.macAddress = machinaMac.mac;
                this.version = machinaMac.version;
                localStorage.setItem('version', this.version);
            }
            //调用医院基本配置信息函数
            this.gethospitalInfo(this.macAddress);



            
        },
        methods: {
            //获取医院基本配置信息
            gethospitalInfo: function (macAddress) {
                this.$getMachineInfo('/equip/number', macAddress, (res)=>{
                    if (res.code === 10000) {
                        this.hospitalInfo = res.data;
                        this.closeStatus=JSON.parse(this.hospitalInfo.close_config).status;
                        this.closeMachineTime=JSON.parse(this.hospitalInfo.close_config).end;
                        if(this.closeStatus==='1' && this.agent == "Windows" && closeActiveXObject){
                            // 时间戳
                            let date = new Date();
                            let year = date.getFullYear();
                            let month = ((date.getMonth() + 1) < 10) ? ('0' + (date.getMonth() + 1)) : (date.getMonth() + 1);
                            let date1 = date.getDate() < 10 ? ('0' + date.getDate()) : date.getDate();
                            let day = year + '/' + month + '/' + date1;
                            let curTime = Date.parse(date) / 1000;
                            let closeTime = (new Date(String(day) + " " + this.closeMachineTime)).getTime() / 1000;

                            this.closeWindow(curTime, closeTime);
                        }
                    }
                })
            },

            //自动关机
            closeWindow: function (currentTime, closeTime) {
                let countDown = (closeTime - currentTime) > 0 ? closeTime - currentTime : false;
                if (countDown) {
                    let timeInterval = setInterval(() => {
                        countDown--;
                        if (countDown === 0) {

                            this.closetimeNum = null;
                            
                            clearInterval(timeInterval);

                            // console.log('关闭 Windows');
                            
                            closeActiveXObject.Run('Shutdown -s -t 0'); 
                            closeActiveXObject = null; 

                            // closeActiveXObject.ShutdownWindows();
                            
                        }else if(countDown<=600){
                            this.closetimeNum = countDown;
                        }
                    }, 1000)

                }
            },
        },
        watch: {
            "$route": function (to, from) {

            }
        }
    }

</script>
<style>
    @import url('./src/static/css/public.css');
    .closeMachineTip{width:320px; padding:20px; position: absolute; z-index: 888; left: 50%; top:125px; margin-left: -160px; text-align: center; background-color: rgba(255,51,0, .8); color: #fff; font-size: 28px; border-radius: 5px }
</style>

