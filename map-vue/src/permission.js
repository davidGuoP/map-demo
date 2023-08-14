import router from './router'
import store from './store'
import { Message } from 'element-ui'
import NProgress from 'nprogress' // progress bar
import 'nprogress/nprogress.css' // progress bar style
import { getToken } from '@/utils/auth' // get token from cookaie
import getPageTitle from '@/utils/get-page-title'
import { each } from 'underscore'

NProgress.configure({ showSpinner: false }) // NProgress Configuration

const whiteList = ['/login'] // no redirect whitelist

router.beforeEach(async(to, from, next) => {
  // start progress bar
  NProgress.start()

  // set page title
  document.title = getPageTitle(to.meta.title)

  // determine whether the user has logged in
  const hasToken = getToken()

  if (hasToken) {
    if (to.path === '/login') {
      // if is logged in, redirect to the home page
      next({ path: '/' })
      NProgress.done()
    } else {
      const hasGetUserInfo = store.getters.name
      // get user info
      console.log("store.getters.name", hasGetUserInfo, store.getters.name)
      next()
      if (!hasGetUserInfo) {
        try {
          // 刷新之后 状态丢失
          store.dispatch('user/getInfo').then(res => {
            const menu = res.menus
            store.dispatch('GenerateRoutes', { menu }).then(() => {
              const arrRoute = store.getters.addRouters
              each(arrRoute, (item, index) => {
                const route = arrRoute[index]
                const redirectObj = route.children[0] || {}
                if (route.redirect) {
                  if (redirectObj.path) {
                    let redirectStr = redirectObj.path
                    if (route.path !== '/') {
                      redirectStr = '/' + redirectObj.path
                    }
                    route.redirect = route.path + redirectStr
                  } else {
                    route.redirect = '/line'
                  }
                }
              })
              console.log(arrRoute)
              router.addRoutes(arrRoute)
              router.addRoutes([{ path: '*', redirect: '/404', hidden: true }])
            })
          })
          // get user info
          // await store.dispatch('user/getInfo')
 
        } catch (error) {
          // remove token and go to login page to re-login
          await store.dispatch('user/resetToken')
          Message.error(error || 'Has Error')
          next(`/login?redirect=${to.path}`)
          NProgress.done()
        }
      }
    }
  } else {
    /* has no token*/

    if (whiteList.indexOf(to.path) !== -1) {
      // in the free login whitelist, go directly
      next()
    } else {
      // other pages that do not have permission to access are redirected to the login page.
      next(`/login?redirect=${to.path}`)
      NProgress.done()
    }
  }
})

router.afterEach(() => {
  // finish progress bar
  NProgress.done()
})
