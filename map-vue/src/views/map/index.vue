<template>
    <div class="wrap">
        <div id="container"></div>
        <div class="box">
            <el-tree
                :data="data"
                show-checkbox
                default-expand-all
                node-key="id"
                :check-strictly='true'
                ref="tree"
                highlight-current
                @check="currentChecked"
                @node-click="handleTreeNodeClick"
                :props="defaultProps">
            </el-tree>
        </div>
        <el-drawer
            title="我是标题"
            :visible.sync="drawer"
            direction="rtl"
            :with-header="false">
            <div class="header">{{ drawerInfo.title }}</div>
            <div class="content">
                <template v-if="drawerInfo.type === 'route'">
                  <el-form :model="form" label-width="120px">
                    <el-form-item label="通道名称">
                      <el-input v-model="form.name" readonly></el-input>
                    </el-form-item>
                    <el-form-item label="通道类型">
                      <el-input v-model="form.route_type_name" readonly></el-input>
                    </el-form-item>
                    <el-form-item label="关联线路">
                      <el-input v-model="form.name" readonly></el-input>
                    </el-form-item>
                    <el-form-item label="通道文件">
                      <el-button
                        size="mini"
                        type="info"
                        icon="el-icon-upload"
                        @click="down(form.file_path)"
                      />
                    </el-form-item>
                  </el-form>

                  <el-card style="margin: 20px;">
                    <div slot="header" class="clearfix">
                      <span>关联柱状标识器</span>
                    </div>
                    <el-table
                      :data="form.table"
                      element-loading-text="Loading"
                      border
                      fit
                      highlight-current-row
                    >
                      <el-table-column label="标识器编号" prop="code" min-width="60" align="center" />
                      <el-table-column label="标识器名称" prop="name" min-width="60" align="center" />
                      <el-table-column label="绑定通道" prop="route_name" min-width="60" align="center" />
                      <el-table-column label="绑定通道类型" prop="route_type_name" min-width="60" align="center" />
                      <el-table-column label="经纬度" prop="longitude" min-width="60" align="center" />
                    </el-table>
                  </el-card>
                </template>

                <template v-if="drawerInfo.type === 'line'">
                  <el-form :model="form" label-width="120px">
                    <el-form-item label="电缆名称">
                      <el-input v-model="form.name" readonly></el-input>
                    </el-form-item>
                    <el-form-item label="所属线路">
                      <el-input v-model="form.line_name" readonly></el-input>
                    </el-form-item>
                    <el-form-item label="所属通道">
                      <el-input v-model="form.route_name" readonly></el-input>
                    </el-form-item>
                    <el-form-item label="起点">
                      <el-input v-model="form.start" readonly></el-input>
                    </el-form-item>
                    <el-form-item label="终点">
                      <el-input v-model="form.end" readonly></el-input>
                    </el-form-item>
                    <el-form-item label="电缆文件" v-if="form.file_path">
                      <el-button
                        size="mini"
                        type="info"
                        icon="el-icon-upload"
                        @click="down(form.file_path)"
                      />
                    </el-form-item>
                  </el-form>

                  <el-card style="margin: 20px;">
                    <div slot="header" class="clearfix">
                      <span>关联片状标识器</span>
                    </div>
                    <el-table
                      :data="form.table"
                      element-loading-text="Loading"
                      border
                      fit
                      highlight-current-row
                    >
                      <el-table-column label="标识器编号" prop="code" min-width="60" align="center" />
                      <el-table-column label="标识器名称" prop="name" min-width="60" align="center" />
                      <el-table-column label="绑定电缆" prop="line_name" min-width="60" align="center" />
                      <el-table-column label="绑定设备类型" min-width="60" align="center">
                        电缆本体
                      </el-table-column>
                      <el-table-column label="标识器坐标" prop="longitude" min-width="60" align="center" />
                    </el-table>
                  </el-card>
                </template>

                <template v-if="drawerInfo.type === 'bar'">
                  <el-form :model="form" label-width="120px">
                    <el-form-item label="标识器编码">
                      <el-input v-model="form.code" readonly></el-input>
                    </el-form-item>
                    <el-form-item label="标识器名称">
                      <el-input v-model="form.name" readonly></el-input>
                    </el-form-item>
                    <el-form-item label="绑定通道">
                      <el-input v-model="form.route_name" readonly></el-input>
                    </el-form-item>
                    <el-form-item label="绑定通道类型">
                      <el-input v-model="form.route_type_name" readonly></el-input>
                    </el-form-item>
                    <el-form-item label="经纬度">
                      <el-input v-model="form.longitude" readonly></el-input>
                    </el-form-item>
                    <el-form-item label="标识器文件" v-if="form.file_path">
                      <el-button
                        size="mini"
                        type="info"
                        icon="el-icon-upload"
                        @click="down(form.file_path)"
                      />
                    </el-form-item>
                  </el-form>

                  <el-card style="margin: 20px;">
                    <div slot="header" class="clearfix">
                      <span>关联片状标识器</span>
                    </div>
                    <el-table
                      :data="form.table"
                      element-loading-text="Loading"
                      border
                      fit
                      highlight-current-row
                    >
                      <el-table-column label="标识器编号" prop="code" min-width="60" align="center" />
                      <el-table-column label="标识器名称" prop="name" min-width="60" align="center" />
                      <el-table-column label="绑定电缆" prop="line_name" min-width="60" align="center" />
                      <el-table-column label="绑定设备类型" min-width="60" align="center">
                        电缆本体
                      </el-table-column>
                      <el-table-column label="标识器坐标" prop="longitude" min-width="60" align="center" />
                    </el-table>
                  </el-card>
                </template>

                <template v-if="drawerInfo.type === 'volume'">
                  <el-form :model="form" label-width="120px">
                    <el-form-item label="标识器编码">
                      <el-input v-model="form.code" readonly></el-input>
                    </el-form-item>
                    <el-form-item label="标识器名称">
                      <el-input v-model="form.name" readonly></el-input>
                    </el-form-item>
                    <el-form-item label="绑定电缆">
                      <el-input v-model="form.line_name" readonly></el-input>
                    </el-form-item>
                    <el-form-item label="绑定设备类型">
                      <el-input value="电缆本体" readonly></el-input>
                    </el-form-item>
                    <el-form-item label="经纬度">
                      <el-input v-model="form.longitude" readonly></el-input>
                    </el-form-item>
                    <el-form-item label="标识器文件" v-if="form.file_path">
                      <el-button
                        size="mini"
                        type="info"
                        icon="el-icon-upload"
                        @click="down(form.file_path)"
                      />
                    </el-form-item>
                  </el-form>

                  <el-card style="margin: 20px;">
                    <div slot="header" class="clearfix">
                      <span>关联柱状标识器</span>
                    </div>
                    <el-table
                      :data="form.table"
                      element-loading-text="Loading"
                      border
                      fit
                      highlight-current-row
                    >
                      <el-table-column label="标识器编号" prop="code" min-width="60" align="center" />
                      <el-table-column label="标识器名称" prop="name" min-width="60" align="center" />
                      <el-table-column label="绑定通道" prop="route_name" min-width="60" align="center" />
                      <el-table-column label="绑定通道类型" prop="route_type_name" min-width="60" align="center" />
                      <el-table-column label="经纬度" prop="longitude" min-width="60" align="center" />
                    </el-table>
                  </el-card>
                </template>
            </div>
        </el-drawer>
    </div>
