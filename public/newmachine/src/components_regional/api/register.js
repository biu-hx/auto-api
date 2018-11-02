import service from '../../js/request'
// 获取医院
export function getHospitalData(query) {
    return service({
        url: '/registration/hospital',
        method: 'get',
        params: query
    })
}

// 获取科室
export function getDepartmentData(query) {
    return service({
        url: '/registration/dept',
        method: 'get',
        params: query
    })
}

// 获取挂号排班时间
export function getRegDate(query) {
    return service({
        url: '/Registration/date',
        method: 'get',
        params: query
    })
}

// 获取排班医生
export function getDoctors(query) {
    return service({
        url: '/registration/schedule',
        method: 'get',
        params: query
    })
}

/**
 * 订单
 */

// 生成订单 _ 挂号
export function createRegOrder(data) {
    return service({
        url: '/registration/lock',
        method: 'post',
        data
    })
}

// 生成订单 _ 其它业务 _ 通用
export function createPublicOrder(query) {
    // serviceType对应接口文档的"功能模块枚举值"
    let serviceType = {
        '3': '/registration/order/fetch',   // 加号取号
        '4': '/payment/order/outpatient',   // 门诊缴费
        '5': '/payment/order/inpatient'     // 住院缴费
    };
    let apiUrl = serviceType[query.serviceType];
    delete query.serviceType;
    return service({
        url: apiUrl,
        method: 'post',
        params: query
    })
}

// 生成订单_二维码
export function createPayQrcode(query) {
    // payMode对应接口文档的"支付方式枚举值"
    let payMode = {
        '1': '/order/qr/wechat', 
        '2': '/order/qr/alipay',
        '3': ''
    };
    let apiUrl = payMode[query.payMode];
    delete query.payMode;
    return service({
        url: apiUrl,
        method: 'post',
        params: query
    })
}

// 查询订单支付状态/订单结果
export function checkPayStatus(query) {
    // serviceType对应接口文档的"功能模块枚举值"
    let serviceType = {
        '2': '/query/registration',         // 预约挂号
        '3': '/query/fetch',                // 加号取号
        '4': '/query/outpatient',           // 门诊缴费
        '5': '/query/inpatient'             // 住院缴费
    };
    let apiUrl = serviceType[query.serviceType];
    delete query.serviceType;
    return service({
        url: apiUrl,
        method: 'post',
        params: query
    })
}