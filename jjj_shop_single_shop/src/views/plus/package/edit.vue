<template>
  <div class="user">
    <div class="common-form">新增活动会场</div>
    <div class="product-content">
      <el-form ref="form" :model="form" :rules="formRules" label-width="150px">
        <el-form-item label="活动标题" prop="name" :rules="[{ required: true, message: ' ' }]">
          <el-input type="text" v-model="form.name" placeholder="请输入活动标题" class="max-w460"></el-input>
        </el-form-item>
        <el-form-item label="背景图" prop="image_id">
          <el-row>
            <el-button type="primary" @click="openUpload">选择图片</el-button>
            <div v-if="form.image_id != ''" class="img"><img :src="file_path" width="100" height="100" /></div>
			<div class="gray">建议上传图片尺寸为100px*100px</div>
          </el-row>
        </el-form-item>
        <el-form-item label="活动日期" prop="value1" :rules="[{ required: true, message: ' ' }]">
          <div class="block">
            <span class="demonstration"></span>
            <el-date-picker
              v-model="form.value1"
              type="datetimerange"
              value-format="YYYY-MM-DD"
              range-separator="至"
              start-placeholder="开始日期"
              end-placeholder="结束日期"
            ></el-date-picker>
          </div>
        </el-form-item>
        <div class="common-form">礼包设置</div>
        <el-form-item label="优惠券 "><el-checkbox v-model="form.is_coupon">只能选择不限等级、不限数量、不限领取数量的优惠券</el-checkbox></el-form-item>
        <el-form-item label="" v-if="form.is_coupon">
          <el-button type="primary" @click="addCoupon()">添加</el-button>
          <el-table :data="form.coupon" style="width: 60%">
            <el-table-column prop="coupon_id" label="优惠券id"></el-table-column>
            <el-table-column prop="name" label="优惠券"></el-table-column>
            <el-table-column prop="coupon_num" label="数量">
              <template #default="scope">
                <el-input type="number" v-model="scope.row.coupon_num" placeholder="" min="1" max="10" @input="max10(scope.row.coupon_num, scope.$index)"></el-input>
              </template>
            </el-table-column>
            <el-table-column prop="address" label="操作">
              <template #default="scope">
                <el-button type="text" size="small" @click="delcoupon(scope.row)">删除</el-button>
              </template>
            </el-table-column>
          </el-table>
        </el-form-item>
        <el-form-item label="商品"><el-checkbox v-model="form.is_product">选择商品</el-checkbox></el-form-item>
        <el-form-item v-if="form.is_product" label="商品选购数量" prop="product_num" :rules="[{ required: true, trigger: 'blur', validator: validatePass }]">
          <el-input type="number" v-model="form.product_num" min="1" placeholder="请输入商品选购数量" class="max-w460"></el-input>
        </el-form-item>
        <el-form-item label="" v-if="form.is_product">
          <el-button type="primary" @click="addProduct()">添加</el-button>
          <el-table :data="prodcutData" style="width: 40%">
            <el-table-column prop="product_name" label="商品"></el-table-column>
            <el-table-column prop="product_" label="操作">
              <template #default="scope">
                <el-button type="text" size="small" @click="delProduct(scope.row)">删除</el-button>
              </template>
            </el-table-column>
          </el-table>
        </el-form-item>
        <el-form-item label="积分 "><el-checkbox v-model="form.is_point">不受每人每日限领规则限制</el-checkbox></el-form-item>
        <el-form-item label=" " v-if="form.is_point">
          <!--:disabled="true"-->
          <el-input min="1" type="number" v-model="form.point" class="max-w460">
            <template #append>
              积分
            </template>
          </el-input>
        </el-form-item>
        <div class="common-form">购买设置</div>
        <el-form-item label="购买金额" prop="money" :rules="[{ required: true, message: ' ' }]">
          <el-input min="1" type="number" v-model="form.money" class="max-w460">
            <template #append>
              元
            </template>
          </el-input>
        </el-form-item>

        <el-form-item label="会员购买等级 ">
          <el-radio-group v-model="form.is_grade">
            <el-radio :label="0">不限</el-radio>
            <el-radio :label="1">指定等级</el-radio>
          </el-radio-group>
        </el-form-item>
        <el-form-item label="会员等级 " v-if="form.is_grade == 1">
          <el-select v-model="form.grade_ids" multiple placeholder="请选择">
            <el-option v-for="(item, index) in Grade" :key="index" :label="item.name" :value="item.grade_id + ''"></el-option>
          </el-select>
        </el-form-item>
        <el-form-item label="购买次数 ">
          <el-radio-group v-model="form.is_times">
            <el-radio :label="0">不限</el-radio>
            <el-radio :label="1">限购</el-radio>
          </el-radio-group>
        </el-form-item>
        <el-form-item label="" v-if="form.is_times == 1">
          <el-input min="1" type="number" v-model="form.times" class="max-w460">
            <template #prepend>
              每人限购
            </template>
            <template #append>
              次
            </template>
          </el-input>
        </el-form-item>
        <div class="common-form">发放设置</div>
        <el-form-item label="发放数量">{{ form.total_num }}</el-form-item>
        <el-form-item label="二维码类型">
          <label v-if="form.code_type == 10">一批一码</label>
          <label v-if="form.code_type == 20">一物一码</label>
        </el-form-item>
        <el-form-item label="增发数量" prop="total_num">
          <el-input min="1" type="number" v-model="form.add_num" placeholder="请输入增发数量" class="max-w460"></el-input>
        </el-form-item>
      </el-form>
      <!--提交-->
      <div class="common-button-wrapper">
        <el-button type="info" @click="gotoBack">返回</el-button>
        <el-button type="primary" @click="onSubmit" :loading="loading">提交</el-button>
      </div>
    </div>
    <!--上传图片组件-->
    <Upload v-if="isupload" :isupload="isupload" :type="type" @returnImgs="returnImgsFunc">上传图片</Upload>
    <!--选择优惠券-->
    <GetCoupon v-if="open_add" :open_add="open_add" @closeDialog="closeProductDialogFunc($event)">选择优惠券弹出层</GetCoupon>
    <!--选择商品-->
    <Product :isproduct="isproduct" :excludeIds="exclude_ids" :islist="false" @closeDialog="closeProductFunc"></Product>
  </div>
