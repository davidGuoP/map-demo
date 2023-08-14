<template>
  <div class="app-container">
    <el-card class="box-card">
      <div slot="header" class="clearfix">
        <span>权限菜单</span>
        <el-button style="float: right;" type="primary" size="mini"  @click="addHandle()">添加一级权限模块</el-button>
      </div>
      <el-table
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
        <el-table-column label="权限名称" prop="name" min-width="60" align="center" />
        <el-table-column label="类型" min-width="60">
          <template slot-scope="scope">
            <el-tag size="mini" :type="['', 'warning', 'success', ''][scope.row.type] || ''">{{ ['', '菜单权限', '按钮权限', '模块名称'][scope.row.type] || '' }}</el-tag>
          </template>
        </el-table-column>
        <el-table-column align="center" label="操作" min-width="40">
          <template slot-scope="scope">
            <el-button
              v-if="scope.row.type === 3"
              type="text"
              size="mini"
              @click="init(2, scope.row.id)">
              下级菜单
            </el-button>
            <el-button
              v-if="scope.row.type !== 3"
              type="text"
              size="mini"
              @click="init(3)">
              上级菜单
            </el-button>
            <el-button
              v-if="scope.row.type === 3"
              type="text"
              size="mini"
              @click="() => update(scope.row)">
              编辑
            </el-button>
            <el-button
              v-else
              type="text"
              size="mini"
              @click="() => edit(scope.row)">
              编辑
            </el-button>
            <el-button
              v-if="scope.row.type === 3"
              type="text"
              size="mini"
              @click="() => append(scope.row)">
              添加二级权限
            </el-button>
            <el-button
              type="text"
              size="mini"
              @click="() => remove(node, data)">
              删除
            </el-button>
          </template>
        </el-table-column>
      </el-table>
    </el-card>

    <!-- 添加 start -->
    <el-dialog :title="oneType === 'add' ? '添加一级权限模块' : '编辑一级权限模块'" :visible.sync="visible" width="40%">
      <el-form ref="formRef" :model="form" :rules="rules" label-width="120px">
        <el-form-item label="一级权限模块" prop="name">
          <el-input v-model="form.name" placeholder="请输入一级权限模块"></el-input>
        </el-form-item>
      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button @click="visible = !visible">取 消</el-button>
        <el-button type="primary" @click="addSubmitHandel">确 定</el-button>
      </div>
    </el-dialog>
    <!-- 添加 end -->

    <!-- 添加 start -->
    <el-dialog :title="type === 'add' ? '添加二级权限' : '编辑二级权限'" :visible.sync="actionVisible" width="40%">
      <el-form ref="actionForm" :model="actionForm" :rules="actionFormRules" label-width="100px">
        <el-form-item label="权限名称" prop="name">
          <el-input v-model="actionForm.name" placeholder="请输入权限名称"></el-input>
        </el-form-item>
        <el-form-item label="权限类型" prop="type">
          <el-select v-model="actionForm.type" placeholder="请选择权限类型">
            <el-option label="菜单权限" value="1"></el-option>
            <el-option label="按钮权限" value="2"></el-option>
          </el-select>
        </el-form-item>
        <el-form-item label="权限值" prop="action">
          <el-input v-model="actionForm.action" placeholder="请输入权限值"></el-input>
        </el-form-item>
      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button @click="actionVisible = !actionVisible">取 消</el-button>
        <el-button type="primary" @click="addHandel">确 定</el-button>
      </div>
    </el-dialog>
    <!-- 添加 end -->
  </div>
</template>

<script>
import { actionParentSave, actionIndex, actionDelete, actionChildrenSave, actionChildrenUpdate } from '@/api/roleAction'

export default {
  name: 'role',
  data() {
    return {
      visible: false,
      form: {
        name: '',
      },
      oneType: '',
      oneId: '',
      rules: {
        name: [
          { required: true, message: '请输入权限模块', trigger: 'blur' }
        ]
      },
      tableData: [],
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
      actionVisible: false,
      actionForm: {
        name: '',
        type: '',
        action: ''
      },
      id: '',
      type: '',
      actionFormRules: {
        name: [
          { required: true, message: '请输入权限名称', trigger: 'blur' }
        ],
        type: [
          { required: true, message: '请选择权限类型', trigger: 'blur' }
        ],
        action: [
          { required: true, message: '请输入权限值', trigger: 'blur' }
        ]
      },
    };
  },
  created() {
    this.init()
  },
  methods: {
    init(type = 3, id = 0) {
      const param = {
        type,
      }
      if (id) {
        param.parentId = id
      }
      actionIndex(param).then(res => {
        this.tableData = res.data
      })
    },
    addHandle() {
      this.visible = !this.visible
      this.oneType = 'add'
      this.oneId = ''
      this.form.name = ''
      this.$nextTick(() => {
        this.$refs['formRef'].resetFields()
        this.$refs['formRef'].clearValidate()
      })
    },
    update(data) {
      this.visible = !this.visible
      this.oneType = 'edit'
      this.oneId = data.id
      this.form.name = data.name
    },
    addSubmitHandel() {
      this.$refs['formRef'].validate(valid => {
        if (valid) {
          const param = this.form
          console.log(this.oneType)
          if (this.oneType === 'edit') {
            param.id = this.oneId
          }
          actionParentSave(param).then(response => {
            this.visible = !this.visible
            this.$message.success('更新成功')
            this.init()
          })
        }
      })
    },
    remove(node, data) {
      const id = data.id
      this.$confirm('确定要删除该级权限吗？', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'error'
      }).then(() => {
        actionDelete({id}).then(res => {
          this.$message.success('删除成功')
          this.init()
        })
      })
    },
    append(data) {
      this.actionVisible = !this.actionVisible
      this.id = data.id
      this.type = 'add'
      // 数据填充
      this.actionForm.name = ''
      this.actionForm.type = ''
      this.actionForm.action = ''
      this.$nextTick(() => {
        this.$refs['actionForm'].resetFields()
        this.$refs['actionForm'].clearValidate()
      })
    },
    addHandel() {
      this.$refs['actionForm'].validate(valid => {
        if (valid) {
          const param = this.actionForm
          if (this.type == 'add') {
            param.parentId = this.id
          }
          if (this.type == 'edit') {
            param.id = this.id
          }
          const api = this.type === 'add' ? actionChildrenSave : actionChildrenUpdate
          api.call(this, param).then(response => {
            this.actionVisible = !this.actionVisible
            this.$message.success('操作成功')
            this.init()
          })
        }
      })
    },
    edit(data) {
      this.actionVisible = !this.actionVisible
      this.id = data.id
      this.type = 'edit'
      // 数据填充
      this.actionForm.name = data.name
      this.actionForm.type = data.type + ''
      this.actionForm.action = data.action
    }
  }
}
</script>

<style lang="scss" scoped>
::v-deep {
  .el-tree-node__content {
    height: 30px;
  }
}
</style>