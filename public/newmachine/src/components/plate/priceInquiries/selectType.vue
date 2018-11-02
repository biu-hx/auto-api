<template :ready>
    <div v-cloak>
        <div class="content">
            <div class="title">
                <p>请选择类型</p>
            </div>
            <div class="card-type-list">
                <div class="item">
                    <div class="card-type-item" @click="drugsPrice">
                        <img src="../../../static/img/drugs-fee.png" alt="" class="card-type-icon">
                        <p>药品费</p>
                    </div>
                    <div class="card-type-item" @click="treatmentPrice">
                        <img src="../../../static/img/treatment-fee.png" alt="" class="card-type-icon">
                        <p>诊疗项目</p>
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

            }
        },
        created: function () {
            //请选择查询方式语音

            this.$audioPlay(14);

        },
        methods: {
            //药品价格查询
            drugsPrice: function () {
                this.$router.push({
                    name: 'searchPrice',
                    params: {
                        byType:'drugs'
                    }
                })
            },
            //诊疗项目查询
            treatmentPrice: function () {
                this.$router.push({
                    name: 'searchPrice',
                    params: {
                        byType:'treatment'
                    }
                })
            }


        },
        destroyed() {

        }
    }
</script>
<style scoped>
    .card-type-list {
        width: 100%;
        overflow: hidden;
        display: flex;
    }

    .item{flex: 1; display: flex; flex-direction: row; flex-wrap: wrap; justify-content:center; margin-top: 120px;}

    .card-type-item {
        width: 530px;
        height: 160px;
        border:#0062A2 1px solid;
        box-sizing: border-box;
        background: url('../../../static/img/card-type-bg.png') #0399E0 no-repeat left bottom;
        display: inline-flex;
        align-items: center;
        justify-content: flex-start;
        border-radius: 8px;
        font-size: 42px;
        color: #fff;
        margin: 35px 50px 35px 0;
        cursor: pointer;
    }

    .card-type-item:nth-child(even){
        margin-right: 0
    }

    .card-type-icon {
        width: 60px;
        height: 56px;
        margin-right: 30px;
        margin-left: 50px;
    }
</style>