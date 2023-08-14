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
        <span>数字电缆</span>
        <!--导出用户信息excel表格-->
        <div style="float: right;" type="primary" size="mini">
          <download-excel
            :data="tableData"
            :fields="jsonFields"
            name="数字电缆列表.xls">
            <el-button type="primary" size="mini" :disabled="tableData.length <= 0" v-permission="['line-download']">导出</el-button>
          </download-excel>
        </div>
        <el-button style="float: right;margin-right: 10px;" type="primary" size="mini" @click="to('add', 0)" v-permission="['line-add']">新建数字电缆</el-button>
      </div>
      <el-form ref="form" :model="form" label-width="100px">
        <el-form-item label="数字电缆名称">
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
        <el-table-column label="电缆名称" prop="name" min-width="60" align="center" />
        <el-table-column label="所属线路" prop="line_name" min-width="60" align="center" />
        <el-table-column label="所属通道" prop="route_name" min-width="60" align="center" />
        <el-table-column label="起点" prop="start" min-width="60" align="center" />
        <el-table-column label="终点" prop="end" min-width="60" align="center" />
        <el-table-column align="center" label="操作" min-width="40">
          <template slot-scope="scope">
            <el-button
              v-permission="['line-edit']"
              size="mini"
              type="primary"
              icon="el-icon-edit"
              @click="to('edit', scope.row.id)"
            />
            <el-button
              v-permission="['line-file-download']"
              v-if="scope.row.file_path"
              size="mini"
              type="info"
              icon="el-icon-upload"
              @click="down(scope.row.file_path)"
            />
            <el-button
              v-permission="['line-delete']"
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
import { lineIndex, lineInfo, lineDelete } from '@/api/line'

export default {
  name: 'Route',
  data() {
    return {
      form: {
        name: ''
      },
      jsonFields: {
        "id": "id",
        "电缆名称": "name",    //常规字段
        "所属线路":"line_name",
        "所属通道":"route_name",
        "起点":"start",
        "终点":"end",
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
      lineIndex({
        name: this.form.name
      }).then(res => {
        this.tableData = res.data
      })
    },
    to(type, id) {
      this.$router.push({
        path: '/line/line-form',
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
        lineDelete({id}).then(res => {
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