<template>

  <div class="user">
    <div class="common-form d-s-c"><span>设置项</span></div>
    <div class="p20">
      <el-form ref="form" size="small" label-width="160px">
        <el-form-item label="是否开启活动" prop="is_open"><el-switch v-model="form.is_open"></el-switch></el-form-item>
        <el-form-item label="是否开启优惠券" prop="is_coupon"><el-switch v-model="form.is_coupon"></el-switch></el-form-item>
        <el-form-item label="是否开启分销" prop="is_agent"><el-switch v-model="form.is_agent"></el-switch></el-form-item>
        <!--提交-->
        <div class="common-button-wrapper">
          <el-button type="primary" @click="onSubmit" :loading="loading">提交</el-button>
        </div>
      </el-form>
    </div>
  </div>
</template>
<script>
import PointProductApi from '@/api/pointproduct.js';
export default {
  data() {
    return {
      form: {
        /*是否开启活动*/
        is_open: false,
        /*是否开启优惠券*/
        is_coupon: false,
         /*是否开启分销*/
        is_agent: false,
      },
      loading: false
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
      PointProductApi.getConfig({}, true)
        .then(data => {
          self.form = data.data.vars.values;
        })
        .catch(error => {});
    },

    /*提交保存*/
    onSubmit() {
      let self = this;
      let params = self.form;
      self.loading = true;
      PointProductApi.saveConfig(params, true)
        .then(data => {
          self.loading = false;
          ElMessage({
            message: '恭喜你，保存成功',
            type: 'success'
          });
          self.getData();
        })
        .catch(error => {
          self.loading = false;
        });
    }
  }
};
</script>

<style>

</style>
