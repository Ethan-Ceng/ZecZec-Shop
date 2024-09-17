<template>

  <div class="product">
    <!--搜索表单-->
    <div class="common-seach-wrap">
      <el-form size="small" :inline="true" :model="searchForm" class="demo-form-inline">
        <el-form-item label="咨詢信息">
          <el-input size="small" v-model="searchForm.question" placeholder="请输入问题搜索"></el-input>
        </el-form-item>
        <el-form-item>
          <el-button size="small" type="primary" icon="Search" @click="onSubmit">查询</el-button>
        </el-form-item>
      </el-form>
    </div>

    <!--内容-->
    <div class="product-content">
      <div class="table-wrap">
        <el-table size="small" :data="tableData" border style="width: 100%" v-loading="loading">
          <el-table-column label="id" prop="id">
          </el-table-column>
          <el-table-column label="用戶">
            <template #default="{row}">
              <div>{{ row?.user?.nickName }}</div>
              <div>{{ row?.user?.email }}</div>
            </template>
          </el-table-column>
          <el-table-column label="關聯商品">
            <template #default="{row}">
              <div>{{ row?.product?.product_name || '' }}</div>
            </template>
          </el-table-column>
          <el-table-column prop="last_replied_at" label="最後消息時間">
            <template #default="{row}">
              <div>{{ formatTime(row.last_replied_at) }}</div>
            </template>
          </el-table-column>
          <el-table-column prop="update_time" label="更新时间"></el-table-column>
          <el-table-column fixed="right" label="操作" width="110">
            <template #default="scope">
              <el-button @click="handleOpen(scope.row)" type="text" size="small">詳情</el-button>
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
        title="咨詢詳情"
        width="500"
        :before-close="handleClose"
    >
      <div class="overflow-auto relative w-full js-message-screen grow">
        <div v-for="item in activeMessage.messageList" :class="['p-4', !item.user_id && 'text-right']">
          <div class="ml-16">
            <div
                class="py-2 mv-child-0 ml-4 px-4 lg:max-w-lg max-w-full border-neutral-200 rounded inline-block border text-left">
              <p>{{ item.content }}</p>
            </div>
            <br>
            <div class="ml-4 text-xs text-neutral-600 tooltip help inline-block tooltip-l"
                 data-title="2024/09/12  03:09">
              {{ item.create_time }}
            </div>
          </div>
        </div>
      </div>

      <template #footer>
        <div class="w-full flex p-4 border-t border-zinc-100">
          <el-input v-model="content" type="textarea" class="flex-1 w-full">

          </el-input>
          <el-button @click="handleSubmit" :loading="loading" class="ml-2"
                     type="success" plain>
            送出訊息
          </el-button>
        </div>
      </template>
    </el-dialog>
  </div>
</template>

<script>
import PorductApi from '@/api/product.js';
import dayjs from "dayjs";

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
      content: '',
      activeMessage: {
        message_box_id: null,
        messageList: [],
      }
    };
  },
  created() {
    /*获取列表*/
    this.getData();
  },
  methods: {
    formatTime(time) {
      if (time) {
        return dayjs.unix(time).format('YYYY-MM-DD HH:mm:ss')
      }

      return ''
    },
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
      PorductApi.getMessageList(Params, true).then(res => {
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
      PorductApi.getMessageList(Params, true).then(data => {
        self.loading = false;
        self.tableData = data.data.list.data;

        self.totalDataNumber = data.data.list.total
      }).catch(error => {
        self.loading = false;
      });
    },
    // 發送消息
    async getMessageAllList(boxId) {
      this.loading = true
      try {
        const {data} = await PorductApi.getMessageBoxDetail({message_box_id: boxId})

        if (data) {
          // 獲取屬性值，作為套餐名稱
          this.activeMessage = {
            ...this.activeMessage,
            messageList: data || []
          }
          this.dialogVisible = true
        }
      } catch (err) {
        this.loading = false
        console.warn(err)
      } finally {
        this.loading = false
      }
    },
    handleOpen(rowData) {
      this.content = ''
      this.activeMessage = rowData
      this.getMessageAllList(rowData.message_box_id)
    },
    handleClose() {
      this.content = ''
      this.activeMessage = {
        message_box_id: null,
        messageList: [],
      }
      this.dialogVisible = false
    },
    /*搜索查询*/
    async handleSubmit() {
      if (!this.content) {
        this.$message.warning('請輸入咨詢信息')
        return
      }
      if (!this.activeMessage?.message_box_id) {
        this.$message.warning('請輸選擇一個對話')
        return
      }
      this.loading = true
      try {
        let params = {
          message_box_id: this.activeMessage?.message_box_id,
          content: this.content
        }
        await PorductApi.sendMessage(params)
        await this.getMessageAllList(this.activeMessage?.message_box_id)
        this.content = ''
        this.$message.success("傳送成功")
      } catch (e) {
      } finally {
        this.loading = false
      }
    },
  }
};
</script>

<style lang="scss" scoped>
.overflow-auto {
  overflow: auto;
}

.flex-grow, .grow {
  flex-grow: 1;
}

.w-full {
  width: 100%;
}

.relative {
  position: relative;
}

.flex {
  display: flex;
}

.flex-1 {
  flex: 1;
}

.ml-2 {
  margin-left: 16px;
}

.text-right {
  text-align: right;
}

.p-4 {
  padding: 1rem;
}


.text-left {
  text-align: left;
}

.py-2 {
  padding-bottom: 0.5rem;
  padding-top: 0.5rem;
}


.px-4 {
  padding-left: 1rem;
  padding-right: 1rem;
}

.border {
  border-width: 1px;
}

.rounded {
  border-radius: 0.25rem;
}

.max-w-full {
  max-width: 100%;
}

.inline-block {
  display: inline-block;
}

.ml-4 {
  margin-left: 1rem;
}

.text-left {
  text-align: left;
}

</style>