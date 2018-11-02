<template>
    <div class="content">
        <div class="title">
            <p>公告公示</p>
        </div>
        <div class="notice-wrap">
            <div class="notice-content">
                <p class="notice-title">{{noticeList[pageNum].title}}</p>
                <p class="notice-time">时间：2018-10-11 15:20</p>
                <div class="notice-desc">{{noticeList[pageNum].content}}</div>
            </div>
            <div class="nextNotice" v-if="pageNum<noticeList.length-1">
                <p class="next-title">下一篇：{{noticeList[pageNum+1].title}}</p>
                <div class="next-page" @click="nextNotice">下一篇</div>
            </div>

        </div>
    </div>
</template>

<script>
    const test = [
        {
            "id": "1",
            "title": "我是公告1",
            "content": "最熟悉的换句话说健康的复活节闪电发货发生的符合惊世毒妃\r\n水电费了金黄色的附近十多年时代峰峻更合适的缴费\r\n\r\n第三方结构化水电费 ",
            "project_id": "1",
            "create_time": "0000000000"
        },
        {
            "id": "2",
            "title": "我是公告2",
            "content": "test notice 2",
            "project_id": "1",
            "create_time": "0000000000"
        },
        {
            "id": "3",
            "title": "我是公告3",
            "content": "test notice 3",
            "project_id": "1",
            "create_time": "0000000000"
        }
    ];
    export default {
        name: "hospitalNotice",
        data() {
            return {
                noticeList: [
                    {
                        title: '',
                        content: '',
                        create_time: '',
                    }
                ],
                currentData: {},
                pageNum: 0,

            }
        },
        created() {
            this.getNotice();
        },
        methods: {
            // 拉取数据
            getNotice() {
                this.$http.get(this.publicUrl + "/propaganda/notice", {
                    params: {},
                    headers: this.config(JSON.parse(localStorage.getItem('hospitalInfo')).number),
                    timeout: 10000
                }).then(res => {
                    if (res.data.code === 10000) {
                        // this.noticeList = res.data.data;
                        this.noticeList = test;
                    }

                })
            },
            // 下一篇
            nextNotice() {
                if (this.pageNum < this.noticeList.length - 1) {
                    this.pageNum++;
                }

            },
        }
    }
</script>

<style scoped>
    .notice-wrap {
        width: 1150px;
        margin-left: 35px;
    }

    .notice-content {
        height: 550px;
        padding-bottom: 10px;
        overflow-y: auto;
        box-sizing: border-box;
    }

    .notice-title {
        font-size: 32px;
        text-align: center;
    }

    .notice-time {
        font-size: 24px;
        text-align: center;
        padding: 15px 0 20px;
    }

    .notice-desc {
        font-size: 24px;
        line-height: 40px;
    }

    .notice-content::-webkit-scrollbar-button {
        background: transparent;
        height: 15px;
    }

    .notice-content::-webkit-scrollbar {
        width: 6px;
    }

    .notice-content::-webkit-scrollbar-thumb {
        background: #bebebe;
        border-radius: 10px;
    }

    .nextNotice {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .next-title {
        padding-right: 40px;
        cursor: pointer;
        font-size: 20px;
        line-height: 40px;
        text-overflow: ellipsis;
        overflow: hidden;
        white-space: nowrap;
        flex: 1;
    }
</style>