<template>

  <div>
    <div class="common-form">其它</div>
    <el-form-item label="限购数量" prop="sort" :rules="[{ required: true, message: '请填写限购数量' }]"><el-input type="number" min="0" v-model="form.limit_num" class="max-w460"></el-input></el-form-item>
    <el-form-item label="排序" prop="sort" :rules="[{ required: true, message: '请填写排序' }]"><el-input type="number" v-model="form.sort" class="max-w460"></el-input></el-form-item>
    <el-form-item label="商品状态">
      <el-radio-group v-model="form.status">
        <el-radio :label="10">上架</el-radio>
        <el-radio :label="20">下架</el-radio>
      </el-radio-group>
    </el-form-item>
  </div>
</template>

<script>
export default {
  inject: ['form']
};
</script>

<style></style>
