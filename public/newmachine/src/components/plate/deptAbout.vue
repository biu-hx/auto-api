<template>
    <div class="content" v-cloak>
        <div class="title">
            <p>科室介绍</p>
        </div>
        <div>
            <div class="dept-wrap">
                <div class="yellow-line"></div>
                <div class="dept-list">
                    <div class="translate-wrap">
                        <div class="classify-item clearfix" v-for="(item ,index) in dept_classify">
                            <div class="positon-tag"><p class="dept-tag"><em class="ponit"></em>{{item.name}}</p></div>
                            <div class="dept-item" v-for="(item,index) in item.classify_dept"
                                 @click="showDesc(item)">{{item.name}}
                            </div>
                        </div>
                    </div>

                    <!--<div class="classify-item clearfix">-->
                    <!--<div class="positon-tag"><p class="dept-tag"><em class="ponit"></em>临床科室</p></div>-->
                    <!--<div class="dept-item" v-if="index<5" v-for="(item,index) in renderDeptList"-->
                    <!--@click="showDesc(item)">{{item.name}}-->
                    <!--</div>-->
                    <!--</div>-->
                    <!--<div class="classify-item clearfix">-->
                    <!--<div class="positon-tag"><p class="dept-tag"><em class="ponit"></em>临床科室</p></div>-->
                    <!--<div class="dept-item" v-if="index<3" v-for="(item,index) in renderDeptList"-->
                    <!--@click="showDesc(item)">{{item.name}}-->
                    <!--</div>-->
                    <!--</div>-->

                </div>
            </div>

            <div class="page-controll" v-if="Math.ceil(currentHeight/defaultHeight)>pageNum+1">
                <div class="prev-page" @click="prevPage" :class="{'unflip':!ablePrev}">上一页</div>
                <div class="page-num">{{pageNum+1}}/{{Math.ceil(currentHeight/defaultHeight)}}</div>
                <div class="next-page" @click="nextPage" :class="{'unflip':!ableNext}">下一页</div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "deptAbout",
        data() {
            return {
                renderDeptList: [],
                //大科室分类
                dept_classify: [
                    {
                        classify_dept: [],
                        name: '临床科室',
                    },//临床科室
                    {
                        classify_dept: [],
                        name: '医技科室'
                    },//医技科室
                ],
                pageNum: 0, //当前页数
                ablePrev: false,
                ableNext: true,
                defaultHeight: '480',//默认一页高度
                currentHeight: '',//当前数据撑开的高度

            }
        },
        created() {
            this.getDeptList();
        },
        methods: {
            getDeptList() {
                this.$http.get(this.publicUrl + "/propaganda/deptDes", {
                    params: {},
                    headers: this.config(JSON.parse(localStorage.getItem('hospitalInfo')).number),
                    timeout: 10000
                }).then(res => {
                    if (res.data.code === 10000) {
                        this.renderDeptList = res.data.data;
                        this.test(res.data.data);
                    }

                })
            },
            //页面显示控制函数
            pageShowControll: function () {
                let countNum = Math.ceil(this.currentHeight / this.defaultHeight);
                let cssObj = document.querySelector('.translate-wrap');
                let topHeight = this.pageNum * this.defaultHeight;
                cssObj.style.marginTop = `-${topHeight}px`;
                if (this.pageNum <= countNum && this.pageNum >= 1) {
                    this.ablePrev = true;
                } else {
                    this.ablePrev = false;
                }
                if (this.pageNum >= 0 && this.pageNum + 1 != countNum) {
                    this.ableNext = true;
                } else {
                    this.ableNext = false;
                }
            },
            //上一页
            prevPage: function () {
                let countNum = Math.ceil(this.currentHeight / this.defaultHeight);
                if (this.pageNum <= countNum && this.pageNum >= 1) {
                    this.pageNum--;
                }
                this.pageShowControll();
            },
            //下一页
            nextPage: function () {
                let countNum = Math.ceil(this.currentHeight / this.defaultHeight);
                if (this.pageNum >= 0 && this.pageNum + 1 != countNum) {
                    this.pageNum++;
                }
                this.pageShowControll();
            },
            // 数据遍历分类
            test(arry) {
                arry.forEach((item) => {
                    //1为临床科室，2为医技科室；
                    if (item.type === '1') {
                        this.dept_classify[0].classify_dept.push(item);
                    }
                    if (item.type === '2') {
                        this.dept_classify[1].classify_dept.push(item);
                    }
                });
                //分页,查询出科室列表后默认渲染第一页科室数据
                this.$nextTick(() => {
                    this.currentHeight = document.querySelector('.translate-wrap').clientHeight;
                    if (Math.ceil(this.currentHeight / 460) > 1) {

                    }
                })


            },
            showDesc(item) {
                this.$router.push({
                    name: 'hospitalAbout',
                    params: {
                        data: item
                    }
                });
                // console.log(item)
            },
        }
    }
</script>

<style scoped>
    .dept-wrap {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        box-sizing: border-box;
        padding: 0 60px 0 30px;
        height: 460px;
        margin-bottom: 75px;
        overflow: hidden;
        margin-top: 10px;
    }

    .yellow-line {
        box-sizing: border-box;
        margin-top: 50px;
        height: 450px;
        border-left: 3px solid #feae00;
    }

    .classify-item {
        position: relative;
    }

    .positon-tag {
        position: absolute;
        left: -226px;
        top: 50px;
    }

    .dept-tag {
        position: relative;
        font-size: 30px;
        color: #ffffff;
        width: 136px;
        height: 46px;
        margin-top: -16px;
        text-align: center;
        border-radius: 6px;
        background: #feae00;
        margin-left: 24px;
    }

    .ponit {
        position: absolute;
        width: 10px;
        height: 10px;
        left: -34px;
        top: 16px;
        background: #ffffff;
        border-radius: 50%;
        border: 3px solid #feae00;
    }

    .dept-list {
        width: 870px;
        height: 480px;
        margin-bottom: 35px;
    }

    .dept-item {
        cursor: pointer;
        float: left;
        font-size: 32px;
        color: #ffffff;
        width: 245px;
        height: 110px;
        margin: 0 40px 50px 0;
        background: #0399e0;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 1px solid #0062a2;
    }

    .clearfix:after {
        content: "";
        display: block;
        clear: both;
        overflow: hidden;
        visibility: visible;
        width: 0;
        height: 0;
    }

</style>