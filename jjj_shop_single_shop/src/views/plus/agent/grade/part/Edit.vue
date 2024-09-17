<template>

  <el-dialog title="编辑等级" v-model="dialogVisible" @close='dialogFormVisible' :close-on-click-modal="false"
    :close-on-press-escape="false" width="600px">
    <el-form size="small" :model="form" ref="form">
      <el-form-item label="等级名称" :label-width="formLabelWidth" prop="name" :rules="[{required: true,message: ' '}]">
        <el-input v-model="form.name" autocomplete="off"></el-input>
      </el-form-item>
      <template v-if="form.is_default == 0">
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
      </template>
    </el-form>
    <template #footer> 
        <div class="dialog-footer">
          <el-button @click="dialogFormVisible">取 消</el-button>
          <el-button type="primary" @click="editGrade" :disabled="submit_loading">确 定</el-button>
        </div>
    </template>
  </el-dialog>
</template>

<script>
  import GradeApi from '@/api/plus/agent/grade.js';
  export default {
    data() {
      return {
        /*左边长度*/
        formLabelWidth: '120px',
        /*是否显示*/
        dialogVisible: false,
        /*是否正在提交*/
        submit_loading: false,
        grade_level: 3
      };
    },
    props: ['open_edit', 'form'],
    created() {
      this.form.open_agent_money=this.form.open_agent_money==1?true:false;
      this.form.open_agent_user=this.form.open_agent_user==1?true:false;
      this.dialogVisible = this.open_edit;
      this.getData();
    },
    methods: {
      getData(){
        let self = this;
        GradeApi.toEditgrade({}, true).then(res => {
          self.grade_level = parseInt(res.data.basicSetting.level);
        })
        .catch(error => {

        });
      },
      /*修改用户*/
      editGrade() {
        let self = this;
        let params = this.form;
        self.$refs.form.validate((valid) => {
          if (valid) {
            self.submit_loading = true;
            if(params.is_default==0){
              params.open_agent_money=params.open_agent_money==true?1:0;
              params.open_agent_user=params.open_agent_user==true?1:0;
            }else{
              delete params.open_agent_money;
              delete params.open_agent_user;
              delete params.agent_money;
              delete params.agent_user;
            }
            GradeApi.editGrade(params, true)
              .then(data => {
                self.submit_loading = false;
                ElMessage({
                  message: '恭喜你，等级修改成功',
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
