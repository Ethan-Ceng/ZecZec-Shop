<template>
  <div class="user">
    <div class="operation-wrap">
      <p> 注：模板消息只能发送给活跃用户，查看活跃用户列表，建议每次发送不超过10人。</p>
      <p> 注：根据腾讯官方规定，滥用模板消息接口有被封号的风险，请谨慎使用！</p>
    </div>
    <el-form ref="form" :model="form" label-width="100px">
      <!--添加门店-->
      <div class="common-form">发送推送消息</div>
      <el-form-item label="用户ID ">
        <el-input v-model="form.article_title" placeholder="请输入用户ID "></el-input>
      </el-form-item>
      <el-form-item label="模板消息ID">
        <el-input v-model="form.article_title" placeholder="请输入模板消息ID"></el-input>
      </el-form-item>
      <el-form-item label=" 模板内容1">
        <el-input v-model="form.virtual_views" placeholder="请输入模板内容第1行内容  没有可以不填写"></el-input>
      </el-form-item>
      <el-form-item label=" 模板内容2">
        <el-input v-model="form.virtual_views" placeholder="请输入模板内容第2行内容  没有可以不填写"></el-input>
      </el-form-item>
      <el-form-item label=" 模板内容3">
        <el-input v-model="form.virtual_views" placeholder="请输入模板内容第3行内容  没有可以不填写"></el-input>
      </el-form-item>
      <el-form-item label=" 模板内容4">
        <el-input v-model="form.virtual_views" placeholder="请输入模板内容第4行内容  没有可以不填写"></el-input>
      </el-form-item>
      <el-form-item label=" 模板内容5">
        <el-input v-model="form.virtual_views" placeholder="请输入模板内容第5行内容  没有可以不填写"></el-input>
      </el-form-item>
      <el-form-item label=" 跳转页面">
        <el-input v-model="form.virtual_views" value="pages/index/index" placeholder="pages/index/index"></el-input>
      </el-form-item>
      <!--提交-->

       <div class="common-button-wrapper">
        <el-button type="primary" @click="onSubmit">提交</el-button>
      </div>

    </el-form>
  </div>
</template>
<script>
  import PushApi from '@/api/push.js';
  export default {
    data() {
      return {
        form: {
          status: 0,
        },
      };
    },
    methods: {
      /*添加文章*/
      onSubmit() {
        let self = this;
        let params = this.form;
        PushApi.send(params, true)
          .then(data => {
            if (data.code == 1) {
              ElMessage({
                message: '恭喜你，添加成功',
                type: 'success'
              });
              self.$router.push('/plus/article/Index');
            } else {
              ElMessage.error('错了哦，这是一条错误消息');
            }
          })
          .catch(error => {

          });
      },
    }
  };
</script>

<style>
</style>
