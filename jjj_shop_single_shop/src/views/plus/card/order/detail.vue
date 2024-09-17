<template>
<div class="pb50" v-loading="loading">
    <!--内容-->
    <div class="product-content">
      <!--基本信息-->
      <div class="common-form">基本信息</div>
      <div class="table-wrap">
        <el-row>
          <el-col :span="5">
            <div class="pb16">
              <span class="gray9">订单号：</span>
              {{ detail.order_no }}
            </div>
          </el-col>
          <el-col :span="5">
            <div class="pb16">
              <span class="gray9">买家：</span>
              {{ detail.user.nickName }}
              <span>用户ID：({{ detail.user.user_id }})</span>
            </div>
          </el-col>
          <el-col :span="5">
            <div class="pb16">
              <span class="gray9">发货状态：</span>
              <template v-if="detail.delivery_status == 0">待发货</template>
              <template v-if="detail.delivery_status == 1">已发货</template>
            </div>
          </el-col>
        </el-row>
      </div>
      <!--编辑-->
      <Add v-if="open_edit" :open_edit="open_edit" :order="userModel" @close="closeDialogFunc($event, 'edit')"></Add>
      <!--商品信息-->
      <div class="common-form mt16">商品信息</div>
      <div class="table-wrap">
        <el-row>
          <el-col :span="5">
            <div class="pb16">
              <span class="gray9">商品图片：</span>
              <img :src="detail.product_image" width="80" height="80" />
            </div>
          </el-col>
          <el-col :span="5">
            <div class="pb16">
              <span class="gray9">商品名称：</span>
              {{ detail.product_name }}
            </div>
          </el-col>
          <el-col :span="5">
            <div class="pb16">
              <span class="gray9">商品规格：</span>
              {{ detail.product_attr }}
            </div>
          </el-col>
          <el-col :span="5">
            <div class="pb16">
              <span class="gray9">商品价格：</span>
              {{ detail.product_price }}
            </div>
          </el-col>
        </el-row>
      </div>
      <!--收货信息-->
      <div>
        <div class="common-form mt16">收货信息</div>
        <div class="table-wrap">
          <el-row>
            <el-col :span="5">
              <div class="pb16">
                <span class="gray9">收货人：</span>
                {{ detail.name }}
              </div>
            </el-col>
            <el-col :span="5">
              <div class="pb16">
                <span class="gray9">收货电话：</span>
                {{ detail.mobile }}
              </div>
            </el-col>
            <el-col :span="5">
              <div class="pb16">
                <span class="gray9">收货地址：</span>
                {{ detail.region.province }} {{ detail.region.city }} {{ detail.region.region }}
                {{ detail.detail }}
              </div>
            </el-col>
          </el-row>
        </div>
      </div>
      <!--发货信息-->
      <div v-auth="'/order/order/delivery'">
        <div v-if="detail.delivery_status == 0">
          <!-- 去发货 -->
          <div class="common-form mt16">去发货</div>
          <el-form size="small" ref="form" :model="form" label-width="100px">
            <el-form-item label="物流公司">
              <el-select v-model="form.express_id" placeholder="请选择快递公司" style="width: 460px;">
                <el-option :label="item.express_name" v-for="(item, index) in expressList" :key="index"
                  :value="item.express_id"></el-option>
              </el-select>
            </el-form-item>
            <el-form-item label="物流单号">
              <el-input v-model="form.express_no" class="max-w460"></el-input>
            </el-form-item>
          </el-form>
        </div>
        <div v-else="">
          <div class="common-form mt16">发货信息</div>
          <div class="table-wrap">
            <el-row>
              <el-col :span="5">
                <div class="pb16">
                  <span class="gray9">物流公司：</span>
                  {{ detail.express.express_name }}
                </div>
              </el-col>
              <el-col :span="5">
                <div class="pb16">
                  <span class="gray9">物流单号：</span>
                  {{ detail.express_no }}
                </div>
              </el-col>
              <el-col :span="5">
                <div class="pb16">
                  <span class="gray9">发货时间：</span>
                  {{ detail.delivery_time }}
                </div>
              </el-col>
            </el-row>
          </div>
        </div>
      </div>
    </div>
    <div class="common-button-wrapper">
      <el-button size="small" type="info" @click="cancelFunc">返回上一页</el-button>
      <!--确认发货-->
      <template v-if="detail.delivery_status == 0">
        <el-button size="small" type="primary" @click="onSubmit">确认发货
        </el-button>
      </template>
    </div>
  </div>
</template>
<script>
  import CardApi from '@/api/card.js';
  import Add from './dialog/Add.vue';
  import {
    deepClone
  } from '@/utils/base.js';
  export default {
    components: {
      Add
    },
    data() {
      return {
        active: 0,
        /*是否加载完成*/
        loading: true,
        /*订单数据*/
        detail: {
          pay_status: [],
          pay_type: [],
          delivery_type: [],
          user: {},
          address: [],
          product: [],
          order_status: [],
          extract: [],
          extract_store: [],
          express: [],
          delivery_status: [],
          extractClerk: [],
          region: {}
        },
        /*是否打开添加弹窗*/
        open_add: false,
        /*一页多少条*/
        pageSize: 20,
        /*一共多少条数据*/
        totalDataNumber: 0,
        /*当前是第几页*/
        curPage: 1,
        /*发货*/
        form: {
          /*订单ID*/
          express_id: null,
          /*订单号*/
          express_no: ''
        },
        forms: {
          is_cancel: 1
        },
        extract_form: {
          order: {
            extract_status: 1
          }
        },
        /*虚拟商品发货*/
        virtual_form: {
          virtual_content: ''
        },
        order: {},
        delivery_type: 0,
        /*配送方式*/
        exStyle: [],
        /*门店列表*/
        shopList: [],
        /*当前编辑的对象*/
        userModel: {},
        /*时间*/
        create_time: '',
        /*快递公司列表*/
        expressList: [],
        shopClerkList: [],
        /*是否打开编辑弹窗*/
        open_edit: false
      };
    },
    created() {
      /*获取列表*/
      this.getParams();
    },
    methods: {
      next() {
        if (this.active++ > 4) this.active = 0;
      },
      /*获取参数*/
      getParams() {
        let self = this;
        // 取到路由带过来的参数
        const params = this.$route.query.order_id;
        CardApi.orderdetail({
              order_id: params
            },
            true
          )
          .then(data => {
            self.loading = false;
            self.detail = data.data.detail;
            self.expressList = data.data.expressList;
            self.shopClerkList = data.data.shopClerkList;
          })
          .catch(error => {
            self.loading = false;
          });
      },

      /*发货*/
      onSubmit() {
        let self = this;
        let param = self.form;
        if (param.express_id == null) {
          ElMessage.error('请选择物流公司');
          return;
        }
        if (param.express_no == '') {
          ElMessage.error('请填写物流单号');
          return;
        }
        let order_id = this.$route.query.order_id;
        CardApi.delivery({
              param: param,
              order_id: order_id
            },
            true
          )
          .then(data => {
            self.loading = false;
            ElMessage({
              message: '恭喜你，发货成功',
              type: 'success'
            });
            self.getParams();
          })
          .catch(error => {
            self.loading = false;
          });
      },

      /*打开编辑*/
      editClick(item) {
        this.userModel = deepClone(item);
        this.open_edit = true;
      },

      /*关闭弹窗*/
      closeDialogFunc(e, f) {
        if (f == 'edit') {
          this.open_edit = e.openDialog;
          this.getParams();
        }
      },

      /*取消*/
      cancelFunc() {
        this.$router.back(-1);
      }

    }
  };
</script>
<style></style>