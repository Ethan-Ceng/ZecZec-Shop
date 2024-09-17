<template>

  <div class="common-seach-wrap">
    <el-tabs v-model="activeName">
      <el-tab-pane label="秒杀活动" name="first"></el-tab-pane>
      <el-tab-pane label="基础设置" name="third"></el-tab-pane>
    </el-tabs>

    <!--秒杀活动-->
    <Active v-if="activeName == 'first'"></Active>

    <!--基础设置-->
    <Setting v-if="activeName == 'third'"></Setting>
  </div>
</template>
<script>
import Active from './active/index.vue';
import Setting from './setting/getSetting.vue';
export default {
  components: {
    Active,
    Setting
  },
  data() {
    return {
      /*是否加载完成*/
      loading: true,
      form: {
        status: 0
      },
      activeName: 'first'
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
