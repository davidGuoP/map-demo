<template>
  <div class="app-container">
    <el-card class="box-card">
      <div slot="header" class="clearfix">
        <span>{{ type === 'add' ? '新建' : '编辑' }}电缆</span>
      </div>

      <el-form ref="formRef" :model="form" :rules="rules" label-width="80px">
        <el-form-item label="电缆名称" prop="name">
          <el-input v-model="form.name" placeholder="请输入电缆名称" style="width: 30%;"></el-input>
        </el-form-item>
        <el-form-item label="所属线路" prop="lineName">
          <el-input v-model="form.lineName" style="width: 30%;" placeholder="请输入所属线路"></el-input>
        </el-form-item>
        <el-form-item label="所属通道" prop="routeId">
          <el-select v-model="form.routeId" filterable placeholder="请选择所属通道" @change="routeChange">
            <template v-for="(item, index) in routes">
              <el-option :key="index" :label="item.name" :value="item.id"></el-option>
            </template>
          </el-select>
        </el-form-item>
        <el-form-item label="起点" prop="start">
          <el-input v-model="form.start" placeholder="请输入起点" style="width: 30%;"></el-input>
        </el-form-item>
        <el-form-item label="终点" prop="end">
          <el-input v-model="form.end" style="width: 30%;" placeholder="请输入终点"></el-input>
        </el-form-item>
        <el-form-item label="电缆文件" prop="fileList">
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
        <div id="container"></div>
        <div style="margin-top: 10px;" v-if="this.longitudes.length > 0">
          <el-button @click="drawRoute" :disabled="markerFlag">绘制路线</el-button>
          <el-button @click="closeRoute" :disabled="!markerFlag">关闭路线</el-button>
          <el-button @click="clearRoute" :disabled="markerPointList.length <= 0">清除路线</el-button>
        </div>
      </el-form>
      <el-button type="primary" style="margin-top: 10px;" @click="addSubmitHandel">保存</el-button>
    </el-card>
  </div>
</template>

<script>
import { routeIndex } from '@/api/route'
import { lineSave, lineUpdate, lineInfo } from '@/api/line'
import AMapLoader from '@amap/amap-jsapi-loader';
import GPS from '@/utils/gpstoGD'

