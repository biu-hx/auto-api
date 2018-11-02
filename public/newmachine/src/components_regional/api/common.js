import service from '../../js/request'
//获取支付方式
export function getPayType(query) {
    return service({
        url: '/payment/getpay',
        method: 'post',
        data: query
    })
}
//获取微信二维码
export function getWerXinQr(query) {
    return service({
        url: '/order/qr/wechat',
        method: 'get',
        params: query
    })
}
//获取支付宝二维码
export function getAlipayQr(query) {
    return service({
        url: '/order/qr/alipay',
        method: 'get',
        params: query
    })
}

// 查询订单详情,使用场景较多
export function queryOrderDetail(params) {
    return service({
        url: '/query/order/detail',
        method: 'get',
        params
    })
}

// 设置小票打印次数
export function setPrintCount(query) {
    return service({
        url: '/order/print',
        method: 'get',
        params: query
    })
}

// 查询就诊人信息
export function queryCardInfo(query) {
    return service({
        url: '/card/patient',
        method: 'get',
        params: query
    })
}