<template>

  <div class="user">
    <el-form size="small" ref="form" :model="form" label-width="150px">
      <el-form-item label="是否开启" prop="is_open">
        <el-switch v-model="form.is_open">
        </el-switch>

      </el-form-item>
       <el-form-item label="个人中心图片" :rules="[{required: true,message: '个人中心图片'}]">
         <div class="ww100">建议尺寸710*302</div>
         <div class="ww100">
          <el-button @click="chooseImg('image')">选择图片</el-button>
         </div>
         <img class="mt10" v-img-url="form.image" :width="375">
       </el-form-item>
      <!--提交-->
      <el-form-item>
        <el-button type="primary" @click="onSubmit" :loading="loading">保存</el-button>
      </el-form-item>
    </el-form>
    <!--上传图片-->
    <Upload v-if="isupload" :isupload="isupload" :type="type" :config="{ total: 3 }" @returnImgs="returnImgsFunc"></Upload>
  </div>
</template>
<script>
  import InvitationGiftApi from '@/api/invitationgift.js';
  import Upload from '@/components/file/Upload.vue';
  export default {
    components:{
      Upload
    },
    data() {
      return {
        form: {
          is_coupon: false,
          is_distribution: false,
          is_point: false,
          image:'',
        },
        setting: [],
        loading: false,
        /*是否打开图片选择*/
        isupload:false
      };
    },
    created() {
      /*获取列表*/
      this.getSetting();
    },
    methods: {
      /*获取设置*/
      getSetting() {
        let self = this;
        let Params = {};
        InvitationGiftApi.getSetting(Params, true)
          .then(data => {
            self.loading = false;
            self.form = data.data.vars.values;
          })
          .catch(error => {});
      },
      /*点击保存*/
      onSubmit() {
        let self = this;
        let params = self.form;
        self.loading = true;
        InvitationGiftApi.saveSetting(params, true)
          .then(data => {
            self.loading = false;
            if (data.code == 1) {
              ElMessage({
                message: '恭喜你，保存成功',
                type: 'success'
              });
              self.getSetting();
            } else {
              self.loading = false;
            }
          })
          .catch(error => {
            self.loading = false;
          });
      },
      /*选择图片*/
      chooseImg(e){
        this.type = e;
        this.isupload=true;
      },

      /*关闭选择图片*/
      returnImgsFunc(e){
        this.isupload=false;
         if (e != null && e.length > 0) {
        if(this.type == 'image'){
           this.form.image=e[0].file_path;
        }
        }
      }
    }
  };
</script>
<style lang="scss" scoped></style>
