import store from '@/store';

export const permission = {
  inserted(el, binding,) {
    const { value } = binding;
    console.log("actions", store.getters.actions)
    const actions = store.getters && store.getters.actions;
    if (value && value instanceof Array && value.length > 0) {
      const permissionRoles = value;
      let hasPermission = actions.some(role => {
        return permissionRoles.includes(role)
      })

      // 管理员不受权限管控
      if (store.getters.name === 'admin') {
        hasPermission = true
      }

      if (!hasPermission) {
        el.parentNode && el.parentNode.removeChild(el)
      }
    } else {
      throw new Error(`need roles! Like v-permission="['admin','editor']"`)
    }
  }
}

// 批量注册指令(现在就一个permission)
const directives = {
    permission
}
//注册的一般写法，循环遍历directives，通过vue.directive注册
export default {
    install(Vue){
        Object.keys(directives).forEach(key=>{
            Vue.directive(key,directives[key])
        })
    }
}
