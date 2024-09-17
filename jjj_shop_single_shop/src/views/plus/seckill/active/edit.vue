<template>

  <div class="pb50" v-loading="loading">
    <el-form size="small" ref="form" :model="form" label-width="150px" v-if="!loading">
      <!--基础设置-->
      <Basic :form="form"></Basic>
      <!--活动时间-->
      <Datetime :form="form"></Datetime>
      <!--活动商品-->
      <ProductIndex :form="form" :type="'edit'"></ProductIndex>
      <!--其它-->
      <Other :form="form"></Other>
    </el-form>

    <!--提交-->
    <div class="common-button-wrapper">
      <el-button size="small" @click="cancelFunc">取消</el-button>
      <el-button size="small" type="primary" @click="onSubmit" :loading="loading">提交</el-button>
    </div>
  </div>
</template>
<script>
import SeckillApi from '@/api/seckill.js';
import Basic from './part/Basic.vue';
import Datetime from './part/Datetime.vue';
import ProductIndex from './part/Product.vue';
import Other from './part/Other.vue';
import {mergeTable} from '@/utils/table.js'
export default {
  components: {
    /*基础设置*/
    Basic,
    /*活动时间*/
    Datetime,
    /*活动商品*/
    ProductIndex,
    /*其它*/
    Other
  },
  data() {
    return {
      /*是否正在加载*/
      loading: true,
      /*表单对象*/
      form: {
        /*活动ID*/
        seckill_activity_id:null,
        /*图片ID*/
        image_id: 0,
        /*活动日期*/
        start_date: '',
        end_date: '',
        /*活动时间*/
        active_time: '',
        /*标题*/
        title: '',
        /*是否生效，默认1为生效，0为不生效*/
        status: 1,
        /*默认排序*/
        sort: 100,
        /*商品源数据*/
        tableData:[],
        /*商品列表*/
        product_list:[]
      }
    };
  },
  created() {
    /*获取列表*/
    this.form.seckill_activity_id = this.$route.query.seckill_activity_id;
    this.getData();
  },
  methods: {
    /*获取数据*/
    getData(){
      let self = this;
      self.loading = true;
      SeckillApi.getActive({seckill_activity_id:self.form.seckill_activity_id}, true)
        .then(res => {
          let tempForm=res.data.detail;
          tempForm.seckill_activity_id=self.form.seckill_activity_id;
          tempForm.product_del_ids=[];
          tempForm.tableData=[];
          let arr = [];
          for (let i = 0; i < res.data.product_list.length; i++) {
            let item = res.data.product_list[i];
            for(let j=0;j<item.seckillSku.length;j++){
              let sku=item.seckillSku[j];
              let obj = {
                product_name: item.product.product_name,
                product_id: item.product.product_id,
                product_price: sku.product_price,
                stock_num: item.product.product_stock,
                spec_type: item.product.spec_type,
                is_delete:item.is_delete,
                product_sku_id:sku.product_sku_id,
                spec_name:sku.product_attr,
                sales_num: item.total_sales,
                seckill_price: sku.seckill_price,
                seckill_stock: sku.seckill_stock,
                seckill_product_id:sku.seckill_product_id,
                limit_num: item.limit_num,
                sort:item.sort,
                seckill_product_sku_id:sku.seckill_product_sku_id,
                sales_initial:item.sales_initial,
                join_num:item.join_num
              };
               arr.push(obj);
            }
          }
          tempForm.tableData=mergeTable(arr);

          self.form=tempForm;
          self.loading = false;
        })
        .catch(error => {});
    },

    /*提交表单*/
    onSubmit() {
      let self = this;
      if(self.form.end_date == null || self.form.end_date == ''){
        ElMessage({
          message: '请填写活动结束日期',
          type: 'error'
        });
        return;
      }
      self.form.product_list=self.tableFormet(self.form.tableData);
      self.$refs.form.validate(valid => {
        if (valid) {
          self.loading = true;
          let params = self.form;
          delete params.tableData;
          SeckillApi.saveActive(params, true)
            .then(data => {
              self.loading = false;
              ElMessage({
                message: '恭喜你，修改成功',
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
    tableFormet(list){
      let spec_list=[];
      let newList=[];
      let curItem=null;
      for(let i=0;i<list.length;i++){
        let item=list[i];
        let obj={
          product_sku_id:item.product_sku_id,
          seckill_stock:item.seckill_stock,
          seckill_price:item.seckill_price,
          product_attr:item.spec_name,
          seckill_product_sku_id:item.seckill_product_sku_id,
          product_price:item.product_price,
          sales_initial:item.sales_initial
        }
        if(curItem==null){
          curItem=item;
        }
        if(curItem.product_id!=item.product_id){
          curItem.spec_list=spec_list;
          newList.push(curItem);
          curItem=item;
          spec_list=[];
        }
        spec_list.push(obj);
      }
      curItem.spec_list=spec_list;
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
<style lang="scss" scoped></style>
