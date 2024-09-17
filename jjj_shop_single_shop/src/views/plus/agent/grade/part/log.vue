<template>
  <div class="user">
    <!--内容-->
    <div class="product-content">
      <div class="table-wrap">
        <el-table size="small" :data="tableData" border style="width: 100%" v-loading="loading">
          <el-table-column prop="user_id" label="用户ID"></el-table-column>
          <el-table-column prop="agent.real_name" label="分销员"></el-table-column>
          <el-table-column prop="oldGrade.name" label="原等级"></el-table-column>
          <el-table-column prop="grade.name" label="新等级"></el-table-column>
          <el-table-column label="升级方式">
            <template #default="scope">
              <span v-if="scope.row.change_type == 10" class="green">管理员操作</span>
              <span v-if="scope.row.change_type == 20">自动升级</span>
            </template>
          </el-table-column>
          <el-table-column prop="create_time" label="升级时间"></el-table-column>
        </el-table>
      </div>
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
</template>

<script>
  import GradeApi from '@/api/plus/agent/grade.js';
  export default {
    data() {
      return {
        /*是否加载完成*/
        loading: true,
        /*列表数据*/
        tableData: [],
        /*一页多少条*/
        pageSize: 20,
        /*一共多少条数据*/
        totalDataNumber: 0,
        /*当前是第几页*/
        curPage: 1,
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
        self.curPage = val;
        self.loading = true;
        self.getData();
      },
      /*每页多少条*/
      handleSizeChange(val) {
        this.curPage = 1;
        this.getData();
      },
      /*获取列表*/
      getData() {
        let self = this;
        let Params = {};
        Params.page = self.curPage;
        Params.list_rows = self.pageSize;
        GradeApi.loglist(Params, true)
          .then(data => {
            self.loading = false;
            self.tableData = data.data.list.data;
            self.totalDataNumber = data.data.list.total;
          })
          .catch(error => {

          });
      },
    }
  };
</script>

<style></style>
