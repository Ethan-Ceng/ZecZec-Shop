<template>

  <el-dialog title="添加等级" v-model="dialogVisible" @close='dialogFormVisible' :close-on-click-modal="false"
    :close-on-press-escape="false" width="600px">
    <el-form size="small" :model="form" ref="form">
      <el-form-item label="等级名称" :label-width="formLabelWidth" prop="name" :rules="[{required: true,message: '请输入等级名称'}]">
        <el-input v-model="form.name" placeholder="请输入等级名称"></el-input>
      </el-form-item>
      <el-form-item label="等级权重" :label-width="formLabelWidth" prop="weight" :rules="[{required: true,message: '请输入等级权重'}]">
        <el-input v-model="form.weight" type="number" placeholder="请输入等级权重"></el-input>
        <div class="gray9">权重越大，等级越高</div>
      </el-form-item>
      <el-form-item label="一级上调" v-if="grade_level >= 1" :label-width="formLabelWidth" prop="first_percent" :rules="[{required: true,message: '请输入等级权重'}]">
        <el-input v-model="form.first_percent" type="number" placeholder="请输入上调比例"></el-input>
        <div class="gray9">在默认分佣比例上上调如果原来是5%，这里上调3%，那么一级拿8%佣金</div>
      </el-form-item>
      <el-form-item label="二级上调" v-if="grade_level >= 2" :label-width="formLabelWidth" prop="second_percent" :rules="[{required: true,message: '请输入等级权重'}]">
        <el-input v-model="form.second_percent" type="number" placeholder="请输入上调比例"></el-input>
      </el-form-item>
      <el-form-item label="三级上调" v-if="grade_level >= 3" :label-width="formLabelWidth" prop="third_percent" :rules="[{required: true,message: '请输入等级权重'}]">
        <el-input v-model="form.third_percent" type="number" placeholder="请输入上调比例"></el-input>
      </el-form-item>
      <el-form-item label="自动升级" :label-width="formLabelWidth" prop="auto_upgrade">
        <el-radio-group v-model="form.auto_upgrade">
          <el-radio :label="1">允许自动升级</el-radio>
          <el-radio :label="0">禁止自动升级</el-radio>
        </el-radio-group>
      </el-form-item>
      <el-form-item label="升级条件" :label-width="formLabelWidth">
        <div>
          <el-radio-group v-model="form.condition_type">
            <el-radio label="and">满足以下所有条件</el-radio>
            <el-radio label="or">满足以下任意条件</el-radio>
          </el-radio-group>
        </div>
        <div class="d-s-c mt16">
          <el-checkbox v-model="form.open_agent_money">累计佣金</el-checkbox>
          <el-input v-model="form.agent_money" type="number" :disabled="form.open_agent_money==0" style="width: 160px; margin-left: 10px;" ></el-input>
          <span class="ml10">元</span>
        </div>
        <div class="d-s-c mt16">
          <el-checkbox v-model="form.open_agent_user">直推分销商</el-checkbox>
          <el-input v-model="form.agent_user" type="number" :disabled="form.open_agent_user==0" style="width: 160px;margin-left: 10px;"></el-input>
          <span class="ml10">人</span>
        </div>
      </el-form-item>
    </el-form>
    <template #footer> 
        <div class="dialog-footer">
          <el-button @click="dialogFormVisible">取 消</el-button>
          <el-button type="primary" @click="addGrade" :disabled="submit_loading">确 定</el-button>
        </div>
    </template>
  </el-dialog>
</template>

<script>
  import GradeApi from '@/api/plus/agent/grade.js';
  export default {
    data() {
      return {
        form: {
          /*昵称*/
          name: '',
          /*是否打开累计消费满*/
          open_agent_money:0,
          agent_money:0,
          open_agent_user:0,
          agent_user:0,
          weight: 100,
          first_percent: 0,
          second_percent: 0,
          third_percent: 0,
          auto_upgrade: 1,
          condition_type: 'and'
        },
        /*左边长度*/
        formLabelWidth: '120px',
        /*是否显示*/
        dialogVisible: false,
        /*是否正在提交*/
        submit_loading: false,
        grade_level: 3
      };
    },
    props: ['open_add'],
    created() {
      this.dialogVisible = this.open_add;
      this.getData();
    },
    methods: {
      getData(){
        let self = this;
        GradeApi.toAddgrade({}, true).then(res => {
          self.grade_level = parseInt(res.data.basicSetting.level);
        })
        .catch(error => {

        });
      },
      /*添加等级*/
      addGrade() {
        let self = this;
        let params = this.form;
        self.$refs.form.validate((valid) => {
          if (valid) {
            self.submit_loading = true;
            params.open_agent_money=params.open_agent_money==true?1:0;
            params.open_agent_user=params.open_agent_user==true?1:0;
            GradeApi.addgrade(params, true).then(data => {
                self.submit_loading = false;
                ElMessage({
                  message: data.msg,
                  type: 'success'
                });
                self.dialogFormVisible(true);
              })
              .catch(error => {
                self.submit_loading = false;
              });
          }
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
      }

    }
  };
</script>

<style></style>
