<template>

  <div class="user">
    <!--内容-->
    <div class="product-content">
      <div class="table-wrap">
        <el-table size="small" :data="tableData" border style="width: 100%" v-loading="loading">
          <el-table-column prop="prize_id" label="ID" width="80"></el-table-column>
          <el-table-column prop="name" label="名称"></el-table-column>
          <el-table-column prop="nickName" label="图片">
            <template #default="scope">
              <img  v-if="scope.row.image" :src="scope.row.image" :width="30" :height="30" />
            </template>
          </el-table-column>
          <el-table-column prop="draw_num" label="抽奖次数"></el-table-column>
          <el-table-column prop="stock" label="活动库存"></el-table-column>
          <el-table-column prop="type" label="礼品类型">
            <template #default="scope">
              <span v-if="scope.row.type==0">无礼品</span>
              <span v-if="scope.row.type==1">优惠券</span>
              <span v-if="scope.row.type==2">积分</span>
              <span v-if="scope.row.type==3">商品</span>
            </template>
          </el-table-column>
          <el-table-column prop="status_text" label="状态">
          </el-table-column>
        </el-table>
      </div>
      <!--分页-->
      <div class="pagination">
        <el-pagination @size-change="handleSizeChange" @current-change="handleCurrentChange" background
          :current-page="curPage" :page-size="pageSize" layout="total, prev, pager, next, jumper"
          :total="totalDataNumber"></el-pagination>
      </div>
    </div>
  </div>
</template>

<script>
  import LotteryApi from '@/api/lottery.js';
  export default {
    components: {},
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
      this.getTableList();
    },
    methods: {
      /*选择第几页*/
      handleCurrentChange(val) {
        let self = this;
        self.curPage = val;
        self.loading = true;
        self.getTableList();
      },

      /*每页多少条*/
      handleSizeChange(val) {
        this.curPage = 1;
        this.pageSize = val;
        this.getTableList();
      },
      /*获取列表*/
      getTableList() {
        let self = this;
        let Params = {};
        Params.page = self.curPage;
        Params.list_rows = self.pageSize;
        LotteryApi.getAward(Params, true)
          .then(res => {
            self.loading = false;
            self.tableData = res.data.list.data;
            self.totalDataNumber = res.data.list.total;
          })
          .catch(error => {
            self.loading = false;
          });
      },
      /*搜索查询*/
      onSubmit() {
        let self = this;
        self.loading = true;
        self.curPage = 1;
        self.getTableList();
      },
    }
  };
</script>
<style lang="scss" scoped></style>
