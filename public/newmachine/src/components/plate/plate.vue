<template :ready>
    <div class="frame" v-cloak @click="operationTimeReset">
        <header>
            <div class="hospital-name">
                <div class="hospital-logo-name">
                    <img :src="hospitalInfo.logoAll" class="project-logo">
                </div>
                <div class="hospital-investorLogo">
                    <img :src="hospitalInfo.bankFullIcon" class="ad-logo">
                </div>
            </div>
            <div class="base-show">
                <div class="current-time" v-html="time"></div>
                <div class="welcome-user" v-if="isLogin">
                    <img src="../../static/img/user.png" alt="" class="user-icon">
                    <p class="username">你好，{{userInfo.cardName}}</p>
                </div>
            </div>
        </header>
        <div id="plate">
            <router-view></router-view>
        </div>
        <footer>
            <div class="system-box">
                <div class="system-info">
                    <p>终端编号：{{hospitalInfo.number}}</p>
                    <p>系统版本：{{version}}</p>
                </div>
                <div class="contact-service">
                    <img src="../../static/img/service-icon.png" alt="" class="call-logo">
                    <div class="service-box">
                        <p class="service-phone">{{hospitalInfo.servicePhone}}</p>
                        <p class="service-title">客服电话</p>
                    </div>
                </div>
            </div>
            <div class="operation-box">
                <div v-if="isShowBackButton" class="back-btn" @click="back">上一步</div>
                <div class="index-btn" @click="backIndex">首页</div>
            </div>
        </footer>
    </div>
</template>
<script>
    export default {
        data() {
            return {
                time: '',
                hospitalInfo: {},
                servicePhone: '',
                userInfo: {},
                isLogin: false,
                isPayDetail: false,
                noOperationTime: 180, //页面无操作时，自动返回首页
                isPringtPage: false, //是否为打印小票页面
                version:'',
                isShowBackButton: true
            }
        },
        created: function () {
            this.showTime();
            this.hospitalInfo = JSON.parse(localStorage.getItem('hospitalInfo'));
            if (localStorage.getItem('userInfo')) {
                this.userInfo = JSON.parse(localStorage.getItem('userInfo'));
                this.isLogin = true;
            } else {
                this.isLogin = false;
            }
            if(localStorage.getItem('version')){
                this.version=localStorage.getItem('version');
            }else{
                this.version='Test 1.0';
            }

            //登录状态监听
            this.timer = setInterval(() => {
                this.showTime();
                this.resetRouteToHome();
                if (localStorage.getItem('userInfo')) {
                    this.userInfo = JSON.parse(localStorage.getItem('userInfo'));
                    this.isLogin = true;
                } else {
                    this.isLogin = false;
                }
            }, 1000)

        },
        methods: {
            //操作时间重置
            operationTimeReset: function () {
                this.noOperationTime = 180;
                localStorage.setItem('getfocus', 'true');
            },

            //页面无操作反馈自动执行返回首页函数
            resetRouteToHome: function () {
                if (this.noOperationTime > 0) {
                    this.noOperationTime--;
                } else {
                    this.$router.push({
                        name: 'home'
                    });
                }
            },

            //生成当前时间
            showTime: function () {
                let year = new Date().getFullYear();
                let month = ((new Date().getMonth() + 1) < 10) ? ('0' + (new Date().getMonth() + 1)) : (new Date().getMonth() +
                    1);
                let date = new Date().getDate() < 10 ? ('0' + new Date().getDate()) : new Date().getDate();
                let day = year + '年' + month + '月' + date + '日';
                let week = this.translateWeek(new Date().getDay());
                let hours = (new Date().getHours() < 10) ? ('0' + new Date().getHours()) : (new Date().getHours());
                let minutes = (new Date().getMinutes() < 10) ? ('0' + new Date().getMinutes()) : (new Date().getMinutes());
                this.time = day + '&nbsp;&nbsp;' + week + '&nbsp;&nbsp;' + hours + ':' + minutes;
            },

            //将数字星期转换为中文星期函数
            translateWeek: function (w) {
                switch (w) {
                    case 0:
                        return '星期日';
                        break;
                    case 1:
                        return '星期一';
                        break;
                    case 2:
                        return '星期二';
                        break;
                    case 3:
                        return '星期三';
                        break;
                    case 4:
                        return '星期四';
                        break;
                    case 5:
                        return '星期五';
                        break;
                    case 6:
                        return '星期六';
                        break;
                    default:
                        break;
                }
            },

            //返回上一步
            back: function () {
                history.back();
            },

            //返回首页
            backIndex: function () {
                // this.dsBridge.call("initwebview", {msg:"initwebview"});
                this.$router.push({
                    name: 'home'
                })
            }
        },

        destroyed: function () {
            clearInterval(this.timer);
        },

        watch: {
            '$route': function(to, from) {

                let toName = to.name;
                let fromName = from.name;
                let thisRouteName = this.$route.name;

                let agent = this.$agent();

                console.log(toName, fromName, thisRouteName)
                
                // "上一页"按钮显示/隐藏, "小票打印页"隐藏此按钮
                if(thisRouteName == "printPage" || thisRouteName == "printPage_win"){
                    this.isShowBackButton = false
                }else{
                    this.isShowBackButton = true
                }

                // 当进入"获取就诊卡页面"时生效
                if(toName=="readCard"){
                    // 清除登录的用户信息
                    localStorage.removeItem('userInfo');
                }

                // 当离开"获取就诊卡页面"时生效
                if(fromName=="readCard"){
                    // 手动关闭dsBridge.call("getDeviceInput")
                    
                }
            }
        }
    }