export default {
  name: 'role',
  data() {
    return {
      map: null,
      AMap: null,
      polylineEditor: null,
      visible: false,
      markerPointList: [],
      form: {
        name: '',
        routeId: '',
        lineName: '',
        fileList: [],
        start: '',
        end: ''
      },
      routes: [],
      type: '',
      id: '',
      rules: {
        name: [
          { required: true, message: '请输入通道名称', trigger: 'blur' }
        ],
        routeId: [
          { required: true, message: '请选择所属通道', trigger: 'blur' }
        ],
        lineName: [
          { required: true, message: '请输入所属线路', trigger: 'blur' }
        ],
        // fileList: [
        //   { required: true, message: '请上传文件', trigger: 'blur' }
        // ],
        start: [
          { required: true, message: '请输入起点', trigger: 'blur' }
        ],
        end: [
          { required: true, message: '请选择终点', trigger: 'blur' }
        ],
      },
      fileList: [],
      action: process.env.VUE_APP_BASE_API + 'upload',
      longitudes: [],
      markerFlag: false,
    };
  },
  created() {
    this.type = this.$route.query.type || ''
    this.id = this.$route.query.id || ''
    // 查所有通道
    routeIndex({ name: '' }).then(res => {
      this.routes = res.data
    })
    if (parseInt(this.id) > 0) {
      this.init()
    }
  },
  mounted(){
    // DOM初始化完成进行地图初始化
    this.initMap();
  },
  methods: {
    init() {
      lineInfo({ id : this.id}).then(res => {
        this.form.name = res.name
        this.form.routeId = res.route_id
        this.form.lineName = res.line_name
        this.form.start = res.start
        this.form.end = res.end
        if (res.fileList) {
          this.form.fileList = [res.file_path]
          this.fileList = [
              {
                  name: res.file_path,
                  url: res.file_path
              }
          ]
        }
        if (res.volumes) {
          this.longitudes = res.volumes
        }
        if (res.position) {
          this.position = res.position
        }
      })
    },
    routeChange() {
      if (!this.form.lineName) {
        this.form.lineName = this.routes.find(item => item.id === this.form.routeId)?.line_name
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
      if (this.markerFlag) {
        this.$message.warning('地图处于编辑中,请先"关闭路线"后再提交')
        return
      }
      this.$refs['formRef'].validate(valid => {
        if (valid) {
          let param = this.form
          // 组装参数
          if (this.type === 'edit') {
            param.id = this.id
          }
          param.position = ''
          if (this.polyline && this.polyline.$x && this.polyline.$x[0]) {
            param.position = JSON.stringify(this.polyline.$x[0])
          }
          const api = this.type === 'add' ? lineSave : lineUpdate
          api.call(this, param).then(response => {
            this.visible = !this.visible
            this.$message.success('更新成功')
            setTimeout(() => {
              this.$router.push('/line/line')
            }, 1000);
          })
        }
      })
    },
    initMap(){
        AMapLoader.load({
            key:"f216211ad19cccb3d51c90a0461dc7e9",             // 申请好的Web端开发者Key，首次调用 load 时必填
            version:"2.0",      // 指定要加载的 JSAPI 的版本，缺省时默认为 1.4.15
              plugin: ['AMap.PolyEditor', 'AMap.ConvertFrom'],       // 需要使用的的插件列表，如比例尺'AMap.Scale'等
        }).then((AMap)=>{
            this.AMap = AMap
            this.map = new AMap.Map("container",{  //设置地图容器id
                viewMode:"3D",    //是否为3D地图模式
                zoom: 7,           //初始化地图级别
                center:[113.255231, 23.154854], //初始化地图中心点位置
            });

            this.loadMarkers(AMap)
            
            if (this.position) {
              let path = []
              // var path = [
              //   [114.0514007, 22.54925084],
              //   [115.0514007, 23.54925084],
              // ]
              // this.longitudes.forEach(item => {
              //   const [latitude, longitude] = item.longitude.split(',')
              //   const mark = GPS.gcj_encrypt(Number(parseFloat(latitude)), Number(parseFloat(longitude)))
              //   path.push([mark.lat, mark.lon])
              // })
              // path = [
              //   [116.39, 39.9],
              //   [116.961225, 39.477194],
              //   [115.526525, 39.403571],
              //   [116.185568, 38.96516],
              //   [117.39, 38.9],
              // ]
              path = JSON.parse(this.position)
              this.polyline = new AMap.Polyline({
                path,
                strokeColor: 'blue',
                strokeOpacity: 1,
                strokeWeight: 4,
                strokeStyle: 'dashed',
                lineJoin: 'round',
                lineCap: 'round'
              })
              this.map.add(this.polyline)
              // 缩放地图到合适的视野级别
              this.map.setFitView([this.polyline])
              this.markerPointList.push(this.polyline)
            }
        }).catch(e=>{
            console.log(e);
        })
    },
    loadMarkers(AMap) {
      // 创建一个Marker实例
      console.log(this.longitudes)
      if (this.longitudes) {
        const markerList = []
        this.longitudes.forEach(item => {
          const [latitude, longitude] = item.longitude.split(',')
          const mark = GPS.gcj_encrypt(Number(parseFloat(latitude)), Number(parseFloat(longitude)))
          markerList.push(new AMap.Marker({
            icon: new this.AMap.Icon({            
              image: require("@/assets/pian.png"),
              size: new this.AMap.Size(30, 30),  //图标大小
              imageSize: new this.AMap.Size(30, 30),
            }),
            position: new AMap.LngLat(mark.lat, mark.lon),   // 经纬度对象，也可以是经纬度构成的一维数组[116.39, 39.9]
            title: item.name,
            //设置偏移量
            offset: new this.AMap.Pixel(-13, -24),
          }))
        })

        // 将创建的点标记添加到已有的地图实例
        this.map.add(markerList);
        this.markerPointList.push(markerList)
      }
    },
    // 绘制矢量路线
    drawRoute () {
      if (!this.markerFlag) {
        // 最开始一次画图
        if (!this.position) {
          const markerList = []
          let path = []
          if (this.longitudes.length > 0) {
            this.longitudes.forEach(item => {
              const [latitude, longitude] = item.longitude.split(',')
              const mark = GPS.gcj_encrypt(Number(parseFloat(latitude)), Number(parseFloat(longitude)))
              path.push([mark.lat, mark.lon])
            })
            this.polyline = new this.AMap.Polyline({
              path,
              strokeColor: 'blue',
              strokeOpacity: 1,
              strokeWeight: 4,
              strokeStyle: 'dashed',
              lineJoin: 'round',
              lineCap: 'round'
            })
            this.map.add(this.polyline)
            // 缩放地图到合适的视野级别
            this.map.setFitView([this.polyline])
          } else {
            return
          }
        }
        this.map.plugin(["AMap.PolyEditor"], () => {
          this.polylineEditor = new AMap.PolyEditor(this.map, this.polyline)
          this.polylineEditor.open()
        })
        this.markerFlag = true
      }
    },
    // 关闭矢量路线
    closeRoute () {
      if (this.polyline) {
        // this.polyline.hide()
        this.polylineEditor.close()
        this.markerFlag = false
      }
    },
    clearRoute() {
      if (this.markerPointList.length !== 0) { // 定时获取点数据，更新前需先清掉原来点
        this.map.remove(this.markerPointList)
        this.markerPointList = []
        this.position = ''
        this.polyline = null
      }
    }
  }
}
</script>

<style lang="scss" scoped>
::v-deep .el-checkbox {
    margin: 4px 0;
}
#container{
    padding:0px;
    margin: 0px;
    width: 100%;
    height: 400px;
}
</style>