<template>
  <div class="active-basic">
    <div class="common-form">基本信息</div>
    <el-form-item label="表单名称" prop="name" :rules="[{ required: true, message: '请输入表单名称', trigger: 'blur' }]">
      <el-input v-model="form.name" placeholder="请输入表单名称" class="max-w460"></el-input>
    </el-form-item>
    <el-form-item label="排序" :rules="[{ required: true, message: '请输入排序' }]" prop="sort">
      <div class="max-w460">
        <el-input v-model="form.sort" type="number"></el-input>
      </div>
    </el-form-item>
  </div>
</template>

<script>
  export default {
  props: {
    form: Object
  },
};
</script>

<style>
</style>
