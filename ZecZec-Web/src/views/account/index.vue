<script setup lang="ts">
import {onMounted, ref, watch} from "vue";
import {useRoute} from "vue-router";

import Order from './components/order.vue'
import Follow from './components/follow.vue'
// import Product from './components/product.vue'
import Setting from "./components/setting.vue";
import Teams from "./components/teams.vue";
import Password from './components/password.vue'
import get from "lodash/get";

const route = useRoute()

const activeName = ref('order')


watch(() => route.query,
  (newVal) => {
    const type = get(newVal, 'type')
    if (['order', 'follow', 'setting', 'teams'].includes(type)) {
      activeName.value = type
    }
  }, {
    immediate: true
  }
)

</script>

<template>
  <div class="account">
    <div class="border-b">
      <div class="header-main container">
        <el-tabs v-model="activeName" class="nav-tabs" @tab-click="handleClick">
          <el-tab-pane label="贊助記錄" name="order"></el-tab-pane>
          <el-tab-pane label="追蹤計畫" name="follow"></el-tab-pane>
          <el-tab-pane label="個人設定" name="setting"></el-tab-pane>
          <el-tab-pane label="團隊設定" name="teams"></el-tab-pane>
          <el-tab-pane label="修改密碼" name="password"></el-tab-pane>
        </el-tabs>
      </div>
    </div>
    <div class="container"></div>

    <Order v-if="activeName === 'order'"/>

    <Follow v-if="activeName === 'follow'"/>

    <Setting v-if="activeName === 'setting'"/>

    <Teams v-if="activeName === 'teams'"/>

    <div class="mx-auto" style="max-width: 320px;">
      <Password v-if="activeName === 'password'"/>
    </div>
  </div>

</template>

<style scoped lang="scss">
.header-main {

  ::v-deep(.el-tabs) {

    .el-tabs__header {
      margin-bottom: 0;
    }

    .el-tabs__item {
      padding-top: 16px;
      padding-bottom: 16px;
      height: 64px;
      color: rgb(82, 82, 82);

      &.is-active {
        color: rgb(0, 0, 0);
        font-weight: 500;
      }
    }

    .el-tabs__active-bar {
      background-color: rgb(0, 0, 0);
    }
  }
}

.tab-pane-main {
  max-width: 1140px;
  margin: 0 auto;
  padding: 16px 0;
}
</style>
