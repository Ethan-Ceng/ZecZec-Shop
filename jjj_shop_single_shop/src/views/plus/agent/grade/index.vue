<template>
  <div>
    <el-tabs size="small" v-model="activeName" type="card" @tab-change="handleClick">
      <el-tab-pane label="等级列表" name="list"></el-tab-pane>
      <el-tab-pane label="升级日志" name="log"></el-tab-pane>
    </el-tabs>
    <!--等级列表-->
    <List v-if="activeName == 'list'"></List>
    <!--升级日志-->
    <Log v-if="activeName == 'log'"></Log>
  </div>
</template>
<script>
import List from './part/list.vue';
import Log from './part/log.vue';
export default {
  components: {
    /*编辑组件*/
    List,
    Log
  },
  data() {
    return {
      /*当前选中*/
      activeName: 'list',
    };
  },
  created() {},
  methods: {

    handleClick(e) {
      this.activeName = e;
    }
  }
};
</script>
