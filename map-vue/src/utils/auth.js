import Cookies from 'js-cookie'

const TokenKey = 'token'

export function getToken() {
  return Cookies.get(TokenKey)
}

export function setToken(token) {
  return Cookies.set(TokenKey, token)
}

export function setGuid(guid) {
  return Cookies.set('guid', guid)
}

export function getGuid() {
  return Cookies.get('guid')
}

export function removeToken() {
  return Cookies.remove(TokenKey)
}

export function removeGuid() {
  return Cookies.remove('guid')
}
