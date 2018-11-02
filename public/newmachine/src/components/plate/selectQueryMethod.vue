<template :ready>
    <div v-cloak>
        <div class="content">
            <div class="title">
                <p>请选择查询方式</p>
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
    </div>
</template>
<script>
    export default {
        data() {
            return {
                cardTypeList: [{
                        icon: window.staticUrl+'/src/static/img/by-scan-code.png',
                        name: '扫描订单二维码',
                        type: 'scanQrcode',
                        id:1
                    }, {
                        icon: window.staticUrl+'/src/static/img/keyboard.png',
                        name: '输入订单编号',
                        type: 'inputOrderSn',
                        id:2
                    },
                    {
                        icon: window.staticUrl+'/src/static/img/keyboard.png',
                        name: '输入住院号',
                        type: 'inputTreatNo',
                        id:3
                    },
                    {
                        icon: window.staticUrl+'/src/static/img/solid-card.png',
                        name: '就诊卡',
                        type: 'card',
                        id:4
                    }
                ],
                cardService: '',//后台配置的可使用的就诊卡类型
                showTypeList: [],//显示的刷卡类型
            }
        },
        created: function () {
            localStorage.removeItem('userInfo');
            //请选择查询方式语音

            this.$audioPlay(24);

            this.cardService = this.getCardType();
            this.showTypeList=this.showList(this.cardService);
        },
        methods: {
            //选择订单查询方式
            selectCardType: function (type) {
                if (type != 'card') { //选择就诊卡查询
                    this.$router.push({
                        name:'queryOrder',
                        params:{
                            method:type
                        }
                    })
                } else {
                    this.$router.push({
                        name: 'istinguishCard'
                    })
                }
            },
            //根据服务功能名称显示后台已开启的订单查询类型
            getCardType: function () {
                const serviceName = this.$route.params.fromName;
                switch (serviceName) {
                    case 'printTicket':
                        return JSON.parse(localStorage.getItem('hospitalInfo')).serviceConf.receipt.ChaXunFangShi;
                        break;
                    case 'queryOrder':
                        return JSON.parse(localStorage.getItem('hospitalInfo')).serviceConf.order.ChaXunFangShi;
                        break;
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
                    if (arr.length === 1) {
                        if (type != 'card') { //选择就诊卡查询
                            this.$router.replace({
                                name:'queryOrder',
                                params:{
                                    method:arr[0],type
                                }
                            })
                        } else {
                            this.$router.replace({
                                name: 'istinguishCard'
                            })
                        }
                    } else {
                        return arr;
                    }
                } else {
                    return arr;
                }
            },
            destroyed() {
               
            }
        }
    }
</script>
<style scoped>
    .card-type-list {
        width: 100%;
        height: 590px;
        overflow: hidden;
        display: flex;
        align-items: center;
    }

    .item{flex: 1; display: flex; flex-direction: row; flex-wrap: wrap; justify-content:flex-start;}

    .card-type-item {
        width: 530px;
        height: 160px;
        border:#0062A2 1px solid;
        box-sizing: border-box;
        background: url('../../static/img/card-type-bg.png') #0399E0 no-repeat left bottom;
        display: inline-flex;
        align-items: center;
        justify-content: flex-start;
        border-radius: 8px;
        font-size: 42px;
        color: #fff;
        margin: 35px 20px 35px 37px;
        cursor: pointer;
    }

    .card-type-item:nth-child(even){
        margin-right: 0
    }

    .card-type-icon {
        width: 60px;
        height: 47px;
        margin-right: 30px;
        margin-left: 50px;
    }
</style>