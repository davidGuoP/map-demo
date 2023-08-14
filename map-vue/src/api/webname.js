import request from '@/utils/request'

export function webNameInfo(data) {
  return request({
    url: 'webNameInfo',
    method: 'post',
    data
  })
}

export function webNameSave(data) {
    return request({
        url: 'webNameSave',
        method: 'post',
        data
    })
}

export function downloadFile(data) {
  return request({
      url: 'download',
      method: 'post',
      config: {
        headers: {
          responseType: "blob"
        }
      },
      data
  })
}

export function indexCount(data) {
  return request({
      url: 'indexCount',
      method: 'post',
      data
  })
}

export function list(data) {
  return request({
      url: 'list',
      method: 'post',
      data
  })
}
