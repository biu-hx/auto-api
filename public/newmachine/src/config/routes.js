/**
 * ===================单体医院======================
 */

/**
 * 基础版本1.0
 * @Chenliangpin
 */

// 引入主页
import Home from "../components/home/home.vue";
import Plate from "../components/plate/plate.vue";
// 引入子页面
import IstinguishCard from "../components/plate/istinguishCard.vue";
import ReadCard from '../components/plate/readCard.vue';
import SelectPayway from '../components/plate/selectPayway.vue';
import PaymentList from "../components/plate/outpatientPayment/paymentList.vue";
import PaymentDetail from "../components/plate/outpatientPayment/paymentDetail.vue";
import ReversionNumList from "../components/plate/reversionNumber/reversionNumList.vue";
import SelectDistrict from "../components/plate/register/selectDistrict.vue";
import SelectDept from "../components/plate/register/selectDept.vue";
import SelectTime from "../components/plate/register/selectTime.vue";
import SelectDoctor from "../components/plate/register/selectDoctor.vue";
import SelectTimeSlot from "../components/plate/register/selectTimeSlot.vue";
import ConfirmAndPay from "../components/plate/confirmAndPay.vue";
import Pay from '../components/plate/pay.vue';
import PayBank_win from "../components/plate/pay_bank.vue";
import HospitalizationDetail from "../components/plate/hospitalPrepayment/hospitalizationDetail.vue";
import SelectQueryMethod from "../components/plate/selectQueryMethod.vue";
import QueryOrder from '../components/plate/queryOrder.vue';
import RegisterDetail from "../components/plate/orderSearch/registerDetail.vue";
import PayDetail from "../components/plate/orderSearch/payDetail.vue";
import PrepaymentDetail from "../components/plate/orderSearch/prepaymentDetail.vue";
import NoRecord from "../components/plate/orderSearch/noRecord.vue";
import PrintList from "../components/plate/printTicket/printList.vue";
import InpatientList from "../components/plate/inpatientList/inpatientList.vue";
import ListDetail from "../components/plate/inpatientList/listDetail.vue";
import ListClass from '../components/plate/inpatientList/listClass.vue';
import PrintPage from "../components/plate/printPage.vue";
import PrintPage_win from "../components/plate/printPage_win.vue";
import SelectType from '../components/plate/priceInquiries/selectType.vue';
import SearchPrice from '../components/plate/priceInquiries/searchPrice.vue';
import PriceList from '../components/plate/priceInquiries/priceList.vue';
import SignIn from '../components/plate/signIn/signInResult.vue';
import WaitSeeDoctor from '../components/plate/waitSeeDoctor/result.vue';
import DoctorRate from '../components/plate/doctorRate.vue';
import DoctorRate_win from '../components/plate/doctorRate_win.vue';
import DeptAbout from '../components/plate/deptAbout.vue';
import HospitalAbout from '../components/plate/hospitalAbout/hospitalAbout.vue';
import HospitalNotice from '../components/plate/hospitalAbout/hospitalNotice.vue';

import needs from '../components/plate/needs/needs.vue';
import getCheckTime from '../components/plate/getCheckTime/getCheckTime.vue';
// 手机端用于展示订单详情页面
import OrderDetail from "../components/phoneOrderDetail/orderDetail.vue";

/**
 * 住院订餐(迭代)
 * 模板路径: /components/plate/ordering
 * @Chengyan 2018.07.26
 */

const Ordering_Attentions = function (resolve) {
    require.ensure([], function () {
        resolve(require('../components/plate/ordering/attentions.vue'))
    }, 'ordering')
}

const Ordering_Choose = function (resolve) {
    require.ensure([], function () {
        resolve(require('../components/plate/ordering/choose.vue'))
    }, 'ordering')
}

/**
 * ===================End======================
 */


/**
 * 平台化新模板相关页面
 * 模板路径: /components_regional
 * @Chengyan 2018.05.10
 */

import Regional_main from "../components_regional/main.vue";
import Regional_home from "../components_regional/home/home.vue";
import Regional_service from "../components_regional/frame.vue";

// 视频问诊页面
import Consult_Hospital from "../components_regional/consult/hospital.vue";
import Consult_Doctor from "../components_regional/consult/doctor.vue";
import Consult_Detail from "../components_regional/consult/detaill.vue";
import Consult_Order from "../components_regional/consult/order.vue";

// 科教视频页面
import Sciedu_Video from "../components_regional/sciedu/video.vue";
import Sciedu_Detail from "../components_regional/sciedu/detail.vue";
import Sciedu_Pay from "../components_regional/sciedu/pay.vue";
import Sciedu_Play from "../components_regional/sciedu/play.vue";

// 预约挂号页面
import Register_Hospital from "../components_regional/register/hospital.vue";
import Register_Department from "../components_regional/register/department.vue";
import Register_Date from "../components_regional/register/date.vue";
import Register_Doctor from "../components_regional/register/doctor.vue";

// 就诊卡
import Card_Choose from "../components_regional/card/chooseCard.vue";
import Card_CreateEle from "../components_regional/card/createEleCard.vue";
import Card_ReadCard from "../components_regional/card/readCardNum.vue";

