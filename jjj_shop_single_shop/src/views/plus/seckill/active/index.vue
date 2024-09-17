<template>

  <div class="user">
    <!--搜索表单-->
    <div class="common-seach-wrap">
      <el-form size="small" :inline="true" :model="formInline" class="demo-form-inline">
        <el-form-item label="">
          <el-select v-model="formInline.status" placeholder="活动状态">
            <el-option label="全部" value="-1"></el-option>
            <el-option v-for="(item,index) in status" :key="index" :label="item" :value="index"></el-option>
          </el-select>
        </el-form-item>
        <el-form-item label=""><el-input v-model="formInline.title" placeholder="请输入活动标题"></el-input></el-form-item>
        <el-form-item><el-button icon="Search" @click="onSubmit">查询</el-button></el-form-item>
      </el-form>
    </div>
    
    <!--添加产品-->
    <div class="common-level-rail">
      <el-button size="small" icon="Plus" type="primary" @click="addClick">添加活动</el-button>
    </div>
    
    <div class="product-content">
      <el-form ref="form" :model="form" label-width="100px">
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
                <el-button @click="editClick(scope.row)" type="text" size="small">编辑</el-button>
                <el-button @click="deleteClick(scope.row)" type="text" size="small">删除</el-button>
              </template>
            </el-table-column>
          </el-table>
        </div>
      </el-form>
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
import SeckillApi from '@/api/seckill.js';
export default {
  components: {},
  inject: ['reload'],
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
      /*搜索表单对象*/
      form: {},
      /*是否打开添加弹窗*/
      open_add: false,
      /*是否打开编辑弹窗*/
      open_edit: false,
      /*当前编辑的对象*/
      userModel: {},
      formInline: {},
      status: ['未开始', '进行中', '已结束']
    };
  },
  created() {

    /*获取列表*/
    this.getTableList();
  },
  methods: {

    /*选择第几页*/
    handleCurrentChange(val) {
      this.curPage = val;
      this.getTableList();
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
      let Params = this.formInline;
      Params.page = self.curPage;
      Params.list_rows = self.pageSize;
      self.loading = true;
      SeckillApi.activeList(Params, true)
        .then(data => {
          self.loading = false;
          self.tableData = data.data.list.data;
          self.totalDataNumber = data.data.list.total;
        })
        .catch(error => {});
    },

    /*搜索查询*/
    onSubmit() {
      this.curPage = 1;
      this.getTableList();
    },

    /*添加活动*/
    addClick() {
      this.$router.push('/plus/seckill/active/add');
    },

    /*编辑活动*/
    editClick(row) {
      let seckill_activity_id = row.seckill_activity_id;
      this.$router.push({
        path: '/plus/seckill/active/edit',
        query: {
          seckill_activity_id: seckill_activity_id
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
          SeckillApi.delActive(
            {
              seckill_activity_id: row.seckill_activity_id
            },
            true
          )
            .then(data => {
              if (data.code == 1) {
                ElMessage({
                  message: '恭喜你，活动删除成功',
                  type: 'success'
                });
                this.onSubmit();
              } else {
                ElMessage.error('错了哦，这是一条错误消息');
              }
            })
            .catch(error => {});
        })
        .catch(() => {});
    }
  }
};
</script>

<style lang="scss" scoped></style>
