<template>

  <div class="user">
    <el-form size="small" ref="form" :model="form" label-width="150px">
      <!--添加门店-->
      <div class="common-form">整点秒杀设置</div>
      <el-form-item label=" 未支付订单" prop="order_close" :rules="[{ required: true, message: ' ' }]">
        <div style="width: 500px;">
          <el-input placeholder="请输入" v-model="form.order_close" type="number">
            <template #append>
              分钟后自动关闭
            </template>
          </el-input>
          <p class="gray">秒杀订单下单未付款，n分钟后自动关闭，设置0则不自动关闭</p>
        </div>
      </el-form-item>
      <el-form-item label="是否开启优惠券抵扣" prop="is_coupon">
        <el-switch v-model="form.is_coupon"></el-switch>
      </el-form-item>
      <el-form-item label="是否开启分销" prop="is_agent">
        <el-switch v-model="form.is_agent"></el-switch>
        <!-- <p>注：如需使用分销功能必须在 [分销中心 - 分销设置] 中开启</p> -->
      </el-form-item>
      <el-form-item label="是否开启积分抵扣" prop="is_point">
        <el-switch v-model="form.is_point"></el-switch>
      </el-form-item>
    </el-form>

    <!--提交-->
    <div class="common-button-wrapper">
      <el-button size="small" type="primary" @click="onSubmit" :disabled="loading">保存</el-button>
    </div>
  </div>
</template>
<script>
import SeckillApi from '@/api/seckill.js';
export default {
  data() {
    return {
      form: {
        order_close: 10,
        is_coupon: false,
        is_agent: false,
        is_point: false
      },
      setting: [],
      loading: false
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
      SeckillApi.getSetting(Params, true)
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
      if (!(params.order_close > -1) || !params.order_close) {
        ElMessage({
          message: '未支付订单时间有误',
          type: 'error'
        });
        return false;
      }
      self.loading = true;
      SeckillApi.saveSetting(params, true)
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
    }
  }
};
</script>

<style lang="scss" scoped></style>
