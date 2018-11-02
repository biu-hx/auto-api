import Vue from "vue";
import axios from "axios";
import qs from "qs";
import that from "../../main.js";

// 创建axios实例
const service = axios.create({
    baseURL: window.httpUrl, // api的base_url
    timeout: 5000 // 请求超时时间
})
// request拦截器
service.interceptors.request.use(config => {
    if (config.method === 'post') {
        config.url = config.url + '?rand='+new Date().getTime()
        config.data = qs.stringify({
            ...config.data
        })
    }else {
        config.params = {
          ...config.params,
          'rand': new Date().getTime()
        }
    }
    let _projectInfo = JSON.parse(localStorage.getItem('hospitalInfo'));
    config.headers = Vue.prototype.config(_projectInfo.number);
    config.headers['Content-Type'] = 'application/x-www-form-urlencoded'
    return config
}, error => {
    that.player.src = that.audioSrc[21];
    that.player.play();
    that.dealError(error);
    that.isLoading = false;
    Promise.reject(error)
})

// respone拦截器
var elseUrl = [
    '/consultant/connect',
    '/consultant/answer'
].map(item=>window.httpUrl+item)
service.interceptors.response.use(
    response => {
        console.log('response')
        console.log(response)
        console.log(JSON.stringify(response))
        if(response.data.code == 10000){
            return response.data
        }else {
            //code不为10000错误处理
            if(elseUrl.indexOf(response.config.url)!='-1'){
                //特殊接口，code不为10000处理
                return response.data;
            }
            // 老版本异常处理
            else if(that.$route.fullPath.indexOf("plate") >= 0){
                return response.data;
            }
            // 平台版本异常处理
            else{
                //常规错误处理
                that.isLoading = false;
                // that.player.src = that.audioSrc[21];
                // that.player.play();
                // that.dealError(response.data.msg)
                that.$router.replace({
                    name: 'error',
                    params: {
                        msg: response.data.msg
                    }
                })
            }
        }
    },
    error => {
        console.log('error')
        console.log(error)
        console.log(JSON.stringify(error))
        that.player.src = that.audioSrc[21];
        that.player.play();
        that.dealError('网络异常，请重试', 'reload');
        that.isLoading = false;
        return Promise.reject(error)
    }
)
export default service