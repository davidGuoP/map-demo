import request from '@/utils/request'

export function lineIndex(data) {
    return request({
        url: 'lineIndex',
        method: 'post',
        data
    })
}

export function lineSave(data) {
    return request({
      url: 'lineSave',
      method: 'post',
      data
    })
  }

  export function lineUpdate(data) {
    return request({
      url: 'lineUpdate',
      method: 'post',
      data
    })
}

export function lineInfo(data) {
    return request({
      url: 'lineInfo',
      method: 'post',
      data
    })
  }

export function lineDelete(data) {
  return request({
    url: 'lineDelete',
    method: 'post',
    data
  })
}
