<template>

  <div class="common-seach-wrap">
    <el-tabs v-model="activeName">
      <el-tab-pane label="活动列表" name="active"></el-tab-pane>
      <el-tab-pane label="砍价记录" name="task"></el-tab-pane>
      <el-tab-pane label="砍价设置" name="setting"></el-tab-pane>
    </el-tabs>

    <!--活动列表-->
    <Active v-if="activeName == 'active'"></Active>
    <!--砍价记录-->
    <Task v-if="activeName == 'task'"></Task>
    <!--砍价设置-->
    <Setting v-if="activeName == 'setting'"></Setting>

  </div>
</template>
<script>
import Active from './active/index.vue';
import Task from './task/index.vue';
import Setting from './setting/index.vue';
export default {
  components: {
    Active,
    Task,
    Setting
  },
  data() {
    return {
      /*当前选中*/
      activeName: 'active'
    };
  },
  methods: {}
};
</script>

<style>

</style>