</template>
<script>
import UserApi from '@/api/user.js';
import PackageApi from '@/api/package.js';
import GetCoupon from '@/components/coupon/GetCoupon.vue';
import Product from '@/components/product/Product.vue';
import Upload from '@/components/file/Upload.vue';
import { formatModel } from '@/utils/base.js';
export default {
  components: {
    /*选择优惠券件*/
    GetCoupon,
    Product,
    Upload
  },
  data() {
    var validatePassFunc = (rule, value, callback) => {
      console.log(value);
      console.log(this.prodcutData.length);
      if (value === '') {
        callback(new Error('请输入选购数量'));
      } else {
        if (value > this.prodcutData.length) {
          callback(new Error('选购数量必须小于商品数量'));
        } else {
          callback();
        }
      }
    };
    const valiNumFunc = (rule, value, callback) => {
      console.log(value);
      if (value > 10) {
        return callback(new Error('数量小于等于10'));
      } else {
        callback();
      }
    };
    return {
      validatePass: validatePassFunc,
      valiNum: valiNumFunc,
      form: {
        is_coupon: false,
        is_product: false,
        coupon: [],
        product: [],
        is_point: false,
        point: 0,
        is_times: 0,
        coupon_num: 1,
        times: 0,
        is_grade: 0,
        grade_ids: '',
        value1: [],
        name: '',
        gift_package_id: 0,
        product_name: '',
        total_num: '',
        image_id: '',
        product_num: '',
        add_num: 0,
        code_type: '',
        money: ''
      },
      file_path: '',
      Grade: {},
      tableData: [],
      prodcutData: [],
      loading: false,
      open_add: false,
      /*是否打开选择商品*/
      isproduct: false,
      exclude_ids: [],
      /*是否打开图片选择*/
      isupload: false,
      formRules: {
        image_id: [
          {
            required: true,
            message: '请上传背景图',
            trigger: 'blur'
          }
        ]
      },
      /*左边长度*/
      formLabelWidth: '120px'
    };
  },
  created() {
    /*获取等级*/
    this.getGradeList();
    /*获取数据*/
    this.getData();
  },
  methods: {
    /*添加优惠券*/
    addCoupon() {
      this.open_add = true;
    },
    /*关闭优惠券*/
    closeProductDialogFunc(e) {
      let self = this;
      self.open_add = e.openDialog;
      if (e.type == 'success') {
        if (self.form.coupon.length < 1) {
          self.form.coupon.push({
            coupon_id: e.params.coupon_id,
            name: e.params.name,
            coupon_num: 1
          });
        } else {
          let flag = true;
          self.form.coupon.forEach((item, index) => {
            if (item.coupon_id == e.params.coupon_id) {
              flag = false;
            }
          });
          if (flag) {
            self.form.coupon.push({
              coupon_id: e.params.coupon_id,
              name: e.params.name,
              coupon_num: 1
            });
          } else {
            ElMessage.error('请勿重复添加');
          }
        }
      }
    },
    /*添加商品*/
    addProduct() {
      this.isproduct = true;
    },
    /*关闭商品*/
    closeProductFunc(e) {
      let self = this;
      self.isproduct = e.openDialog;
      if (e.type == 'success') {
        self.form.product.push(e.params.product_id);
        self.prodcutData.push(e.params);
      }
    },
    /*获取等级*/
    getGradeList() {
      let self = this;
      let Params = {};
      Params.page = self.curPage;
      Params.list_rows = self.pageSize;
      UserApi.gradelist(Params, true)
        .then(data => {
          self.Grade = data.data.list.data;
        })
        .catch(error => {});
    },
    /*获取数据*/
    getData() {
      let self = this;
      let gift_package_id = self.$route.query.gift_package_id;
      PackageApi.getData(
        {
          gift_package_id: gift_package_id
        },
        true
      )
        .then(data => {
          self.form = formatModel(self.form, data.data.data);
          if (data.data.data.coupon) {
            self.form.coupon = self.form.coupon;
          } else {
            self.form.coupon = [];
          }
          if (data.data.data.is_product == 0) {
            self.form.product = [];
            self.prodcutData = [];
          } else {
            self.form.product_ids = [];
            self.prodcutData = data.data.data.product_list;
          }
          self.file_path = data.data.data.file_path;
          console.log(self.form.coupon);
        })
        .catch(error => {});
    },
    delProduct(item) {
      let self = this;
      let n = self.prodcutData.indexOf(item);
      self.prodcutData.splice(n, 1);
      self.form.product.splice(n, 1);
    },
    delcoupon(item) {
      let self = this;
      let n = self.form.coupon.indexOf(item);
      self.form.coupon.splice(n, 1);
    },
    /*提交表单*/
    onSubmit() {
      let self = this;
      let form = self.form;
      if (!self.form.coupon || self.form.coupon.length <= 0) {
        form.coupon = '';
      }
      if (!self.form.is_coupon && !self.form.is_product && !self.form.is_point) {
        ElMessage.error('请至少设置一个礼包类型');
        return;
      }
      if (self.form.is_coupon && self.form.coupon.length <= 0) {
        ElMessage.error('请至少设置一个优惠券');
        return;
      }
      if (self.form.is_product && self.form.product.length <= 0) {
        ElMessage.error('请至少设置一个一个商品');
        return;
      }
      if (self.form.is_point && self.form.point <= 0) {
        ElMessage.error('设置积分不能为0');
        return;
      }
      self.$refs.form.validate(valid => {
        if (valid) {
          self.loading = true;
          PackageApi.EditPackage(form, true)
            .then(data => {
              self.loading = false;
              if (data.code == 1) {
                ElMessage({
                  message: data.msg,
                  type: 'success'
                });
                self.$router.push('/plus/package/index');
              } else {
                self.loading = false;
              }
            })
            .catch(error => {
              self.loading = false;
            });
        }
      });
    },
    /*上传*/
    openUpload(e) {
      this.type = e;
      this.isupload = true;
    },
    /*获取图片*/
    returnImgsFunc(e) {
      if (e != null && e.length > 0) {
        this.file_path = e[0].file_path;
        this.form.image_id = e[0].file_id;
      }
      this.isupload = false;
    },
    /*返回上一页面*/
    gotoBack() {
      this.$router.back(-1);
    },
    max10(e, n) {
      if (e >= 10) {
        this.form.coupon[n].coupon_num = 10;
      }
      if (e <= 0) {
        this.form.coupon[n].coupon_num = '';
      }
    }
  }
};
</script>
