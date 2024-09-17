<template>

  <div>
    <div class="common-form">其它</div>

    <el-form-item label="购买条件">
      <el-radio-group v-model="form.conditions">
        <el-radio :label="1">砍到底价可购买</el-radio>
        <el-radio :label="0">砍价中可购买</el-radio>
      </el-radio-group>
      <div class="ml10 gray">
        <template v-if="form.conditions==1">只有砍到底价才可以购买</template>
        <template v-if="form.conditions==0">砍价中可购买是指不需要砍到底价就可以以当前价格下单购买</template>
      </div>
    </el-form-item>

    <el-form-item label="状态">
      <el-radio-group v-model="form.status">
        <el-radio :label="1">生效</el-radio>
        <el-radio :label="0">未生效</el-radio>
      </el-radio-group>
    </el-form-item>

    <el-form-item label="排序">
      <el-input v-model="form.sort" type="number" class="max-w460"></el-input>
      <div class="ml10 gray">(数值越大，活动排在越前面)</div>
    </el-form-item>

  </div>
</template>

<script>
export default {
  inject: ['form']
};
</script>

<style></style>
