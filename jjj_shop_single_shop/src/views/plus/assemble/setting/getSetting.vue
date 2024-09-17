<template>

  <div class="user">
    <el-form size="small" ref="form" :model="form" label-width="150px">
      <el-form-item label="是否开启优惠券抵扣" prop="is_coupon">
        <el-switch v-model="form.is_coupon">
        </el-switch>
        <!-- <p>注：如需使用分销功能必须在 [分销中心 - 分销设置] 中开启</p> -->
      </el-form-item>
      <el-form-item label="是否开启分销" prop="is_agent">
        <el-switch v-model="form.is_agent">
        </el-switch>
        <!-- <p>注：如需使用分销功能必须在 [分销中心 - 分销设置] 中开启</p> -->
      </el-form-item>
      <el-form-item label="是否开启积分抵扣" prop="is_point">
        <el-switch v-model="form.is_point">
        </el-switch>
        <!-- <p>注：如需使用分销功能必须在 [分销中心 - 分销设置] 中开启</p> -->
      </el-form-item>
      <!--提交-->
      <el-form-item>
        <el-button type="primary" @click="onSubmit" :loading="loading">保存</el-button>
      </el-form-item>
    </el-form>

  </div>
</template>
<script>
  import AssembleApi from '@/api/assemble.js';
  export default {
    data() {
      return {
        form: {
          is_coupon: false,
          is_distribution: false,
          is_point: false,
        },
        setting: [],
        loading: false,
      };
    },
    created() {
      /*获取列表*/
      this.getSetting();
    },
    methods: {
      /*获取设置*/
      getSetting() {
        let self = this;
        let Params = {};
        AssembleApi.getSetting(Params, true)
          .then(data => {
            self.loading = false;
            self.form = data.data.vars.values;
          })
          .catch(error => {});
      },
      /*点击保存*/
      onSubmit() {
        let self = this;
        let params = self.form;
        self.loading = true;
        AssembleApi.saveSetting(params, true)
          .then(data => {
            self.loading = false;
            if (data.code == 1) {
              ElMessage({
                message: '恭喜你，保存成功',
                type: 'success'
              });
              self.getSetting();
            } else {
              self.loading = false;
            }
          })
          .catch(error => {
            self.loading = false;
          });
      },
    }
  };
</script>

<style>
</style>
