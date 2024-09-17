<template>

  <div class="user" v-loading="loading">
    <div class="common-form d-s-c">
      <span>活动商品</span>
      <div class="ml20"><el-button type="primary" size="small" icon="Plus" @click="changeProduct" v-auth="'/plus/points/product/add'">选择商品</el-button></div>
    </div>
    <div class="product-content point-list">
      <div class="table-wrap">
        <el-table size="small" :data="tableData" border style="width: 100%">
          <el-table-column prop="product_name" label="商品名称" width="360">
            <template #default="scope">
              <div class="product-info">
                <div class="pic" style="width: 40px; height: 40px;"><img v-img-url="scope.row.product.image[0].file_path" alt="" /></div>
                <div class="info">
                  <div class="name">{{ scope.row.product.product_name }}</div>
                </div>
              </div>
            </template>
          </el-table-column>
          <el-table-column label="规格">
            <template #default="scope">
              <span v-if="scope.row.product.spec_type == 10">单规格</span>
              <span v-if="scope.row.product.spec_type == 20">多规格</span>
            </template>
          </el-table-column>
          <el-table-column label="积分">
            <template #default="scope">
              {{ scope.row.sku[0].point_num }}
            </template>
          </el-table-column>
          <el-table-column label="金额">
            <template #default="scope">
              <span class="orange fb">{{ scope.row.sku[0].point_money }}</span>
            </template>
          </el-table-column>
          <el-table-column prop="limit_num" label="限购数量"></el-table-column>
          <el-table-column prop="stock" label="活动库存"></el-table-column>
          <el-table-column prop="sort" label="排序"></el-table-column>
          <el-table-column label="状态">
            <template #default="scope">
              <span v-if="scope.row.status == 10" class="green">上架</span>
              <span v-if="scope.row.status == 20" class="gray">下架</span>
            </template>
          </el-table-column>
          <el-table-column fixed="right" label="操作" width="110">
            <template #default="scope">
              <el-button v-auth="'/plus/points/product/edit'" @click="editClick(scope.row.point_product_id)" type="text" size="small">编辑</el-button>
              <el-button v-auth="'/plus/points/product/delete'" @click="deleteClick(scope.row.point_product_id)" type="text" size="small">删除</el-button>
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

    <!--商品选择-->
    <Product :isproduct="isproduct" :excludeIds="exclude_ids" :islist="false" @closeDialog="closeProductFunc"></Product>
  </div>
</template>
<script>
import PointProductApi from '@/api/pointproduct.js';
import Product from '@/components/product/Product.vue';
export default {
  components: {
    /*选择商品*/
    Product
  },
  data() {
    return {
      /*表格数据*/
      tableData: [],
      /*一页多少条*/
      pageSize: 15,
      /*一共多少条数据*/
      totalDataNumber: 0,
      /*当前是第几页*/
      curPage: 1,
      /*是否加载完成*/
      loading: true,
      /*是否修改*/
      open_edit: false,
      /*当前编辑的对象*/
      userModel: {},
      /*是否打开选择商品*/
      isproduct: false,
      /*已有的id*/
      exclude_ids: []
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
      PointProductApi.productList(Params, true)
        .then(res => {
          self.exclude_ids = res.data.exclude_ids;
          self.tableData = res.data.list.data;
          self.totalDataNumber = res.data.list.total;
          self.loading = false;
        })
        .catch(error => {
          self.loading = false;
        });
    },

    /*打开添加*/
    changeProduct(list) {
      this.isproduct = true;
    },

    /*关闭商品选择*/
    closeProductFunc(e) {
      this.isproduct = false;
      if (e.type == 'success') {
        this.addClick(e.params.product_id);
      }
    },

    /*添加商品*/
    addClick(product_id) {
      this.$router.push('/plus/points/product/add?product_id=' + product_id);
    },

    /*编辑商品*/
    editClick(e) {
      let self = this;
      this.$router.push({
        path: '/plus/points/product/edit',
        query: {
          point_product_id: e
        }
      });
    },

    /* 查询*/
    onSubmit() {
      let self = this;
      let params = self.form;
      self.loading = true;
      self.getTableList();
    },

    /*删除用户*/
    deleteClick(e) {
      let self = this;
      ElMessageBox.confirm('此操作将永久删除该记录, 是否继续?', '提示', {
          confirmButtonText: '确定',
          cancelButtonText: '取消',
          type: 'warning'
        })
        .then(() => {
          self.loading = true;
          PointProductApi.deleteProduct(
            {
              id: e
            },
            true
          )
            .then(data => {
              self.loading = false;
              ElMessage({
                message: data.msg,
                type: 'success'
              });
              self.getTableList();
            })
            .catch(error => {
              self.loading = false;
            });
        })
        .catch(() => {
          self.loading = false;
        });
    }

  }
};
</script>

<style>
.point-list .el-input-number--small {
  width: auto;
}
</style>
