<template>

  <div class="user">
    <!--添加等级-->
    <div class="common-level-rail">
      <el-button size="small" type="primary" @click="addClick" icon="Plus" v-auth="'/plus/agent/grade/add'">添加等级</el-button>
    </div>
    <!--内容-->
    <div class="product-content">
      <div class="table-wrap">
        <el-table size="small" :data="tableData" border style="width: 100%" v-loading="loading">
          <el-table-column prop="name" label="等级名称" width="300"></el-table-column>
          <el-table-column prop="weight" label="权重"></el-table-column>
          <el-table-column prop="remark" label="升级条件">
            <template #default="scope">
              <div v-html="keepTextStyle(scope.row.remark)"></div>
            </template>
          </el-table-column>
          <el-table-column fixed="right" label="操作" width="110">
            <template #default="scope">
              <el-button @click="editClick(scope.row)" type="text" size="small" v-auth="'/plus/agent/grade/edit'" >编辑</el-button>
              <el-button @click="deleteClick(scope.row)" type="text" size="small" v-if="scope.row.is_default == 0"  v-auth="'/plus/agent/grade/delete'">删除</el-button>
            </template>
          </el-table-column>
        </el-table>
      </div>
    </div>
    <!--添加-->
    <Add v-if="open_add" :open_add="open_add" @closeDialog="closeDialogFunc($event, 'add')"></Add>
    <!--编辑-->
    <Edit v-if="open_edit" :open_edit="open_edit" :form="userModel" @closeDialog="closeDialogFunc($event, 'edit')"></Edit>
  </div>
</template>

<script>
  import GradeApi from '@/api/plus/agent/grade.js';
  import Edit from './Edit.vue';
  import Add from './Add.vue';
  import {deepClone} from '@/utils/base.js';
  export default {
    components: {
      /*编辑组件*/
      Edit,
      Add
    },
    data() {
      return {
        /*是否加载完成*/
        loading: true,
        /*列表数据*/
        tableData: [],
        /*横向表单数据模型*/
        form: {
          become: '0'
        },
        /*是否打开添加弹窗*/
        open_add: false,
        /*是否打开编辑弹窗*/
        open_edit: false,
        /*当前编辑的对象*/
        userModel: {}
      };
    },
    created() {
      /*获取列表*/
      this.getTableList();

    },
    methods: {

      /*换行*/
      keepTextStyle(val){
        let str=val.replace(/(\\r\\n)/g,'<br/>');
        return str;
      },
      /*获取列表*/
      getTableList() {
        let self = this;
        let Params = {};
        GradeApi.gradelist(Params, true)
          .then(data => {
            self.loading = false;
            self.tableData = data.data.list;
          })
          .catch(error => {

          });
      },

      /*打开添加*/
      addClick() {
        this.open_add = true;
      },

      /*打开编辑*/
      editClick(item) {
        this.userModel = deepClone(item);
        this.open_edit = true;
      },

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
      },

      /*删除用户*/
      deleteClick(row) {
        let self = this;
        ElMessageBox.confirm('此操作将永久删除该记录, 是否继续?', '提示', {
          confirmButtonText: '确定',
          cancelButtonText: '取消',
          type: 'warning'
        }).then(() => {
          self.loading = true;
          GradeApi.deletegrade({
              grade_id: row.grade_id
            }, true)
            .then(data => {
              self.loading = false;
              if (data.code == 1) {
                ElMessage({
                  message: data.msg,
                  type: 'success'
                });
                self.getTableList();
              } else {
                ElMessage.error('错了哦，这是一条错误消息');
              }
            })
            .catch(error => {
              self.loading = false;
            });

        }).catch(() => {

        });
      }

    }
  };
</script>

<style></style>
