<template>
  <div class="user">
    <div class="operation-wrap">
      <p> 注：仅展示7天内在小程序中活跃（浏览点击）的用户。</p>
      <p> 注：formId有效期是7天，可用于向用户发送模板消息。</p>
    </div>
    <div class="table-wrap">
      <template>
        <el-table :data="tableData" style="width: 100%">
          <el-table-column prop="article_id" label="用户ID" width="180"> </el-table-column>
          <el-table-column prop="article_title" label="	用户头像"></el-table-column>
          <el-table-column prop="address" label="	用户昵称"></el-table-column>
          <el-table-column prop="category.name" label="formid数量" width="180"></el-table-column>
          <el-table-column prop="virtual_views" label="	注册时间" width="180"></el-table-column>
          <el-table-column prop="name" label="操作" width="150">
            <template #default="scope">
              <el-button @click="editArticle(scope.row)" type="text" size="small">编辑</el-button>
              <el-button @click="deleteArticle(scope.row)" type="text" size="small">删除</el-button>
            </template>
          </el-table-column>
        </el-table>
      </template>
    </div>
  </div>
</template>
<script>
  import PushApi from '@/api/push.js';
  export default {
    data() {
      return {
        form: {
          status: 0,
        },
      };
    },
    created() {
      this.getTableList();
    },
    methods: {
      getTableList() {
        let self = this;
        let Params = {};
        PushApi.getList(Params, true)
          .then(data => {
            self.loading = false;
            self.catgory = data.data.catgory;
          })
          .catch(error => {

          });
      },
    }
  };
</script>

<style>
</style>
