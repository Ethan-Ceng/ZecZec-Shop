<template>

  <div class="active-basic">
    <div class="common-form">基本信息</div>

    <el-form-item label="活动标题" prop="title" :rules="[{ required: true, message: '请输入活动标题' }]">
      <el-input v-model="form.title" placeholder="请输入活动标题" class="max-w460"></el-input>
    </el-form-item>

    <el-form-item label="活动广告" prop="image_id" :rules="rulesImage">
      <div class="d-c-c ad-picture" @click="openUpload">
        <img v-if="form.image_id > 0" :src="form.file_path" width="300" height="124" />
        <el-icon v-else class="el-icon-picture-outline">
          <Picture />
        </el-icon>
      </div>
      <p class="ml10 gray">提示：图片尺寸建议750x310</p>
    </el-form-item>

    <!--上传图片组件-->
    <Upload v-if="isupload" :isupload="isupload" @returnImgs="returnImgsFunc">上传图片</Upload>
  </div>
</template>

<script>
import Upload from '@/components/file/Upload.vue';
export default {
  components: {
    /*选择商品组件*/
    Upload
  },
  data() {
    /*验证图片是否上传*/
    const validatorImages = (rule, value, callback) => {
      if (value < 1) {
        return callback(new Error('请上传活动图'));
      } else {
        callback();
      }
    };

    return {
      /*是否上传图片*/
      isupload: false,
      /*图片是否上传了*/
      rulesImage: [{ validator: validatorImages, required: true, message: '请上传活动广告图片', trigger: 'change' }]
    };
  },
  inject: ['form'],
  methods: {
    /*上传*/
    openUpload(e) {
      this.isupload = true;
    },

    /*获取图片*/
    returnImgsFunc(e) {
      if (e != null && e.length > 0) {
        this.form.file_path = e[0].file_path;
        this.form.image_id = e[0].file_id;
      }
      this.isupload = false;
    }
  }
};
</script>

<style lang="scss" scoped>
::v-deep(.el-icon-picture-outline svg){ 
  width: auto;
  height: auto;
}
.active-basic .ad-picture {
  width: 300px;
  height: 124px;
  border-radius: 16px;
  font-size: 40px;
  border: 1px dashed #cccccc;
  color: var(--el-color-primary);
  cursor: pointer;
  overflow: hidden;
}
.active-basic .ad-picture:hover {
  border: 1px dashed var(--el-color-primary);
}

.active-basic .ad-picture img {
  object-fit: fill;
}
</style>
