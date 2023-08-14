import request from '@/utils/request'

export function actionIndex(data) {
    return request({
        url: 'actionIndex',
        method: 'post',
        data
    })
}

export function actionList(data) {
    return request({
        url: 'actionList',
        method: 'post',
        data
    })
}
  
export function actionParentSave(data) {
  return request({
    url: 'actionParentSave',
    method: 'post',
    data
  })
}
  
export function actionDelete(data) {
    return request({
        url: 'actionDelete',
        method: 'post',
        data
    })
}

export function actionChildrenSave(data) {
    return request({
        url: 'actionChildrenSave',
        method: 'post',
        data
    })
}


export function actionChildrenUpdate(data) {
    return request({
        url: 'actionChildrenUpdate',
        method: 'post',
        data
    })
}


  