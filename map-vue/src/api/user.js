import request from '@/utils/request'

export function login(data) {
  return request({
    url: 'login',
    method: 'post',
    data
  })
}

export function getInfo(data) {
  return request({
    url: 'info',
    method: 'post',
    data
  })
}

export function editPassword(data) {
  return request({
    url: 'editPassword',
    method: 'post',
    data
  })
}

export function logout() {
  return request({
    url: '/vue-admin-template/user/logout',
    method: 'post'
  })
}

export function adminIndex(data) {
  return request({
    url: 'adminIndex',
    method: 'post',
    data
  })
}

export function adminSave(data) {
  return request({
    url: 'adminSave',
    method: 'post',
    data
  })
}

export function adminUpdate(data) {
  return request({
    url: 'adminUpdate',
    method: 'post',
    data
  })
}

export function adminDelete(data) {
  return request({
    url: 'adminDelete',
    method: 'post',
    data
  })
}
