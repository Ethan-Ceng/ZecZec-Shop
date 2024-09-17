<template>

  <div class="product-add pb50" v-loading="loading">
    <!--编辑文章-->
     <el-form size="small" :model="form" ref="form" label-width="100px">
       <div class="common-form">提货码</div>
       <el-form-item label="卡券名称" prop="card_title">
         {{model.card_title}}
       </el-form-item>
       <el-form-item label="前缀">
         <el-input v-model="form.prefix" placeholder="" class="max-w460"></el-input>
       </el-form-item>
       <el-form-item label="起始数字" :rules="[{ required: true, message: ' ' }]" prop="start_num">
         <el-input v-model="form.start_num" placeholder="" class="max-w460"></el-input>
       </el-form-item>
       <el-form-item label="密码位数" :rules="[{ required: true, message: ' ' }]" prop="code_len">
         <el-input v-model="form.code_len" placeholder="" class="max-w460"></el-input>
       </el-form-item>
       <el-form-item label="生成数量" :rules="[{ required: true, message: ' ' }]" prop="code_count">
         <el-input v-model="form.code_count" placeholder="" class="max-w460"></el-input>
       </el-form-item>
       <el-form-item label="提货时间" :rules="[{ required: true, message: ' ' }]" prop="start_time">
         <el-date-picker
           v-model="form.start_time"
           type="date"
           value-format="YYYY-MM-DD"
           placeholder="选择开始时间">
         </el-date-picker>
         <span></span>
         <el-date-picker
           v-model="form.end_time"
           type="date"
           value-format="YYYY-MM-DD"
           placeholder="选择结束时间">
         </el-date-picker>
       </el-form-item>
       <!--提交-->
       <div class="common-button-wrapper">
         <el-button size="small" type="info" @click="cancelFunc" :loading="loading">取消</el-button>
         <el-button size="small" type="primary" @click="onSubmit" :loading="loading">提交</el-button>
       </div>
     </el-form>
  </div>
</template>

<script>
import CardApi from '@/api/card.js';
import Upload from '@/components/file/Upload.vue';
export default {
  components: {
    /*图片上传*/
    Upload
  },
  data() {
    return {
      /*是否加载完成*/
      loading: true,
      /*是否上传图片*/
      isupload: false,
      /*form表单数据*/
      form: {
        card_id: 0,
        prefix: '',
        start_num: '',
        code_len: 6,
        code_count: '',
        start_time: '',
        end_time: '',
      },
      model: {},
      /*新闻类别*/
      category: [],
      product: []
    };
  },
  created() {

    this.getDetail();

  },

  methods: {

    /*上传*/
    openUpload() {
      this.isupload = true;
    },
    /*获取图片*/
    returnImgsFunc(e) {
      let self = this;
      if (e != null) {
        this.form.image_id = e[0].file_id;
        this.form.image.file_path = e[0].file_path;
      }
      this.isupload = false;
    },

    getDetail() {
      let self = this;
      // 取到路由带过来的参数
      const params = self.$route.query.card_id;
      CardApi.toCodeCard({card_id: params},true).then(res => {
          self.model = res.data.model;
          self.loading = false;
        })
        .catch(error => {});
    },

    /*修改文章*/
    onSubmit() {
      let self = this;
      let params = self.form;
      params.card_id = self.$route.query.card_id;
      // 取到路由带过来的参数
      CardApi.codeCard(params, true)
        .then(data => {
          ElMessage({
            message: data.msg,
            type: 'success'
          });
          self.cancelFunc();
        })
        .catch(error => {});
    },

    /*取消添加，返回文章列表*/
    cancelFunc() {
      this.$router.back(-1);
    }
  }
};
</script>

<style>
</style>
