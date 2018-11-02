import service from '../../js/request'
//获取医院
export function getHospitalData(query) {
    return service({
        url: '/consultant/hospital',
        method: 'get',
        params: query
    })
}
//获取科室
export function getDeptData(query) {
    return service({
        url: '/consultant/dept',
        method: 'get',
        params: query
    })
}
//获取医生列表
export function getDoctorData(query) {
    return service({
        url: '/consultant/doctor',
        method: 'get',
        params: query
    })
}
//获取医生详情
export function getDoctorDetail(query) {
    return service({
        url: '/consultant/detail',
        method: 'get',
        params: query
    })
}
//问诊下单
export function getOrderDetail(query) {
    return service({
        url: '/consultant/order',
        method: 'post',
        data: query
    })
}
//问诊查询
export function getConsultantType(query) {
    return service({
        url: '/query/consultant',
        method: 'get',
        params: query
    })
}
//发起咨询
export function connectConsultant(query) {
    return service({
        url: '/consultant/connect',
        method: 'post',
        data: query
    })
}
//是否接听
export function isConsultantAnswer(query) {
    return service({
        url: '/consultant/answer',
        method: 'get',
        params: query
    })
}


//测试假地址

export function isTest() {
    return service({
        url: '/test/testVideoUrl',
        method: 'get'
    })
}
