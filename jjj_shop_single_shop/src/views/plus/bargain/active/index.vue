<template>

  <div class="bargain-active-index">
    <!--搜索表单-->
    <div class="d-b-c">
      <div class="mb18"><el-button size="small" icon="Plus" type="primary" @click="addClick">添加砍价活动</el-button></div>
      <el-form size="small" :inline="true" :model="searchForm" class="demo-form-inline">
        <el-form-item label=""><el-input v-model="searchForm.search" placeholder="请输入活动名称"></el-input></el-form-item>
        <el-form-item><el-button type="primary" icon="Search" @click="onSubmit">查询</el-button></el-form-item>
      </el-form>
    </div>

    <div class="product-content">
      <div class="table-wrap">
        <el-table size="small" :data="tableData" border style="width: 100%" v-loading="loading">
          <el-table-column prop="title" label="活动名称"></el-table-column>
          <el-table-column prop="start_time_text" label="开始日期" width="150"></el-table-column>
          <el-table-column prop="end_time_text" label="结束时间" width="150"></el-table-column>
          <el-table-column prop="status_text" label="活动状态" width="120"></el-table-column>
          <el-table-column prop="product_num" label="产品数" width="70"></el-table-column>
          <el-table-column prop="total_sales" label="订单数" width="70"></el-table-column>
          <el-table-column prop="create_time" label="创建时间" width="150"></el-table-column>
          <el-table-column fixed="right" label="操作" width="110">
            <template #default="scope">
              <el-button @click="editsClick(scope.row.bargain_activity_id)" type="text" size="small">编辑</el-button>
              <el-button @click="deleteClick(scope.row.bargain_activity_id)" type="text" size="small">删除</el-button>
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
          :page-size="pageSize"
          layout="total, prev, pager, next, jumper"
          :total="totalDataNumber"
        ></el-pagination>
      </div>
    </div>
  </div>
</template>
<script>
import BargainApi from '@/api/bargain.js';
export default {
  data() {
    return {
      /*是否加载完成*/
      loading: true,
      /*搜索表单对象*/
      searchForm: {
        search:''
      },
      /*列表数据*/
      tableData: [],
      /*一页多少条*/
      pageSize: 10,
      /*一共多少条数据*/
      totalDataNumber: 0,
      /*当前是第几页*/
      curPage: 1
    };
  },
  created() {
    /*获取列表*/
    this.getData();
  },
  methods: {
    /*查询*/
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
      BargainApi.bargainList(Params, true)
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

    /*添加砍价活动*/
    addClick() {
      this.$router.push('/plus/bargain/active/add');
    },

    /*修改砍价活动*/
    editsClick(e) {
      let self = this;
      this.$router.push({
        path: '/plus/bargain/active/edit',
        query: {
          bargain_activity_id: e
        }
      });
    },

    /*删除砍价活动*/
    deleteClick(e) {
      let self = this;
      ElMessageBox.confirm('此操作将永久删除该记录, 是否继续?', '提示', {
          confirmButtonText: '确定',
          cancelButtonText: '取消',
          type: 'warning'
        })
        .then(() => {
          BargainApi.deleteBargain(
            {
              bargain_activity_id: e
            },
            true
          )
            .then(data => {
              ElMessage({
                message: data.msg,
                type: 'success'
              });
              self.getData();
            })
            .catch(error => {});
        })
        .catch(() => {
          ElMessage({
            type: 'info',
            message: '已取消删除'
          });
        });
    }
  }
};
</script>

<style></style>
