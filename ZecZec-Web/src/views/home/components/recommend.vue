<script setup lang="ts">
import {onMounted, reactive, ref} from "vue";
import {getProductList} from "@/api/product";
import get from "lodash/get";
import useLoading from "@/hooks/loading";
import {lastDay} from "@/utils";

const {loading, setLoading} = useLoading(false)

const recommend = ref({})
const recommendList = ref([])


const fetchTableData = async () => {
  setLoading(true)
  try {
    let params = {
      sortType: 'all',
      page: 1,
      list_row: 5,
    }

    const {data} = await getProductList(params)

    if (data && data?.list) {
      let list = data?.list?.data || []
      recommend.value = list.shift()
      recommendList.value = list
    }

  } catch (err) {
    // console.warn(err)
  } finally {
    setLoading(false)
  }
}

onMounted(() => {
  fetchTableData(0)
})
</script>

<template>
  <div class="bg-zec-bg pt-4">
    <div class="container px-4 md:px-0">
      <router-link
        class="rounded flex flex-col overflow-hidden block group shadow-md"
        :to="{path: `/project/${recommend.product_id}`}"
      >
        <img width="1600" height="900"
             class="rounded-t bg-cover overflow-hidden bg-zinc-100 aspect-video object-cover block group-hover:scale-105 transition-all duration-500 ease-out"
             :src="get(recommend, 'product_image')">
        <div
          class="flex items-center relative py-3 md:py-4 md:px-6 leading-loose md:space-x-6 border-zec-darkgreen bg-zec-darkgreen">
          <h2
            class="font-semibold text-white md:text-xl line-clamp-2 md:line-clamp-1 md:flex-1 w-full md:w-auto px-4 md:px-0">
            {{ get(recommend, 'product_name') }}</h2>
          <div class="border-t border-white w-full mt-2 pb-2 md:hidden"></div>
          <div
            class="flex md:flex-row-reverse items-center space-x-4 md:flex-initial px-4 md:px-0 justify-between w-full md:w-auto">
            <h3 class="leading-none text-white text-sm md:hidden">{{ get(recommend, 'type_text') }}</h3>
            <h3 class="text-white">
              <span class="md:text-2xl font-semibold">$ {{ get(recommend, 'total_money') }}</span>
            </h3>
            <h3 class="text-white">
              <span class="font-semibold">{{ get(recommend, 'product_sales') }}</span>
              <span class="text-xs sm:text-sm">人支援</span>
            </h3>
          </div>
          <div
            class="absolute -top-4 left-4 md:left-0 mb-12 text-white text-sm font-semibold rounded flex items-center border-zec-darkgreen bg-zec-darkgreen">
            <h4 class="leading-none text-white hidden md:block pl-3 py-2 last:pr-3">{{ get(recommend, 'product_name') }}</h4>
            <div class="h-3 w-px bg-white inline-block ml-3 hidden md:block"></div>
            <h4 class="leading-none text-white inline-block px-3 py-2">
              剩下 {{ lastDay(get(recommend, 'active_time[1]')) }} 天
            </h4>
          </div>
        </div>
      </router-link>
    </div>

    <div class="container px-4 md:px-0 py-8 grid grid-cols-4 gap-6">
      <!--  迴圈輸出  -->
      <router-link
        v-for="item in recommendList" :key="item.product_id"
        class="col-span-4 flex md:col-span-2 rounded xs:bg-white xs:shadow"
        :to="{path: `/project/${item.product_id}`}"
      >
        <img width="1600" height="900"
             class="h-24 lg:h-36 rounded xs:rounded-none xs:rounded-s w-auto object-cover aspect-video"
             :src="get(item, 'product_image')">
        <div class="flex flex-col flex-1 px-3 lg:p-4 justify-between">
          <h3 class="font-bold text-sm xs:text-base mb-2 line-clamp-2">
            {{ get(item, 'product_name', '') }}
          </h3>
          <div class="flex justify-between font-bold items-center">
            <h5 class="text-sm md:text-base w-full xs:w-auto mb-1 text-zec-blue-600">
              $ {{ get(item, 'total_money', '') }}
            </h5>
            <h5 class="text-gray-700 text-xs text-right bg-primary-200 rounded px-1 mb-1 leading-relaxed">
              {{ get(item, 'product_sales', '') }}
              <span class="text-xs">人支援</span>
            </h5>
          </div>
        </div>
      </router-link>

    </div>
  </div>
</template>

<style scoped lang="scss">
.recommend {
}
</style>
