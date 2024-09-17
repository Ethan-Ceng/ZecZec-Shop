<template>
  <div class="wrapper">
    <Header/>
    <main class="main">
      <router-view />
    </main>
    <Footer/>
  </div>
</template>

<script lang="ts" setup>
import Header from '@/components/header/index.vue'
import Footer from '@/components/footer/index.vue'
</script>

<style scoped lang="scss">
.wrapper {
  width: 100%;
  height: 100%;
}
</style>
