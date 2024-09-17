<template>
  <div class="user">
    <div class="common-form">编辑转盘</div>
    <div class="product-content">
      <el-form ref="form" :model="form" :rules="formRules" label-width="150px">
        <el-form-item label="每日次数" prop="times" :rules="[{required: true,message: ' '}]">
          <el-input type="text" v-model="form.times" placeholder="请输入每日次数" class="max-w460"></el-input>
        </el-form-item>
        <el-form-item label="消耗积分" prop="points" :rules="[{required: true,message: ' '}]">
          <el-input type="text" v-model="form.points" placeholder="请输入消耗积分" class="max-w460"></el-input>
        </el-form-item>
        <el-form-item label="活动状态" prop="status" :rules="[{required: true,message: ' '}]">
          <el-switch v-model="form.status" active-color="#13ce66" inactive-color="#999999" :active-value="1"
            :inactive-value="0">
          </el-switch>
        </el-form-item>
        <el-form-item label="活动规则" prop="content" :rules="[{required: true,message: ' '}]">
          <el-input type="textarea" :rows="3" v-model="form.content" placeholder="请输入活动规则" class="max-w460"></el-input>
        </el-form-item>
        <el-form-item label="背景图" prop="file_path">
          <el-row>
            <el-button type="primary" @click="openUpload" size="small">选择图片</el-button>
            <div v-if="form.file_path!=''" class="img">
              <img :src="form.file_path" width="100" height="100" />
            </div>
          </el-row>
		  <div>建议图片上传尺寸为750px*1340px</div>
        </el-form-item>
        <div class="common-form">礼品设置</div>
		 <div>建议图片上传尺寸为200px*200px</div>
        <el-form-item label="" v-if="tableData.length<8">
          <el-button type="primary" @click="addCoupon()">添加优惠券</el-button>
          <el-button type="primary" @click="addProduct()">添加商品</el-button>
          <el-button type="primary" @click="addPoint()">添加积分</el-button>
          <el-button type="primary" @click="addNothing()">添加空项</el-button>
        </el-form-item>
        <el-form-item>
          <el-table :data="tableData" highlight-current-row @current-change="handleCurrentChange" ref="singleTable">
            <el-table-column prop="lottery_id" label="序号" type="index">
            </el-table-column>
            <el-table-column prop="name" label="名称" :rules="[{required: true,message: ' '}]">
              <template #default="scope">
                <el-input type="text" v-if="scope.row.type==0" v-model="scope.row.name" placeholder="请输入">
                </el-input>
                <span v-else>{{scope.row.name}}</span>
              </template>
            </el-table-column>
            <el-table-column prop="image" label="图片" width="180">
              <template #default="scope">
                <img v-img-url="hasImages(scope.row.image)" alt="" :width="50" @click="changeImg(scope.row)" />
              </template>
            </el-table-column>
            <el-table-column prop="type" label="类型">
              <template #default="scope">
                <span v-if="scope.row.type==0">无礼品</span>
                <span v-if="scope.row.type==1">优惠券</span>
                <span v-if="scope.row.type==2">积分</span>
                <span v-if="scope.row.type==3">商品</span>
              </template>
            </el-table-column>
            <el-table-column prop="stock" label="活动库存" :rules="[{required: true,message: ' '}]">
              <template #default="scope">
                <el-input type="number" v-model="scope.row.stock" placeholder="">
                </el-input>
              </template>
            </el-table-column>
            <el-table-column prop="draw_num" label="已送出">
            </el-table-column>
            <el-table-column prop="is_default" label="是否默认" :rules="[{required: true,message: ' '}]">
              <template #default="scope">
                <el-switch @change="changeSwitch(scope.row)" :model-value="scope.row.is_default==1" active-color="#13ce66"
                  inactive-color="#999999">
                </el-switch>
              </template>
            </el-table-column>
            <el-table-column prop="is_play" label="是否播报" :rules="[{required: true,message: ' '}]">
              <template #default="scope">
                <el-switch @change="changePlaySwitch($event,scope.row)" :active-value="1" :inactive-value="0"
                  v-model="scope.row.is_play" active-color="#13ce66" inactive-color="#999999">
                </el-switch>
              </template>
            </el-table-column>
            <el-table-column prop="address" label="操作">
              <template #default="scope">
                <el-button type="text" size="small" @click='delcoupon(scope.row)'> 删除 </el-button>
              </template>
            </el-table-column>
          </el-table>
        </el-form-item>
      </el-form>
      <!--提交-->
      <div class="common-button-wrapper">
        <el-button type="info" @click="gotoBack">返回</el-button>
        <el-button type="primary" @click="onSubmit" :loading="loading">提交</el-button>
      </div>
    </div>
    <el-dialog title="添加积分" v-model="isPoint">
      <el-input v-model="point" autocomplete="off"></el-input>
      <template #footer> 
          <div class="dialog-footer">
              <el-button @click="closePointFunc(false)">取 消</el-button>
              <el-button type="primary" @click="closePointFunc(true)">确 定</el-button>
          </div>
      </template>
    </el-dialog>
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
  import LotteryApi from '@/api/lottery.js';
  import GetCoupon from '@/components/coupon/GetCoupon.vue';
  import Product from '@/components/product/Product.vue';
  import Upload from '@/components/file/Upload.vue';
  import {
    formatModel
  } from '@/utils/base.js';
  export default {
    components: {
      /*选择优惠券件*/
      GetCoupon,
      Product,
      Upload
    },
    data() {
      return {
        form: {
          prize: [],
          points: 0,
          is_times: 0,
          coupon_num: 1,
          times: '',
          name: '',
          lottery_id: 0,
          image_id: '',
          content: '',
          file_path: '',
          status: 0
        },
        file_path: '',
        Grade: {},
        tableData: [],
        loading: false,
        open_add: false,
        /*是否打开选择商品*/
        isproduct: false,
        exclude_ids: [],
        spec_type: 10,
        /*是否打开图片选择*/
        isupload: false,
        formRules: {
          image_id: [{
            required: true,
            message: '请上传背景图',
            trigger: 'blur'
          }],
        },
        /*左边长度*/
        formLabelWidth: '120px',
        isPoint: false,
        point: '',
        isNothing: false,
        isLottery: -1,
        prize_ids: []
      };
    },
    created() {
      /*获取数据*/
      this.getData();
    },
    methods: {
      hasImages(e) {
        if (e) {
          return e;
        } else {
          return '';
        }
      },
      /*添加优惠券*/
      addCoupon() {
        this.open_add = true;
      },
      /*关闭优惠券*/
      closeProductDialogFunc(e) {
        let self = this;
        self.open_add = e.openDialog;
        if (e.type == 'success') {
          let params = {
            prize_id: 0,
            award_id: e.params.coupon_id,
            name: e.params.name,
            stock: 1,
            type: 1,
            image: '',
            points: 0,
            is_default: 0,
            is_play: 0
          }
          self.tableData.push(params);
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
          let params = {
            prize_id: 0,
            award_id: e.params.product_id,
            name: e.params.product_name,
            stock: 1,
            type: 3,
            image: e.params.product_image,
            points: 0,
            is_default: 0,
            is_play: 0
          }
          self.tableData.push(params);
        }
      },
      addPoint() {
        this.isPoint = true;
      },
      closePointFunc(e) {
        let self = this;
        self.isPoint = e.openDialog;
        let points = this.point;
        if (e) {
          let params = {
            prize_id: 0,
            award_id: 0,
            name: '积分',
            points: 0,
            stock: 1,
            type: 2,
            image: '',
            is_default: 0,
            is_play: 0
          }
          params.points = points;
          params.name = points + '积分';
          self.tableData.push(params);
        }
      },
      addNothing() {
        let self = this;
        let params = {
          prize_id: 0,
          award_id: 0,
          name: '祝你好运',
          stock: 1,
          type: 0,
          image: '',
          points: 0,
          is_default: 0,
          is_play: 0
        }
        self.tableData.push(params);
      },
      /*获取数据*/
      getData() {
        let self = this;
        LotteryApi.getData(true).then(data => {
          self.form = formatModel(self.form, data.data.data);
          if (self.form.prize != null) {
            self.tableData = self.form.prize;
            self.prize_ids = [];
          }
        }).catch(error => {});
      },
      delProduct(item) {
        let self = this;
        let n = self.prodcutData.indexOf(item);
        self.prodcutData.splice(n, 1);
        self.form.product.splice(n, 1);
      },
      delcoupon(item) {
        let self = this;
        let n = self.tableData.indexOf(item);
        if (item.prize_id) {
          self.prize_ids = [...self.prize_ids, item.prize_id];
        }
        self.tableData.splice(n, 1);
      },
      /*提交表单*/
      onSubmit() {
        let self = this;
        let form = self.form;
        if (self.tableData.length != 8) {
          ElMessage.error('请设置8项礼品信息');
          return
        }
        let flag = false;
        self.tableData.forEach((item, index) => {
          if (item.is_default == 1) {
            flag = true;
            return
          }
        })
        if (!flag) {
          ElMessage.error('请设置默认项');
          return
        }
        form.prize = self.tableData;
        if (self.prize_ids.length > 0) {
          form.prize_ids = self.prize_ids;
        } else {
          form.prize_ids = '';
        }
        self.$refs.form.validate((valid) => {
          if (valid) {
            self.loading = true;
            LotteryApi.EditLottery(form, true).then(data => {
                self.loading = false;
                if (data.code == 1) {
                  ElMessage({
                    message: data.msg,
                    type: 'success'
                  });
                  self.tableData = [];
                  self.getData();
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
      changeImg(e) {
        this.isLottery = this.tableData.indexOf(e);
        this.isupload = true;
      },
      /*上传*/
      openUpload(e) {
        this.type = e;
        this.isupload = true;
      },
      /*获取图片*/
      returnImgsFunc(e) {

        if (e != null && e.length > 0) {
          if (this.isLottery != -1) {
            this.tableData[this.isLottery].image = e[0].file_path;
          } else {
            this.form.file_path = e[0].file_path;
            this.form.image_id = e[0].file_id;
          }

        }
        this.isLottery = -1;
        this.isupload = false;
      },
      /*返回上一页面*/
      gotoBack() {
        this.$router.back(-1);
      },
      setCurrent(row) {
        this.$refs.singleTable.setCurrentRow(row);
      },
      handleCurrentChange(val) {
        this.currentRow = val;
      },
      changeSwitch(row) {
        this.tableData.forEach((item, index) => {
          item.is_default = 0;
        })
        row.is_default = 1
      },
      changePlaySwitch(e, row) {
        row.is_play = e;
      }
    }
  };
</script>
<style lang="scss" scoped></style>
