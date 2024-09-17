<template>

  <div>
    <div class="common-level-rail"><el-button size="small" type="primary" icon="Plus" @click="addArticle">添加卡券</el-button></div>
    <div class="table-wrap">
      <el-table :data="tableData" style="width: 100%" v-loading="loading">
        <el-table-column prop="card_id" label="卡券ID" width="60"></el-table-column>
        <el-table-column label="图片" width="50">
          <template #default="scope">
            <img v-img-url="scope.row.image && scope.row.image.file_path" :width="30" :height="30" />
          </template>
        </el-table-column>
        <el-table-column prop="card_title" label="卡券名称">
          <template #default="scope">
            <div class="text-ellipsis" :title="scope.row.card_title">{{scope.row.card_title}}</div>
          </template>
        </el-table-column>
        <el-table-column prop="category.name" label="卡券分类"></el-table-column>
        <el-table-column prop="card_price" label="市场价格"></el-table-column>
        <el-table-column prop="sell_price" label="销售价格"></el-table-column>
        <!--<el-table-column prop="stock_num" label="库存"></el-table-column>-->
        <el-table-column prop="sell_num" label="已提货"></el-table-column>
        <el-table-column prop="wait_num" label="待提货"></el-table-column>
        <el-table-column prop="card_sort" label="排序"></el-table-column>
        <el-table-column prop="card_status" label="状态">
          <template #default="scope">
            <span v-if="scope.row.card_status == 10" class="green">上架</span>
            <span v-if="scope.row.card_status == 20" class="gray">下架</span>
          </template>
        </el-table-column>
        <el-table-column prop="create_time" label="添加时间" width="180"></el-table-column>
        <el-table-column fixed="right" label="操作" width="160">
          <template #default="scope">
            <el-button @click="codeArticle(scope.row)" type="text" size="small">提货码</el-button>
            <el-button @click="editArticle(scope.row)" type="text" size="small">编辑</el-button>
            <el-button @click="deleteCard(scope.row)" type="text" size="small">删除</el-button>
          </template>
        </el-table-column>
      </el-table>

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
import CardApi from '@/api/card.js';
export default {
  components: {},
  data() {
    return {
      /*分类*/
      categoryData: [],
      /*表单数据*/
      tableData: [],
      /*是否打开添加弹窗*/
      open_add: false,
      /*是否打开编辑弹窗*/
      open_edit: false,
      /*当前编辑的对象*/
      userModel: {},
      commentData: [],
      loading: true,
      /*一页多少条*/
      pageSize: 20,
      /*一共多少条数据*/
      totalDataNumber: 0,
      /*当前是第几页*/
      curPage: 1
    };
  },
  created() {
    /*获取文章列表*/
    this.getTableList();
  },
  methods: {

    /*获取文章列表*/
    getTableList() {
      let self = this;
      let Params = {};
      Params.page = self.curPage;
      Params.list_rows = self.pageSize;
      CardApi.cardlist(Params, true)
        .then(data => {
          self.loading = false;
          self.tableData = data.data.list.data;
          self.totalDataNumber = data.data.list.total;
        })
        .catch(error => {
          self.loading = false;
        });
    },

    /*添加文章*/
    addArticle() {
      this.$router.push({
        path: '/plus/card/card/add'
      });
    },

    /*编辑文章*/
    editArticle(row) {
      let params = row.card_id;
      this.$router.push({
        path: '/plus/card/card/edit',
        query: {
          card_id: params
        }
      });
    },
    
    /*编辑文章*/
    codeArticle(row) {
      let params = row.card_id;
      this.$router.push({
        path: '/plus/card/card/code',
        query: {
          card_id: params
        }
      });
    },

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

    /*删除文章*/
    deleteCard(row) {
      let self = this;
      ElMessageBox.confirm('此操作将永久删除该记录, 是否继续?', '提示', {
          confirmButtonText: '确定',
          cancelButtonText: '取消',
          type: 'warning'
        })
        .then(() => {
          self.loading = true;
          CardApi.deleteCard(
            {
              card_id: row.card_id
            },
            true
          )
            .then(data => {
              ElMessage({
                message: data.msg,
                type: 'success'
              });
              self.loading = false;
              self.getTableList();
            })
            .catch(error => {});
        })
        .catch(() => {});
    },
    handleClick(tab, event) {},

    /*关闭弹窗*/
    closeDialogFunc(e, f) {
      if (f == 'add') {
        this.open_add = e.openDialog;
        if (e.type == 'success') {
          this.getTableList();
        }
      }
      if (f == 'edit') {
        this.open_edit = e.openDialog;
        if (e.type == 'success') {
          this.getTableList();
        }
      }
    }
  }
};
</script>

<style></style>
