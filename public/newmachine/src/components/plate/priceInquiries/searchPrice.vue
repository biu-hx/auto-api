<template>
    <div class="content" v-cloak>
        <Loading v-if="unLoaded" :title="loadingText"></Loading>
        <Dialog v-if="infoBomb" :text="infoNotice" :buttonconfig="['确定']" @hideBox="hideBox"></Dialog>
        <div class="title">
            <p>{{cardTitle}}</p>
        </div>
        <div class="demonstration-box">
            <div class="input-box">
                <div class="input">{{inputContent}}</div>
                <div class="confirm-btn" @click="confirmSearch">确认</div>
            </div>
            <div class="keyboard-box">
                <div class="keyboard-item" v-for="(item,index) in keyList" @click="selectKey(item,index)">{{item}}</div>
            </div>
        </div>
    </div>
</template>
<script>
    export default {
        data() {
            return {
                unLoaded: false,
                loadingText: "",
                infoBomb: false,
                infoNotice: "当前输入框内容均不能为空！",
                cardTitle: '',
                cardType: '',
                keyList: [1, 2, 3, 4, 5, 6, 7, 8, 9, 0, '删除', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K',
                    'L', 'M',
                    'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', '清空'
                ],
                curIndex: -1,
                inputContent: '',
                inputList: [],
                canSearch: true,
                byType: '',
                placeholderName: ''
            }
        },
        created: function () {
            this.nameControll();
        },
        methods: {
            //名称控制函数
            nameControll: function () {
                this.byType = this.$route.params.byType;
                if (this.byType == 'drugs') {
                    this.cardTitle = '请输入药品名称-搜索药品费物价表';
                    this.placeholderName = '请输入药品名称';
                } else {
                    this.cardTitle = '请输入项目名称-诊疗项目';
                    this.placeholderName = '请输入项目名称';
                }
            },
            //键盘按键
            selectKey: function (item, index) {
                this.curIndex = index;
                //退格键
                if (index === 10) {
                    this.inputList.pop();
                } else if (index === 37) {
                    //清除键
                    this.inputList = [];
                } else {
                    //其他正常按键
                    this.inputList.push(item);
                }
                this.inputContent = this.inputList.join('');
                this.timerOut = setTimeout(() => {
                    this.curIndex = -1;
                }, 300)
            },
            //输入内容时确定搜索
            confirmSearch: function () {
                if (this.inputContent != '') {
                    if (this.byType == 'drugs') {
                        this.searchPrice(this.inputContent, '/list/drug');
                    } else {
                        this.searchPrice(this.inputContent, '/list/diagnosis');
                    }
                    this.inputContent = '';
                    this.inputList = [];
                } else {
                    this.infoBomb = true;
                }
            },
            //物价查询函数
            searchPrice: function (value, url) {
                this.unLoaded = true;
                this.loadingText = '查询中，请稍候...';
                this.$http.get(this.publicUrl + url, {
                    params: {
                        search: value,
                        timeStr: new Date().getTime()
                    },
                    headers: this.config(JSON.parse(localStorage.getItem('hospitalInfo')).number),
                    timeout: 10000
                }).then((res) => {
                    if (res.data.code == 10000) {

                        this.$audioPlay(28);

                        this.loadingText = '查询成功，请稍候...';
                        setTimeout(() => {
                            this.unLoaded = false;
                            this.$router.push({
                                name: 'priceList',
                                params: {
                                    value: value,
                                    url:url
                                }
                            });
                        }, 2000)
                    }else{
                        this.loadingText = '查询失败，重新输入...';

                        this.$audioPlay(29);

                        setTimeout(() => {
                            this.unLoaded = false;
                        }, 2000)
                    }
                }).catch((err) => {
                    this.unLoaded = false;

                    this.$audioPlay(21);
                    
                    this.dealError();
                })
            },
            //隐藏弹框
            hideBox: function () {
                this.infoBomb = false;
            }
        },
        destroyed: function () {

        },
        watch: {
            '$route' (to, from) {
                this.nameControll();
            }
        }
    }
</script>
<style scoped>
    .demonstration-box {
        width: 1125px;
        height: 600px;
        border-radius: 10px;
        border: 1px solid #d2d5d5;
        margin-left: 35px;
        overflow: hidden;
    }

    .demonstration-title {
        width: 100%;
        height: 170px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 38px;
        margin: 0;
    }

    .demonstration-title span {
        color: #f70c0c;
    }

    .demonstration-img {
        width: 100%;
        text-align: center;
    }

    .qrcode-box {
        width: 100%;
        height: 190px;
        overflow: hidden;
    }

    .qrcode-box-title {
        width: 100%;
        margin-top: 50px;
        font-size: 38px;
        text-align: center;
    }

    .qrcode-box-info {
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 30px;
        color: #838383;
        margin-top: 25px;
    }

    .next-img {
        width: 14px;
        height: 20px;
        margin: 0 15px;
    }

    .input-box {
        width: 100%;
        height: 200px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .input {
        width: 600px;
        height: 75px;
        line-height: 75px;
        border-radius: 5px;
        border: 1px solid #9d9d9d;
        font-size: 38px;
        outline: none;
        text-indent: 20px;
        overflow: hidden;
    }

    .confirm-btn {
        width: 225px;
        height: 80px;
        border-radius: 10px;
        background: #018ede;
        font-size: 34px;
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-left: 25px;
        cursor: pointer;
    }

    .keyboard-box {
        width: 960px;
        margin-left: 85px;
        display: flex;
        flex-wrap: wrap;
        overflow: hidden;
        -webkit-tap-highlight-color:transparent;     
        -webkit-user-select:none;
    }

    .keyboard-item {
        width: 77px;
        height: 77px;
        background: #1491e8;
        font-size: 30px;
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 10px;
        margin-bottom: 10px;
        border-radius: 5px;
        font-weight: bold;
        cursor: pointer;
    }

    .keyboard-item:nth-child(11) {
        background: #e7a100;
        font-weight: 500;
    }

    .keyboard-item:nth-child(12) {
        margin-left: 40px;
    }

    .keyboard-item:nth-child(22) {
        margin-left: 80px;
    }

    .keyboard-item:nth-child(30) {
        margin-right: 80px;
    }

    .keyboard-item:nth-child(31) {
        margin-left: 120px;
    }

    .keyboard-item:nth-child(38) {
        background: #e7a100;
        font-weight: 500;
    }

    .keyboard-item:active{
        background: #4ae15b;
    }

    .press-key {
        background: #4ae15b;
    }
</style>