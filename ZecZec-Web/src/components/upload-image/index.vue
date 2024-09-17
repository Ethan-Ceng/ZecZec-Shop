<script setup lang="ts">
import {ref, defineProps, defineEmits} from "vue";
import {ElLoading, ElMessage} from "element-plus"
import {uploadImage} from "@/api/common";


const props = defineProps(['modelValue'])
const emit = defineEmits(['update:modelValue'])


const refUpload = ref(null)
/*选择图片之前*/
const onBeforeUploadImage = (file) => {
  // 清空旧的数据
  refUpload.value?.clearFiles();
  return true;
}

/*上传图片*/
const UploadImage = (param) => {
  const loading = ElLoading.service({
    lock: true,
    text: '图片上传中,请等待',
    background: 'rgba(0, 0, 0, 0.7)',
  });
  const formData = new FormData();
  formData.append('iFile', param.file);
  formData.append('group_id', 0);
  formData.append('file_type', 'image');
  uploadImage(formData)
    .then(({code, data}) => {
      loading.close();
      ElMessage({
        message: '上傳成功',
        type: 'success',
      });
      if (code === 1) {
        emit('update:modelValue', data.file_path)
      }
    })
    .catch((response) => {
      loading.close();
      ElMessage({
        message: '本次上传图片失败',
        type: 'warning',
      });
      param.onError();
    });
}

</script>

<template>
  <el-upload
    class="avatar-uploader"
    multiple
    ref="refUpload"
    action="string"
    accept="image/jpeg,image/png,image/jpg"
    :before-upload="onBeforeUploadImage"
    :limit="1"
    :http-request="UploadImage"
    :show-file-list="false"
  >
    <el-button icon="Upload" type="primary">Click to upload</el-button>
  </el-upload>

</template>

<style scoped lang="scss">

</style>
