<template>

  <div class="product-add">
    <!--form表单-->
    <el-form size="small" ref="form" :model="form" label-width="200px">
      <el-form-item label="是否开启优惠券抵扣" prop="is_coupon">
        <el-switch v-model="form.is_coupon"></el-switch>
        <!-- <p>注：如需使用分销功能必须在 [分销中心 - 分销设置] 中开启</p> -->
      </el-form-item>
      <el-form-item label="是否开启分销" prop="is_distribution">
        <el-switch v-model="form.is_agent"></el-switch>
        <!-- <p>注：如需使用分销功能必须在 [分销中心 - 分销设置] 中开启</p> -->
      </el-form-item>
      <el-form-item label="是否开启积分抵扣" prop="is_distribution">
        <el-switch v-model="form.is_point"></el-switch>
        <!-- <p>注：如需使用分销功能必须在 [分销中心 - 分销设置] 中开启</p> -->
      </el-form-item>

      <el-form-item label="砍价规则">
        <div class="max-w460">
          <el-input v-model="form.bargain_rules" type="textarea" rows="10"></el-input>
        </div>
      </el-form-item>

      <!--提交-->
      <div class="common-button-wrapper">
        <el-button size="small" type="primary" @click="onSubmit">提交</el-button>
      </div>
    </el-form>
  </div>
</template>

<script>
import BargainApi from '@/api/bargain.js';
export default {
  data() {
    return {
      /*form表单数据*/
      form: {
        is_agent: 'false',
        is_coupon: 'false',
        is_point: 'false'
      }
    };
  },
  created() {
    this.getData();
  },

  methods: {

    /*获取数据*/
    getData() {
      let self = this;
      BargainApi.settingDetail({}, true)
        .then(data => {
          self.form = data.data.vars.values;
          if (self.form.is_coupon == 'true') {
            self.form.is_coupon = true;
          }
          if (self.form.is_agent == 'true') {
            self.form.is_agent = true;
          }
          if (self.form.is_point == 'true') {
            self.form.is_point = true;
          }
        })
        .catch(error => {});
    },

    /*提交表单*/
    onSubmit() {
      let self = this;
      let params = this.form;
      BargainApi.editSetting(params, true)
        .then(data => {
          ElMessage({
            message: '恭喜你，设置成功',
            type: 'success'
          });
          this.getData();
        })
        .catch(error => {});
    }
  }
};
</script>

<style></style>
