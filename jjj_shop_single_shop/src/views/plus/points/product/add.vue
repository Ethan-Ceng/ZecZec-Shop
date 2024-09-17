<template>

  <div class="pb50">
    <el-form size="small" ref="form" :model="form" label-width="150px" v-if="!loading">
      <!--商品信息-->
      <Info></Info>

      <!--规格-->
      <Spec></Spec>

      <!--其它-->
      <Other></Other>

      <!--提交-->
    </el-form>

    <div class="common-button-wrapper">
      <el-button size="small" type="info" @click="cancelFunc">取消</el-button>
      <el-button size="small" type="primary" @click="onSubmit" :loading="loading">提交</el-button>
    </div>
  </div>
</template>
<script>
import PointProductApi from '@/api/pointproduct.js';
import Info from './part/Info.vue';
import Spec from './part/Spec.vue';
import Other from './part/Other.vue';
import { formatModel } from '@/utils/base.js';
export default {
  components: {
    /*产品基本信息*/
    Info,
    /*规格*/
    Spec,
    /*其它*/
    Other
  },
  data() {
    return {
      /*是否正在加载*/
      loading: true,
      /*表单*/
      form: {
        /*商品ID*/
        product_id: 0,
        /*商品状态*/
        status: 10,
        /*商品名称*/
        product_name: '',
        /*排序*/
        sort: 100,
        /*规格*/
        product_sku_id: 0,
        /*限购数量*/
        limit_num: 1,
        /*商品表格*/
        tableData: []
      }
    };
  },
  provide: function() {
    return {
      form: this.form,
      type: 'add'
    };
  },
  created() {
    if (this.$route.query.product_id != null) {
      this.form.product_id = this.$route.query.product_id;
      this.getData();
    }
  },
  methods: {
    /*获取商品*/
    getData() {
      let self = this;
      PointProductApi.getProduct(
        {
          product_id: self.form.product_id
        },
        true
      ).then(res => {
        Object.assign(self.form, res.data.model);
        self.creatSpec();
        self.loading = false;
      });
    },

    /*格式规格表格数据*/
    creatSpec() {
      let self = this;
        let obj = {
          product_name: self.form.product_name,
          product_id: self.form.product_id,
          product_price: self.form.product_price,
          stock_num: self.form.product_stock,
          product_sku_id: self.form.product_sku.product_sku_id,
          sales_num: 0,
          spec_type: self.form.spec_type,
          point_price: null,
          point_money:0,
          point_stock: 0,
          point_num: 0,
          sort: 100,
          tempProduct: true,
          is_delete: 0,
          point_product_id:0,
          point_product_sku_id: 0,
          sales_initial:0
        };
        self.form.tableData.push(obj);
    },

    /*提交表单*/
    onSubmit() {
      let self = this;
      self.$refs.form.validate(valid => {
        if (valid) {
          let params = {
            product: {}
          };
          params.product_id = self.form.product_id;
          params.status = self.form.status;
          params.sort = self.form.sort;
          params.limit_num = self.form.limit_num;
          params.product.spec_list = self.tableFormet(self.form.tableData);
          self.loading = true;
          PointProductApi.addProduct(params, true)
            .then(data => {
              self.loading = false;
              ElMessage({
                message: '恭喜你，添加成功',
                type: 'success'
              });
              self.cancelFunc();
            })
            .catch(error => {
              self.loading = false;
            });
        }
      });
    },

    /*表格数据格式化*/
    tableFormet(list) {
      list.forEach(item=>{
        item.product_attr=item.spec_name!=null?item.spec_name:'';
      });
      return list;
    },

    /*取消*/
    cancelFunc() {
      this.$router.back(-1);
    }
  }
};
</script>
<style>
</style>
