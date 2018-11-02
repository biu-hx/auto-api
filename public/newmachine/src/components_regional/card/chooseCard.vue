<template>
    <div class="registerWrap" v-cloak>
        <Loading v-if="isLoading" :title="loadingText"></Loading>
        <div class="cardList">
            <ul>
                <li @click="handleCard('0')">
                    <i><img src="../static/images/card_icon_createEle.png"></i>
                    <strong>立即办理电子卡</strong>
                </li>
                <li @click="handleCard('2')">
                    <i><img src="../static/images/card_icon_ele.png"></i>
                    <strong>电子卡</strong>
                </li>
                <li @click="handleCard('1')">
                    <i><img src="../static/images/card_icon_mate.png"></i>
                    <strong>实体卡</strong>
                </li>
                <li @click="handleCard('5')">
                    <i><img src="../static/images/card_icon_medical.png"></i>
                    <strong>医保卡</strong>
                </li>
            </ul>
        </div>
    </div>
</template>

<script>
    /**
     * cardType 对应接口文档"卡类型枚举值"
     */
    export default {
        data() {
            return {
                isLoading: false,
                loadingText: '正在加载数据...',
            }
        },
        created: function () {
            // this.getDepartmentData();
        },
        computed: {},
        methods: {
            handleCard: function (res) {
                if(res == '0'){
                    this.$router.push(
                        {
                            path: 'createEleCard',
                            // params: {
                            //     deptId: departmentId,
                            //     hospitalId: "321312"
                            // }
                        }
                    )
                }else{
                    this.$router.push(
                        {
                            name: 'read-cardId',
                            params: {
                                cardType: res
                            }
                        }
                    )
                }
                
            }
        },
        destroyed: function () {
        }
    }
</script>

<style lang="less" src='../static/less/card.less' scoped></style>