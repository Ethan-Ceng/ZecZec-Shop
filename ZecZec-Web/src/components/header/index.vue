<script setup lang="ts">
import HeaderMain from './header-main.vue'
</script>

<template>
  <header class="relative z-20 border-b py-2 px-4 lg:px-0 border-zinc-100">
    <div class="cf container">
      <HeaderMain />
    </div>
  </header>

</template>

<style scoped lang="scss">
.header-wrapper {
  height: 60px;
  padding: 8px 0;
  box-sizing: border-box;
  border-bottom: 1px solid #f4f4f5;

  .header-main {

    .divider {
      width: 1px;
      height: 28px;
      background: #e0e0e0;
      margin: 0 30px;
    }

    .header-nav {
      list-style: none;
      padding: 0;
      margin: 0;
      display: flex;
      align-items: center;

      .nav-item {
        margin-right: 20px;
      }
    }

    .login-nav {
      display: flex;
      align-items: center;
    }
  }


}

.el-dropdown-link {
  display: inline-flex;
  align-items: center;
  cursor: pointer;
}
</style>
