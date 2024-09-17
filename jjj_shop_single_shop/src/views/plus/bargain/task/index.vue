<template>

  <div class="bargain-task">
    <!--搜索表单-->
    <div class="common-seach-wrap">
      <el-form size="small" :inline="true" :model="searchForm" class="demo-form-inline" style="float: right;">
        <el-form-item label=""><el-input v-model="searchForm.search" placeholder="请输入商品名称/用户昵称"></el-input></el-form-item>
        <el-form-item><el-button type="primary" icon="Search" @click="onSubmit">查询</el-button></el-form-item>
      </el-form>
    </div>

    <div class="product-content">
      <div class="table-wrap">
        <el-table size="small" :data="tableData" border style="width: 100%" v-loading="loading">
          <el-table-column prop="product_name" label="商品名称" width="200">
            <template #default="scope">
              <div class="d-s-c">
                <img class="product-pic" :src="scope.row.file_path" />
                <div class="text-ellipsis">{{ scope.row.product_name }}</div>
              </div>
            </template>
          </el-table-column>
          <el-table-column prop="nickName" label="用户信息" width="200">
            <template #default="scope">
              <div class="user-info d-s-c">
                <img :src="scope.row.user.avatarUrl" />
                <div class="ml10">
                  <span>{{ scope.row.user.nickName }}</span>
                  <span class="gray">ID:{{ scope.row.user.user_id }}</span>
                </div>
              </div>
            </template>
          </el-table-column>
          <el-table-column prop="bargain_price" label="砍价底价">
            <template #default="scope">
              <div class="red fb">{{ scope.row.bargain_price }}</div>
            </template>
          </el-table-column>
          <el-table-column prop="cut_money" label="已砍金额">
            <template #default="scope">
              <div class="orange">{{ scope.row.cut_money }}</div>
            </template>
          </el-table-column>
          <el-table-column prop="is_buy" label="是否购买">
            <template #default="scope">
              <span v-if="scope.row.is_buy == 0" class="gray">暂无购买</span>
              <span v-if="scope.row.is_buy == 1" class="green">已购买</span>
            </template>
          </el-table-column>
          <el-table-column prop="status" label="砍价状态">
            <template #default="scope">
              <span v-if="scope.row.status == 0" class="gray3">进行中</span>
              <span v-if="scope.row.status == 1" class="green">砍价成功</span>
              <span v-if="scope.row.status == 2" class="gray">砍价失败</span>
            </template>
          </el-table-column>
          <el-table-column prop="end_time_text" label="截止时间" width="140"></el-table-column>
          <el-table-column prop="create_time" label="创建时间" width="140"></el-table-column>
          <el-table-column fixed="right" label="操作" width="75">
            <template #default="scope">
              <el-button @click="detailFunc(scope.row)" type="text" size="small">砍价详情</el-button>
            </template>
          </el-table-column>
        </el-table>
      </div>

      <!--分页-->
      <div class="pagination">
        <el-pagination
          @size-change="handleSizeChange"
          @current-change="handleCurrentChange"
          background
          :current-page="curPage"
          :page-sizes="[2, 10, 20, 50, 100]"
          :page-size="pageSize"
          layout="total, sizes, prev, pager, next, jumper"
          :total="totalDataNumber"
        ></el-pagination>
      </div>
    </div>

    <!--砍价详情-->
    <Detail :is_detail="is_detail" :curModel="curModel" @close="closeFunc"></Detail>
  </div>
</template>
<script>
import BargainApi from '@/api/bargain.js';
import Detail from './dialog/detail.vue';
export default {
  components: {
    Detail
  },
  data() {
    return {
      /*搜索表单*/
      searchForm: {},
      /*表格数据*/
      tableData: [],
      /*一页多少条*/
      pageSize: 10,
      /*一共多少条数据*/
      totalDataNumber: 0,
      /*当前是第几页*/
      curPage: 1,
      /*是否加载完成*/
      loading: true,
      /*当前砍价*/
      curModel: {},
      /*是否显示详情*/
      is_detail: false
    };
  },
  created() {
    /*获取列表*/
    this.getData();
  },
  methods: {
    /*搜索*/
    onSubmit() {
      this.curPage = 1;
      this.getData();
    },

    /*获取列表*/
    getData() {
      let self = this;
      let Params = self.searchForm;
      Params.page = self.curPage;
      Params.list_rows = self.pageSize;
      self.loading = true;
      BargainApi.taskList(Params, true)
        .then(res => {
          self.loading = false;
          self.tableData = res.data.list.data;
          self.totalDataNumber = res.data.list.total;
        })
        .catch(error => {});
    },

    /*选择第几页*/
    handleCurrentChange(val) {
      this.curPage = val;
      this.getData();
    },

    /*每页多少条*/
    handleSizeChange(val) {
      this.curPage = 1;
      this.pageSize = val;
      this.getData();
    },

    /*查看砍价详情*/
    detailFunc(e) {
      this.curModel = e;
      this.is_detail = true;
    },

    /*关闭砍价详情*/
    closeFunc() {
      this.curModel = {};
      this.is_detail = false;
    }
  }
};
</script>

<style>
.bargain-task .user-info img {
  width: 30px;
  height: 30px;
  border-radius: 50%;
}
.bargain-task .product-pic {
  width: 30px;
  height: 30px;
}
</style>
