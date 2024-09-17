<script setup lang="ts">
import {onMounted, reactive, ref} from "vue";
import get from 'lodash/get';
import useLoading from "@/hooks/loading";

const {loading, setLoading} = useLoading(false)
// 列表
import {getProductList} from "@/api/product";

const recentlyData = ref([])
const classicData = ref([])

const fetchRecentlyData = async () => {
  setLoading(true)
  try {
    let params = {
      scope: 'recently_ended',
      page: 1,
      list_row: 3,
    }

    const {data} = await getProductList(params)

    if (data && data?.list) {
      recentlyData.value = data?.list?.data || []
    }

  } catch (err) {
    // console.warn(err)
  } finally {
    setLoading(false)
  }
}

const fetchClassicData = async () => {
  setLoading(true)
  try {
    let params = {
      scope: 'recently_ended',
      page: 1,
      list_row: 3,
    }

    const {data} = await getProductList(params)

    if (data && data?.list) {
      classicData.value = data?.list?.data || []
    }

  } catch (err) {
    // console.warn(err)
  } finally {
    setLoading(false)
  }
}


onMounted(async () => {
  await fetchRecentlyData()
  await fetchClassicData()
})
</script>

<template>
  <!--  <div class=" hidden border-t border-gray-200 pb-16 pt-16">-->
  <!--    <div class="container grid xs:grid-cols-2 gap-6 px-4 lg:px-0">-->
  <!--      <a class="block text-gray-800 bg-gray-50 rounded group shadow"-->
  <!--         data-click-event="select_content"-->
  <!--         data-content-type="update"-->
  <!--         data-item-id="45836"-->
  <!--         data-creative-slot="Home/PromotedUpdates"-->
  <!--         href="/projects/tp1945game/updates/45836">-->
  <!--        <article class="flex">-->
  <!--          <img data-src="https://assets.zeczec.com/asset_850196_image_original.jpg?1723186309" width="1600" height="900"-->
  <!--               class="rounded-t w-auto lg:w-1/2 lg:h-auto lg:rounded-l lg:flex-1 bg-zinc-100 aspect-video object-cover lazy block entered loaded"-->
  <!--               src="https://assets.zeczec.com/asset_850196_image_original.jpg?1723186309" data-ll-status="loaded">-->
  <!--          <div class="w-full p-3 lg:flex-1">-->
  <!--            <span class="text-xs font-bold">計畫更新</span>-->
  <!--            <h3 class="text-sm font-bold leading-relaxed line-clamp-2">《2045》集資百萬達標！主題曲解鎖衝刺中！</h3>-->
  <!--            <span-->
  <!--              class="block leading-relaxed w-full text-center rounded border border-transparent bg-gray-200 text-xs font-bold py-1 mt-3 group-hover:bg-zinc-100 group-hover:border group-hover:border-current group-hover:text-zec-blue">-->
  <!--              完整計畫更新-->
  <!--            </span>-->
  <!--          </div>-->
  <!--        </article>-->
  <!--      </a>-->

  <!--      <a class="block text-gray-800 bg-gray-50 rounded group shadow" data-click-event="select_content"-->
  <!--         data-content-type="update" data-item-id="45928" data-creative-slot="Home/PromotedUpdates"-->
  <!--         href="/projects/fellow-aiden/updates/45928">-->
  <!--        <article class="flex">-->
  <!--          <img data-src="https://assets.zeczec.com/asset_852208_image_original.jpg?1723541836" width="1600" height="900"-->
  <!--               class="rounded-t w-auto lg:w-1/2 lg:h-auto lg:rounded-l lg:flex-1 bg-zinc-100 aspect-video object-cover lazy block entered loaded"-->
  <!--               src="https://assets.zeczec.com/asset_852208_image_original.jpg?1723541836" data-ll-status="loaded">-->
  <!--          <div class="w-full p-3 lg:flex-1">-->
  <!--            <span class="text-xs font-bold">計畫更新</span>-->
  <!--            <h3 class="text-sm font-bold leading-relaxed line-clamp-2">Fellow Aiden 極萃智慧手沖咖啡機，好評不斷，募資時間延長-->
  <!--              | 商品開發進度更新</h3>-->
  <!--            <span-->
  <!--              class="block leading-relaxed w-full text-center rounded border border-transparent bg-gray-200 text-xs font-bold py-1 mt-3 group-hover:bg-zinc-100 group-hover:border group-hover:border-current group-hover:text-zec-blue">-->
  <!--              完整計畫更新-->
  <!--            </span>-->
  <!--          </div>-->
  <!--        </article>-->
  <!--      </a>-->

  <!--      <a class="block text-gray-800 bg-gray-50 rounded group shadow" data-click-event="select_content"-->
  <!--         data-content-type="update" data-item-id="45958" data-creative-slot="Home/PromotedUpdates"-->
  <!--         href="/projects/SuperDoublePlus-2024-08-21/updates/45958">-->
  <!--        <article class="flex">-->
  <!--          <img data-src="https://assets.zeczec.com/asset_853751_image_original.jpg?1723720856" width="1600" height="900"-->
  <!--               class="rounded-t w-auto lg:w-1/2 lg:h-auto lg:rounded-l lg:flex-1 bg-zinc-100 aspect-video object-cover lazy block entered loaded"-->
  <!--               src="https://assets.zeczec.com/asset_853751_image_original.jpg?1723720856" data-ll-status="loaded">-->
  <!--          <div class="w-full p-3 lg:flex-1">-->
  <!--            <span class="text-xs font-bold">計畫更新</span>-->
  <!--            <h3 class="text-sm font-bold leading-relaxed line-clamp-2">計畫更新 #5 輕輕背Plus護脊書包｜✨ 好評延長再 5-->
  <!--              天，感恩加碼 3 好康✨ </h3>-->
  <!--            <span-->
  <!--              class="block leading-relaxed w-full text-center rounded border border-transparent bg-gray-200 text-xs font-bold py-1 mt-3 group-hover:bg-zinc-100 group-hover:border group-hover:border-current group-hover:text-zec-blue">-->
  <!--            完整計畫更新-->
  <!--            </span>-->
  <!--          </div>-->
  <!--        </article>-->
  <!--      </a>-->
  <!--      <a class="block text-gray-800 bg-gray-50 rounded group shadow" data-click-event="select_content"-->
  <!--         data-content-type="update" data-item-id="45812" data-creative-slot="Home/PromotedUpdates"-->
  <!--         href="/projects/mag-cable/updates/45812">-->
  <!--        <article class="flex">-->
  <!--          <img data-src="https://assets.zeczec.com/asset_849644_image_original.jpg?1723099039" width="1600" height="900"-->
  <!--               class="rounded-t w-auto lg:w-1/2 lg:h-auto lg:rounded-l lg:flex-1 bg-zinc-100 aspect-video object-cover lazy block entered loaded"-->
  <!--               src="https://assets.zeczec.com/asset_849644_image_original.jpg?1723099039" data-ll-status="loaded">-->
  <!--          <div class="w-full p-3 lg:flex-1">-->
  <!--            <span class="text-xs font-bold">計畫更新</span>-->
  <!--            <h3 class="text-sm font-bold leading-relaxed line-clamp-2">計畫更新 #3【 Mag Slim Qi2 磁吸行動電源 】世界最薄-->
  <!--              7.3mm，充電 11 倍快！嘖嘖全新登場 ✨</h3>-->
  <!--            <span-->
  <!--              class="block leading-relaxed w-full text-center rounded border border-transparent bg-gray-200 text-xs font-bold py-1 mt-3 group-hover:bg-zinc-100 group-hover:border group-hover:border-current group-hover:text-zec-blue">-->
  <!--              完整計畫更新-->
  <!--            </span>-->
  <!--          </div>-->
  <!--        </article>-->
  <!--      </a>-->
  <!--    </div>-->
  <!--  </div>-->

  <div class="border-t border-gray-200 pb-16">
    <div class="container">
      <div class="flex items-center justify-between my-4 p-4 lg:px-0">
        <h2 class="text-2xl font-bold">近期成功計畫</h2>
        <router-link
          class="inline-block text-base font-bold px-3 py-1 rounded text-white bg-zec-green hover:bg-zec-darkgreen"
          to="/category?scope=recently_ended">更多 &gt;
        </router-link>
      </div>
      <div class="flex lg:-mx-4">
        <router-link
          v-for="item in recentlyData" :key="item.product_id"
          class="overflow-hidden inline-block text-black rounded xs:w-1/2 lg:w-1/3 px-4 pb-4 mb-4 group"
          :to="`/project/${item.product_id}`"
        >
          <div class="bg-white h-full rounded pb-4 flex flex-col group-hover:shadow-md">
            <img
              :src="item.product_image"
              :data-src="item.product_image"
              width="1600" height="900"
              class="rounded-t bg-zinc-100 placeholder-16:9 aspect-video object-cover lazy block entered loaded"
              data-ll-status="loaded">
            <div class="flex flex-col flex-1 h-full justify-between px-3">
              <h3 class="my-4 font-bold leading-relaxed line-clamp-3">
                {{ get(item, 'product_name', '') }}
              </h3>
              <div>
                <div class="flex items-center pb-2 space-x-2">
                  <div class="relative flex-1">
                    <div class="border-4 absolute w-full rounded border-zec-green bg-zec-green"
                         style="width: 100%">
                    </div>
                    <div class="border-4 border-gray-200 bg-gray-200 rounded"></div>
                  </div>
                  <h4 class="text-xs flex-initial text-zec-green">
                    {{ (get(item, 'total_money', 1) / get(item, 'target_money', 1)).toFixed(1) }}%
                  </h4>
                </div>

                <div class="flex items-center space-x-2">
                  <h4 class="text-sm font-semibold flex-1">
                    NT$ {{ get(item, 'total_money', '') }}
                  </h4>
                  <h4 class="text-sm font-semibold flex items-center text-zec-green">
                    <svg-icon class="text-sm text-gray-500 leading-none align-middle mr-px" name="user"/>
                    <span
                    class="text-sm inline-block mr-px">{{ get(item, 'product_sales', '') }}</span>
                    人
                  </h4>
                  <span
                    class="inline-block font-bold text-xs text-white px-2 py-1 rounded border-zec-green bg-zec-green">集資成功</span>
                </div>

              </div>
            </div>
          </div>
        </router-link>

      </div>
    </div>
  </div>


  <div class="border-t border-gray-200 pb-16">
    <div class="container">
      <div class="flex items-center justify-between my-4 p-4 lg:px-0">
        <h2 class="text-2xl font-bold">經典計畫</h2>
      </div>
      <div class="flex lg:-mx-4">
        <router-link
          v-for="item in classicData" :key="item.product_id"
          class="overflow-hidden inline-block text-black rounded xs:w-1/2 lg:w-1/3 px-4 pb-4 mb-4 group"
          :to="`/project/${item.product_id}`">
          <div class="bg-white h-full rounded pb-4 flex flex-col group-hover:shadow-md">
            <img
              width="1600" height="900"
              class="rounded-t bg-zinc-100 placeholder-16:9 aspect-video object-cover lazy block entered loaded"
              :src="item.product_image"
              :data-src="item.product_image"
              data-ll-status="loaded">
            <div class="flex flex-col flex-1 h-full justify-between px-3">
              <h3 class="my-4 font-bold leading-relaxed line-clamp-3">
                {{ get(item, 'product_name', '') }}
              </h3>
              <div>
                <div class="flex items-center pb-2 space-x-2">
                  <div class="relative flex-1">
                    <div class="border-4 absolute w-full rounded border-zec-green bg-zec-green"
                         style="width: 100%"></div>
                    <div class="border-4 border-gray-200 bg-gray-200 rounded"></div>
                  </div>
                  <h4 class="text-xs flex-initial text-zec-green">
                    {{ (get(item, 'total_money', 1) / get(item, 'target_money', 1)).toFixed(1) }}%
                  </h4>
                </div>

                <div class="flex items-center space-x-2">
                  <h4 class="text-sm font-semibold flex-1">
                    NT$ {{ get(item, 'total_money', '') }}
                  </h4>
                  <h4 class="text-sm font-semibold flex items-center text-zec-green">
                    <svg-icon class="text-sm text-gray-500 leading-none align-middle mr-px" name="user"/>
                    <span class="text-sm inline-block mr-px">{{ get(item, 'product_sales', '') }}6</span>
                    人
                  </h4>
                  <span
                    class="inline-block font-bold text-xs text-white px-2 py-1 rounded border-zec-green bg-zec-green">集資成功</span>
                </div>

              </div>
            </div>
          </div>
        </router-link>
      </div>
    </div>
  </div>
</template>

<style scoped lang="scss">

</style>
