import Vue from 'vue'
import Router from 'vue-router'

Vue.use(Router)

/* Layout */
import Layout from '@/layout'

/**
 * Note: sub-menu only appear when route children.length >= 1
 * Detail see: https://panjiachen.github.io/vue-element-admin-site/guide/essentials/router-and-nav.html
 *
 * hidden: true                   if set true, item will not show in the sidebar(default is false)
 * alwaysShow: true               if set true, will always show the root menu
 *                                if not set alwaysShow, when item has more than one children route,
 *                                it will becomes nested mode, otherwise not show the root menu
 * redirect: noRedirect           if set noRedirect will no redirect in the breadcrumb
 * name:'router-name'             the name is used by <keep-alive> (must set!!!)
 * meta : {
    roles: ['admin','editor']    control the page roles (you can set multiple roles)
    title: 'title'               the name show in sidebar and breadcrumb (recommend set)
    icon: 'svg-name'/'el-icon-x' the icon show in the sidebar
    breadcrumb: false            if set false, the item will hidden in breadcrumb(default is true)
    activeMenu: '/example/list'  if set path, the sidebar will highlight the path you set
  }
 */

/**
 * constantRoutes
 * a base page that does not have permission requirements
 * all roles can be accessed
 */
export const constantRoutes = [
  {
    path: '/login',
    component: () => import('@/views/login/index'),
    hidden: true
  },
  {
    path: '/404',
    component: () => import('@/views/404'),
    hidden: true
  },
  {
    path: '/user',
    redirect: '/user/userlist',
    component: Layout,
    meta: { title: '管理账号', icon: 'el-icon-s-help' },
    children: [
      {
        path: 'userlist',
        name: 'userlist',
        component: () => import('@/views/user/list'),
        meta: { title: '账号列表', icon: 'dashboard' }
      },
      {
        path: 'role',
        name: 'role',
        component: () => import('@/views/user/role'),
        meta: { title: '账号分组', icon: 'dashboard' }
      },
      {
        path: 'action',
        name: 'action',
        component: () => import('@/views/user/action'),
        meta: { title: '权限菜单', icon: 'dashboard' }
      },
    ]
  },
  {
    path: '/system',
    component: Layout,
    redirect: '/system/webname',
    meta: { title: '基础设置', icon: 'el-icon-s-help' },
    children: [
      {
        path: 'webname',
        name: 'webname',
        component: () => import('@/views/system/webname'),
        meta: { title: '基础设置', icon: 'dashboard' }
      },
    ]
  },

  // 404 page must be placed at the end !!!
  // { path: '*', redirect: '/404', hidden: true }
]

export const asyncRouterMap = [
  {
    path: '/',
    component: Layout,
    redirect: '/map',
    children: [{
      path: 'map',
      name: 'map',
      component: () => import('@/views/map/index'),
      meta: { title: '地图', icon: 'dashboard', role: ['/map'] }
    }]
  },
  {
    path: '/line',
    component: Layout,
    redirect: '/line/route',
    meta: { title: '台账', icon: 'el-icon-s-help' },
    children: [
      {
        path: 'route',
        name: 'route',
        component: () => import('@/views/line/route'),
        meta: { title: '数字通道', icon: 'dashboard', role: ['/line/route'] }
      },
      {
        path: 'route-form',
        name: 'route-form',
        hidden: true,
        component: () => import('@/views/line/route-form'),
        meta: { title: '数字通道', icon: 'dashboard' }
      },
      {
        path: 'line',
        name: 'line',
        component: () => import('@/views/line/line'),
        meta: { title: '数字电缆', icon: 'dashboard', role: ['/line/line'] }
      },
      {
        path: 'line-form',
        name: 'line-form',
        hidden: true,
        component: () => import('@/views/line/line-form'),
        meta: { title: '数字电缆', icon: 'dashboard' }
      },
      {
        path: 'bar',
        name: 'bar',
        component: () => import('@/views/line/bar'),
        meta: { title: '柱状标识器', icon: 'dashboard', role: ['/line/bar'] }
      },
      {
        path: 'bar-form',
        name: 'bar-form',
        hidden: true,
        component: () => import('@/views/line/bar-form'),
        meta: { title: '柱状标识器', icon: 'dashboard' }
      },
      {
        path: 'volume',
        name: 'volume',
        component: () => import('@/views/line/volume'),
        meta: { title: '片状标识器', icon: 'dashboard', role: ['/line/volume'] }
      },
      {
        path: 'volume-form',
        name: 'volume-form',
        hidden: true,
        component: () => import('@/views/line/volume-form'),
        meta: { title: '片状标识器', icon: 'dashboard' }
      },
    ]
  },
]

const createRouter = () => new Router({
  // mode: 'history', // require service support
  scrollBehavior: () => ({ y: 0 }),
  routes: constantRoutes
})

const router = createRouter()

// Detail see: https://github.com/vuejs/vue-router/issues/1234#issuecomment-357941465
export function resetRouter() {
  const newRouter = createRouter()
  router.matcher = newRouter.matcher // reset router
}

export default router
