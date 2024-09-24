<script setup lang="ts">
import {ref, defineProps, computed} from "vue";
import get from 'lodash/get';
import {lastDay} from "../../utils";

const props = defineProps({
  dataSource: {}
})

// 1：群眾集資 2 預購式專案 bg-zec-blue 3 訂閱式專案 4 再登場專案
const productType = ref(1)

const colorClass = computed(() => {
  if (props.dataSource.type === 1) {
    return 'border-zec-green bg-zec-green'
  }

  if (props.dataSource.type === 2) {
    return 'border-zec-blue bg-zec-blue'
  }

  if (props.dataSource.type === 3) {
    return 'border-zec-purple bg-zec-purple'
  }
})

const textClass = computed(() => {
  if (props.dataSource.type === 1) {
    return 'text-zec-green'
  }
  if (props.dataSource.type === 2) {
    return 'text-zec-blue'
  }
  if (props.dataSource.type === 3) {
    return 'text-zec-purple'
  }
})
</script>

<template>
  <router-link class="inline-block text-black rounded xs:w-1/2 lg:w-1/3 px-4 pb-4 mb-4 group"
               data-click-event="select_content" data-content-type="project" data-item-id="gellis-evo-cushion0822"
               data-creative-slot="Categories" :to="{path: `/project/${dataSource.product_id}`}">
    <div class="bg-white h-full rounded pb-4 flex flex-col group-hover:shadow-md">
      <img
        :src="dataSource.product_image"
        :data-src="dataSource.product_image"
        width="1600" height="900"
        class="rounded-t bg-zinc-100 placeholder-16:9 aspect-video object-cover lazy block entered loaded"
        data-ll-status="loaded"
      >
      <div class="flex flex-col flex-1 h-full justify-between px-3">
        <h3 class="my-4 font-bold leading-relaxed line-clamp-3">{{ get(dataSource, 'product_name', '') }}</h3>
        <!-- type 4 -->
        <div v-if="dataSource.type === 4">
          <div class="flex items-center pb-2 space-x-2">
            <div class="relative flex-1">
              <div class="border-4 absolute w-full rounded border-zec-cyan bg-zec-cyan" style="width: 100%"></div>
              <div class="border-4 border-gray-200 bg-gray-200 rounded"></div>
            </div>
            <h4 class="text-xs flex-initial text-zec-cyan">
              <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 34 35" fill="none"
                   class="inline-block" style="padding-right: 0.1rem">
                <path fill-rule="evenodd" clip-rule="evenodd"
                      d="M0.423977 31.5682L3.42908 34.1951C3.89683 34.5581 4.23572 34.5451 4.47095 34.1093L12.9851 19.9224C13.0537 19.793 13.1487 19.6796 13.2637 19.5897C13.3788 19.4997 13.5112 19.4352 13.6522 19.4006C13.7932 19.3659 13.9396 19.3618 14.0816 19.3886C14.2235 19.4155 14.3579 19.4725 14.4756 19.556L19.6992 23.707C19.9286 23.8647 20.2119 23.9233 20.4868 23.87C20.7616 23.8167 21.0056 23.6559 21.165 23.4229L33.2282 3.72504C33.526 3.23758 33.5468 2.88703 33.0572 2.48204L30.1109 0.168239C29.5586 -0.129872 29.0991 -0.0311419 28.8345 0.459141L19.7283 14.9446C19.5627 15.1909 19.3081 15.3627 19.0199 15.4225C18.7317 15.4824 18.433 15.4255 18.1887 15.2643L13.0975 11.0792C13.0021 10.9826 12.8867 10.9085 12.7594 10.8618C12.632 10.8152 12.4958 10.7972 12.3602 10.8092C12.2246 10.8211 12.0928 10.8627 11.9741 10.9309C11.8553 10.9992 11.7525 11.0926 11.6727 11.2046L0.189048 30.3058C-0.102429 30.7816 -0.0810426 31.1549 0.423977 31.5682Z"
                      fill="#0891B2"></path>
              </svg>
              再登場
            </h4>
          </div>

          <div class="flex items-center space-x-2">
            <h4 class="text-sm font-semibold flex-1">
              $ {{ dataSource.total_money }}
            </h4>
            <h4 class="text-sm font-semibold flex items-center text-zec-cyan">
              <svg-icon class="text-sm text-gray-500 leading-none align-middle mr-px" name="user"/>
              <span
              class="text-sm inline-block mr-px">{{ dataSource.product_sales }}</span>
              人
            </h4>
          </div>

        </div>
        <!-- type 123 -->
        <div v-else>
          <div class="flex items-center pb-2 space-x-2">
            <div class="relative flex-1">
              <div class="border-4 absolute w-full rounded"
                   :class="colorClass"
                   style="width: 100%"></div>
              <div class="border-4 border-gray-200 bg-gray-200 rounded"></div>
            </div>
            <h4 class="text-xs flex-initial" :class="textClass">
              109%
            </h4>
          </div>

          <div class="flex items-center space-x-2">
            <h4 class="text-sm font-semibold flex-1">
              $ {{ dataSource.target_money }}
            </h4>
            <h4 class="text-sm font-semibold flex items-center" :class="textClass">
              <svg-icon class="text-sm text-gray-500 leading-none align-middle mr-px" name="user"/>
              <span class="text-sm inline-block mr-px">{{ dataSource.product_sales }}</span>
              人
            </h4>
            <h4 class="font-semibold flex items-center" :class="textClass">
              <svg-icon class="text-sm text-gray-500 leading-none align-middle mr-px" name="downtime"/>
              <span class="text-sm">{{ lastDay(get(dataSource, 'active_time[1]')) }} 天</span>
            </h4>
          </div>
        </div>
      </div>

    </div>
  </router-link>
</template>

<style scoped lang="scss">

</style>
