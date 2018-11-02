<template>
    <div class="registerWrap" v-cloak>
        <Loading v-if="isLoading" :title="loadingText"></Loading>
        <div class="readCardBox">

            <!-- 就诊卡操作示意图 -->
            <div v-if="cardType=='1'">
                <p class="step">
                    请在机器右侧刷您的就诊卡
                    <span>(磁条朝左)</span>
                </p>
                <p class="animationImg">
                    <img src="../../static/img/base-right.png" class="solid-card-img">
                    <img src="../../static/img/solid-animation-right.png" class="card-right">
                </p>
            </div>

            <!-- 电子就诊卡二维码操作示意图 -->
            <div v-if="cardType=='2'">
                <div>
                    <p class="step">请出示您的电子就诊卡二维码</p>
                    <p class="step-assistant">
                        <span>打开"***医院公众号"</span>
                        <img src="../../static/img/next.png" class="next-img">
                        <span>点开"我的"</span>
                        <img src="../../static/img/next.png" class="next-img">
                        <span>点击就诊卡右上角的二维码</span>
                    </p>
                </div>
                <p class="animationImg">
                    <img src="../../static/img/ele-base.png" class="solid-card-img">
                    <img src="../../static/img/ele-card-animation.png" class="electronics-card">
                </p>
            </div>

            <!-- 二代社保卡操作示意图 -->
            <div v-if="cardType=='5'">
                <p class="step">
                    请插入您的二代社保卡
                    <span>(磁条朝下)</span>
                </p>
                <p class="animationImg">
                    <img src="../../static/img/base-insert.png" class="solid-card-img">
                    <img src="../../static/img/sociel-animation.png" class="healthy-insert">
                </p>
            </div>

        </div>

    </div>
</template>

<script>
    /**
     * cardType 对应接口文档"卡类型枚举值"
     */
    import {queryCardInfo} from '../../components_regional/api/common';
    import enumerate from '../../js/enumerate';

    export default {
        data() {
            return {
                isLoading: false,
                loadingText: '正在加载数据...',

                cardType: undefined,
                timing: undefined,
                timer: undefined,
                orderInfo: undefined,

                isFree: true    // 当前工作状态是否空闲
            }
        },
        created: function () {
            if (this.$agent() === 'Windows') {
                //window:打开扫码设备
                window.external.openScanner();
                this.timer = setInterval(() => {
                    //window:打开扫码设备
                    window.external.openScanner();
                }, 15000)
            }

        },
        activated: function () {
            // 语音:请按照图示方式刷卡
            this.player.src = this.audioSrc[9];
            if (this.$agent() == "Android") {
                this.player.play();
            }
            // 路由参数
            let routeParams = this.$route.params;
            this.cardType = routeParams.cardType;
            // 更改title
            this.$route.meta.title = enumerate.cardType[this.cardType];
            this.orderInfo = JSON.parse(localStorage.getItem('currentOrderInfo'));

            // 监听刷卡动作
            if (!this.isPc() && this.cardType != '6') {

                this.timing = setInterval(() => {
                    // 安卓方法: 获取其他输入设备的值,并在查询失败后继续聚焦文本框
                    let code = this.$machineApi.watchInput();

                    if (code && this.isFree) {
                        // 普通就诊卡查询接口
                        this.checkCardDetail('cardId', this.extractCard(code));
                        code = '';
                    }

                    this.dsBridge.call('setF', {msg: 'setF'});

                }, 1000)
            }

            // 进入相应的业务
            // this.isLoading = true;
            // setTimeout(() => {
            //     this.isLoading = false;
            //     let user = {cardId: "123", IDCard: "", UserIdKey: "00024147", cardName: "测试", balance: "0.00"}
            //     localStorage.setItem('userInfo', JSON.stringify(user));
            //     this.setNextRouterName();   // 去向
            // }, 1600)
        },
        methods: {

            // 通用查询就诊人信息函数 cardId 就诊卡卡号 url 通用接口或居民健康卡专用查询接口
            checkCardDetail: function (key, cardId) {
                this.isFree = false;
                this.isLoading = true;
                this.loadingText = '查询中，请稍候...';

                let params = {[key]: cardId};
                queryCardInfo(params).then(res => {

                    this.$machineApi.clearWatchInput(); // 安卓方法: 取消监听

                    // 执行查询后禁用查询，等查询完成后再重置查询状态
                    this.isFree = true;

                    if (res.code == 10000) {
                        // 播放查询成功语音
                        this.player.src = this.audioSrc[5];
                        if (this.$agent() == "Android") {
                            this.player.play();
                        }
                        this.loadingText = '查询成功，请稍候...';

                        // 存储就诊卡信息
                        localStorage.setItem('userInfo', JSON.stringify(res.data));

                        // 进入相应的业务
                        setTimeout(() => {
                            this.isLoading = false;
                            this.setNextRouterName();   // 去向
                        }, 1600)

                    } else {
                        // 播放查询失败语音
                        this.player.src = this.audioSrc[6];
                        if (this.$agent() == "Android") {
                            this.player.play();
                        }

                        this.loadingText = '查询失败,请重新录入就诊人信息';

                        setTimeout(() => {
                            this.isLoading = false;
                            this.$machineApi.setInputFocus(); // 安卓方法: 设置焦点
                        }, 2000)
                    }
                })
            },
            getScanId: function (str) {
                const carId = str;
                this.checkCardDetail('cardId', carId);
            },
            test(str) {
                alert(str);
            },
            // 截取刷卡卡号
            extractCard: function (str) {

                //根据刷卡后含有的字符串判断就诊卡类型
                if (str.search(/=/) > 0) {
                    //四川省人民医院
                    if (str.split('=')[0].search(/:/) < 0) {
                        return str.split('=')[0].replace(/[^0-9]/ig, "");
                    } else {
                        //社保卡
                        return str.split('=')[1].replace(/[^0-9]/ig, "");
                    }
                } else {
                    //其他类型就诊卡
                    return str.split('?')[0].replace(/[^0-9]/ig, "");
                }
            },

            //路由控制函数,根据参数确定录入信息后需要跳转的路由
            setNextRouterName: function () {
                let serviceType = this.orderInfo.serviceType;
                switch (serviceType) {
                    case "2":
                        this.$router.replace({
                            name: 'pay-mode'
                        });
                        break;
                }
            },
        },
        destroyed: function () {
            clearInterval(this.timing);
            clearInterval(this.timer);
        }
    }
    var wpfObj = null;
    function IniWPFObjectOfHtml(obj) {
        wpfObj = obj;
    }

</script>

<style lang="less" src='../static/less/card.less' scoped></style>