<template>
  <div>
    <el-scrollbar wrap-class="scrollbar-wrapper">
      <el-menu
        :default-active="activeMenu"
        :background-color="variables.menuBg"
        :text-color="variables.menuText"
        :unique-opened="false"
        :active-text-color="variables.menuActiveText"
        :collapse-transition="false"
        mode="vertical"
      >
        <sidebar-item v-for="route in routes" :key="route.path" :item="route" :base-path="route.path" />
      </el-menu>
    </el-scrollbar>
  </div>
</template>

<script>
import { mapGetters } from 'vuex'
import Logo from './Logo'
import SidebarItem from './SidebarItem'
import variables from '@/styles/variables.scss'

export default {
  components: { SidebarItem, Logo },
  computed: {
    ...mapGetters([
      'sidebar'
    ]),
    routes() {
      console.log("123123", this.$store.getters.routers)
      // let routes = this.$router.options.routes
      let routes = this.$store.getters.routers
      const tempRoutes = []
      if (this.$route.path.includes('system') || this.$route.path.includes('user')) {
        routes.forEach((item) => {
          if (!['/line', '/map'].includes(item.path) && item.path !== '/') {
            tempRoutes.push(item)
          }
        })
      }
      if (this.$route.path.includes('line')) {
        routes.forEach((item) => {
          if (!['/system', '/user', '/map'].includes(item.path) && item.path !== '/') {
            tempRoutes.push(item)
          }
        })
      }
      if (this.$route.path.includes('map')) {
        routes.forEach((item) => {
          if (!['/line', '/system', '/user'].includes(item.path)) {
            tempRoutes.push(item)
          }
        })
      }
      console.log(this.$route.path, tempRoutes)
      console.log("this.$router.options.routes", this.$router.options.routes)
      return tempRoutes
    },
    activeMenu() {
      const route = this.$route
      const { meta, path } = route
      // if set path, the sidebar will highlight the path you set
      if (meta.activeMenu) {
        return meta.activeMenu
      }
      return path
    },
    showLogo() {
      return this.$store.state.settings.sidebarLogo
    },
    variables() {
      return variables
    },
    isCollapse() {
      return !this.sidebar.opened
    }
  }
}
</script>
