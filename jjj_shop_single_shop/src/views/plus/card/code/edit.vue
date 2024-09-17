<template>

  <div class="product-add pb50" v-loading="loading">
    <!--编辑文章-->
     <el-form size="small" :model="form" ref="form" label-width="100px">
       <div class="common-form">编辑提货码</div>
       <el-form-item label="所属卡券">
         {{ model.card && model.card.card_title }}
       </el-form-item>
       <el-form-item label="提货码">
         {{model.code_no}}
       </el-form-item>
       <el-form-item label="提货密码" :rules="[{ required: true, message: ' ' }]" prop="code_pwd">
         <el-input v-model="form.code_pwd" placeholder="" class="max-w460"></el-input>
       </el-form-item>
       <el-form-item label="提货时间" :rules="[{ required: true, message: ' ' }]" prop="start_time">
         <el-date-picker
           v-model="form.start_time"
           type="date"
           value-format="YYYY-MM-DD"
           placeholder="选择开始时间">
         </el-date-picker>
         <span>-</span>
         <el-date-picker
           v-model="form.end_time"
           type="date"
           value-format="YYYY-MM-DD"
           placeholder="选择结束时间">
         </el-date-picker>
       </el-form-item>
       <el-form-item label="提货状态">
         <el-radio-group v-model="form.code_status">
           <el-radio :label="0">未提货</el-radio>
           <el-radio :label="1">已提货</el-radio>
         </el-radio-group>
       </el-form-item>
       <el-form-item label="是否作废">
         <el-radio-group v-model="form.is_delete">
           <el-radio :label="0">正常</el-radio>
           <el-radio :label="1">作废</el-radio>
         </el-radio-group>
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
import { formatModel } from '@/utils/base.js';
export default {
  data() {
    return {
      /*是否加载完成*/
      loading: true,
      /*form表单数据*/
      form: {
        code_id: 0,
        code_pwd: '',
        start_time: '',
        end_time: '',
        code_status: '',
        is_delete: ''
      },
      /*新闻类别*/
      model: {}
    };
  },
  created() {
    this.getDetail();
  },

  methods: {

    getDetail() {
      let self = this;
      // 取到路由带过来的参数
      const params = self.$route.query.code_id;
      CardApi.toEditCode({code_id: params},true).then(res => {
          self.model = res.data.model;
          self.form = formatModel(self.form, res.data.model);
          self.loading = false;
        })
        .catch(error => {});
    },

    /*修改文章*/
    onSubmit() {
      let self = this;
      let params = self.form;
      params.code_id = self.$route.query.code_id;
      // 取到路由带过来的参数
      CardApi.editCode(params, true)
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
