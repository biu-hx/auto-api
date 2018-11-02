<template>
    <div v-cloak>
        <Loading v-if="isLoading" :title="loadingText"></Loading>
        <div id="header">
            <img :src="projectInfo.logoAll">
            <p class="time" v-if="clock" v-html="clock"></p>
        </div>
        <div id="main">
            <div class="notice">{{machineState.noticeText}}</div>
            <div class="nav">
                <ul>
                    <li v-for="item in navArray" @click="routerPush(item.path)">
                        <i :class="item.icon"></i>
                        <strong>{{item.name}}</strong>
                    </li>
                </ul>
            </div>
        </div>
        <div id="footer">由义幻医疗科技有限公司提供技术支持 NO:{{projectInfo.number}}</div>
    </div>
</template>
<script>
    export default {
        data() {
            return {
                isLoading: false,
                loadingText: '正在加载数据...',

                clock: "" ,

                // 导航菜单
                navArray:[
                    {
                        name:"视频问诊",
                        path:"consultHospital",
                        icon:"nav_icon_consult"
                    },
                    {
                        name:"科教视频",
                        path:"scieduVideo",
                        icon:"nav_icon_teach"
                    },
                    {
                        name:"预约挂号",
                        path:"regHospital",
                        icon:"nav_icon_register"
                    },
                    {
                        name:"订单查询",
                        path:"orderQuery",
                        icon:"nav_icon_order"
                    }
                ],
                // 项目信息
                projectInfo:{},
                // 机器基本信息
                machineInfo:{
                    macAddress: '',     // MAC地址
                    version: '',        // 软件版本号
                },
                // 机器状态
                machineState:{
                    state: true,                              // 设备状态
                    noticeText: '欢迎使用自助医疗设备！',     // 设备状态提醒
                }
            }
        },
        created: function () {
            // 时钟
            this.clock = this.$getTime();
            setInterval(() => {
                this.clock = this.$getTime();
            }, 1000)

            // 获取设备MAC和版本号
            let _getMacAddress = this.$getMacAddress();
            this.machineInfo={
                macAddress:_getMacAddress.mac,
                version:_getMacAddress.version
            }

            // 判断是否有缓存，若存在缓存则先显示缓存内容，再通过接口更新缓存及界面
            let local_projectInfo = localStorage.getItem('hospitalInfo');
            if (local_projectInfo) {
                local_projectInfo = JSON.parse(local_projectInfo);
                this.projectInfo = local_projectInfo;
            } 

            // 获取设备的项目信息
            this.getMachineInfo(this.machineInfo.macAddress);

            // 播放选择业务音频
            this.player.src = this.audioSrc[18];
            if(this.$agent()=="Android"){
                this.player.play();
            }
            // localStorage.removeItem('userInfo');

        },
        methods: {
            routerPush(path){
                this.$router.push({path: 'service/'+path});
            },
            getMachineInfo: function(macAddress) {
                this.isLoading = true;
                this.loadingText = "设备信息加载中...";

                this.$getMachineInfo('/equip/number', macAddress, (res)=>{
                    this.isLoading = false;
                    if (res.code === 10000) {
                        this.projectInfo = res.data;
                        localStorage.setItem('hospitalInfo', JSON.stringify(this.projectInfo));

                        // 开启定时检测任务
                        this.timeTask = setInterval(() => {
                            this.checkNetwork(this.machineInfo.macAddress);
                            this.checkPrintState();
                        }, 1000);

                    }else if(res.data.code === 20001) {
                        // 设备异常
                        
                    }
                })
            },

            //访问后台接口，报告设备网络情况
            checkNetwork: function (macAddress) {
                this.$getMachineInfo('/equip/network', macAddress, (res)=>{
                    if(res.code == 10000){
                        clearInterval(this.timeTask);
                    }
                    if(!res){
                        this.dealError('网络错误，请网络正常后重试!');
                    }
                })
            },

            // 检测打印机工作状态
            checkPrintState: function(){
                if (!this.isPc()) {
                    let status = this.$machineApi.getMachine_printStatus();
                    if (status == '0') {
                        //正常
                        this.machineState.state = true;
                    } else if (status == '-1') {
                        //缺纸
                        this.machineState.state = false;
                        this.machineState.noticeText = '打印机缺纸，请联系管理员换纸！';
                    } else if (status == '-2') {
                        //异常
                        this.machineState.state = false;
                        this.machineState.noticeText = '该设备打印机异常，请联系管理员进行维修！'
                    }
                } else {
                    this.machineState.noticeText = '欢迎使用自助医疗设备！';
                }
            }

        },
        destroyed: function () {
            if(this.timeTask) {
                clearInterval(this.timeTask);
            }
        }
    }
</script>

<style lang="less" src='../static/less/regional_home.less' scoped></style>