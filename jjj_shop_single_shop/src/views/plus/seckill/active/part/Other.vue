<template>

  <div>
    <div class="common-form">其它</div>

    <el-form-item label="活动排序" :rules="[{ required: true, message: '请输入活动排序' }]" prop="sort">
      <div class="max-w460">
        <el-input v-model="form.sort" type="number"></el-input>
      </div>
    </el-form-item>
    <el-form-item label="是否生效">
      <el-radio-group v-model="form.status">
        <el-radio :label="0">未生效</el-radio>
        <el-radio :label="1">生效</el-radio>
      </el-radio-group>
    </el-form-item>
  </div>
</template>

<script>
export default {
  props: ['form']
};
</script>

<style lang="scss" scoped></style>
