<script setup lang="ts">
import {ref} from "vue";

/**
 * 群眾集資 text-zec-green
 * 預購式專案
 * 再登場專案
 * 訂閱式專案
 */
// const productType = ref(null)
</script>

<template>
  <a class="inline-block text-black rounded xs:w-1/2 lg:w-1/3 px-4 pb-4 mb-4 group"
     data-click-event="select_content" data-content-type="project"
     data-item-id="flusso_2024_sunprotect"
     data-creative-slot="Home/Sprint"
     href="/projects/flusso_2024_sunprotect">
    <div class="bg-white h-full rounded pb-4 flex flex-col group-hover:shadow-md">
      <img width="1600" height="900"
           class="rounded-t bg-zinc-100 placeholder-16:9 aspect-video object-cover lazy block entered loaded"
           src="https://assets.zeczec.com/asset_857731_image_big.jpg?1724333922">
      <div class="flex flex-col flex-1 h-full justify-between px-3">
        <h3 class="my-4 font-bold leading-relaxed line-clamp-3">【FLUSSO瞬適光學｜最新專利科技力作】護駕眼鏡・Made in
          Taiwan：逆光，依舊舒適前行</h3>
        <div>
          <div class="flex items-center pb-2 space-x-2">
            <div class="relative flex-1">
              <div class="border-4 absolute w-full rounded border-zec-green bg-zec-green"
                   style="width: 100%"></div>
              <div class="border-4 border-gray-200 bg-gray-200 rounded"></div>
            </div>
            <h4 class="text-xs flex-initial text-zec-green">
              22,252%
            </h4>
          </div>

          <div class="flex items-center space-x-2">
            <h4 class="text-sm font-semibold flex-1">
              $ 2,225,250
            </h4>
            <h4 class="text-sm font-semibold flex items-center text-zec-green">
              <a-icon class="material-icons text-sm text-gray-500 leading-none align-middle mr-px">
                <a-icon><UserFilled /></a-icon>
              </a-icon>
              <span class="text-sm inline-block mr-px">704</span>人
            </h4>
            <h4 class="font-semibold flex items-center text-zec-green"><i
              class="material-icons text-sm text-gray-500 leading-none align-middle mr-px">hourglass_top</i>
              <span class="text-sm">20 天</span>
            </h4>
          </div>
        </div>
      </div>
    </div>
  </a>
</template>

<style scoped lang="scss">

</style>
