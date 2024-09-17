<template>

  <div class="product">
    <!--搜索表单-->
    <div class="common-seach-wrap">
      <el-form size="small" :inline="true" :model="searchForm" class="demo-form-inline">
        <el-form-item label="问题">
          <el-input size="small" v-model="searchForm.question" placeholder="请输入问题搜索"></el-input>
        </el-form-item>
        <el-form-item>
          <el-button size="small" type="primary" icon="Search" @click="onSubmit">查询</el-button>
          <el-button plain @click="handleOpen">
            添加
          </el-button>
        </el-form-item>
      </el-form>
    </div>

    <!--内容-->
    <div class="product-content">
      <div class="table-wrap">
        <el-table size="small" :data="tableData" border style="width: 100%" v-loading="loading">
          <el-table-column label="问题" prop="question">
          </el-table-column>
          <el-table-column prop="answer" label="回答">
          </el-table-column>
          <el-table-column prop="sort" label="排序"></el-table-column>
          <el-table-column prop="update_time" label="更新时间"></el-table-column>
          <el-table-column fixed="right" label="操作" width="110">
            <template #default="scope">
              <el-button @click="handleOpen(scope.row)" type="text" size="small">編輯</el-button>
              <el-button @click="delClick(scope.row)" type="text" size="small">删除</el-button>
            </template>
          </el-table-column>
        </el-table>
      </div>
      <!--分页-->
      <div class="pagination">
        <el-pagination @size-c`han`ge="handleSizeChange" @current-change="handleCurrentChange" background
                       :current-page="curPage" :page-size="pageSize" layout="total, prev, pager, next, jumper"
                       :total="totalDataNumber">
        </el-pagination>
      </div>
    </div>

    <!--  添加常见问题  -->
    <el-dialog
        v-model="dialogVisible"
        title="常見問題"
        width="500"
        :before-close="handleClose"
    >
      <el-form :model="formState" ref="refFormState" label-width="auto">
        <el-form-item label="常見問題" prop="question" :rules="[{ required: true, message: '請輸入常見問題', trigger: 'blur' }]">
          <el-input v-model="formState.question" placeholder="請輸入常見問題" clearable />
        </el-form-item>
        <el-form-item label="解答" prop="answer" :rules="[{ required: true, message: '請輸入解答', trigger: 'blur' }]">
          <el-input v-model="formState.answer" type="textarea" placeholder="請輸入解答" clearable />
        </el-form-item>
        <el-form-item label="排序" prop="sort" :rules="[{ required: true, message: '請輸入排序', trigger: 'blur' }]">
          <el-input v-model="formState.sort" type="number" placeholder="數字越小越靠前" />
        </el-form-item>
      </el-form>
      <template #footer>
        <div class="dialog-footer">
          <el-button @click="handleClose">取消</el-button>
          <el-button :loading="loading" type="primary" @click="handleSubmit">
            確認
          </el-button>
        </div>
      </template>
    </el-dialog>
  </div>
</template>

<script>
import PorductApi from '@/api/product.js';

export default {
  components: {},
  data() {
    return {
      loading: true,
      /*列表数据*/
      tableData: [],
      /*一页多少条*/
      pageSize: 20,
      /*总条数*/
      totalDataNumber: 0,
      /*当前是第几页*/
      curPage: 1,
      /*搜索参数*/
      searchForm: {
        question: ''
      },
      dialogVisible: false,
      formState: {
        faq_id: null,
        question: '',
        answer: '',
        sort: 100
      }
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
      let Params = self.searchForm;
      self.getData(Params);
    },

    /*每页多少条*/
    handleSizeChange(val) {
      this.curPage = 1;
      this.pageSize = val;
      this.getData();
    },

    /*获取列表*/
    getData(param = '') {
      let self = this;
      let Params = {};
      Params.status = self.status;
      Params.page = self.curPage;
      Params.list_rows = self.pageSize;
      if (param != '') {
        Params.question = param.question;
      }
      PorductApi.getFaqList(Params, true).then(res => {
        self.loading = false;
        self.tableData = res.data.list.data;
        self.totalDataNumber = res.data.list.total
      }).catch(error => {
        self.loading = false;
      });
    },

    /*删除*/
    delClick: function (row) {
      let self = this;
      ElMessageBox.confirm('删除后不可恢复，确认删除该记录吗?', '提示', {
        type: 'warning'
      }).then(() => {
        PorductApi.delFaq({
          comment_id: row.comment_id
        }).then(data => {
          ElMessage({
            message: '删除成功',
            type: 'success'
          });
          self.getData();
        });
      });
    },

    /*搜索查询*/
    onSubmit() {
      let self = this;
      self.loading = true;
      let Params = self.searchForm;
      Params.page = self.curPage;
      Params.list_rows = self.pageSize;
      Params.status = self.status;
      PorductApi.getFaqList(Params, true).then(data => {
        self.loading = false;
        self.tableData = data.data.list.data;

        self.totalDataNumber = data.data.list.total
      }).catch(error => {
        self.loading = false;
      });
    },
    handleOpen(rowData) {
      if (rowData.faq_id) {
        this.formState = {...rowData}
      }
      this.dialogVisible = true
    },
    handleClose() {
      this.formState = {
        id: null,
        question: '',
        answer: '',
        sort: 100
      }
      this.dialogVisible = false
    },
    /*搜索查询*/
    handleSubmit() {
      let self = this;

      if (this.$refs.refFormState) {
        this.$refs.refFormState.validate((valid, fields) => {
          if (valid) {
            self.loading = true;
            console.log(this.formState)
            if (this.formState.faq_id) {
              PorductApi.editFaq(this.formState, true).then(data => {
                self.loading = false;
                self.handleClose()
                self.handleCurrentChange(1)
              }).catch(error => {
                self.loading = false;
              });

            } else {
              PorductApi.addFaq(this.formState, true).then(data => {
                self.loading = false;
                self.handleClose()
                self.handleCurrentChange(1)
              }).catch(error => {
                self.loading = false;
              });
            }
          }
        })
      }
    },
  }
};
</script>

<style></style>