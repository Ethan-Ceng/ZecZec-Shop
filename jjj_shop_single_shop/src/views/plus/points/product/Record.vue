<template>

  <div class="user">
    <!--搜索表单-->
    <div class="common-seach-wrap">
      <el-form size="small" :inline="true" :model="formInline" class="demo-form-inline">
        <el-form-item label="订单状态">
          <el-select v-model="formInline.order_status" placeholder="订单">
            <el-option label="全部" value="-1"></el-option>
            <el-option v-for="(item, index) in status" :key="index" :label="item.name" :value="item.id"></el-option>
          </el-select>
        </el-form-item>
        <el-form-item label="用户昵称"><el-input v-model="formInline.nickName" placeholder="请输入用户昵称"></el-input></el-form-item>
        <el-form-item><el-button type="primary" icon="Search" @click="onSubmit">查询</el-button></el-form-item>
      </el-form>
    </div>

    <div class="product-content">
      <div class="table-wrap">
        <el-table size="small" :data="tableData" border style="width: 100%" v-loading="loading">
          <el-table-column prop="user.nickName" label="用户">
            <template #default="scope">
              <div class="d-s-c">
                <img v-img-url="scope.row.user.avatarUrl" :width="30" :height="30" />
                <div class="ml10">{{ scope.row.user.nickName }}</div>
              </div>
            </template>
          </el-table-column>
          <el-table-column prop="points_num" label="积分">
            <template #default="scope">
              <span class="orange fb">{{ scope.row.points_num }}</span>
            </template>
          </el-table-column>
          <el-table-column prop="pay_price" label="金额"></el-table-column>
          <el-table-column prop="create_time" label="时间"></el-table-column>
          <el-table-column prop="state_text" label="订单状态"></el-table-column>
          <el-table-column fixed="right" label="操作" width="80">
            <template #default="scope">
              <el-button @click="toDetail(scope.row)" type="text" size="small" v-auth="'/order/order/detail'">订单详情</el-button>
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
import PointProductApi from '@/api/pointproduct.js';
export default {
  data() {
    return {
      /*搜索订单数据对象*/
      formInline: {
        /*状态*/
        order_status: '-1',
        /*昵称*/
        nickName: ''
      },
      tableData: [],
      /*一页多少条*/
      pageSize: 20,
      /*一共多少条数据*/
      totalDataNumber: 0,
      /*当前是第几页*/
      curPage: 1,
      /*是否加载完成*/
      loading: true,
      /*订单状态列表*/
      status: [
        {
          id: 10,
          name: '进行中'
        },
        {
          id: 20,
          name: '已取消'
        },
        {
          id: 21,
          name: '待取消'
        },
        {
          id: 30,
          name: '已完成'
        }
      ]
    };
  },
  created() {
    /*获取列表*/
    this.getData();
  },
  methods: {

    /*搜索*/
    onSubmit() {
      this.loading = true;
      this.curPage = 1;
      this.getData(true);
    },

    /*获取列表*/
    getData(isSearch) {
      let self = this;
      let Params = self.formInline;
      Params.page = self.curPage;
      Params.list_rows = self.pageSize;
      PointProductApi.getExchange(Params, true)
        .then(data => {
          self.loading = false;
          self.tableData = data.data.list.data;
          self.totalDataNumber = data.data.list.total;
        })
        .catch(error => {});
    },

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
      this.pageSize = val;
      this.getData();
    },

    /*查看详情*/
    toDetail(row) {
      let self = this;
      let params = row.order_id;
      this.$router.push({
        path: '/order/order/Detail',
        query: {
          order_id: params
        }
      });
    }
  }
};
</script>

<style></style>
