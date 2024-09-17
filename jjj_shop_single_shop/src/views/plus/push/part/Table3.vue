<template>
  <div class="user">
    <el-form ref="form" :model="form" label-width="150px">
      <!--添加门店-->
      <div class="common-form">满额包邮设置</div>
      <el-form-item label="是否开启满额包邮">
        <el-radio-group v-model="form.status">
          <el-radio :label="0">关</el-radio>
          <el-radio :label="1">开</el-radio>
        </el-radio-group>
      </el-form-item>
      <el-form-item label=" 单笔订单满 ">
        <div style="width: 500px;">
          <el-input placeholder="请输入金额" v-model="input2">
            <template #append>元</template>
          </el-input>
          <p>如果开启满额包邮，设置0为全场包邮</p>
        </div>
      </el-form-item>
      <el-form-item label="不参与包邮的商品">
        <el-button type="primary">请选择商品</el-button>
      </el-form-item>
      <el-form-item label="不参与包邮的地区">
        <el-button type="primary">请选择地区</el-button>
      </el-form-item>
      <!--提交-->

      <el-form-item>
        <el-button type="primary" @click="onSubmit">提交</el-button>
      </el-form-item>

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
      onSubmit() {
        let self = this;
        let params = this.form;
        PushApi.shipping(params, true)
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
