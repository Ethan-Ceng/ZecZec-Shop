<script setup lang="ts">
import CoverCard from './components/cover-card.vue'
import Recommend from './components/recommend.vue'
import NavBar from './components/navbar.vue'
import CategoryList from './components/category-list.vue'
import RenewList from './components/renew-list.vue'
</script>

<template>
  <div class="container"></div>

  <Recommend/>


  <!--型別導航-->
  <NavBar/>
  <!-- 分類列表 -->

  <div class="bg-zec-bg">
    <CategoryList :product-type="1"/>


    <div class="border-t border-gray-200 pb-16">
      <CategoryList :product-type="2"/>
    </div>


    <div class="border-t border-gray-200 pb-16">
      <CategoryList :product-type="4"/>
    </div>


    <div class="border-t border-gray-200 pb-16">
      <CategoryList :product-type="3"/>
    </div>
  </div>


  <RenewList/>
</template>

<style lang="scss" scoped>

</style>
