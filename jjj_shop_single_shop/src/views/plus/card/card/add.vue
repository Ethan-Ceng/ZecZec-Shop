<template>

  <div class="product-add pb50">
    <!--添加文章-->
    <el-form size="small" :model="form" ref="form" label-width="100px">
      <div class="common-form">添加卡券</div>
      <el-form-item label="卡券标题" prop="card_title" :rules="[{ required: true, message: ' ' }]">
        <el-input v-model="form.card_title" placeholder="" class="max-w460"></el-input>
      </el-form-item>
      <el-form-item label="卡券描述" prop="card_content" :rules="[{ required: true, message: ' ' }]">
        <el-input v-model="form.card_content" placeholder="" class="max-w460"></el-input>
      </el-form-item>
      <el-form-item label="卡券售价：" prop="sell_price" :rules="[{ required: true, message: ' ' }]">
        <el-input v-model="form.sell_price" type="number" class="max-w460"></el-input>
      </el-form-item>
      <el-form-item label="市场价格：" prop="card_price" :rules="[{ required: true, message: ' ' }]">
        <el-input v-model="form.card_price" type="number" class="max-w460"></el-input>
      </el-form-item>
      <!--<el-form-item label="库存：" prop="stock_num" :rules="[{ required: true, message: ' ' }]">
        <el-input v-model="form.stock_num" type="number" class="max-w460"></el-input>
      </el-form-item>-->
      <el-form-item label="卡券封面">
        <div>
          <el-button type="primary" @click="openUpload">上传图片</el-button>
          <img :src="path" class="mt10" :width="120" v-if="isImg" :isImg="isImg" />
          <!--上传图片组件-->
          <Upload v-if="isupload" :isupload="isupload" @returnImgs="returnImgsFunc">上传图片</Upload>
        </div>
      </el-form-item>
      <el-form-item label="卡券分类">
        <el-select v-model="form.category_id" placeholder="请选择" style="width: 460px;">
          <el-option v-for="(item, index) in category" :key="index" :label="item.name" :value="item.category_id">
          </el-option>
        </el-select>
      </el-form-item>
      <el-form-item label="关联商品">
        <el-select v-model="form.product_id" filterable placeholder="请选择" style="width: 460px;">
          <el-option v-for="(item, index) in product" :key="index" :label="item.product_name" :value="item.product_id">
          </el-option>
        </el-select>
      </el-form-item>
      <el-form-item label="商品规格" prop="product_attr">
        <el-input v-model="form.product_attr" placeholder="" class="max-w460"></el-input>
      </el-form-item>
      <el-form-item label="卡券状态">
        <el-radio-group v-model="form.card_status">
          <el-radio :label="10">上架</el-radio>
          <el-radio :label="20">下架</el-radio>
        </el-radio-group>
      </el-form-item>
      <el-form-item label="排序">
        <el-input type="number" v-model="form.card_sort" class="max-w460"></el-input>
        <div class="tips">数字小的排前面</div>
      </el-form-item>
      <!--提交-->
      <div class="common-button-wrapper">
        <el-button size="small" type="info" @click="cancelFunc">取消</el-button>
        <el-button size="small" type="primary" @click="onSubmit" :loading="loading">提交</el-button>
      </div>
    </el-form>
  </div>
</template>

<script>
  import CardApi from '@/api/card.js';
  import Upload from '@/components/file/Upload.vue';
  export default {
    components: {
      /*图片上传*/
      Upload
    },
    data() {
      return {
        /*是否上传图片*/
        isupload: false,
        isImg: false,
        path: '',
        /*form表单数据*/
        form: {
          card_title: '',
          category_id: '',
          image_id: '',
          card_sort: 100,
          card_status: 10,
          card_content: '',
          stock_num: '',
          product_id: '',
          card_price: '',
          sell_price: '',
          product_attr: ''
        },
        /*文章类别*/
        category: [],
        product: [],
        loading: false,
      };
    },
    created() {
      /*获取列表*/
      this.getCategoryList();
    },
    methods: {
      /*上传*/
      openUpload() {
        this.isupload = true;
      },

      /*获取图片*/
      returnImgsFunc(e) {
        let self = this;
        if (e != null) {
          this.form.image_id = e[0].file_id;
          this.path = e[0].file_path;
          this.isImg = true;
        }
        this.isupload = false;
      },

      /*获取文章类别*/
      getCategoryList() {
        let self = this;
        let Params = {};

        CardApi.toAddCard(Params, true)
          .then(res => {
            self.category = res.data.category;
            if (self.category.length > 0) {
              self.form.category_id = self.category[0].category_id;
            }
            self.product = res.data.product;
            if (self.product.length > 0) {
              self.form.product_id = self.product[0].product_id;
            }
          })
          .catch(error => {

          });
      },

      /*添加文章*/
      onSubmit() {
        let self = this;
        let form = self.form;
        self.$refs.form.validate(valid => {
          if (valid) {
            self.loading = true;
            CardApi.addCard(form, true)
              .then(data => {
                self.loading = false;
                ElMessage({
                  message: data.msg,
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

      /*取消添加，返回文章列表*/
      cancelFunc() {
        this.$router.back(-1);
      }
    }
  };
</script>

<style>
</style>
