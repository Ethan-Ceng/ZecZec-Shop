<script setup lang="ts">
import {ref} from "vue";
import {CaretBottom, CaretRight} from "@element-plus/icons-vue";

const isOpen = ref(false)
</script>

<template>
  <div class="js-details block bg-zinc-100 border border-transparent mb-4 rounded hover:border-neutral-600 mt-4">
    <div class="js-summary font-bold p-4" @click="isOpen = !isOpen">
      <el-icon class="mr-2">
        <template v-if="isOpen">
          <CaretBottom/>
        </template>
        <template v-else>
          <CaretRight/>
        </template>
      </el-icon>
      集資的時間有限制嗎？
    </div>
    <div v-show="isOpen" class="bg-white mv-child-0 p-4 rounded-sm mb-4 ml-4 mr-4">
      <p>嘖嘖平臺上發起最短的計畫為一天；最長的計畫為五個月。我們通常建議集資計畫在 35-45
        天是最理想的。計畫時間過短或太長，都對計畫集資幫助不大。</p>
    </div>
  </div>
</template>

<style scoped lang="scss">

</style>
