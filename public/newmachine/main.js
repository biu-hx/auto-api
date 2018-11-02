import Vue from "vue";
import VueRouter from "vue-router";
import axios from "axios";
import VueAxios from "vue-axios";
Vue.use(require('vue-moment'));
//引入loading公共组件
import Loading from "./src/components/publicAassembly/loading";
//引入提醒框公告组件
import Dialog from "./src/components/publicAassembly/dialog";
//引入音频文件
import audioList from './src/components/publicAassembly/audio';


//引入分页
import Pageination from './src/components_regional/common/pageination';

//处理ie的promise兼容性
require("es6-promise").polyfill();
require("es6-promise/auto");

//请求错误处理函数
import DealError from "./src/components/publicAassembly/error";

//引入入口文件
import App from "./App.vue";

// 引用路由配置文件
import routes from "./src/config/routes";
import qs from "qs";
import sha1 from "js-sha1";

//使用路由
Vue.use(VueRouter);
Vue.use(VueAxios, axios);
Vue.use(Loading);
Vue.use(Dialog);
Vue.use(Pageination);
Vue.use(Pageination);

// 使用配置文件规则
const router = new VueRouter({ routes });

//定义全局变量
// Vue.prototype.appid = "machine";
// Vue.prototype.key = "A185279AA0ADCF93";
Vue.prototype.publicUrl = window.httpUrl;

//引入与安卓交互的dsbridge
Vue.prototype.dsBridge = require("dsbridge");

// 通用方法、硬件接口
import util from './src/js/util.js';
Vue.use(util);


//创建全局音频播放器

const music = Vue.prototype.$agent()=="Android" ? document.createElement("audio") : document.getElementById('snd_ie');
music.src = "";
Vue.prototype.player = music;
Vue.prototype.audioSrc = audioList;
Vue.prototype.audioSrc_new = "";

//生成32位随机字符串
Vue.prototype.randomString = () => {
  let data = ["0","1","2","3", "4","5","6", "7","8","9","A","B","C","D","E","F","G","H","I","J","K","L","M",
    "N","O","P","Q","R","S","T","U","V","W","X","Y","Z","a","b","c","d","e","f","g","h","i","j","k","l",
    "m","n","o","p","q","r","s","t","u","v","w","x","y","z"
  ];
  let result = "";
  for (let i = 0; i < 32; i++) {
    let r = Math.floor(Math.random() * 62); //取得0-62间的随机数，目的是以此当下标取数组data里的值！
    result += data[r]; //输出32次随机数的同时，让rrr加32次，就是32位的随机字符串了。
  }
  return result;
};

//全局签名函数
Vue.prototype.signGenerate = (obj, token) => {
  let str0 = "";
  for (let i in obj) {
    if (i != "sign") {
      let str1 = "";
      str1 = i + "=" + obj[i];
      str0 += str1;
    }
  }

  return sha1(str0 + token);
};
Vue.prototype.dealError = DealError;

//axios全局请求配置，用于与后端通信前验证
Vue.prototype.config = (macAddress) => {
  const signData = {
    number: macAddress,
    nonce: Vue.prototype.randomString(),
    timestamp: parseInt(new Date().getTime() / 1000)
  };
  let signature = Vue.prototype.signGenerate(
    signData,
    "9a6fa47e641d2a71f2203299bed2d622"
  );
  let headers = {
    number: signData.number,
    nonce: signData.nonce,
    timestamp: signData.timestamp,
    signature: signature,
    "api-version": "2"
  };
  return headers;
};

//封装axios post请求
Vue.prototype.postData = (number,obj = {},url, callback) => {
  obj.timeStr = new Date().getTime();
  Vue.axios({
    method: 'post',
    url: Vue.prototype.publicUrl+url,
    headers: Vue.prototype.config(number),
    timeout: 10000,
    data: qs.stringify(obj)
  })
  .then(res => {
    callback(res);
  })
  .catch(err => {
    //处理异常，无法访问或者请求超时重定向页面到首页，只对服务器异常进行处理
    if(navigator.onLine){
      if (err.response) {
        music.src = Vue.prototype.audioSrc[21];
        music.play();
        DealError('请求超时,请稍候再试！');
      } else if (err.request) {
        music.src = Vue.prototype.audioSrc[21];
        music.play();
        DealError('请求超时,请稍候再试！');
      }
    }else{
      DealError('网络错误，请网络正常后重试!');
    }
  });
};

//封装全局过滤字符串函数
Vue.prototype.filterASCII = str => {
  return str.replace(/[^\u4e00-\u9fa5]/gi, "");
};

//判断设备为pc还是移动端
Vue.prototype.isPc = () => {
  
  // 本地PC测试
  if(location.search.indexOf("test") > 0){
    return true;
  }

  let userAgentInfo = navigator.userAgent;
  let Agents = [
    "Android",
    "iPhone",
    "SymbianOS",
    "Windows Phone",
    "iPad",
    "iPod"
  ];

  let flag = true;
  for (let v = 0; v < Agents.length; v++) {
    if (userAgentInfo.indexOf(Agents[v]) > 0) {
      flag = false;
      break;
    }
  }
  return flag;

};

// 一些通用方法和配置
import DialogError from "./src/components_regional/common/dialogError";
Vue.use(DialogError);


// 注册实例,不使用vuex
var that = new Vue({
  el: "#app",
  router: router,
  render: h => h(App)
});

export default that
