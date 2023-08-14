<template>
    <div class="app-container">
        <el-card>
            <div slot="header" class="clearfix">
                <span>基本设置</span>
            </div>
            <el-form ref="form" :model="form" :rules="rules" label-width="80px">
                <el-form-item label="网站名称" prop="name">
                    <el-input type="text" style="width: 500px;" v-model="form.name"></el-input>
                </el-form-item>
            </el-form>

            <el-button type="primary" @click="submit">确 定</el-button>
        </el-card>
    </div>
</template>

<script>
import { webNameInfo, webNameSave } from '@/api/webname'
export default {
    name: 'webName',
    data() {
        return {
            form: {
                name: ''
            },
            rules: {
                name: [
                    { required: true, message: '请输入网站名称', trigger: 'blur' },
                    { min: 2, max: 20, message: '长度在2到20个字符', trigger: 'blur' }
                ],
            }
        }
    },
    created() {
        this.init()
    },
    methods: {
        init() {
            webNameInfo().then(res => {
                if (res) {
                    this.form.name = res.name
                }
            })
        },
        submit() {
            this.$refs.form.validate((valid) => {
                if (valid) {
                    webNameSave({
                        name: this.form.name
                    }).then(res => {
                        this.$message.success('更新成功')
                    })
                }
            })
        }
    }
}
</script>
