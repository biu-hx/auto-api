import service from '../../js/request'

/**
 * --------排对候诊-------------
 */

// 候诊信息查询
export function getWaitInfos(query) {
    return service({
        url: '/registration/getwait',
        method: 'post',
        data: query
    })
}

/**
 * --------B超预约-------------
 */

// 获取B超提示信息
export function getCheckCaution(query) {
    return service({
        url: '/typeb/getCheckCaution',
        method: 'post',
        data: query
    })
}

// 获取病人patient_id
export function getPatientInfo(query) {
    return service({
        url: '/typeb/getPatientInfo',
        method: 'post',
        data: query
    })
}

// 获取可预约时间
export function getCheckCalendar(query) {
    return service({
        url: '/typeb/getCheckCalendar',
        method: 'post',
        data: query
    })

}

/**
 * --------挂号预约-------------
 * 
 */

// 获取挂号时段
export function getDoctorTimeSlot(query) {
    return service({
        url: '/Registration/time',
        method: 'post',
        data: query
    })
}