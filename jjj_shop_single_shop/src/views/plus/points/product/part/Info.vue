<template>

  <div>
    <div class="common-form">商品信息</div>
    <el-form-item label="商品名称">
      <p class="fb">{{ form.product_name }}</p>
    </el-form-item>
    <el-form-item label="商品图片"><img :src="form.product_image" width="80" /><div class="gray">建议图片上传尺寸为800px*800px</div></el-form-item>
  </div>
</template>

<script>
export default {
  data() {
    return {};
  },
  inject: ['form'],
  created() {
    
  }
};
</script>

<style></style>
