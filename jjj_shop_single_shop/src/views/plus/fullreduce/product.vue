<template>
  <!--
      描述：商品满减
  -->
  <div class="product-list">
    <!--搜索表单-->
    <div class="common-seach-wrap">
      <el-form size="small" :inline="true" :model="searchForm" class="demo-form-inline">
        <el-form-item label="商品分类">
          <el-select size="small" v-model="searchForm.category_id" placeholder="所有分类">
            <el-option label="全部" value="0"></el-option>
            <el-option v-for="(item, index) in categoryList" :key="index" :label="item.name" :value="item.category_id">
            </el-option>
          </el-select>
        </el-form-item>
        <el-form-item label="商品名称">
          <el-input size="small" v-model="searchForm.product_name" placeholder="请输入商品名称"></el-input>
        </el-form-item>
        <el-form-item>
          <el-button size="small" type="primary" icon="Search" @click="onSubmit">查询</el-button>
        </el-form-item>
        <el-form-item>
          <el-button size="small" type="success"  @click="reset">重置</el-button>
        </el-form-item>
      </el-form>
    </div>
    <!--内容-->
    <div class="product-content">
      <el-tabs v-model="activeName" @tab-change="handleClick">
        <el-tab-pane label="全部" name="-1">
          <template #label>
            <span>全部</span>
          </template>
        </el-tab-pane>
        <el-tab-pane label="参与" name="1">
          <template #label>
            <span>参与</span>
          </template>
        </el-tab-pane>
        <el-tab-pane label="不参与" name="0">
          <template #label>
            <span>不参与</span>
          </template>
        </el-tab-pane>
      </el-tabs>
      <div class="table-wrap">
        <el-table size="small" :data="tableData" border style="width: 100%" v-loading="loading" ref="multipleTable">
          <el-table-column type="selection" width="55">
          </el-table-column>
          <el-table-column prop="product_name" label="产品" width="300px">
            <template #default="scope">
              <div class="product-info">
                <div class="pic" v-if="scope.row.image">
                  <img v-img-url="scope.row.image && scope.row.image[0] && scope.row.image[0].file_path" alt=""  /></div>
                <div class="info">
                  <div class="name">{{ scope.row.product_name }}</div>
                </div>
              </div>
            </template>
          </el-table-column>
          <el-table-column prop="product_price" label="价格">
            <template #default="scope">
              <div class="gray3">￥{{ scope.row.product_price }}</div>
            </template>
          </el-table-column>
          <el-table-column prop="product_stock" label="库存"></el-table-column>
          <el-table-column prop="sales_actual" label="实际销量"></el-table-column>
          <el-table-column prop="is_agent" label="是否满减">
            <template #default="scope">
              <div class="green" v-if="scope.row.reduce_pid != null">是</div>
              <div class="gray3" v-else>否</div>
            </template>
          </el-table-column>
          <el-table-column label="满减活动">
            <template #default="scope">
              <div v-if="scope.row.reduce_pid != null">
                <div v-for="(item,index) in scope.row.reduce_list" :key="index" :label="item" :value="index">
                <span v-if="item.full_type == 1">满{{item.full_value}}元</span>
                <span v-if="item.full_type == 2">满{{item.full_value}}件</span>
                <span v-if="item.reduce_type == 1">减{{item.reduce_value}}元</span>
                <span v-if="item.reduce_type == 2">折扣{{100 - item.reduce_value}}%</span>
              </div>
              </div>
            </template>
          </el-table-column>
          <el-table-column fixed="right" label="操作" width="80">
            <template #default="scope">
              <div class="table-btn-column">
                <el-button @click="settingsClick(scope.row)" type="text" size="small">设置</el-button>
              </div>
            </template>
          </el-table-column>
        </el-table>
      </div>
    </div>
    <!--分页-->
    <div class="pagination">
      <el-pagination @size-change="handleSizeChange" @current-change="handleCurrentChange" background
        :current-page="curPage" :page-size="pageSize" layout="total, prev, pager, next, jumper"
        :total="totalDataNumber"></el-pagination>
    </div>
    <!--添加-->
    <Settings v-if="open_settings" :open_settings="open_settings" :productModel="productModel"
      @closeDialog="closeDialogFunc($event, 'settings')"></Settings>
  </div>
</template>

<script>
  import FullreduceApi from '@/api/plus/fullreduce.js';
  import Settings from './dialog/Settings.vue';
  export default {
    components: {
      Settings
    },
    data() {
      return {
        /*切换菜单*/
        activeName: '-1',
        /*切换选中值*/
        activeIndex: '0',
        /*是否正在加载*/
        loading: true,
        /*一页多少条*/
        pageSize: 10,
        /*一共多少条数据*/
        totalDataNumber: 0,
        /*当前是第几页*/
        curPage: 1,
        /*搜索参数*/
        searchForm: {
          product_name: '',
          category_id: ''
        },
        /*列表数据*/
        tableData: [],
        /*全部分类*/
        categoryList: [],
        /*商品统计*/
        product_count: {},
        open_settings: false,
        productModel: {}
      };
    },
    created() {
      /*获取列表*/
      this.getData();
    },
    methods: {
      /*选择第几页*/
      handleCurrentChange(val) {
        let self = this;
        self.loading = true;
        self.curPage = val;
        self.getData();
      },

      /*每页多少条*/
      handleSizeChange(val) {
        this.pageSize = val;
        this.getData();
      },

      /*切换菜单*/
      handleClick(tab, event) {
        let self = this;
        self.activeName = tab;
        self.curPage = 1;
        self.getData();
      },

      /*获取列表*/
      getData() {
        let self = this;
        let Params = self.searchForm;
        Params.page = self.curPage;
        Params.list_rows = self.pageSize;
        Params.is_join = self.activeName;
        console.log("self.activeName",self.activeName)
        self.loading = true;
        FullreduceApi.product(Params, true)
          .then(data => {
            self.loading = false;
            self.tableData = data.data.list.data;
            self.categoryList = data.data.category;
            self.totalDataNumber = data.data.list.total;
            self.product_count = data.data.product_count;
          })
          .catch(error => {
            self.loading = false;
          });
      },
      /*搜索查询*/
      onSubmit() {
        this.curPage = 1;
        this.getData();
      },
      reset() {
        this.curPage = 1;
        this.searchForm = {
          product_name: '',
          category_id: ''
        }
        this.getData();
      },
      settingsClick(row) {
        this.productModel = {
          'product_id': row.product_id,
          'product_name': row.product_name,
          'reduce_pid': row.reduce_pid,
          'reduce_list': row.reduce_list
        };
        this.open_settings = true;
      },
      /*关闭弹窗*/
      closeDialogFunc(e, f) {
        this.open_settings = e.openDialog;
        if (e.type == 'success') {
          this.getData();
        }
      },
    }
  };
</script>

<style></style>
