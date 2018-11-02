<template :ready>
    <div v-cloak>
        <div class="content">
            <div class="title">
                <p>请选择餐品</p>
            </div>

            <div class="mainBox">
                <div class="chooseTimeBar">
                    <div class="time">
                        明天 06月23日
                    </div>
                    <div class="mealTimes">
                        <span class="active">早餐</span>
                        <span>午餐</span>
                        <span>晚餐</span>
                        <span>晚上加汤</span>
                        <span>治疗餐</span>
                    </div>
                </div>

                <div class="mealList">
                    <div class="mealMenu">
                        <ul>
                            <li>经典套餐</li>
                            <li class="active">炒菜</li>
                            <li>汤类</li>
                            <li>一次性餐盒</li>
                            <li>10元荤菜</li>
                        </ul>
                    </div>

                    <div class="foods">
                        <ul class="foodList">
                            <li>
                                <div class="pic"><img src="../../../static/img/bank-pay-icon.png"/></div>
                                <div class="disc">
                                    <dl>
                                        <dt>鱼香肉丝</dt>
                                        <dd>肉丝+茄子</dd>
                                        <dd>￥<em>20</em>/份</dd>
                                    </dl>
                                    <div class="addCart">
                                        <img src="../../../static/img/ordering_reduce_icon.png"/>
                                        <span>2</span>
                                        <img src="../../../static/img/ordering_add_icon.png"/>
                                    </div>
                                </div>
                            </li>
                            <li>
                                <div class="pic"><img src="../../../static/img/bank-pay-icon.png"/></div>
                                <div class="disc">
                                    <dl>
                                        <dt>鱼香肉丝</dt>
                                        <dd>肉丝+茄子</dd>
                                        <dd>￥<em>20</em>/份</dd>
                                    </dl>
                                    <div class="addCart">
                                        <img src="../../../static/img/ordering_reduce_icon.png"/>
                                        <span>2</span>
                                        <img src="../../../static/img/ordering_add_icon.png"/>
                                    </div>
                                </div>
                            </li>
                            <li>
                                <div class="pic"><img src="../../../static/img/bank-pay-icon.png"/></div>
                                <div class="disc">
                                    <dl>
                                        <dt>鱼香肉丝</dt>
                                        <dd>肉丝+茄子</dd>
                                        <dd>￥<em>20</em>/份</dd>
                                    </dl>
                                    <div class="addCart">
                                        <img src="../../../static/img/ordering_reduce_icon.png"/>
                                        <span>2</span>
                                        <img src="../../../static/img/ordering_add_icon.png"/>
                                    </div>
                                </div>
                            </li>
                            <li>
                                <div class="pic"><img src="../../../static/img/bank-pay-icon.png"/></div>
                                <div class="disc">
                                    <dl>
                                        <dt>鱼香肉丝</dt>
                                        <dd>肉丝+茄子</dd>
                                        <dd>￥<em>20</em>/份</dd>
                                    </dl>
                                    <div class="addCart">
                                        <img src="../../../static/img/ordering_reduce_icon.png"/>
                                        <span>2</span>
                                        <img src="../../../static/img/ordering_add_icon.png"/>
                                    </div>
                                </div>
                            </li>
                            <li>
                                <div class="pic"><img src="../../../static/img/bank-pay-icon.png"/></div>
                                <div class="disc">
                                    <dl>
                                        <dt>鱼香肉丝</dt>
                                        <dd>肉丝+茄子</dd>
                                        <dd>￥<em>20</em>/份</dd>
                                    </dl>
                                    <div class="addCart">
                                        <img src="../../../static/img/ordering_reduce_icon.png"/>
                                        <span>2</span>
                                        <img src="../../../static/img/ordering_add_icon.png"/>
                                    </div>
                                </div>
                            </li>
                            <li>
                                <div class="pic"><img src="../../../static/img/bank-pay-icon.png"/></div>
                                <div class="disc">
                                    <dl>
                                        <dt>鱼香肉丝</dt>
                                        <dd>肉丝+茄子</dd>
                                        <dd>￥<em>20</em>/份</dd>
                                    </dl>
                                    <div class="addCart">
                                        <img src="../../../static/img/ordering_reduce_icon.png"/>
                                        <span>2</span>
                                        <img src="../../../static/img/ordering_add_icon.png"/>
                                    </div>
                                </div>
                            </li>
                        </ul>
                        
                        <!-- 分页/购物车 -->
                        <div class="mealFooter">
                            <div class="cart">
                                <img src="../../../static/img/cart_icon.png"/>
                                <p>
                                    <small>2</small>
                                    <strong>￥<em>40</em></strong>
                                    <span>免费配送</span>
                                </p>
                            </div>
                            <div class="pagination">
                                <button type="button" class="button_default">上一页</button>
                                <button type="button" class="button_default">下一页</button>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            
        </div>
    </div>
</template>

<script>
    let timeInterval;
    export default {
        data() {
            return {
                result: undefined,
                time: 8
            };
        },
        created: function () {
            this.result = true;
        },
        methods: {
            
        },
        destroyed: function () {
        }
    };
</script>

<style lang="less" src='../../static/less/ordering.less' scoped></style>