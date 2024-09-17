<template>
  <div class="user">
    <div class="common-form">卡券设置</div>
    <div class="product-content">
      <el-form ref="form" :model="form" :rules="formRules" label-width="150px">
        <el-form-item label="背景图" prop="image" >
          <el-row>
            <el-button type="primary" @click="openUpload" size="small">选择图片</el-button>
            <div v-if="form.image!=''" class="img">
              <img :src="form.image" :width="200" :height="200" />
            </div>
			<div class="gray9">建议图片尺寸大小为614px*628px</div>
          </el-row>
        </el-form-item>
      </el-form>
      <!--提交-->
      <div class="common-button-wrapper">
        <el-button type="primary" @click="onSubmit" :loading="loading">提交</el-button>
      </div>
    </div>
    <!--上传图片组件-->
    <Upload v-if="isupload" :isupload="isupload" :type="type" @returnImgs="returnImgsFunc">上传图片</Upload>
  </div>
</template>
<script>
  import CardApi from '@/api/card.js';
  import Upload from '@/components/file/Upload.vue';
  import {
    formatModel
  } from '@/utils/base.js';
  export default {
    components: {
      Upload
    },
    data() {
      return {
        /*是否加载完成*/
        loading: false,
        form: {
          image: '',
        },
        /*是否打开图片选择*/
        isupload: false,
        formRules: {
          image: [{
            required: true,
            message: '请上传背景图',
            trigger: 'blur'
          }],
        },
      };
    },
    created() {
      /*获取数据*/
      this.getData();
    },
    methods: {
      /*获取数据*/
      getData() {
        let self = this;
        CardApi.getSetting({}).then(data => {
          self.form = formatModel(self.form, data.data.vars.values);
        }).catch(error => {});
      },
      /*提交表单*/
      onSubmit() {
        let self = this;
        let form = self.form;
        self.$refs.form.validate((valid) => {
          if (valid) {
            self.loading = true;
            CardApi.editSetting(form, true).then(data => {
                self.loading = false;
                if (data.code == 1) {
                  ElMessage({
                    message: data.msg,
                    type: 'success'
                  });
                } else {
                  self.loading = false;
                }
              })
              .catch(error => {
                self.loading = false;
              });
          }
        });
      },
      /*上传*/
      openUpload(e) {
        this.type = e;
        this.isupload = true;
      },
      /*获取图片*/
      returnImgsFunc(e) {
        this.form.image = e[0].file_path;
        this.isupload = false;
      }
    },
  }
</script>
