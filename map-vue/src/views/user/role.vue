<template>
  <div class="app-container">
    <el-card class="box-card">
      <div slot="header" class="clearfix">
        <span>账号分组</span>
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
        <el-table-column label="id" prop="id" min-width="90%" align="center">
        </el-table-column>
        <el-table-column label="角色名称" prop="name" min-width="60" align="center" />
        <el-table-column align="center" label="操作" min-width="40">
          <template slot-scope="scope">
            <el-button
              size="mini"
              type="primary"
              icon="el-icon-edit"
              @click="edit(scope.row)"
            />
            <el-button
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
    <el-dialog :title="type === 'add' ? '添加角色' : '编辑角色'" :visible.sync="visible" width="40%">
      <el-form ref="formRef" :model="form" :rules="rules" label-width="80px">
        <el-form-item label="角色名称" prop="name">
          <el-input v-model="form.name" placeholder="请输入角色名称"></el-input>
        </el-form-item>
      </el-form>
      <el-card class="box-card">
        <el-checkbox :indeterminate="isIndeterminate" v-model="checkAll" @change="handleCheckAllChange">全选</el-checkbox>
        <div style="margin: 15px 0;"></div>
        <el-checkbox-group v-model="checkedCities" @change="handleCheckedCitiesChange">
            <template v-for="(item, index) in data">
                <div :key="index">
                    <div v-if="item.level === 0">
                        <el-checkbox :label="item.id">{{ item.name }}</el-checkbox>
                    </div>
                    <div style="display:flex;flex-wrap: wrap;">
                        <template v-for="(v, k) in data">
                            <div :key="k" v-if="v.level === 1 && item.id === v.parent_id" style="padding-left: 30px;">
                                <el-checkbox  :label="v.id">
                                    {{ v.name }}
                                    <el-tag size="mini" :type="['', 'warning', 'success', ''][v.type] || ''">{{ ['', '菜单权限', '按钮权限', '模块名称'][v.type] || '' }}</el-tag>
                                </el-checkbox>
                            </div>
                        </template>
                    </div>
                </div>
            </template>
        </el-checkbox-group>
      </el-card>
      <div slot="footer" class="dialog-footer">
        <el-button @click="visible = !visible">取 消</el-button>
        <el-button type="primary" @click="addSubmitHandel">确 定</el-button>
      </div>
    </el-dialog>
    <!-- 添加 end -->
  </div>
</template>

<script>
import { actionList } from '@/api/roleAction'
import { roleIndex, roleSave, roleDelete, roleUpdate, roleInfo } from '@/api/role'

export default {
  name: 'role',
  data() {
    return {
      visible: false,
      form: {
        name: '',
      },
      type: '',
      id: '',
      rules: {
        name: [
          { required: true, message: '请输入权限模块', trigger: 'blur' }
        ]
      },
      data: [
        // {
        //   label: '一级 1',
        //   children: [{
        //     label: '二级 1-1',
        //     children: [{
        //       label: '三级 1-1-1'
        //     }]
        //   }]
        // }
      ],
      defaultProps: {
        children: 'children',
        label: 'name'
      },
      checkAll: false,
      isIndeterminate: true,
      checkedCities: [],
      /** 全局变量 **/
      fullScreenLoading: false,
      tableData: []
    };
  },
  created() {
    this.init()
  },
  methods: {
    init() {
      actionList().then(res => {
        this.data = res
      })
      roleIndex().then(res => {
        this.tableData = res.data
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
    handleCheckAllChange(val) {
      this.checkedCities = val ? this.data.map(item => item.id) : [];
      this.isIndeterminate = false;
    },
    handleCheckedCitiesChange() {},
    addSubmitHandel() {
      if (this.checkedCities.length <= 0) {
        this.$message.warning('请选择权限')
        return
      }
      this.$refs['formRef'].validate(valid => {
        if (valid) {
          let param = this.form
          // 组装参数
          param.actions = this.checkedCities
          if (this.type === 'edit') {
            param.id = this.id
          }
          const api = this.type === 'add' ? roleSave : roleUpdate
          api.call(this, param).then(response => {
            this.visible = !this.visible
            this.$message.success('更新成功')
            this.init()
          })
        }
      })
    },
    edit({ id, name}) {
      // 请求当时接口
      roleInfo({id}).then(res => {
        this.visible = !this.visible
        this.type = 'edit'
        this.id = id
        this.form.name = name
        if (res) {
          this.checkedCities = res.map(item => item.role_action_id)
        }
      })
    },
    del(id) {
      this.$confirm('确定要删除角色吗？', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'error'
      }).then(() => {
        roleDelete({id}).then(res => {
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