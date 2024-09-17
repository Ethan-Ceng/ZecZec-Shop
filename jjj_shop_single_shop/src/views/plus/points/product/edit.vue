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
        point_product_id: 0,
        /*商品状态*/
        status: 10,
        /*商品名称*/
        product_name: '',
        /*图片*/
        product_image:'',
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
    if (this.$route.query.point_product_id != null) {
      this.form.point_product_id = this.$route.query.point_product_id;
      this.getData();
    }
  },
  methods: {
    /*获取商品*/
    getData() {
      let self = this;
      PointProductApi.getPointProduct(
        {
          point_product_id: self.form.point_product_id
        },
        true
      ).then(res => {
        self.form.product_name=res.data.model.product.product_name;
        self.form.product_image=res.data.model.product.image[0].file_path;
        self.form.product_id=res.data.model.product.product_id;
        self.form.limit_num=res.data.model.limit_num;
        self.form.status=res.data.model.status;
        self.form.sort=res.data.model.sort;
        self.form.stock=res.data.model.stock;
        res.data.model.sku.forEach(item=>{
          item.product_name= self.form.product_name;
          item.spec_name=item.product_attr;
          item.spec_type=res.data.model.product.spec_type;
        });
        self.form.tableData=res.data.model.sku;
        self.loading = false;
      });
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
          params.point_product_id=self.form.point_product_id;
          params.product_id=self.form.product_id;
          params.product.spec_list = self.tableFormet(self.form.tableData);
          self.loading = true;
          PointProductApi.editPointProduct(params, true)
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
        item.product_attr=item.spec_name;
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
