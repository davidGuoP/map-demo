<template>
  <div :class="classObj" class="app-wrapper">
    <div style="padding-left: 20px;box-sizing: border-box;height: 50px;line-height: 50px;width: 100%;background:#304156;display:flex;justify-content: space-between;align-items: center;">
      <div style="display:flex;">
        <div style="width: 80px;color: #fff;cursor: pointer;" v-if="isMap" @click="$router.push('/map')" :style="{color: this.$route.path.includes('map') ? '#409eff' : '#fff'}">地图</div>
        <div style="width: 80px;color: #fff;cursor: pointer;" v-if="isLine" @click="$router.push('/line')" :style="{color: this.$route.path.includes('line') ? '#409eff' : '#fff'}">台账</div>
        <div v-if="guid === 'a727d3819e4c9bb19d3c93ccdad81eff'" @click="$router.push('/user')" style="width: 80px;cursor: pointer;" :style="{color: (this.$route.path.includes('system') || this.$route.path.includes('user')) ? '#409eff' : '#fff'}">系统设置</div>
      </div>
      <div class="right-menu">
        <el-dropdown class="avatar-container" trigger="click">
          <div class="avatar-wrapper">
            <img src="@/assets/404_images/icon-avator.png" width="32" alt="404">
            <span style="color: #fff;">{{ name }}</span>
            <i class="el-icon-caret-bottom" />
          </div>
          <el-dropdown-menu slot="dropdown" class="user-dropdown">
            <el-dropdown-item @click.native="editPwd">
              <span style="display:block;">修改密码</span>
            </el-dropdown-item>
            <el-dropdown-item divided @click.native="logout">
              <span style="display:block;">退出</span>
            </el-dropdown-item>
          </el-dropdown-menu>
        </el-dropdown>
      </div>
    </div>
    <!-- <navbar /> -->
    <div class="main-container" :class="isMapRoute ? 'hide_left' : '' ">
      <sidebar class="sidebar-container" v-if="!isMapRoute" />
      <!-- <div :class="{'fixed-header':fixedHeader}">
      </div> -->
      <app-main />
    </div>

    <el-dialog
      title="修改密码"
      :visible.sync="dialogVisible"
      width="30%">
      <el-form ref="form" :model="form" :rules="rules" label-width="80px">
        <el-form-item label="原始密码" prop="oldPassword">
          <el-input type="password" v-model="form.oldPassword"></el-input>
        </el-form-item>
        <el-form-item label="修改密码" prop="newPassword">
          <el-input type="password" v-model="form.newPassword"></el-input>
        </el-form-item>
      </el-form>
      <span slot="footer" class="dialog-footer">
        <el-button @click="dialogVisible = false">取 消</el-button>
        <el-button type="primary" @click="submit">确 定</el-button>
      </span>
    </el-dialog>
  </div>
</template>

<script>
import { Navbar, Sidebar, AppMain } from './components'
import ResizeMixin from './mixin/ResizeHandler'
import { getGuid } from '@/utils/auth'

export default {
  name: 'Layout',
  components: {
    Navbar,
    Sidebar,
    AppMain,
  },
  data() {
    return {
      dialogVisible: false,
      form: {
        oldPassword: '',
        newPassword: ''
      },
      rules: {
        oldPassword: [
          { required: true, message: '请输入旧密码', trigger: 'blur' },
          { min: 6, max: 20, message: '长度在6到20个字符', trigger: 'blur' }
        ],
        newPassword: [
          { required: true, message: '请输入新密码', trigger: 'change' },
          { min: 6, max: 20, message: '长度在6到20个字符', trigger: 'blur' }
        ],
      },
      guid: getGuid()
    }
  },
  mixins: [ResizeMixin],
  computed: {
    isMap() {
      let isMap = false
      this.$store.getters.routers.forEach(item => {
        if (item.children) {
          item.children.forEach(v => {
            if (v.path === 'map') {
              isMap = true
            }
          })
        }
      })
      return isMap
    },
    isLine() {
      let isLine = false
      this.$store.getters.routers.forEach(item => {
        if (item.children) {
          item.children.forEach(v => {
            if (['route', 'line', 'bar', 'volume'].includes(v.path)) {
              isLine = true
            }
          })
        }
      })
      return isLine
    },
    isMapRoute() {
      return this.$route.path === '/map'
    },
    name() {
      return this.$store.state.user.name
    },
    sidebar() {
      return this.$store.state.app.sidebar
    },
    device() {
      return this.$store.state.app.device
    },
    fixedHeader() {
      return this.$store.state.settings.fixedHeader
    },
    classObj() {
      return {
        hideSidebar: !this.sidebar.opened,
        openSidebar: this.sidebar.opened,
        withoutAnimation: this.sidebar.withoutAnimation,
        mobile: this.device === 'mobile'
      }
    }
  },
  methods: {
    handleClickOutside() {
      this.$store.dispatch('app/closeSideBar', { withoutAnimation: false })
    },
    async logout() {
      await this.$store.dispatch('user/logout')
      this.$router.push(`/login?redirect=${this.$route.fullPath}`)
    },
    editPwd() {
      this.dialogVisible = !this.dialogVisible
      this.$nextTick(() => {
        this.$refs['form'].resetFields()
        this.$refs['form'].clearValidate()
      })
    },
    submit() {
      this.$refs.form.validate((valid) => {
        if (valid) {
          const param = this.form
          param.guid = getGuid()
          editPassword(param).then(res => {
            this.dialogVisible = false
            this.$message.success('修改密码成功,即将跳转登陆')
            setTimeout(() => {
              this.logout()
            }, 1000);
          })
        }
      })
    }
  }
}
</script>

<style lang="scss" scoped>
  @import "~@/styles/mixin.scss";
  @import "~@/styles/variables.scss";

  .app-wrapper {
    @include clearfix;
    position: relative;
    height: 100%;
    width: 100%;
    &.mobile.openSidebar{
      position: fixed;
      top: 0;
    }
  }
  .drawer-bg {
    background: #000;
    opacity: 0.3;
    width: 100%;
    top: 0;
    height: 100%;
    position: absolute;
    z-index: 999;
  }

  .fixed-header {
    position: fixed;
    top: 0;
    right: 0;
    z-index: 9;
    width: calc(100% - #{$sideBarWidth});
    transition: width 0.28s;
  }

  .hideSidebar .fixed-header {
    width: calc(100% - 54px)
  }

  .mobile .fixed-header {
    width: 100%;
  }

.right-menu {
    height: 50px;
    line-height: 50px;

    &:focus {
      outline: none;
    }

    .right-menu-item {
      display: inline-block;
      padding: 0 8px;
      height: 100%;
      font-size: 18px;
      color: #5a5e66;
      vertical-align: text-bottom;

      &.hover-effect {
        cursor: pointer;
        transition: background .3s;

        &:hover {
          background: rgba(0, 0, 0, .025)
        }
      }
    }

    .avatar-container {
      margin-right: 30px;

      .avatar-wrapper {
        margin-top: 5px;
        position: relative;

        .user-avatar {
          cursor: pointer;
          width: 40px;
          height: 40px;
          border-radius: 10px;
        }

        .el-icon-caret-bottom {
          cursor: pointer;
          position: absolute;
          right: -20px;
          top: 25px;
          font-size: 12px;
        }
      }
    }
  }
</style>
