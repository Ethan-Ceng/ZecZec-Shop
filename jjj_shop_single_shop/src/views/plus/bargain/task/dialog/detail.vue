<template>

<el-dialog title="砍价详情" v-model="dialogVisible" @close="dialogFormVisible" :close-on-click-modal="false" :close-on-press-escape="false" width="600px">

    <!--内容-->
      <div class="table-wrap">
        <el-table size="small" :data="tableData" border style="width: 100%" highlight-current-row v-loading="loading">
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
          <el-table-column prop="bargain_price" label="砍价">
            <template #default="scope">
              <div class="red fb">-{{ scope.row.cut_money }}</div>
            </template>
          </el-table-column>
          <el-table-column prop="create_time" label="创建时间" width="140"></el-table-column>
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
  </el-dialog>
</template>

<script>
import BargainApi from '@/api/bargain.js';
  export default {
    data() {
      return {
        /*是否加载完成*/
        loading: true,
        /*是否显示*/
        dialogVisible: false,
        /*表格数据*/
        tableData: [],
        /*一页多少条*/
        pageSize: 10,
        /*一共多少条数据*/
        totalDataNumber: 0,
        /*当前是第几页*/
        curPage: 1,
      };
    },
    props: ['is_detail','curModel'],
    watch:{
      is_detail:function(n,o){
        if(n!=o){
          if(n){
            this.specsList=[];
            this.dialogVisible=n;
            this.getData();
          }
        }
      }
    },
    created() {

    },
    methods: {

      /*获取规格列表*/
      getData() {
        let self = this;
        let Params = {};
        Params.bargain_task_id=self.curModel.bargain_task_id;
        Params.page = self.curPage;
        Params.list_rows = self.pageSize;
        self.loading = true;
        BargainApi.help(Params, true)
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

      /*关闭弹窗*/
      dialogFormVisible() {
        this.$emit('close', false);
      },

    }
  };
</script>

<style>
</style>
