<template>

  <el-dialog title="商品满减设置" v-model="dialogVisible" @close="dialogFormVisible" :close-on-click-modal="false"
    :close-on-press-escape="false" width="600px">
    <el-form size="small" :model="form" ref="form">
      <el-form-item label="商品名称" :label-width="formLabelWidth">
        <span>{{productModel.product_name}}</span>
      </el-form-item>
      <el-form-item label="是否参与活动" :label-width="formLabelWidth">
        <el-radio-group v-model="form.is_join">
          <el-radio :label="1">参与</el-radio>
          <el-radio :label="0">不参与</el-radio>
        </el-radio-group>
      </el-form-item>
      <template v-if="form.is_join == 1">
        <el-form-item label="满类型" :label-width="formLabelWidth">
          <div>
            <el-radio v-model="form.full_type" :label="1">满金额</el-radio>
            <el-radio v-model="form.full_type" :label="2">满件数</el-radio>
          </div>
        </el-form-item>
        <el-form-item label="减类型" :label-width="formLabelWidth">
          <div>
            <el-radio v-model="form.reduce_type" :label="1">减金额</el-radio>
            <el-radio v-model="form.reduce_type" :label="2">打折</el-radio>
          </div>
        </el-form-item>
        <el-form-item label="满减设置：" :label-width="formLabelWidth">
          <div class="tips">
            如果是打折，填写30，表示减去30，打7折，订单为原价的70%
          </div>
          <el-table :data="form.reduce_list" style="width: 100%">
            <el-table-column label="满值">
              <template #default="scope">
                <el-input type="number" :precision="1" :step="1" :min="0" placeholder="请输入满值" v-model="scope.row.full_value"></el-input>
              </template>
            </el-table-column>
            <el-table-column label="减值">
              <template #default="scope">
                <el-input type="number" :precision="1" :step="1" :min="0" placeholder="请输入满值" v-model="scope.row.reduce_value"></el-input>
              </template>
            </el-table-column>
            <el-table-column label="操作">
              <template #default="scope">
                <el-link type="primary" @click="delReduce(scope.$index)">删除</el-link>
              </template>
            </el-table-column>
          </el-table>
          <el-link type="primary" @click="addReduce()">添加</el-link>
        </el-form-item>
      </template>
    </el-form>
    <template #footer> 
        <div class="dialog-footer">
            <el-button @click="dialogFormVisible(false)">取 消</el-button>
            <el-button type="primary" @click="saveProduct" :loading="loading">确 定</el-button>
        </div>
    </template>
  </el-dialog>
</template>

<script>
  import FullreduceApi from '@/api/plus/fullreduce.js';
  export default {
    data() {
      return {
        form:{
            is_join: 0,
            /*满类型*/
            full_type: 1,
            /*减类型*/
            reduce_type: 1,
            reduce_list: []
        },
        /*左边长度*/
        formLabelWidth: '120px',
        /*是否显示*/
        dialogVisible: false,
        loading: false,
      };
    },
    props: ['open_settings', 'productModel'],
    created() {
      let self = this;
      self.form.is_join = self.productModel.reduce_pid != null?1:0;
      if(self.form.is_join == 1){
        self.productModel.reduce_list.forEach(function(item) {
          self.form.reduce_list.push({
            full_value: item.full_value,
            reduce_value: item.reduce_value
          });
        });
        self.form.full_type = self.productModel.reduce_list[0].full_type;
        self.form.reduce_type = self.productModel.reduce_list[0].reduce_type;
      }
      self.dialogVisible = self.open_settings;
    },
    methods: {
      /*保存*/
      saveProduct() {
        let self = this;
        let params = self.form;
        params.product_id = self.productModel.product_id;
        self.loading = true;
        FullreduceApi.editProduct(params).then(data => {
          self.loading = false;
          ElMessage({
            message: data.msg,
            type: 'success'
          });
          self.dialogFormVisible(true);
        }).catch(error => {
          self.loading = false;
        });
      },
      /*关闭弹窗*/
      dialogFormVisible(e) {
        if (e) {
          this.$emit('closeDialog', {
            type: 'success',
            openDialog: false
          })
        } else {
          this.$emit('closeDialog', {
            type: 'error',
            openDialog: false
          })
        }
      },
      addReduce() {
        this.form.reduce_list.push({
          full_value: '',
          reduce_value: ''
        });
      },
      delReduce(index) {
        this.form.reduce_list.splice(index, 1);
      },
    }
  };
</script>

<style>
 .img {
    margin-top: 10px;
  }
</style>
