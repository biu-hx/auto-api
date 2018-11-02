import service from '../../js/request'
//获取科教视频
export function getVideoList(params) {
    return service({
        url: '/video/list',
        method: 'get',
        params
    })
}
//获取视频详情
export function getVideoDetail(params) {
    return service({
        url: '/video/detail',
        method: 'get',
        params
    })
}
//视频下单
export function getVideoOrder(data) {
    return service({
        url: '/video/order',
        method: 'post',
        data
    })
}
//视频支付查询
export function getVideoPayType(params) {
    return service({
        url: '/query/video',
        method: 'get',
        params
    })
}
//获取视频
export function getVideoUrl(params) {
    return service({
        url: '/video/getUrl',
        method: 'get',
        params
    })
}