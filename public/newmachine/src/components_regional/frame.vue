<template>
    <div class="videoCommon" v-cloak>
        <div id="header">
            <span>
                {{projectInfo.projectName}}
            </span>
            <div class="current-time" v-html="time"></div>
        </div>
        <div id="main">
            <div class="chooseHosital">
                <div>
                    <span class="choose_title">{{this.$route.meta.title}}
                        <img class="choose_img" src="./static/images/chooseHospital_img.png" alt="">
                    </span>
                </div>
                <div :class="checkbg ? 'chooseHosital_box else' : 'chooseHosital_box'">
                    <keep-alive include="consult-hospital">
                        <router-view></router-view>
                    </keep-alive>
                </div>
            </div>
        </div>
        <div id="footer">
            <span>由义幻医疗科技有限公司提供技术支持 NO:{{projectInfo.number}}</span>
            <div>
                <div class="backindex btnGradient" @click="backIndex">首页</div>
                <div class="back btnGradient" @click="back">上一步</div>
            </div>
        </div>
    </div>
</template>
<script>
    export default {
        data() {
            return {
                time: '',
                checkbg: false,
                // 项目信息
                projectInfo: undefined
            }
        },
        created: function () {
            this.time = this.$getTime();

            setInterval(() => {
                this.time = this.$getTime();
            }, 1000);

            this.checkbg = (this.$route.name == 'consult-hospital');

            // 判断是否有缓存，若存在缓存则先显示缓存内容，再通过接口更新缓存及界面
            let local_projectInfo = localStorage.getItem('hospitalInfo');
            if (local_projectInfo) {
                local_projectInfo = JSON.parse(local_projectInfo);
                this.projectInfo = local_projectInfo;
            } 
        },
        methods: {
            //返回上一步
            back: function () {
                history.back(-1);
            },
            //返回首页
            backIndex: function () {
                this.$router.push({
                    name: 'regional_home'
                })
            }
        },
        beforeRouteUpdate: function (to,from,next) {
            this.checkbg = (to.name == 'consult-hospital');
            next()
        },
        destroyed: function () {

        }
    }
</script>

<style lang="less" src='./static/less/chooseCommon.less' scoped></style>