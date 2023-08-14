<template>
  <div class="app-container">
    <el-card class="box-card-box">
          <el-card class="box-card">
              <div slot="header" class="clearfix">
              <span>数字通道</span>
              </div>
              {{ result.route_count || 0 }}
          </el-card>

          <el-card class="box-card">
              <div slot="header" class="clearfix">
              <span>数字电缆</span>
              </div>
              {{ result.line_count || 0 }}
          </el-card>

          <el-card class="box-card">
              <div slot="header" class="clearfix">
              <span>柱状标识器</span>
              </div>
              {{ result.bar_count || 0 }}
          </el-card>

          <el-card class="box-card">
              <div slot="header" class="clearfix">
              <span>片状标识器</span>
              </div>
              {{ result.volume_count || 0 }}
          </el-card>
      </el-card>

    <el-card style="margin-top: 20px;">
      <div slot="header" class="clearfix">
        <span>片状标识器</span>
        <!--导出用户信息excel表格-->
        <div style="float: right;" type="primary" size="mini">
          <download-excel
            :data="tableData"
            :fields="jsonFields"
            name="片状标识器列表.xls">
            <el-button type="primary" size="mini" :disabled="tableData.length <= 0" v-permission="['volume-download']">导出</el-button>
          </download-excel>
        </div>
        <el-button style="float: right;margin-right: 10px;" type="primary" size="mini" @click="to('add', 0)" v-permission="['volume-add']">新建片状标识器</el-button>
      </div>
      <el-form ref="form" :model="form" label-width="100px">
        <el-form-item label="标识器名称">
          <el-input v-model="form.name" style="width:200px;margin-right: 20px;"></el-input>
          <el-button type="success" size="mini" @click="init">查询</el-button>
        </el-form-item>
      </el-form>

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
        <el-table-column label="标识器编号" prop="code" min-width="60" align="center" />
        <el-table-column label="标识器名称" prop="name" min-width="60" align="center" />
        <el-table-column label="绑定电缆" prop="line_name" min-width="60" align="center" />
        <el-table-column label="绑定设备类型" prop="line_type_name" min-width="60" align="center" />
        <el-table-column label="经纬度" prop="longitude" min-width="60" align="center" />
        <el-table-column align="center" label="操作" min-width="40">
          <template slot-scope="scope">
            <el-button
              v-permission="['volume-edit']"
              size="mini"
              type="primary"
              icon="el-icon-edit"
              @click="to('edit', scope.row.id)"
            />
            <el-button
              v-permission="['volume-file-download']"
              v-if="scope.row.file_path"
              size="mini"
              type="info"
              icon="el-icon-upload"
              @click="down(scope.row.file_path)"
            />
            <el-button
              v-permission="['volume-delete']"
              size="mini"
              type="danger"
              icon="el-icon-delete"
              @click="del(scope.row.id)"
            />
          </template>
        </el-table-column>
      </el-table>
    </el-card>
  </div>
</template>

<script>
import { downloadFile, indexCount } from '@/api/webname'
import { volumeIndex, volumeDelete } from '@/api/volume'

export default {
  name: 'Route',
  data() {
    return {
      form: {
        name: ''
      },
      jsonFields: {
        "id": "id",
        "标识器编号": "code",
        "标识器名称": "name",    // 常规字段
        "绑定电缆":"line_name",
        "绑定设备类型":"line_type_name",
        "经纬度":"longitude",
      },
      tableData: [],
      result: {}
    }
  },
  created() {
    this.init()
  },
  methods: {
    init() {
      indexCount().then(res => {
        this.result = res.data
      })
      volumeIndex({
        name: this.form.name
      }).then(res => {
        this.tableData = res.data.map(item => {
          item.line_type_name = '电缆本体'
          return item
        })
      })
    },
    to(type, id) {
      this.$router.push({
        path: '/line/volume-form',
        query: {
          type,
          id
        }
      })
    },
    del(id) {
      this.$confirm('确定要删除吗？', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'error'
      }).then(() => {
        volumeDelete({id}).then(res => {
          this.$message.success('删除成功')
          this.init()
        })
      })
    },
    down(path) {
      downloadFile({
        name: path
      }).then()
    }
  }
}
</script>

<style lang="scss" scoped>
::v-deep .box-card-box .el-card__body{
  display: flex;
  .box-card {
    flex: 1;
    margin-right: 20px;
  }
}
</style>