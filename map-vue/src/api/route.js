import request from '@/utils/request'

export function routeIndex(data) {
    return request({
        url: 'routeIndex',
        method: 'post',
        data
    })
}

export function routeSave(data) {
    return request({
      url: 'routeSave',
      method: 'post',
      data
    })
  }

  export function routeUpdate(data) {
    return request({
      url: 'routeUpdate',
      method: 'post',
      data
    })
}

export function routeInfo(data) {
    return request({
      url: 'routeInfo',
      method: 'post',
      data
    })
  }

export function routeDelete(data) {
  return request({
    url: 'routeDelete',
    method: 'post',
    data
  })
}
