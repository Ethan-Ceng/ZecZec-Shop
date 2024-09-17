<template>

  <div>
    <div class="common-seach-wrap">
      <el-form size="small" :inline="true" :model="searchForm" class="demo-form-inline">
        <el-form-item label="提货码">
          <el-input size="small" v-model="searchForm.code_no" placeholder="提货码"></el-input>
        </el-form-item>
        <el-form-item label="提货状态">
          <el-select size="small" v-model="searchForm.code_status" placeholder="请选择">
            <el-option label="全部" value=""></el-option>
            <el-option label="待提货" value="0"></el-option>
            <el-option label="已提货" value="1"></el-option>
          </el-select>
        </el-form-item>
        <el-form-item label="作废状态">
          <el-select size="small" v-model="searchForm.is_delete" placeholder="请选择">
            <el-option label="全部" value=""></el-option>
            <el-option label="正常" value="0"></el-option>
            <el-option label="作废" value="1"></el-option>
          </el-select>
        </el-form-item>
        <el-form-item label="卡券分类">
          <el-select size="small" v-model="searchForm.category_id" placeholder="请选择">
            <el-option label="全部" value=""></el-option>
            <el-option v-for="(item, index) in categoryList" :key="index" :label="item.name" :value="item.category_id">
            </el-option>
          </el-select>
        </el-form-item>
        <el-form-item label="所属卡券">
          <el-select size="small" v-model="searchForm.card_id" filterable placeholder="请选择">
            <el-option label="全部" value=""></el-option>
            <el-option v-for="(item, index) in cardList" :key="index" :label="item.card_title" :value="item.card_id">
            </el-option>
          </el-select>
        </el-form-item>
        <el-form-item>
          <el-button size="small" type="primary" icon="Search" @click="onSubmit">查询</el-button>
        </el-form-item>
        <el-form-item>
          <el-button size="small" type="success" @click="onExport">导出</el-button>
        </el-form-item>
      </el-form>
    </div>
    <div class="table-wrap">
      <el-table :data="tableData" style="width: 100%" v-loading="loading">
        <el-table-column prop="code_no" label="提货码"></el-table-column>
        <el-table-column prop="code_pwd" label="提货密码"></el-table-column>
        <el-table-column prop="card_title" label="所属卡券"></el-table-column>
        <el-table-column prop="start_time_str" label="提货开始日期"></el-table-column>
        <el-table-column prop="end_time_str" label="提货截止日期"></el-table-column>
        <!--<el-table-column prop="active_status" label="激活状态">
          <template #default="scope">
            <span v-if="scope.row.active_status == 0" class="gray">未激活</span>
            <span v-if="scope.row.active_status == 1" class="green">已激活</span>
          </template>
        </el-table-column>-->
        <el-table-column prop="code_status" label="提货状态">
          <template #default="scope">
            <span v-if="scope.row.code_status == 0" class="gray">未提货</span>
            <span v-if="scope.row.code_status == 1" class="green">已提货</span>
          </template>
        </el-table-column>
        <el-table-column prop="code_status" label="作废状态">
          <template #default="scope">
            <span v-if="scope.row.is_delete == 1" class="gray">作废</span>
            <span v-if="scope.row.is_delete == 0" class="green">正常</span>
          </template>
        </el-table-column>
        <el-table-column prop="create_time" label="添加时间"></el-table-column>
        <el-table-column fixed="right" label="操作" width="100">
          <template #default="scope">
            <el-button v-if="scope.row.code_status == 0" @click="editArticle(scope.row)" type="text" size="small">编辑</el-button>
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
import qs from 'qs';
import { useUserStore } from '@/store';
const { token } = useUserStore();
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
      curPage: 1,
      /*横向表单数据模型*/
      searchForm: {
        code_no: '',
        category_id: '',
        card_id: '',
        code_status: '',
        is_delete: '',
        token,
      },
      categoryList: [],
      cardList: [],
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
      let Params = self.searchForm;
      Params.page = self.curPage;
      Params.list_rows = self.pageSize;
      CardApi.codelist(Params, true)
        .then(res => {
          self.loading = false;
          self.tableData = res.data.list.data;
          self.totalDataNumber = res.data.list.total;
          self.categoryList = res.data.categoryList;
          self.cardList = res.data.cardList;
        })
        .catch(error => {
          self.loading = false;
        });
    },
    /*编辑文章*/
    editArticle(row) {
      let params = row.code_id;
      this.$router.push({
        path: '/plus/card/code/edit',
        query: {
          code_id: params
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

    /*搜索查询*/
    onSubmit() {
      this.curPage = 1;
      this.tableData = [];
      this.getTableList();
    },

  onExport: function() {
	let url = "/index.php/shop/plus.card.code/export";
	let params = this.searchForm;
	this.$filter.onExportFunc(url, params);
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