</script>
<style lang="less" src='../static/less/common.less'></style>
<style scoped>
    .frame {
        width: 1280px;
        margin: 0 auto;
        padding: 0;
        overflow: hidden;
    }

    header {
        width: 100%;
        height: 146px;
        background: url('../../static/img/header-bg.jpg')no-repeat;
        background-size: 100% 100%;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .hospital-name {
        width: 970px;
        height: 100%;
        display: flex;
        align-items: center;
        padding-bottom: 5px;
    }

    .hospital-logo-name {
        
    }
    .hospital-investorLogo{
        margin-left: 35px;
    }
    .project-logo{
        width: auto;
        height: 90px;
        margin-left: 20px;
    }
    .ad-logo {
        width: auto;
        height: 90px;
    }

    .zn-name {
        font-size: 34px;
        font-weight: bolder;
        text-shadow: 2px 2px 2px #fff;
    }

    .en-name {
        margin-top: 10px;
        font-size: 12px;
        text-shadow: 2px 2px 2px #fff;
    }

    .base-show {
        height: 100%;
        display: flex;
        flex-direction: row; 
        flex-wrap: wrap;
        align-items: center;
        justify-content:flex-end;
        margin-right: 10px;
        padding-right: 15px;
    }
    .current-time{color:rgba(255,255,255,.9); height: 40px; margin-top: 15px; font-size: 16px;}

    .welcome-user {
        height: auto;
        display: flex;
        font-size: 26px;
        color: rgba(255,255,255,.9);
        height: 80px;
    }

    .user-icon {
        width: 35px;
        height: 35px;
        border-radius: 50%;
        margin-right: 10px;
    }

    #plate {
        width: 100%;
        height: 801px;
        margin: 0;
        background: url('../../static/img/content-bg.jpg')no-repeat;
        background-size: 100% 100%;
        overflow: hidden;
    }

    footer {
        width: 100%;
        height: 77px;
        background: url('../../static/img/footer-bg.jpg')no-repeat;
        background-size: 100% 100%;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .system-box {
        width: 50%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: flex-start;
    }

    .system-info {
        height: 100%;
        font-size: 14px;
        color: #fff;
        margin-left: 20px;
    }

    .system-info p:first-child {
        margin-top: 15px;
    }

    .system-info p:last-child {
        margin-top: 10px;
    }

    .contact-service {
        height: 100%;
        margin-left: 70px;
        margin-top: 1px;
        display: flex;
        align-items: center;
        justify-content: flex-start;
    }

    .call-logo {
        width: auto;
        height: 50px;
        margin-right: 15px;
    }

    .service-box {
        height: 100%;
        overflow: hidden;
        font-size: 20px;
        color: #fff;
    }

    .service-phone {
        margin-top: 10px;
        font-weight: 700;
    }

    .service-title {
        letter-spacing: 20px;
    }

    .operation-box {
        width: 50%;
        height: 100%;
        display: flex;
        align-items: flex-start;
        justify-content: flex-end;
    }

    .back-btn {
        width: 140px;
        height: 53px;
        background: #2e7bba;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 26px;
        border-radius: 53px;
        color: #fff;
        cursor: pointer;
    }

    .index-btn {
        width: 140px;
        height: 53px;
        background: #2e7bba;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 26px;
        color: #fff;
        border-radius: 53px;
        margin: 0 40px 0 50px;
        cursor: pointer;
    }

</style>