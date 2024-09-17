<template>

  <div class="pb50">
    <el-form size="small" ref="form" :model="form" label-width="150px">
      <!--基础-->
      <Basic :form="form"></Basic>
      <!--字段-->
      <Fields :form="form"></Fields>
    </el-form>

    <!--提交-->
    <div class="common-button-wrapper">
      <el-button size="small" @click="cancelFunc">取消</el-button>
      <el-button size="small" type="primary" @click="onSubmit" :loading="loading">提交</el-button>
    </div>
  </div>
</template>
<script>
import TableApi from '@/api/plus/table.js';
import Basic from './part/Basic.vue';
import Fields from './part/Fields.vue';
export default {
  components: {
    /*基础设置*/
    Basic,
    /*活动商品*/
    Fields
  },
  data() {
    return {
      /*表单对象*/
      form: {
        name: '',
        /*默认排序*/
        sort: 100,
        /*商品源数据*/
        tableData: []
      },
      /*是否正在加载*/
      loading: false
    };
  },
  created() {},
  methods: {
    /*提交表单*/
    onSubmit() {
      let self = this;
	  let flag = self.form.tableData.every((v)=>{
	    if(v.type !='radio' && v.type !='radio-group' && v.type !='select'){
	      return true
	    }
	    return v.select_value
	  })
	  if(!flag){
	    ElMessage.error('备选值必填');
	    return
	  }
      self.$refs.form.validate(valid => {
        if (valid) {
          let params = self.form;
          self.loading = true;
          TableApi.add(params, true)
            .then(data => {
              self.loading = false;
              ElMessage({
                message: '恭喜你，添加成功',
                type: 'success'
              });
              self.cancelFunc();
            })
            .catch(error => {
              self.loading = false;
            });
        }
      });
    },
    /*取消*/
    cancelFunc() {
      this.$router.back(-1);
    }
  }
};
</script>
