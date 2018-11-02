<template>
    <div class="content">
        <div class="title">
            <p>{{title}}介绍<strong v-if="fromName==='deptAbout'" class="timeStr">时间：{{deptData.create_time*1000
                |moment("YYYY-MM-DD HH:mm:ss")}}</strong></p>
        </div>
        <!--科室介绍-->
        <div v-if="fromName==='deptAbout'" class="hospital-desc">{{deptData.content}}</div>
        <!--医院介绍-->
        <div v-else class="hospital-desc" v-html="hospitalData.describe"></div>
    </div>
</template>

<script>
    export default {
        name: "hospitalAbout",
        data() {
            return {
                deptData: '',//科室介绍数据
                hospitalData: '',//医院介绍数据
                title: '医院',
                fromName: '',

            }
        },
        created() {
            this.fromName = this.$route.params.fromName;
            this.getData();
        },
        methods: {
            getData() {
                if (this.fromName === "deptAbout") {
                    let data = this.$route.params.data;
                    this.deptData = data;
                    this.title = data.name;

                } else {
                    this.title = '医院';
                    this.$http.get(this.publicUrl + "/propaganda/hospitalDes", {
                        params: {},
                        headers: this.config(JSON.parse(localStorage.getItem('hospitalInfo')).number),
                        timeout: 10000
                    }).then(res => {
                        if (res.data.code === 10000) {
                            this.hospitalData = res.data.data;
                        }

                    })
                }

            }
        },
    }
</script>

<style scoped>
    .hospital-desc {
        width: 1155px;
        max-height: 600px;
        margin-left: 35px;
        overflow-y: auto;
    }

    .timeStr {
        font-size: 20px;
        font-weight: normal;
        padding-left: 25px;
    }

    .hospital-desc::-webkit-scrollbar-button {
        background: transparent;
        height: 5px;
    }

    .hospital-desc::-webkit-scrollbar {
        width: 6px;
    }

    .hospital-desc::-webkit-scrollbar-thumb {
        background: #bebebe;
        border-radius: 10px;
    }
</style>