// 支付
import Pay_Mode from "../components_regional/payment/payMode.vue";
import Pay_Scan from "../components_regional/payment/payScan.vue";
import Pay_Bank from "../components_regional/payment/payBank.vue";
import Pay_Muster from "../components_regional/payment/payMuster.vue";

// 打印小票
import Print_Receipt from "../components_regional/print/printPage.vue";

// 订单查询
import Order_Query from "../components_regional/orderQuery/query.vue";

// 无数据
import Common_Error from "../components_regional/common/error.vue";


// 重定向选择
let redirectName = function () {
    if (location.search.indexOf("regional") > 0) {
        return "regional_home";
    } else {
        return "home";
    }
};

// 配置路由
export default [

    // 单体医院
    {
        path: "",
        redirect: {name: redirectName()}
    },
    {
        path: "/home",
        name: "home",
        component: Home
    },
    {
        path: "/plate/:fromName",
        component: Plate,
        name: "plate",
        children: [
            {
                path: "istinguishCard",
                name: "istinguishCard",
                component: IstinguishCard
            },
            {
                path: 'readCard/:type',
                name: 'readCard',
                component: ReadCard
            },
            {
                path: "paymentList",
                name: "paymentList",
                component: PaymentList
            },
            {
                path: "paymentDetail/:registerInfo",
                name: "paymentDetail",
                component: PaymentDetail
            },
            {
                path: "reversionNumList",
                name: "reversionNumList",
                component: ReversionNumList
            },
            {
                path: "selectDistrict",
                name: "selectDistrict",
                component: SelectDistrict
            },
            {
                path: "selectDept",
                name: "selectDept",
                component: SelectDept
            },
            {
                path: "selectTime/:deptId",
                name: "selectTime",
                component: SelectTime
            },
            {
                path: "selectDoctor/:deptId/:dateTime/:period",
                name: "selectDoctor",
                component: SelectDoctor
            },
            {
                path: "selectTimeSlot/:registerInfo",
                name: "selectTimeSlot",
                component: SelectTimeSlot
            },
            {
                path: "selectPayway/:registerInfo",
                name: "selectPayway",
                component: SelectPayway
            },
            {
                path: "confirmAndPay/:payway/:payType/:payData",
                name: "confirmAndPay",
                component: ConfirmAndPay
            },
            {
                path: "pay/:payway/:payType/:payData",
                name: "pay",
                component: Pay
            },
            {
                // path: "payBank_win/:payway/:payType/:payData",
                path: "payBank_win",
                name: "payBank_win",
                component: PayBank_win
            },
            {
                path: "hospitalizationDetail",
                name: "hospitalizationDetail",
                component: HospitalizationDetail
            },
            {
                path: "selectQueryMethod",
                name: "selectQueryMethod",
                component: SelectQueryMethod
            },
            {
                path: "queryOrder/:method",
                name: "queryOrder",
                component: QueryOrder
            },
            {
                path: "registerDetail/:orderNum",
                name: "registerDetail",
                component: RegisterDetail
            },
            {
                path: "payDetail/:orderNum",
                name: "payDetail",
                component: PayDetail
            },
            {
                path: "prepaymentDetail/:orderNum",
                name: "prepaymentDetail",
                component: PrepaymentDetail
            },
            {
                path: "noRecord",
                name: "noRecord",
                component: NoRecord
            },
            {
                path: "printList",
                name: "printList",
                component: PrintList
            },
            {
                path: "inpatientList",
                name: "inpatientList",
                component: InpatientList
            },
            {
                path: "listDetail/:arpbl/:dateFrom/:admId",
                name: "listDetail",
                component: ListDetail
            },
            {
                path: "listClass/:admId/:arpbl",
                name: "listClass",
                component: ListClass
            },
            {
                path: "printPage/:orderNum/:num",
                name: "printPage",
                component: PrintPage
            },
            {
                path: "printPage_win/:orderNum/:num",
                name: "printPage_win",
                component: PrintPage_win
            },
            {
                path: "selectType",
                name: "selectType",
                component: SelectType
            },
            {
                path: "searchPrice/:byType",
                name: "searchPrice",
                component: SearchPrice
            },
            {
                path: "priceList/:value/:url",
                name: "priceList",
                component: PriceList
            },
            {
                path: "signIn",
                name: "signIn",
                component: SignIn
            },
            {
                path: "waitSeeDoctor",
                name: "waitSeeDoctor",
                component: WaitSeeDoctor
            },
            {
                path: "needs",
                name: "needs",
                component: needs
            },
            {
                path: "getCheckTime",
                name: "getCheckTime",
                component: getCheckTime
            },
            // 院内介绍
            {
                path:'doctorRate',
                name:'doctorRate',
                component:DoctorRate
            },
            {
                path:'doctorRate_win',
                name:'doctorRate_win',
                component:DoctorRate_win
            },
            {
                path:'deptAbout',
                name:'deptAbout',
                component:DeptAbout
            },
            {
                path:'hospitalAbout',
                name:'hospitalAbout',
                component:HospitalAbout
            },
            {
                path:'hospitalNotice',
                name:'hospitalNotice',
                component:HospitalNotice
            },
            // 订餐业务
            {
                path: "orderingAttentions",
                name: "ordering-attentions",
                component: Ordering_Attentions
            },
            {
                path: "orderingChoose",
                name: "ordering-choose",
                component: Ordering_Choose
            }

        ]
    },
    {
        path: "/orderDetail/:orderNum",
        component: OrderDetail,
        name: "orderDetail"
    },

    /**
     * 平台化版本
     */
    {
        path: "/regional",
        component: Regional_main,
        children: [
            {
                path: "",
                redirect: {name: "regional_home"}
            },
            {
                path: "home",
                name: "regional_home",
                component: Regional_home
            },
            // 服务模块
            {
                path: "service",
                component: Regional_service,
                children: [

                    /*
                     * 视频问诊
                     */

                    // 选择医院科室
                    {
                        path: "consultHospital",
                        name: "consult-hospital",
                        component: Consult_Hospital,
                        meta: {title: '请选择科室和医院'}
                    },
                    //选择医生
                    {
                        path: "consultDoctor/:deptId/:hospitalId",
                        name: "consult-doctor",
                        component: Consult_Doctor,
                        props: true,
                        meta: {title: '请选择医生'}
                    },
                    //医生详情
                    {
                        path: "consultDetail/:doctorId",
                        name: "consult-detail",
                        component: Consult_Detail,
                        meta: {title: '医生详情'}
                    },
                    //医生下单
                    {
                        path: "consultOrder/:doctorId",
                        name: "consult-order",
                        component: Consult_Order,
                        meta: {title: '请支付订单'}
                    },
                    //科教视频
                    {
                        path: "scieduVideo",
                        name: "sciedu-video",
                        component: Sciedu_Video,
                        meta: {title: '科教视频'}
                    },
                    //视频详情
                    {
                        path: "scieduDetail/:videoId",
                        name: "sciedu-detail",
                        component: Sciedu_Detail,
                        meta: {title: '视频详情'}
                    },
                    //视频播放
                    {
                        path: "scieduPlay",
                        name: "sciedu-play",
                        component: Sciedu_Play,
                        meta: {title: '视频播放'}
                    },
                    //视频支付
                    {
                        path: "scieduPay/:videoId",
                        name: "sciedu-pay",
                        component: Sciedu_Pay,
                        meta: {title: '请支付订单'}
                    },

                    /*
                     * 预约挂号
                     */

                    // 选择医院
                    {
                        path: "regHospital",
                        name: "reg-hospital",
                        component: Register_Hospital,
                        meta: {title: '请选择医院'}
                    },
                    {
                        path: "regDepartment/:hospitalId",
                        name: "reg-department",
                        component: Register_Department,
                        meta: {title: '请选择科室'}
                    },
                    {
                        path: "regDate/:hospitalId/:deptId",
                        name: "reg-date",
                        component: Register_Date,
                        meta: {title: '请选择就诊的时间'}
                    },
                    {
                        path: "regDoctor/:hospitalId/:deptId/:date",
                        name: "reg-doctor",
                        component: Register_Doctor,
                        meta: {title: '请选择就诊的医生'}
                    },

                    // 就诊卡: 挂号、门诊缴费等业务通用
                    {
                        path: "chooseCard",
                        name: "choose-card",
                        component: Card_Choose,
                        meta: {title: '请选择就诊卡类型'}
                    },
                    {
                        path: "createEleCard",
                        name: "create-eleCard",
                        component: Card_CreateEle,
                        meta: {title: '办理电子就诊卡'}
                    },
                    {
                        path: "readCardId/:cardType",
                        name: "read-cardId",
                        component: Card_ReadCard,
                        meta: {title: ''}
                    },

                    // 支付: 挂号、门诊缴费等业务通用
                    {
                        // 选择支付方式
                        path: "payMode",
                        name: "pay-mode",
                        component: Pay_Mode,
                        meta: {title: '请支付订单'}
                    },
                    {
                        // 扫码支付并带示意图
                        path: "payScan/:payMode/:payType",
                        name: "pay-scan",
                        component: Pay_Scan,
                        meta: {title: '扫码支付'}
                    },
                    {
                        // 确认订单并支付
                        path: "payMuster/:payMode/:payType",
                        name: "pay-muster",
                        component: Pay_Muster,
                        meta: {title: '请支付订单'}
                    },

                    // 打印小票
                    {
                        path: "printReceipt",
                        name: "print-receipt",
                        component: Print_Receipt,
                        meta: {title: '打印小票'}
                    },

                    // 订单查询
                    {
                        path: "orderQuery",
                        name: "order-query",
                        component: Order_Query,
                        meta: {title: '订单查询'}
                    },

                    // 错误信息
                    {
                        path: "error/:msg",
                        name: "error",
                        component: Common_Error,
                        meta: {title: '温馨提示'}
                    },
                ]
            }
        ]
    },

];
