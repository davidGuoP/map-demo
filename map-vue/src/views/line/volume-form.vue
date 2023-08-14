<template>
  <div class="app-container">
    <el-card class="box-card">
      <div slot="header" class="clearfix">
        <span>{{ type === 'add' ? '新建' : '编辑' }}片状标识器</span>
      </div>

      <el-form ref="formRef" :model="form" :rules="rules" label-width="120px">
        <el-form-item label="标识器编码" prop="code">
          <el-input v-model="form.code" placeholder="请输入24位标识器编码" style="width: 30%;"></el-input>
        </el-form-item>
        <el-form-item label="标识器名称" prop="name">
          <el-input v-model="form.name" placeholder="请输入标识器名称" style="width: 30%;"></el-input>
        </el-form-item>
        <el-form-item label="绑定电缆" prop="lineId">
          <el-select v-model="form.lineId" filterable placeholder="请选择绑定电缆" @change="routeChange">
            <template v-for="(item, index) in routes">
              <el-option :key="index" :label="item.name" :value="item.id"></el-option>
            </template>
          </el-select>
        </el-form-item>
        <el-form-item label="绑定设备类型" prop="routeType">
          <el-select v-model="form.routeType" placeholder="请选择绑定设备类型" :disabled="true">
            <template v-for="(item, index) in typeOpetions">
              <el-option :key="index" :label="item.label" :value="item.value"></el-option>
            </template>
          </el-select>
        </el-form-item>
        <el-form-item label="经纬度" prop="longitude">
          <el-input v-model="form.longitude" placeholder="请输入经纬度" style="width: 30%;"></el-input>
        </el-form-item>
        <el-form-item label="关联柱状">
          <el-select v-model="form.barId" filterable placeholder="请选择关联柱状标识器">
            <template v-for="(item, index) in bars">
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
import { barIndex } from '@/api/bar'
import { lineIndex } from '@/api/line'
import { volumeSave, volumeUpdate, volumeInfo } from '@/api/volume'

export default {
  name: 'role',
  data() {
    return {
      map: null,
      visible: false,
      form: {
        code: '',
        name: '',
        lineId: '',
        routeType: '',
        lineName: '',
        fileList: [],
        longitude: '',
        barId: []
      },
      typeOpetions: [
        { value: 1, label: '电缆本体' },
      ],
      bars: [],
      routes: [],
      type: '',
      id: '',
      rules: {
        code: [
          { required: true, message: '请输入24位标识器编码', trigger: 'blur' },
          { min: 24, max: 24, message: '请输入24位标识器编码', trigger: ['blur', 'change'] }
        ],
        name: [
          { required: true, message: '请输入标识器名称', trigger: 'blur' }
        ],
        lineId: [
          { required: true, message: '请选择绑定电缆', trigger: 'blur' }
        ],
        routeType: [
          { required: true, message: '请选择绑定设备类型', trigger: 'blur' }
        ],
        longitude: [
          { required: true, message: '请输入经纬度', trigger: 'blur' }
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
    lineIndex({ name: '' }).then(res => {
      this.routes = res.data
    })
    barIndex({ name: '' }).then(res => {
      this.bars = res.data.map(item => ({
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
      volumeInfo({ id : this.id}).then(res => {
        this.form.code = res.code
        this.form.name = res.name
        this.form.lineId = res.line_id
        this.form.routeType = 1
        this.form.longitude = res.longitude
        if (res.bar_id) {
          this.form.barId = parseInt(res.bar_id)
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
        this.form.routeType = 1
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
          const api = this.type === 'add' ? volumeSave : volumeUpdate
          api.call(this, param).then(response => {
            this.visible = !this.visible
            this.$message.success('更新成功')
            setTimeout(() => {
              this.$router.push('/line/volume')
            }, 1000);
          })
        }
      })
    }
  }
}
</script>
