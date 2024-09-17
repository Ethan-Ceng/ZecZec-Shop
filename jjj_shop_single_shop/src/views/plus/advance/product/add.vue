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
  import AdvanceApi from '@/api/advance.js';
  import Info from './part/Info.vue';
  import Spec from './part/Spec.vue';
  import Other from './part/Other.vue';
  import {
    formatModel
  } from '@/utils/base.js';
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
          /*定金*/
          money: '',
          /*尾款立减金额*/
          reduce_money: '',
          /*活动时间*/
          active_time: [],
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
        AdvanceApi.getProduct({
            product_id: self.form.product_id
          },
          true
        ).then(res => {
          Object.assign(self.form, res.data.model);
          res.data.model.sku.forEach(item => {
            item.product_name = self.form.product_name;
            item.spec_type = res.data.model.spec_type;
            res.data.specList.forEach(sitem => {
              if (item.spec_sku_id == sitem.spec_sku_id) {
                item.spec_name = sitem.spec_name;
              }
            });
            item.advance_price = 0;
            item.advance_stock = 0;
            item.sort = 100;
            item.tempProduct = true;
            item.is_delete = 0;
            item.advance_product_id = 0;
            item.advance_product_sku_id = 0;
            item.sales_initial = 0
          });

          self.form.tableData = res.data.model.sku;
          // self.creatSpec();
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
          spec_type: self.form.spec_type,
          advance_price: 0,
          advance_stock: 0,
          sort: 100,
          tempProduct: true,
          is_delete: 0,
          advance_product_id: 0,
          advance_product_sku_id: 0,
          sales_initial: 0
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
            params.money = self.form.money;
            params.status = self.form.status;
            params.sort = self.form.sort;
            params.limit_num = self.form.limit_num;
            params.active_time = self.form.active_time;
            params.reduce_money = self.form.reduce_money;
            params.product.spec_list = self.tableFormet(self.form.tableData);
            self.loading = true;
            AdvanceApi.addProduct(params, true)
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
        list.forEach(item => {
          item.product_attr = item.spec_name != null ? item.spec_name : '';
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
<style lang="scss" scoped></style>
