<template :ready>
    <div class="content" v-cloak>
        <div class="title">
            <p>请选择您的就诊卡类型</p>
        </div>
        <div class="card-type-list">
            <div class="item">
                <div class="card-type-item" v-for="(item,index) in showTypeList" @click="selectCardType(item.type)">
                    <img :src="item.icon" alt="" class="card-type-icon">
                    <p>{{item.name}}</p>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
    export default {
        data() {
            return {
                cardTypeList: [
                    {
                        icon: window.staticUrl + '/src/static/img/solid-card.png',
                        name: '实体就诊卡',
                        type: 'solid',
                        id: 1
                    },
                    {
                        icon: window.staticUrl + '/src/static/img/electronics-card.png',
                        name: '电子就诊卡',
                        type: 'electronics',
                        id: 2
                    },
                    {
                        icon: window.staticUrl + '/src/static/img/electronics-healthy-card.png',
                        name: '电子居民健康卡',
                        type: 'electronicsHealthy',
                        id: 3
                    },
                    {
                        icon: window.staticUrl + '/src/static/img/resident-healthy-card.png',
                        name: '居民健康卡',
                        type: 'healthy',
                        id: 4
                    },
                    {
                        icon: window.staticUrl + '/src/static/img/social-security-card.png',
                        name: '社保卡',
                        type: 'social',
                        id: 5
                    },
                    {
                        icon: window.staticUrl + '/src/static/img/keyboard.png',
                        name: '输入就诊卡号',
                        type: 'input',
                        id: 6
                    },
                    {
                        icon: window.staticUrl + '/src/static/img/ccb-card.png',
                        name: '银行卡',
                        type: 'bank',
                        id: 7
                    },
                    {
                        icon: window.staticUrl + '/src/static/img/ccb-card.png',
                        name: '人脸识别',
                        type: 'face',
                        id: 9
                    },
                    {
                        icon: window.staticUrl + '/src/static/img/keyboard.png',
                        name: '输入住院号',
                        type: 'inputTreatNo',
                        id: 10
                    },
                ],
                cardService: '',    //后台配置的可使用的就诊卡类型
                showTypeList: [],   //显示的刷卡类型
                unLoaded: false,
                loadingText: ""

            };
        },
        created: function () {
            this.fromName = this.$route.params.fromName;

            // 清除登录的用户信息
            localStorage.removeItem('userInfo');

            // 请选择就诊卡类型语音

            this.$audioPlay(22);

            // 就诊卡类型列表数据
            this.cardService = this.getCardType();

            if (this.cardService) {
                this.showTypeList = this.showList(this.cardService);
            }

        },
        methods: {

            //根据服务功能名称显示后台已开启的刷卡类型
            getCardType: function () {
                const serviceName = this.$route.params.fromName;
                var _hospitalInfo = JSON.parse(localStorage.getItem('hospitalInfo'));
                switch (serviceName) {
                    case 'register':
                        return _hospitalInfo.serviceConf.registration.KaLeiXing;
                        break;
                    case 'reversionNumList':
                        return _hospitalInfo.serviceConf.fetch.KaLeiXing;
                        break;
                    case 'outpatient':
                        return _hospitalInfo.serviceConf.outpatient.KaLeiXing;
                        break;
                    case 'hospitalizationDetail':
                        return _hospitalInfo.serviceConf.inpatient.KaLeiXing;
                        break;
                    case 'inpatientList':
                        return _hospitalInfo.serviceConf.list.KaLeiXing;
                        break;
                    case 'printTicket':
                        return _hospitalInfo.serviceConf.receipt.KaLeiXing;
                        break;
                    case 'queryOrder':
                        return _hospitalInfo.serviceConf.order.KaLeiXing;
                        break;
                    case 'sign':
                        return _hospitalInfo.serviceConf.sign.KaLeiXing;
                        break;
                    case 'waiting':
                        return _hospitalInfo.serviceConf.waiting.KaLeiXing;
                        break;
                    case 'typeb':
                        return _hospitalInfo.serviceConf.typeb.KaLeiXing;
                }
            },

            //根据id判断可用的刷卡类型
            showList: function (array) {
                let arr = [];
                if (array.length > 0) {
                    for (let i = 0, len = array.length; i < len; i++) {
                        this.cardTypeList.forEach((element) => {
                            if (array[i] == element.id) {
                                arr.push(element);
                            }
                        })
                    }
                    if (arr.length === 1 && arr[0].type === 'inputTreatNo') { //选择住院号查询
                        this.$router.replace({
                            name: 'queryOrder',
                            params: {
                                method: arr[0].type
                            }
                        })
                    }
                    else if (arr.length === 1) {
                        this.$router.replace({
                            name: 'readCard',
                            params: {
                                type: arr[0].type,
                            }
                        })
                    } else {
                        return arr;
                    }
                } else {
                    return arr;
                }
            },

            // 选择就诊卡类型
            selectCardType: function (type) {
                if (type === 'inputTreatNo') { //选择就诊卡查询
                    this.$router.push({
                        name: 'queryOrder',
                        params: {
                            method: type
                        }
                    })
                } else {
                    this.$router.push({
                        name: 'readCard',
                        params: {
                            type: type
                        }
                    })
                }

            }
        },
        destroyed: function () {

        }

    };
</script>

<style scoped>
    .card-type-list {
        width: 100%;
        height: 580px;
        overflow: hidden;
        display: flex;
        align-items: center;
    }

    .item {
        flex: 1;
        flex-direction: row;
        flex-wrap: wrap;
        justify-content: center;
    }

    .card-type-item {
        width: 530px;
        height: 160px;
        border: #0062A2 1px solid;
        box-sizing: border-box;
        background: url('../../static/img/card-type-bg.png') #0399E0 no-repeat left bottom;
        display: inline-flex;
        align-items: center;
        justify-content: flex-start;
        border-radius: 8px;
        color: #fff;
        margin: 35px 12px 35px 40px;
        cursor: pointer;
    }

    .card-type-item:nth-child(even) {
        margin-right: 0
    }

    .card-type-icon {
        width: 60px;
        height: 47px;
        margin-left: 50px;
    }

    .card-type-item p {
        width: 420px;
        font-size: 42px;
        margin-left: 30px;
        text-align: left;
    }
</style>