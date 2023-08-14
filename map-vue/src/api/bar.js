import request from '@/utils/request'

export function barIndex(data) {
    return request({
        url: 'barIndex',
        method: 'post',
        data
    })
}

export function barSave(data) {
    return request({
      url: 'barSave',
      method: 'post',
      data
    })
  }

  export function barUpdate(data) {
    return request({
      url: 'barUpdate',
      method: 'post',
      data
    })
}

export function barInfo(data) {
    return request({
      url: 'barInfo',
      method: 'post',
      data
    })
  }

export function barDelete(data) {
  return request({
    url: 'barDelete',
    method: 'post',
    data
  })
}
