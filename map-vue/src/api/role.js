import request from '@/utils/request'

export function roleIndex(data) {
    return request({
        url: 'roleIndex',
        method: 'post',
        data
    })
}
  
export function roleDelete(data) {
  return request({
    url: 'roleDelete',
    method: 'post',
    data
  })
}

export function roleSave(data) {
    return request({
      url: 'roleSave',
      method: 'post',
      data
    })
  }

  export function roleUpdate(data) {
    return request({
      url: 'roleUpdate',
      method: 'post',
      data
    })
}

export function roleInfo(data) {
    return request({
      url: 'roleInfo',
      method: 'post',
      data
    })
  }
