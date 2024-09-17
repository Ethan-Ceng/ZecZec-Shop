<template>

  <div>
    <el-form size="small" :model="form" ref="ruleForm" label-width="100px">
      <div class="mt16">
        <div class="common-form">活动广告</div>
        <el-form-item label="" prop="back_image">
          <div class="d-c-c ad-picture" @click="openUpload">
            <img v-if="form.back_image" :src="form.back_image" :width="186" :height="128" />
            <el-icon v-else><Picture /></el-icon>
          </div>
          <p class="ml10 gray">提示：图片尺寸建议750*256(像素)</p>
        </el-form-item>
        <el-tabs v-model="activeName">
          <el-tab-pane label="成长任务" name="grow_task"></el-tab-pane>
          <el-tab-pane label="每日任务" name="day_task"></el-tab-pane>
        </el-tabs>
        <template v-if="activeName=='grow_task'">
          <div v-for="(item, index) in form.grow_task" :key="index">
            <div class="common-form">{{ item.name }}</div>
            <el-form-item label="状态:" prop="is_open">
              <el-radio-group v-model="item.is_open">
                <el-radio :label="'1'">开启</el-radio>
                <el-radio :label="'0'">关闭</el-radio>
              </el-radio-group>
            </el-form-item>
            <template v-if="item.is_open == '1'">
              <el-form-item label="奖励:" prop="points">
                <div class="max-w460 d-s-c">
                  <div class="flex-1"><el-input v-model="item.points" placeholder="0" type="number"></el-input></div>
                  <span class="ml10">积分</span>
                </div>
              </el-form-item>
              <el-form-item label="规则:" prop="rule">
                <div class="max-w460 d-s-c">
                  <div class="flex-1"><el-input type="textarea" resize="none" :autosize="{ minRows: 2, maxRows: 4 }" placeholder="请输入内容" v-model="item.rule"></el-input></div>
                </div>
              </el-form-item>
            </template>
          </div>
        </template>
        <template v-if="activeName=='day_task'">
          <div v-for="(item, index) in form.day_task" :key="index">
            <div class="common-form">{{ item.name }}</div>
            <el-form-item label="状态:" prop="is_open">
              <el-radio-group v-model="item.is_open">
                <el-radio :label="'1'">开启</el-radio>
                <el-radio :label="'0'">关闭</el-radio>
              </el-radio-group>
            </el-form-item>
            <template v-if="item.is_open == '1'">
              <el-form-item label="奖励:" prop="points">
                <div class="max-w460 d-s-c">
                  <div class="flex-1"><el-input v-model="item.points" placeholder="0" type="number"></el-input></div>
                  <span class="ml10">积分</span>
                </div>
              </el-form-item>
              <el-form-item label="规则:" prop="rule">
                <div class="max-w460 d-s-c">
                  <div class="flex-1"><el-input type="textarea" resize="none" :autosize="{ minRows: 2, maxRows: 4 }" placeholder="请输入内容" v-model="item.rule"></el-input></div>
                </div>
              </el-form-item>
            </template>
          </div>
        </template>
      </div>
      <!--提交-->
      <div class="common-button-wrapper"><el-button type="primary" size="small" @click="onSubmit">提交</el-button></div>
    </el-form>
    <!--上传图片组件-->
    <Upload v-if="isupload" :isupload="isupload" @returnImgs="returnImgsFunc">上传图片</Upload>
  </div>
</template>
<script>
import SettingApi from '@/api/setting.js';
import Upload from '@/components/file/Upload.vue';
export default {
  components: {
    Upload
  },
  data() {
    return {
      activeName:"grow_task",
      /*表单数据对象*/
      form: {
        back_image: '',
        grow_task: [],
        day_task: []
      },
      /*是否正在加载*/
      loading: true,
      isupload: false
    };
  },
  created() {
    /*获取列表*/
    this.getData();
  },
  methods: {
    /*上传*/
    openUpload(e) {
      this.isupload = true;
    },
    /*获取图片*/
    returnImgsFunc(e) {
      if (e != null && e.length > 0) {
        this.form.back_image = e[0].file_path;
      }
      this.isupload = false;
    },
    /*获取列表*/
    getData() {
      let self = this;
      self.loading = true;
      SettingApi.getTask()
        .then(data => {
          self.loading = false;
          let val = data.data.vars.values;
          self.form.back_image = val.back_image;
          self.form.grow_task = val.grow_task;
          self.form.day_task = val.day_task;
        })
        .catch(error => {});
    },

    /*提交签到有礼*/
    onSubmit() {
      let self = this;
      let params = self.form;
      self.loading = true;
      self.$refs.ruleForm.validate(valid => {
        if (valid) {
          SettingApi.editTask(params, true)
            .then(data => {
              ElMessage({
                message: '恭喜你，保存成功',
                type: 'success'
              });
              self.getData();
            })
            .catch(error => {});
        }
      });
    },

    /*获取富文本编辑器的内容*/
    getContent: function() {
      return this.$refs.ue.getUEContent();
    },

    /*添加奖励*/
    addReward() {
      let obj = {
        day: 0,
        is_point: false,
        point: 0,
        is_coupon: false,
        coupon: []
      };
      this.form.reward_data.push(obj);
    },

    /*删除奖励*/
    deleteRewardClick(e) {
      this.form.reward_data.splice(e, 1);
    }
  }
};
</script>

<style scoped="scoped">
.open-sign {
  padding: 20px;
  background: #e2f0ff;
}

.reward-list .reward-item {
  margin-bottom: 10px;
  padding: 20px 20px 0;
}

.reward-list .delete-reward {
  position: absolute;
  top: -10px;
  right: -10px;
  cursor: pointer;
  z-index: 10;
}

.el-button--small.is-circle {
  padding: 4px;
}
.ad-picture {
  width: 186px;
  height: 128px;
  font-size: 40px;
  color: var(--el-color-primary);
  cursor: pointer;
  overflow: hidden;
}
.ad-picture:hover {
  border: 1px dashed var(--el-color-primary);
}

.ad-picture img {
  object-fit: fill;
}
</style>