</template>

<script>
import AMapLoader from '@amap/amap-jsapi-loader'
import { list, downloadFile } from '@/api/webname'
import { barInfo } from '@/api/bar'
import { volumeInfo } from '@/api/volume'
import { routeInfo } from '@/api/route'
import { lineInfo } from '@/api/line'
import GPS from '@/utils/gpstoGD'

export default {
    data() {
        return {
            map: null,
            AMap: null,
            drawer: false,
            data: [],
            markerPointList: [],
            defaultProps: {
                children: 'children',
                label: 'name'
            },
            drawerInfo: {
                title: '',
                type: ''
            },
            form: {},
            typeOpetions: [
              '',
              '电缆沟',
              '直埋',
              '工井',
              '电缆隧道',
              '排管',
              '桥架',
              '架空',
              '线槽',
            ],
        }
    },
    created() {
        list().then(res => {
            this.data = [
                {
                    id: -1,
                    name: '数字通道',
                    children: res.data.routes
                },
                {
                    id: -2,
                    name: '数字电缆',
                    children: res.data.lines
                },
            ]
        })
    },
    mounted(){
        // DOM初始化完成进行地图初始化
        this.initMap()
    },
    methods: {
        initMap() {
            AMapLoader.load({
                key:"f216211ad19cccb3d51c90a0461dc7e9",             // 申请好的Web端开发者Key，首次调用 load 时必填
                version:"2.0",      // 指定要加载的 JSAPI 的版本，缺省时默认为 1.4.15
                plugins:[''],       // 需要使用的的插件列表，如比例尺'AMap.Scale'等
            }).then((AMap)=> {
                this.AMap = AMap
                this.map = new AMap.Map("container",{  //设置地图容器id
                    viewMode:"3D",    //是否为3D地图模式
                    zoom: 10,           //初始化地图级别
                    center:[113.23333, 23.16667], //初始化地图中心点位置
                });
            }).catch(e=>{
                console.log(e);
            })
        },
        handleTreeNodeClick(data, node, elem) {
          console.log(data, node, elem)
          // 清除先前标记点
          // if (this.markerPointList.length !== 0) { // 定时获取点数据，更新前需先清掉原来点
          //   this.map.remove(this.markerPointList)
          //   this.markerPointList = []
          // }
          // if (node.level === 3) {
          //   this.loadMarkers(node.data)
          // }
          // if (node.level === 2) {
          //   this.drawRoute(node.data)
          // }
        },
        currentChecked(nodeObj, SelectedObj) {
          console.log(nodeObj, SelectedObj)
          // console.log(SelectedObj)
          // console.log(SelectedObj.checkedKeys)   // 这是选中的节点的key数组
          // console.log(SelectedObj.checkedNodes)  // 这是选中的节点数组
          let isLine = false
          SelectedObj.checkedNodes.forEach(item => {
            if (['bar', 'volume'].includes(item.type)) {
              isLine = true
            }
          })
          // 检查全部是点的时候,是否有新增,没有新增的时候,将点移除即可
          console.log("isLine", isLine)
          if (isLine && this.markerPointList.length !== 0) { // 定时获取点数据，更新前需先清掉原来点
            let extData = this.markerPointList.map(item => item.getExtData()?.eventId)
            const extDataFilter = extData.filter(item => item.includes('bar'))
            console.log("extData", extData)
            let checkNodes = SelectedObj.checkedNodes.filter(item => ['bar', 'volume'].includes(item.type))
            checkNodes = checkNodes.map(item => `${item.type}${item.id}`)
            // 只点了一个点时,点取消时
            if (checkNodes.length <= 0) {
              this.map.remove(this.markerPointList)
            }
            // 点了多个点,点取消时
            console.log(extDataFilter, checkNodes)
            if (extDataFilter.length >= checkNodes.length) {
              // 删除多余的点
              extData.map((item, index) => {
                if (!checkNodes.includes(item)) {
                  this.map.remove(this.markerPointList[index])
                }
              })
              return
            }
            console.log(checkNodes)
          }
          // 清除先前标记点
          if (this.markerPointList.length !== 0) { // 定时获取点数据，更新前需先清掉原来点
            this.map.remove(this.markerPointList)
            this.markerPointList = []
          }
          SelectedObj.checkedNodes.forEach(item => {
            if (['bar', 'volume'].includes(item.type)) {
              this.loadMarkers(item)
            }
            if (['route', 'line'].includes(item.type)) {
              item.isLine = isLine
              this.drawRoute(item)
            }
          })
        },
        loadMarkers({ id, type, name, longitude, isLine, volume_longitude = '' }) {
          if (!longitude || (longitude.split(',') && longitude.split(',').length !== 2)) {
            this.$message.error(`该${type === 'bar' ? '柱状标识器' : '片状标识器'}下面暂无对应的坐标、或解析格式不正确`)
            return
          }
          // 创建一个Marker实例
          let volumeMarkerList = []
          const [lat, long] = longitude.split(',')
          const mark = GPS.gcj_encrypt(Number(parseFloat(lat)), Number(parseFloat(long)))
          const marker = new this.AMap.Marker({
              icon: new this.AMap.Icon({            
                  image: type === 'bar' ? require("@/assets/zhu.png") : require("@/assets/pian.png"),
                  size: new this.AMap.Size(30, 30),  //图标大小
                  imageSize: new this.AMap.Size(30, 30),
              }),
              extData: {
                id,
                type,
                eventId: `${type}${id}`
              },
              position: new this.AMap.LngLat(mark.lat, mark.lon),   // 经纬度对象，也可以是经纬度构成的一维数组[116.39, 39.9]
              title: name,
              //设置偏移量
              offset: new this.AMap.Pixel(-13, -24),
          })
          marker.on('click', (e) => {
              this.show(e)
          })
          // 标记点的list，清空点时用
          this.markerPointList.push(marker)
          volumeMarkerList.push(marker)

          // 增加片
          console.log('1111111111', volume_longitude)
          if (volume_longitude) {
            volume_longitude.forEach(item => {
              // 创建一个Marker实例
              const [lat, long] = item.longitude.split(',')
              const mark = GPS.gcj_encrypt(Number(parseFloat(lat)), Number(parseFloat(long)))
              const marker = new this.AMap.Marker({
                  icon: new this.AMap.Icon({            
                      image: item.type === 'bar' ? require("@/assets/zhu.png") : require("@/assets/pian.png"),
                      size: new this.AMap.Size(30, 30),  //图标大小
                      imageSize: new this.AMap.Size(30, 30),
                  }),
                  extData: {
                    id: item.id,
                    type: item.type,
                    eventId: `${item.type}${item.id}` 
                  },
                  position: new this.AMap.LngLat(mark.lat, mark.lon),   // 经纬度对象，也可以是经纬度构成的一维数组[116.39, 39.9]
                  title: item.name,
                  //设置偏移量
                  offset: new this.AMap.Pixel(-13, -24),
              })
              marker.on('click', (e) => {
                  this.show(e)
              })
              // 标记点的list，清空点时用
              this.markerPointList.push(marker)
              volumeMarkerList.push(marker)
            })
          }

          // 将创建的点标记添加到已有的地图实例
          console.log(2222222, volumeMarkerList)
          this.map.add(volumeMarkerList)
          // 缩放地图到合适的视野级别
          this.map.setFitView(volumeMarkerList)
          if (!isLine) {
            var zoom = this.map.getZoom()
				    this.map.setZoom(10)
          }
        },
        drawRoute({ id, type, name, position }) {
          if (!position) {
            this.$message.error(`该${type === 'route' ? '数字通道' : '数字电缆'}下面暂无关联的标识器`)
            return
          }
          const markerList = []
          let path = JSON.parse(position)
          // [
          //   [116.39, 39.9],
          //   [116.961225, 39.477194],
          //   [115.526525, 39.403571],
          //   [116.185568, 38.96516],
          //   [117.39, 38.9],
          // ]
          const polyline = new this.AMap.Polyline({
            path,
            strokeColor: 'blue',
            strokeOpacity: 1,
            strokeWeight: 4,
            strokeStyle: 'solid',
            lineJoin: 'round',
            lineCap: 'round',
            extData: {
              id,
              type,
              eventId: '1111111'
            },
          })
          // 标记点的list，清空点时用
          this.markerPointList.push(polyline)
          this.map.add(polyline)
          // 缩放地图到合适的视野级别
          this.map.setFitView([polyline])
          polyline.on('click', (e) => {
            this.showPolyline(e)
          })
        },
        show(e) {
            console.log(11111, e)
            if (e && e.target && e.target._opts && e.target._opts.extData && e.target._opts.extData.id) {
              // 请求柱状图的接口
              if (e.target._opts.extData.type === 'bar') {
                barInfo({ id: e.target._opts.extData.id }).then(res => {
                  this.drawer = true
                  this.drawerInfo.title = '柱状标识器详情'
                  this.drawerInfo.type = 'bar'
                  res.route_type_name = this.typeOpetions[res.route_type] || ''
                  this.form = res
                })
              }

              // 请求片状图的接口
              if (e.target._opts.extData.type === 'volume') {
                volumeInfo({ id: e.target._opts.extData.id }).then(res => {
                  this.drawer = true
                  this.drawerInfo.title = '片状标识器详情'
                  this.drawerInfo.type = 'volume'
                  if (res.table) {
                    res.table = res.table.map(item => {
                      item.route_type_name = this.typeOpetions[item.route_type] || ''
                      return item
                    })
                  }
                  this.form = res
                })
              }
            } else {
              this.$message.warning('未找到该数据的id')
            }
        },
        showPolyline(e) {
          if (e && e.target && e.target._opts && e.target._opts.extData && e.target._opts.extData.id) {
            // 请求柱状图的接口
            if (e.target._opts.extData.type === 'route') {
              routeInfo({ id: e.target._opts.extData.id }).then(res => {
                this.drawer = true
                this.drawerInfo.title = '通道详情'
                this.drawerInfo.type = 'route'
                res.route_type_name = this.typeOpetions[res.type] || ''
                if (res.table) {
                  res.table = res.table.map(item => {
                    item.route_type_name = this.typeOpetions[item.route_type] || ''
                    return item
                  })
                }
                this.form = res
              })
            }

            // 请求片状图的接口
            if (e.target._opts.extData.type === 'line') {
              lineInfo({ id: e.target._opts.extData.id }).then(res => {
                this.drawer = true
                this.drawerInfo.title = '电缆详情'
                this.drawerInfo.type = 'line'
                if (res.table) {
                  res.table = res.table.map(item => {
                    item.route_type_name = this.typeOpetions[item.route_type] || ''
                    return item
                  })
                }
                this.form = res
              })
            }
          } else {
            this.$message.warning('未找到该数据的id')
          }
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
#container{
    padding:0px;
    margin: 0px;
    width: 100%;
    height: calc(100vh - 60px);
    z-index: 99;
}
.wrap {
   position: relative;
   .box {
       position: absolute;
       top: 10px;
       left: 20px;
       width: 300px;
       height: calc(100% - 40px);
       z-index: 1000;
       background: #fff;
       padding: 20px;
       box-sizing: border-box;
       overflow: auto;
   } 
}
::v-deep .el-tree > .el-tree-node > .el-tree-node__content .el-checkbox {
  display: none;
}
.header {
    -webkit-box-align: center;
    -ms-flex-align: center;
    align-items: center;
    color: #72767b;
    display: -webkit-box;
    display: -ms-flexbox;
    display: flex;
    margin-bottom: 32px;
    padding: 20px 20px 0;
}
</style>
