<template>

  <div class="product-add">
    <!--form表单-->
    <el-form size="small" ref="form" :model="form" label-width="200px">

      <!--基本信息-->
      <Basic></Basic>

      <!--活动时间-->
      <Datetime></Datetime>

       <!--商品设置-->
      <ProductIndex></ProductIndex>

      <!--其它设置-->
      <Other></Other>

    </el-form>

    <!--提交-->
    <div class="common-button-wrapper">
      <el-button size="small" @click="cancelFunc">取消</el-button>
      <el-button size="small" type="primary" @click="onSubmit" :disabled="loading">提交</el-button>
    </div>

  </div>
</template>

<script>
import BargainApi from '@/api/bargain.js';
import Basic from './part/Basic.vue';
import Datetime from './part/Datetime.vue';
import ProductIndex from './part/Product.vue';
import Other from './part/Other.vue';
export default {
  components: {
    Basic,
    Datetime,
    ProductIndex,
    Other
  },
  data() {
    return {
      /*是否正在加载*/
      loading:false,
      /*form表单对象*/
      form: {
        /*活动名称*/
        title: '',
        /*广告图片ID*/
        image_id: 0,
        /*活动广告图*/
        file_path: '',
        /*活动时间*/
        start_time: '',
        end_time: '',
        /*砍价有效期*/
        together_time: 24,
        /*状态*/
        status: 1,
        /*购买条件*/
        conditions: 1,
        /*活动排序*/
        sort: 1,
        /*商品源数据*/
        tableData: [],
        /*商品列表*/
        product_list: []
      }
    };
  },
  provide: function() {
    return {
      form: this.form,
      type:''
    };
  },
  created() {},
  methods: {
    /*添加*/
    onSubmit() {
      let self = this;
      if(self.form.end_time == null || self.form.end_time == ''){
        ElMessage({
          message: '请填写活动结束时间',
          type: 'error'
        });
        return;
      }
      self.$refs.form.validate(valid => {
        if (valid) {
          self.form.product_list = self.tableFormet(self.form.tableData);
          let params = self.form;
          delete params.tableData;
          self.loading = true;
          BargainApi.addActive(params, true)
            .then(data => {
              ElMessage({
                message: '恭喜你，砍价活动添加成功',
                type: 'success'
              });
              self.$router.push('/plus/bargain/index');
            })
            .catch(error => {});
        }
      });

    },

    /*表格数据格式化*/
    tableFormet(list) {
      let spec_list = [];
      let newList = [];
      let curItem = null;
      for (let i = 0; i < list.length; i++) {
        let item = list[i];
        let obj = {
          product_sku_id: item.product_sku_id,
          bargain_stock: item.bargain_stock,
          bargain_price: item.bargain_price,
          product_attr: item.spec_name,
          product_price:item.product_price,
          bargain_num:item.bargain_num,
          bargain_product_sku_id:item.bargain_product_sku_id,
          sales_initial:item.sales_initial
        };
        if (curItem == null) {
          curItem = item;
        }
        if (curItem.product_id != item.product_id) {
          curItem.spec_list = spec_list;
          newList.push(curItem);
          curItem = item;
          spec_list = [];
        }
        spec_list.push(obj);
      }
      curItem.spec_list = spec_list;
      newList.push(curItem);
      return newList;
    },

    /*取消*/
    cancelFunc() {
      this.$router.back(-1);
    }
  }
};
</script>

<style lang="scss" scoped>
.product-add {
  padding-bottom: 50px;
}

.tips {
  color: #ccc;
}

.img {
  margin-top: 10px;
}
</style>
