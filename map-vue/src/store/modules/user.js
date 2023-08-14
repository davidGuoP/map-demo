import { login, logout, getInfo } from '@/api/user'
import { getToken, setToken, getGuid, setGuid, removeToken, removeGuid } from '@/utils/auth'
import { resetRouter } from '@/router'

const getDefaultState = () => {
  return {
    token: getToken(),
    name: '',
    avatar: '',
    menus: [],
    actions: [],
  }
}

const state = getDefaultState()

const mutations = {
  RESET_STATE: (state) => {
    Object.assign(state, getDefaultState())
  },
  SET_TOKEN: (state, token) => {
    state.token = token
  },
  SET_NAME: (state, name) => {
    state.name = name
  },
  SET_MENU: (state, menus) => {
    state.menus = menus
  },
  SET_ACTION: (state, actions) => {
    state.actions = actions
  }
}

const actions = {
  // user login
  login({ commit }, userInfo) {
    return new Promise((resolve, reject) => {
      login(userInfo).then(response => {
        commit('SET_TOKEN', response.token)
        setToken(response.token)
        setGuid(response.guid)
        resolve(response)
      }).catch(error => {
        reject(error)
      })
    })
  },

  // get user info
  getInfo({ commit, state }) {
    return new Promise((resolve, reject) => {
      getInfo({ guid: getGuid(), token: getToken() }).then(response => {
        console.log("response", response)
        if (response) {
          // 真实姓名
          commit('SET_NAME', response.name)
          // 菜单栏选项
          let menus = response.role_action.filter(item => parseInt(item.type) === 1)
          menus = menus.map(item => item.action)
          response.menus = menus
          commit('SET_MENU', menus)
          // 用户所拥有的操作动作
          let actions = response.role_action.filter(item => parseInt(item.type) === 2)
          actions = actions.map(item => item.action)
          commit('SET_ACTION', actions)
          console.log(222222222222, actions)
          resolve(response)
        }
      }).catch(error => {
        reject(error)
      })
    })
  },

  // user logout
  logout({ commit, state }) {
    return new Promise((resolve, reject) => {
      removeToken() // must remove  token  first
      removeGuid()
      resetRouter()
      commit('RESET_STATE')
      resolve()
    })
  },

  // remove token
  resetToken({ commit }) {
    return new Promise(resolve => {
      removeToken() // must remove  token  first
      commit('RESET_STATE')
      resolve()
    })
  }
}

export default {
  namespaced: true,
  state,
  mutations,
  actions
}

