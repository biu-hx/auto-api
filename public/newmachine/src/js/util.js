/**
 * Created by chengyan on 2018.5.12
 */
function getDevicePort() {
    if (localStorage.getItem('hospitalInfo')) {
        let device_port = JSON.parse(localStorage.getItem('hospitalInfo')).device_port;
        return JSON.parse(device_port);
    }
}

exports.install = function (Vue, options) {

    /**
     * 返回设备的系统内核
     *
     */

    Vue.prototype.$agent = function () {

        let userAgentInfo = navigator.userAgent;
        let Agents = [
            "Android",
            "Windows"
        ];

        let flag = '';
        for (let i = 0; i < Agents.length; i++) {
            if (userAgentInfo.indexOf(Agents[i]) > 0) {
                flag = Agents[i];
                break;
            }
        }

        let w_search = location.search;
        if (w_search.indexOf("agent=pc") > 0) {
            flag = "Test";
        }

        return flag;
    }


    /**
     * 返回当前时间
     *
     */
    Vue.prototype.$getTime = function () {
        let weekArr = ["星期日", "星期一", "星期二", "星期三", "星期四", "星期五", "星期六"]
        let date = new Date();
        let year = date.getFullYear();
        let month = ((date.getMonth() + 1) < 10) ? ('0' + (date.getMonth() + 1)) : (date.getMonth() + 1);
        let date1 = date.getDate() < 10 ? ('0' + date.getDate()) : date.getDate();
        let day = year + '年' + month + '月' + date1 + '日';
        let week = weekArr[date.getDay()];
        let hours = (date.getHours() < 10) ? ('0' + date.getHours()) : (date.getHours());
        let minutes = (date.getMinutes() < 10) ? ('0' + date.getMinutes()) : (date.getMinutes());
        let seconds = (date.getSeconds() < 10) ? ('0' + date.getSeconds()) : (date.getSeconds());
        return day + '  ' + hours + ':' + minutes + ':' + seconds + '  ' + week;
    }


    /**
     * 播放语音
     *
     */

    Vue.prototype.$audioPlay = function (key) {

        let audios_new = JSON.parse(localStorage.getItem('audios_new'));

        // 语音地址
        this.player.src = "http://auto.mobimedical.cn/" + audios_new[key];
        console.log(this.player.src)

        // 播放
        // Android需要play()播放; Windows自动播放
        if (this.$agent() == "Android") {
            this.player.play();
        }

    }


    /**
     * 设备硬件接口
     *
     */
    Vue.prototype.$machineApi = {
        /**
         * 安卓
         */

        // 获取MAC
        getMachine_mac: function () {
            let _mac = Vue.prototype.dsBridge.call("getMac", {msg: "getMac"});
            return _mac
        },
        // 获取软件版本号
        getMachine_appVersion: function () {
            let _appVersion = Vue.prototype.dsBridge.call("getAppVersion", {msg: "getAppVersion"});
            return _appVersion
        },
        // 检查打印机工作状态
        getMachine_printStatus: function () {
            let _printStatus = Vue.prototype.dsBridge.call("getPrintStatus", {msg: 'getPrintStatus'});
            return _printStatus
        },
        // 打印小票
        printOrderReceipt: function (printService, orderDetail) {
            if (Vue.prototype.isPc()) return;
            Vue.prototype.dsBridge.call(printService, {msg: orderDetail});
        },
        // 监听输入设备输入的值
        watchInput: function () {
            try {
                let str = Vue.prototype.dsBridge.call("getInput", {msg: "getInput"})
                return str
            } catch (e) {
                return "error";
            }

        },
        // 取消监听输入设备输入的值
        clearWatchInput: function () {
            Vue.prototype.dsBridge.call('clearInput', {msg: 'clearInput'});
        },
        // 设备输入框焦点，从而达到监听输入值的目的
        setInputFocus: function () {
            Vue.prototype.dsBridge.call('setF', {msg: 'setF'});
        },
        // 关闭应用
        FinishApp: function () {
            Vue.prototype.dsBridge.call('finishApp', {msg: 'finishApp'})
        },
        // 人脸识别
        faceRec: function () {
            Vue.prototype.dsBridge.call('startTracker', {msg: 'startTracker'})
        },

        /**
         * Windows
         */

        /**
         * 条码扫描
         */

        // 初始化扫描
        win_intScan: function () {
            try {
                let result = HWA.ZZ_BarScan("1001", getDevicePort().barCode);
                return result;
            } catch (e) {
                alert("初始化扫码:" + e)
            }
        },

        // 取条码数据
        win_getScanData: function () {
            try {
                let result = HWA.ZZ_BarScan("1002");
                return result;
            } catch (e) {
                alert("扫码取值:" + e)
            }
        },


        /**
         * 就诊卡刷卡/插卡
         */

        // 初始化读卡
        settingCardCOM: function () {
            try {
                let result = HWA.ZZ_M100_Reader("1001", getDevicePort().cardReader, "32");
                return result;
            } catch (e) {
                alert("读卡初始化:" + e)
            }
        },

        // 读卡
        readCardNumberCOM: function () {
            try {
                let result = HWA.ZZ_M100_Reader("1003");
                return result;
            } catch (e) {
                alert("读取就诊卡卡号:" + e)
            }
        },

        // 退卡
        rejectcardCOM: function () {
            try {
                let result = HWA.ZZ_M100_Reader("1002");
                return result;
            } catch (e) {
                alert("退卡:" + e)
            }
        },
        // 自贡初始化读卡
        zgSettingCardCOM: function () {
            try {
                let result = HWA.ZZ_U100Reader("1001");
                return result;
            } catch (e) {
                alert("读卡初始化:" + e)
            }
        },
        // 自贡读卡
        zgReadCardNumberCom: function () {
            try {
                let result = HWA.ZZ_U100Reader("1002");
                return result;
            } catch (e) {
                alert("读取就诊卡卡号:" + e)
            }
        },
        // 自贡关闭读卡
        zgRejectcardCOM: function () {
            try {
                let result = HWA.ZZ_U100Reader("1003");
                return result;
            } catch (e) {
                alert("退卡:" + e)
            }
        },
        /**
         * 打印机设备
         */

        // 初始化
        win_printerInt: function () {
            try {
                let result = HWA.ZZ_Printer("1001");
                return result;
            } catch (e) {
                alert("打印机初始化:" + e)
            }
        },

        // 取状态
        win_printerStatus: function () {
            try {
                let result = HWA.ZZ_Printer("1002");
                return result;
            } catch (e) {
                alert("打印机状态:" + e)
            }
        },

        // 打印
        win_printerIng: function (template) {
            try {
                let result = HWA.ZZ_Printer("1003", template);
                return result;
            } catch (e) {
                alert("打印:" + e)
            }
        },

        /**
         * 灯条开关
         */

        // 打开灯条
        win_lightOn: function () {
            try {
                let result = HWA.ZZ_M100_Reader("1004", getDevicePort().cardReader, "32");
                return result;
            } catch (e) {
                alert("打开灯条:" + e)
            }
        },

        // 关闭灯条
        win_lightOff: function () {
            try {
                let result = HWA.ZZ_M100_Reader("1005", getDevicePort().cardReader, "30");
                return result;
            } catch (e) {
                alert("关闭灯条:" + e)
            }
        },

        /**
         * 银联卡操作
         */

        // 初始化
        win_initUmps: function () {
            try {
                let result = HWA.ZZ_UMSUnionPay("1001");
                return result;
            } catch (e) {
                alert("银联卡初始化:" + e);
            }
        },

        // 设置交易参数
        win_settingTradeParams: function (money) {
            try {
                let result = HWA.ZZ_UMSUnionPay("1002", money);
                return result;
            } catch (e) {
                alert("交易参数:" + e);
            }
        },

        // 设置读卡器进卡
        win_settingCardIn: function () {
            try {
                let result = HWA.ZZ_UMSUnionPay("1003");
                return result;
            } catch (e) {
                alert("设置银联卡进卡:" + e);
            }
        },

        // 读卡
        win_readerBankCard: function () {
            try {
                let result = HWA.ZZ_UMSUnionPay("1004");
                return result;
            } catch (e) {
                alert("银联卡读卡:" + e);
            }
        },

        // 开启密码键盘
        win_startBankPin: function () {
            try {
                let result = HWA.ZZ_UMSUnionPay("1006");
                return result;
            } catch (e) {
                alert("开启密码键盘:" + e);
            }
        },

        // 获取键值
        win_getkeyValue: function () {
            try {
                let result = HWA.ZZ_UMSUnionPay("1007");
                return result;
            } catch (e) {
                alert("获取密码键盘键值:" + e);
            }
        },

        // 交易
        win_startConsume: function (money, orderNum) {
            try {
                let result = HWA.ZZ_UMSUnionPay("1008", money, orderNum);
                return result;
            } catch (e) {
                alert("银联交易:" + e);
            }
        },

        // 退卡
        win_rejectBankCard: function () {
            try {
                let result = HWA.ZZ_UMSUnionPay("1005");
                return result;
            } catch (e) {
                alert("退卡:" + e);
            }
        },

        // 关闭应用
        win_closeApp: function () {
            try {
                HWA.ZZ_Computers();
            } catch (e) {
                alert("关闭应用:" + e);
            }
        },

        // 设备监控(开启)
        win_startMoniter: function () {
            try {
                HWA.ZZ_StartMonitorHardware(getDevicePort().barCode, getDevicePort().cardReader, getDevicePort().keyboard);
            } catch (e) {
                alert("设备监控开启:" + e);
            }
        },

        // 设备监控(关闭)
        win_stopMoniter: function () {
            try {
                HWA.ZZ_StopMonitorHardware();
            } catch (e) {
                alert("设备监控关闭:" + e);
            }
        }

    }

    /**
     * 返回设备MAC和版本号
     * ---------------------------------
     * 支持双系统
     * 通用
     *
     */
    Vue.prototype.$getMacAddress = function () {
        let mac = '', version = '';
        let getAgent = this.$agent();

        // 安卓版
        if (getAgent == "Android") {
            let get_mac = Vue.prototype.$machineApi.getMachine_mac();
            let get_version = Vue.prototype.$machineApi.getMachine_appVersion();
            mac = get_mac ? get_mac : '';
            version = get_version ? get_version : '';
        }
        // Windows版
        else if (getAgent == "Windows") {
            try {
                let result = HWA.ZZ_GetMacAdreess();
                if (result != "获取mac地址异常") {
                    mac = result;
                }
            } catch (e) {
                console.log("ZZ_GetMacAdreess接口异常")
            }
            version = "Win 1.0";
        }

        // PC端调试专用
        let hrefReg = new RegExp("(^|&)test=([^&]*)(&|$)");
        let r = window.location.search.substr(1).match(hrefReg);
        if (r != null) mac = unescape(r[2]);

        return {
            mac: mac ? mac.toLocaleUpperCase() : 'AD-DC-12-XX-SD-X3',
            version: version ? version : 'Test 1.0'
        }
    }

    /**
     * 获取设备信息
     * 医院基本配置信息
     *
     */
    Vue.prototype.$getMachineInfo = function (url, mac, cb) {

        // 请求
        let httpUrl = Vue.prototype.publicUrl + url;
        this.$http.get(httpUrl, {
            params: {
                mac: mac,
                'rand': new Date().getTime()
            },
            headers: Vue.prototype.config(mac),
            timeout: 10000
        }).then((res) => {
            cb(res.data)
        }).catch((err) => {
            cb(false)
            Vue.prototype.$audioPlay(21);
            Vue.prototype.dealError();
        })
    }

}