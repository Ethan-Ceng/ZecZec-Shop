<template>

  <div class="user">
    <!--添加管理员-->
    <div class="common-level-rail">
      <el-button size="small" type="primary" icon="Plus" @click="addClick" v-auth="'/plus/table/table/add'">添加表单</el-button>
    </div>

    <!--内容-->
    <div class="product-content">
      <div class="table-wrap">
        <el-table size="small" :data="tableData" border style="width: 100%" v-loading="loading">
          <el-table-column prop="name" label="表单名称"></el-table-column>
          <el-table-column prop="total_count" label="表单记录"></el-table-column>
          <el-table-column prop="create_time" label="添加时间"></el-table-column>
          <el-table-column fixed="right" label="操作" width="120">
            <template #default="scope">
              <el-button @click="editClick(scope.row)" type="text" size="small" v-auth="'/plus/table/table/edit'">编辑</el-button>
              <el-button @click="deleteClick(scope.row)" type="text" size="small" v-auth="'/plus/table/table/delete'">删除</el-button>
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
import TableApi from '@/api/plus/table.js';
export default {
  data() {
    return {
      /*是否加载完成*/
      loading: true,
      /*列表数据*/
      tableData: [],
      /*一页多少条*/
      pageSize: 15,
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
      TableApi.getList(Params, true)
        .then(data => {
          self.loading = false;
          self.tableData = data.data.list.data;
          self.totalDataNumber = data.data.list.total;
        })
        .catch(error => {});
    },

    /*打开添加*/
    addClick() {
        this.$router.push('/plus/table/table/add');
    },

    /*打开编辑*/
    editClick(row) {
      this.$router.push({
        path: '/plus/table/table/edit',
        query: {
          table_id: row.table_id
        }
      });
    },

    /*删除*/
    deleteClick(row) {
      let self = this;
      ElMessageBox.confirm('此操作将永久删除该记录, 是否继续?', '提示', {
          confirmButtonText: '确定',
          cancelButtonText: '取消',
          type: 'warning'
        })
        .then(() => {
          self.loading = true;
          TableApi.del(
            {
              table_id: row.table_id
            },
            true
          )
            .then(data => {
              self.loading = false;
              if (data.code == 1) {
                ElMessage({
                  message: '恭喜你，删除成功',
                  type: 'success'
                });
                //刷新页面
                self.getTableList();
              } else {
                self.loading = false;
              }
            })
            .catch(error => {
              self.loading = false;
            });
        })
        .catch(() => {});
    }
  }
};
</script>

<style></style>
