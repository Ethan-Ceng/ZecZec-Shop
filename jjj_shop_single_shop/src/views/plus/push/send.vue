<template>
    <div class="common-seach-wrap">
      <el-tabs v-model="activeName" @tab-change="handleClick">
        <el-tab-pane label="发送消息" name="first">
        </el-tab-pane>
        <el-tab-pane label="活跃用户" name="second">
        </el-tab-pane>
        <el-tab-pane label="满额包邮" name="third">
        </el-tab-pane>
      </el-tabs>

      <!--发送消息-->
      <Table1 v-if="activeName=='first'"></Table1>
       <!--活跃用户-->
      <Table2 v-if="activeName=='second'"></Table2>
       <!--满额包邮-->
      <Table3 v-if="activeName=='third'"> </Table3>
    </div>
</template>
<script>
  import Table1 from './part/Table1.vue';
  import Table2 from './part/Table2.vue';
  import Table3 from './part/Table3.vue';
  export default {
    components: {
      Table1,
      Table2,
      Table3,
    },
    data() {
      return {
        form: {
          status: 0,
        },
        activeName: 'first'
      };
    },
    methods: {

    }
  };
</script>

<style>
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
