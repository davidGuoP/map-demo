<template>
  <div class="app-container">
    <el-card class="box-card">
      <div slot="header" class="clearfix">
        <span>{{ type === 'add' ? '新建' : '编辑' }}柱状标识器</span>
      </div>

      <el-form ref="formRef" :model="form" :rules="rules" label-width="120px">
        <el-form-item label="标识器编码" prop="code">
          <el-input v-model="form.code" placeholder="请输入24位标识器编码" style="width: 30%;"></el-input>
        </el-form-item>
        <el-form-item label="标识器名称" prop="name">
          <el-input v-model="form.name" placeholder="请输入标识器名称" style="width: 30%;"></el-input>
        </el-form-item>
        <el-form-item label="绑定通道" prop="routeId">
          <el-select v-model="form.routeId" filterable placeholder="请选择绑定通道" @change="routeChange">
            <template v-for="(item, index) in routes">
              <el-option :key="index" :label="item.name" :value="item.id"></el-option>
            </template>
          </el-select>
        </el-form-item>
        <el-form-item label="绑定通道类型" prop="routeType">
          <el-select v-model="form.routeType" placeholder="请选择绑定通道类型" :disabled="true">
            <template v-for="(item, index) in typeOpetions">
              <el-option :key="index" :label="item.label" :value="item.value"></el-option>
            </template>
          </el-select>
        </el-form-item>
        <el-form-item label="经纬度" prop="longitude">
          <el-input v-model="form.longitude" placeholder="请输入经纬度" style="width: 30%;"></el-input>
        </el-form-item>
        <el-form-item label="关联片状">
          <el-select v-model="form.volumeIds" multiple filterable placeholder="请选择关联片状" style="width: 100%;">
            <template v-for="(item, index) in volumes">
              <el-option :key="index" :label="item.label" :value="item.value"></el-option>
            </template>
          </el-select>
        </el-form-item>
        <el-form-item label="标识器文件" prop="fileList">
          <el-upload
            class="upload-demo"
            style="width: 20%;"
            :action="action"
            multiple
            :limit="1"
            :on-success="handleSuccess"
            :on-remove="handleRemove"
            :file-list="fileList">
            <el-button size="small" type="primary">点击上传</el-button>
            </el-upload>
        </el-form-item>
      </el-form>
      <el-button type="primary" style="margin-top: 10px;" @click="addSubmitHandel">保存</el-button>
    </el-card>
  </div>
</template>

<script>
import { routeIndex } from '@/api/route'
import { volumeIndex } from '@/api/volume'
import { barSave, barUpdate, barInfo } from '@/api/bar'

export default {
  name: 'role',
  data() {
    return {
      map: null,
      visible: false,
      form: {
        code: '',
        name: '',
        routeId: '',
        routeType: '',
        lineName: '',
        fileList: [],
        longitude: '',
        volumeIds: []
      },
      volumes: [],
      typeOpetions: [
        { value: 1, label: '电缆沟' },
        { value: 2, label: '直埋' },
        { value: 3, label: '工井' },
        { value: 4, label: '电缆隧道' },
        { value: 5, label: '排管' },
        { value: 6, label: '桥架' },
        { value: 7, label: '架空' },
        { value: 8, label: '线槽' },
      ],
      routes: [],
      type: '',
      id: '',
      rules: {
        code: [
          { required: true, message: '请输入24位标识器编码', trigger: ['blur', 'change'] },
          { min: 24, max: 24, message: '请输入24位标识器编码', trigger: ['blur', 'change'] }
        ],
        name: [
          { required: true, message: '请输入标识器名称', trigger: ['blur', 'change'] }
        ],
        routeId: [
          { required: true, message: '请选择绑定通道', trigger: ['blur', 'change'] }
        ],
        routeType: [
          { required: true, message: '请选择绑定通道类型', trigger: ['blur', 'change'] }
        ],
        longitude: [
          { required: true, message: '请输入经纬度', trigger: ['blur', 'change'] }
        ],
      },
      fileList: [],
      action: process.env.VUE_APP_BASE_API + 'upload'
    };
  },
  created() {
    this.type = this.$route.query.type || ''
    this.id = this.$route.query.id || ''
    // 查所有通道
    routeIndex({ name: '' }).then(res => {
      this.routes = res.data
    })
    volumeIndex({ name: '' }).then(res => {
      this.volumes = res.data.map(item => ({
        value: item.id,
        label: item.name
      }))
      if (parseInt(this.id) > 0) {
        this.init()
      }
    })
  },
  methods: {
    init() {
      barInfo({ id : this.id}).then(res => {
        this.form.code = res.code
        this.form.name = res.name
        this.form.routeId = res.route_id
        this.form.routeType = res.route_type
        this.form.longitude = res.longitude
        if (res.volume_ids) {
          this.form.volumeIds = res.volume_ids.split(',').map(item => parseInt(item))
        }
        if (res.fileList) {
          this.form.fileList = [res.file_path]
          this.fileList = [
              {
                  name: res.file_path,
                  url: res.file_path
              }
          ]
        }        
      })
    },
    routeChange() {
      if (!this.form.lineName) {
        this.form.routeType = this.routes.find(item => item.id === this.form.routeId)?.type
      }
    },
    handleSuccess(res, file) {
      console.log(res, file)
      if (res.ServerNo === 200) {
        this.form.fileList = [res.ResultData]
      }
    },
    handleRemove(file, fileList) {
      this.fileList = []
      this.form.fileList = []
    },
    addSubmitHandel() {
      console.log(this.form)
      this.$refs['formRef'].validate(valid => {
        if (valid) {
          const result = this.form.longitude.split(',')
          if (result.length != 2) {
            this.$message.warning('经纬度格式解析不正确')
            return
          }
          let param = this.form
          // 组装参数
          if (this.type === 'edit') {
            param.id = this.id
          }
          const api = this.type === 'add' ? barSave : barUpdate
          api.call(this, param).then(response => {
            this.visible = !this.visible
            this.$message.success('更新成功')
            setTimeout(() => {
              this.$router.push('/line/bar')
            }, 1000);
          })
        }
      })
    }
  }
}
</script>
