import request from '@/utils/request'

export function volumeIndex(data) {
    return request({
        url: 'volumeIndex',
        method: 'post',
        data
    })
}

export function volumeSave(data) {
    return request({
      url: 'volumeSave',
      method: 'post',
      data
    })
  }

  export function volumeUpdate(data) {
    return request({
      url: 'volumeUpdate',
      method: 'post',
      data
    })
}

export function volumeInfo(data) {
    return request({
      url: 'volumeInfo',
      method: 'post',
      data
    })
  }

export function volumeDelete(data) {
  return request({
    url: 'volumeDelete',
    method: 'post',
    data
  })
}
