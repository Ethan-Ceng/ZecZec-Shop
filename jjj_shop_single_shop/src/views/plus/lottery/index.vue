<template>

  <div class="common-seach-wrap">
    <el-tabs v-model="activeName">
      <el-tab-pane label="基础设置" name="setting"></el-tab-pane>
      <el-tab-pane label="抽奖记录" name="record"></el-tab-pane>
      <el-tab-pane label="下架礼品" name="award"></el-tab-pane>
    </el-tabs>
    <!--基础设置-->
    <Setting v-if="activeName == 'setting'"></Setting>
    <!--抽奖记录-->
    <Record v-if="activeName == 'record'"></Record>
    <!--下架礼品 -->
    <Award v-if="activeName == 'award'"></Award>
  </div>
</template>
<script>
  import Record from './record.vue';
  import Setting from './setting.vue';
  import Award from './award.vue';
  export default {
    components: {
      Record,
      Setting,
      Award
    },
    data() {
      return {
        /*是否加载完成*/
        loading: true,
        activeName: 'setting'
      };
    },
    methods: {}
  };
</script>

<style lang="scss" scoped>
  .operation-wrap {
    height: 124px;
    border-radius: 8px;
    -webkit-box-pack: center;
    -ms-flex-pack: center;
    justify-content: center;
    padding: 30px 30px;
    -webkit-box-orient: vertical;
    -webkit-box-direction: normal;
    -ms-flex-direction: column;
    flex-direction: column;
    overflow: hidden;
    background: #909399;
    background-size: 100% 100%;
    color: #fff;
  }
</style>
