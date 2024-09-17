<template>

  <div>
    <div class="product-content">
      <el-form size="small" ref="form" :model="form" label-width="150px">
        <!--基础设置-->
        <Basic :form="form"></Basic>

        <!--时间设置-->
        <Datetime :form="form"></Datetime>

        <!--活动商品-->
        <ProductIndex :form="form"></ProductIndex>

        <!--其它-->
        <Other :form="form"></Other>
      </el-form>
    </div>

    <!--提交-->
    <div class="common-button-wrapper">
      <el-button size="small" @click="cancelFunc">取消</el-button>
      <el-button size="small" type="primary" @click="onSubmit" :disabled="loading">提交</el-button>
    </div>
  </div>
</template>
<script>
import AssembleApi from '@/api/assemble.js';
import Basic from './part/Basic.vue';
import Datetime from './part/Datetime.vue';
import ProductIndex from './part/Product.vue';
import Other from './part/Other.vue';
export default {
  components: {
    /*基础设置*/
    Basic,
    /*时间设置*/
    Datetime,
    /*活动商品*/
    ProductIndex,
    /*其它*/
    Other
  },
  data() {
    return {
      form: {
        /*活动名称*/
        title: '',
        /*广告图片ID*/
        image_id: 0,
        /*活动广告图*/
        file_path:'',
        /*活动时间*/
        start_time: '',
        end_time: '',
        /*是否生效，默认1为生效，0为不生效*/
        status: 1,
        /*拼团失败处理方式：0失败退款1自动拼团成功*/
        fail_type: 0,
        /*是否单团0否1是*/
        is_single: 0,
        /*默认排序*/
        sort: 100,
        /*凑团时间*/
        together_time: 24,
        /*商品源数据*/
        tableData: [],
        /*商品列表*/
        product_list: []
      },
      /*判断是否正在加载*/
      loading: false,
    };
  },
  created() {},
  methods: {

    /*提交表单*/
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
          AssembleApi.addActive(params, true)
            .then(data => {
              self.loading = false;
              ElMessage({
                message: '恭喜你，添加成功',
                type: 'success'
              });
              self.$router.push('/plus/assemble/index');
            })
            .catch(error => {
              self.loading = false;
            });
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
          assemble_stock: item.assemble_stock,
          assemble_price: item.assemble_price,
          product_attr: item.spec_name,
          assemble_product_sku_id:item.assemble_product_sku_id,
          product_price:item.product_price,
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
