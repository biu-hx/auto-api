<template>
  <div class="printBox">
    <div class="printWrap">
      <Loading v-if="isLoading" :title="loadingText"></Loading>
      <Dialog v-if="infoBomb" :text="infoNotice" :buttonconfig="['知道了']" @hideBox="hideBox"></Dialog>
      <div class="demonstration">
        <p class="demonstration-title">{{printTitle}}</p>
        <div class="demonstration-img">
          <img src="../../static/img/printing-ticket.png" class="printing-ticket" v-if="printStatus=='printing'">
          <img src="../../static/img/printed-ticket.png" class="printing-ticket" v-if="printStatus=='printed'">
        </div>
      </div>
    </div>
  </div>
</template>

<script>
  import {queryOrderDetail} from '../../components_regional/api/common'

  export default {
    data() {
      return {
        isLoading: false,
        loadingText: "正在检测打印机...",
        infoBomb: false,
        infoNotice: "",
        printTitle: '正在打印小票',
        orderDetail: {},
        printStatus: "printing",    // 打印进度

        buttonconfig: ['知道了'],
        printDeviceStatus: '',      // 打印机设备状态
        bIsAndroid: false
      }
    },
    props: {
      orderNum: {
        type: String
      },
      printType: {
        type: String
      }
    },
    created: function () {
      var sUserAgent = navigator.userAgent;
      this.bIsAndroid= sUserAgent.toLowerCase().match(/android/i) == "android";
      this.isLoading = true;
      // 获取打印机设备工作状态
      if(this.bIsAndroid){
        this.printDeviceStatus = this.$machineApi.getMachine_printStatus();
      }else {
        this.printDeviceStatus = window.external.CheckStatus();
      }
      console.log('printDeviceStatus' + this.printDeviceStatus)
      if (this.printDeviceStatus == '0') {
        // 正常
        this.loadingText = '正在加载数据...',
          // 播放打印小票语音
        this.player.src = this.audioSrc[7];
          if(this.$agent()=="Android"){
              this.player.play();
          }
        // 获取打印数据
        this.getOrderDetail();
      } else if (this.printDeviceStatus == '-1') {
        //缺纸
        this.isLoading = false;
        this.infoBomb = true;
        this.infoNotice = '<p style="text-align:left; font-size:46px;">打印机缺纸，无法打印小票<p>';
        console.log('printDeviceStatus---1')

      } else if (this.printDeviceStatus == '-2') {
        //异常
        this.isLoading = false;
        this.infoBomb = true;
        this.infoNotice = '<p style="text-align:left; font-size:46px;">打印机设备异常<p>';
        console.log('printDeviceStatus---2')
      }
    },
    methods: {
      //根据订单号查询交易详情
      getOrderDetail: function () {
        // if (!this.isPc()) {
        queryOrderDetail({
          orderNum: this.orderNum,
            rand:new Date().getTime()
        }).then(res => {
          // 数据处理
          if (res.code == 10000) {
            this.isLoading = false;
            this.orderDetail = res.data;
            console.log('获取数据成功')
            console.log(res)
            // 调用安卓/执行打印操作
            let printService = this.printType;
            if(this.bIsAndroid){
              // 打印小票
              this.dsBridge.call(printService,{
                msg: this.orderDetail});
            }else {
              window.external[printService](JSON.stringify(res));
            }
            // 6秒后
            setTimeout(() => {
              //播放打印小票完成语音
              this.player.src = this.audioSrc[3];
                if(this.$agent()=="Android"){
                    this.player.play();
                }
              //将打印状态变为已打印
              this.printTitle = '打印完成，请取走小票';
              this.printStatus = "printed";
              setTimeout(()=>{
                this.$emit('printOver');
              },1000)
            }, 6000);

          }
          // 数据异常
          else {
            if (this.filterASCII(res.data)) {
              this.infoNotice = this.filterASCII(res.data);
            } else {
              this.infoNotice = res.data;
            }
            this.infoBomb = true;
          }
          this.isLoading = false
        })
      },
      // 关闭Dailog
      hideBox: function () {
        this.infoBomb = false;
        if (this.printDeviceStatus == "-1" || this.printDeviceStatus == "-2") {
          //处理异常
          setTimeout(()=>{
            this.$emit('printOver');
          },1000)
        }
      }
    }
  };
</script>

<style scoped>
  .printWrap {
    width: 870px;
    padding: 35px;
    height: 604px;
    box-sizing: border-box;
    position: relative;
    background: white;
    border-radius: 10px;
  }

  .printBox {
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 2, 0.6);
    position: fixed;
    top: 0;
    left: 0;
    overflow: hidden;
    z-index: 9999;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .demonstration {
    height: 600px;
  }

  .demonstration-title {
    width: 100%;
    height: 85px;
    padding-top: 30px;
    text-align: center;
    font-size: 50px;
    color: black;
  }

  .demonstration-info {
    font-size: 28px;
    text-align: center;
    color: black;
  }

  .demonstration-img {
    height: 380px;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
  }

  .printing-ticket {
    width: 260px;
    height: 260px;
  }

  .count-time {
    position: absolute;
    right: -20px;
    top: -86px;
  }
</style>