<template>
  <div class="app-container">
    <el-card class="box-card">
      <div slot="header" class="clearfix">
        <span>账号列表</span>
        <el-button style="float: right;" type="primary" size="mini"  @click="addHandle()">添加</el-button>
      </div>

      <el-table
        v-loading.fullscreen.lock="fullScreenLoading"
        :data="tableData"
        element-loading-text="Loading"
        border
        fit
        highlight-current-row
      >
        <el-table-column align="center" label="序号" width="50">
          <template slot-scope="scope">
            {{ scope.$index + 1 }}
          </template>
        </el-table-column>
        <el-table-column label="id" prop="id" min-width="90%" align="center" />
        <el-table-column label="用户名" prop="username" min-width="60" align="center" />
        <el-table-column label="角色名" prop="role_name" min-width="60" align="center" />
        <el-table-column label="最后登陆时间" prop="login_time" min-width="60" align="center" />
        <el-table-column align="center" label="操作" min-width="40">
          <template slot-scope="scope">
            <el-button
              size="mini"
              type="primary"
              icon="el-icon-edit"
              @click="edit(scope.row)"
            />
            <el-button
              v-if="scope.row.username !== 'admin'"
              size="mini"
              type="danger"
              icon="el-icon-delete"
              @click="del(scope.row.id)"
            />
          </template>
        </el-table-column>
      </el-table>
    </el-card>

    <!-- 添加 start -->
    <el-dialog :title="type === 'add' ? '新建账号' : '编辑账号'" :visible.sync="visible" width="40%">
      <el-form ref="formRef" :model="form" :rules="rules" label-width="80px">
        <el-form-item label="用户名" prop="name">
          <el-input v-model="form.name" placeholder="请输入用户名" :disabled="form.name === 'admin'"></el-input>
        </el-form-item>
        <el-form-item label="密码" prop="password">
          <el-input type="password" v-model="form.password" placeholder="请输入密码"></el-input>
        </el-form-item>
        <el-form-item label="角色" prop="roleId" v-if="form.name !== 'admin'">
          <el-select v-model="form.roleId" placeholder="请选择角色">
            <template v-for="(item, index) in roleData">
              <el-option :key="index" :label="item.name" :value="item.id"></el-option>
            </template>
          </el-select>
        </el-form-item>
      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button @click="visible = !visible">取 消</el-button>
        <el-button type="primary" @click="addSubmitHandel">确 定</el-button>
      </div>
    </el-dialog>
    <!-- 添加 end -->
  </div>
</template>

<script>
import { roleIndex } from '@/api/role'
import { adminIndex, adminSave, adminUpdate, adminDelete } from '@/api/user'

export default {
  name: 'role',
  data() {
    return {
      visible: false,
      form: {
        name: '',
        password: '',
        roleId: '',
      },
      type: '',
      id: '',
      rules: {
        name: [
          { required: true, message: '请输入用户名', trigger: 'blur' }
        ],
        password: [
          { required: true, message: '请输入密码', trigger: 'blur' }
        ],
        roleId: [
          { required: true, message: '请选择角色', trigger: 'blur' }
        ]
      },
      /** 全局变量 **/
      fullScreenLoading: false,
      tableData: [],
      roleData: []
    };
  },
  created() {
    this.init()
  },
  methods: {
    init() {
      roleIndex().then(res => {
        this.roleData = res.data
      })
      adminIndex().then(res => {
        this.tableData = res.data.map(item => {
          if (item.login_time) {
            item.login_time = this.dateFormat(item.login_time)
          }
          if (item.username === 'admin') {
            item.role_name = '超级管理员'
          }
          return item
        })
      })
    },
    addHandle() {
      this.visible = !this.visible
      this.type = 'add'
      this.id = ''
      this.$nextTick(() => {
        this.$refs['formRef'].resetFields()
        this.$refs['formRef'].clearValidate()
      })
    },
    dateFormat(time) {
      const str = parseInt(time) * 1000
      var newDate = new Date(str)
      var year = newDate.getUTCFullYear()
      var month = newDate.getUTCMonth() + 1
      var nowday = newDate.getUTCDate()
      var hours = newDate.getHours()
      var minutes = newDate.getMinutes()
      var seconds = newDate.getSeconds()
      return year + "-" + month + "-" + nowday + " " + hours + ":" + minutes + ":" + seconds;//拼接 2017-2-21 12:23:43 }
    },
    addSubmitHandel() {
      this.$refs['formRef'].validate(valid => {
        if (valid) {
          let param = this.form
          // 组装参数
          if (this.type === 'edit') {
            param.id = this.id
          }
          console.log(param)
          const api = this.type === 'add' ? adminSave : adminUpdate
          api.call(this, param).then(response => {
            this.visible = !this.visible
            this.$message.success('更新成功')
            this.init()
          })
        }
      })
    },
    edit({ id, username, role_id: roleId}) {
      this.visible = !this.visible
      this.type = 'edit'
      this.id = id
      this.form.name = username
      this.form.roleId = roleId
    },
    del(id) {
      this.$confirm('确定要删除角色吗？', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'error'
      }).then(() => {
        adminDelete({id}).then(res => {
          this.$message.success('删除成功')
          this.init()
        })
      })
    },
  }
}
</script>

<style lang="scss" scoped>
::v-deep .el-checkbox {
    margin: 4px 0;
}
</style>