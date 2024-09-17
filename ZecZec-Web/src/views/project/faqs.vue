<script setup lang="ts">
import {onMounted, ref, computed} from "vue";
import get from "lodash/get";
import {getProductDetail, getProductFaq} from "@/api/product.ts";
import {useRoute} from "vue-router";
import useLoading from "@/hooks/loading.ts";
import isArray from "lodash/isArray";

const route = useRoute()
const {loading, setLoading} = useLoading(false)
const productId = ref(null)
const productDetail = ref({})
const isLike = ref(false)


const faqNum = computed(() => {
  try {
    console.log(productDetail.value.faq_ids)
    if (productDetail.value.faq_ids) {
      let idList = productDetail.value.faq_ids.split(',')
      console.log(idList)
      if (isArray(idList)) {
        return idList.length
      }
      return 0
    }
  } catch (e) {
    return 0
  }
})

const fetchDetail = async (pid) => {
  if (!pid) return
  setLoading(true)
  try {
    const {data} = await getProductDetail(pid)

    if (data && data.detail) {
      // 獲取屬性值，作為套餐名稱
      let spec_items = get(data, 'detail.product_multi_spec.spec_attr[0].spec_items', []) || []
      let product_sku = data.detail.sku || []
      product_sku = product_sku.map(item => {
        let specItem = spec_items.find(i => i.item_id - 0 === item.spec_sku_id - 0)
        return {
          ...item,
          spec_item: specItem
        }
      })
      productDetail.value = {
        ...data.detail,
        sku: product_sku.filter(item => item.type - 0 === 0)
      }
      console.log(productDetail.value)

      isLike.value = data.is_fav
    }

  } catch (err) {
    // console.warn(err)
  } finally {
    setLoading(false)
  }
}

const faqList = ref([])
const fetchFaqList = async (pid) => {
  if (!pid) return
  setLoading(true)
  try {
    const {data} = await getProductFaq(pid)

    if (data && data.list) {
      // 獲取屬性值，作為套餐名稱
      faqList.value = data.list || []
    }

  } catch (err) {
    // console.warn(err)
  } finally {
    setLoading(false)
  }
}

onMounted(() => {
  const id = get(route, 'params.id')
  if (id) {
    productId.value = id
    fetchDetail(id)
    fetchFaqList(id)
  }
})

</script>

<template>
  <div class="container"></div>

  <div class="border-t border-neutral-200">
    <div class="container cf">
      <div class="w-full flex">
        <div class="inline-block lg:float-left w-full lg:w-1/3">
          <div class="aspect-ratio-project-cover bg-zinc-100 lg:mr-6"
               style="background-image: url('https://assets.zeczec.com/asset_817258_image_big.jpg?1717127997')"></div>
        </div>
        <div class="py-2 lg:px-2 px-4 flex-1">

          <a href="/projects/iris-6-0">
            <h2 class="text-base mb-0 inline-block mt-1 text-base font-bold">
              {{ get(productDetail, 'category.name') }}
            </h2>
          </a>
          <div class="text-neutral-600 text-xs mb-4">
            <!--            <span>提案人</span>-->
            <!--            <a class="font-bold text-zec-cyan" href="/users/tw-irisohyama">可易家電-->
            <!--            </a><span class="mx-2">|</span>-->
            {{ productDetail.type_text }}
          </div>

        </div>
      </div>
    </div>
  </div>


  <div class="px-4 lg:px-0 lg:mt-0 mt-6 top-0 bg-gray-50 sticky">
    <div class="container cf text-sm relative">
      <div class="-mx-4 flex">
        <div
          class="px-4 overflow-auto whitespace-nowrap flex flex-nowrap items-center space-x-8 my-1 tracking-widest lg:w-7/10">

          <router-link
            class="inline-block py-2 hover:border-zec-green lg:border-b-2 text-black border-transparent"
            :to="`/project/${productDetail.product_id}`">專案內容
          </router-link>
          <router-link
            class="inline-block py-2 hover:border-zec-cyan lg:border-b-2 text-black border-transparent border-zec-cyan font-bold"
            :to="`/project/${productDetail.product_id}/faqs`">常見問答
            <span class="text-xs font-bold ml-1 text-neutral-600">{{ faqNum }}</span>
          </router-link>
          <router-link class="inline-block py-2 hover:border-zec-green lg:border-b-2 text-black border-transparent"
                       :to="`/project/${productDetail.product_id}/comments`">留言
            <span class="text-xs font-bold ml-1 text-neutral-600">{{ get(productDetail, 'comment_data_count') }}</span>
          </router-link>

        </div>
        <div class="px-4 py-3 text-center w-full flex lg:static bg-gray-50 bottom-0 z-50 lg:w-3/10 fixed">
          <a
            class="p-2 inline-block flex-initial mr-2 transition-transform hover:scale-105 focus:scale-105 active:scale-90 text-zec-cyan border-2 border-current rounded tooltip tooltip-l"
            data-method="post" data-click-event="follow_project" aria-label="追蹤後會收到公開的專案更新和計畫結束提醒。"
            href="/projects/iris-6-0/follow">
            <svg xmlns="http://www.w3.org/2000/svg" height="48" viewBox="0 96 960 960" width="48"
                 class="w-6 h-6 fill-current">
              <path
                d="m479.435 948.718-48.609-43.978q-106.231-97.889-175.847-168.98-69.616-71.091-110.93-127.066-41.314-55.975-57.53-101.96-16.215-45.986-16.215-93.289 0-94.915 63.544-158.528 63.544-63.613 157.087-63.613 55.885 0 103.877 25.304t84.623 74.543q43.13-51.739 88.77-75.793 45.639-24.054 99.859-24.054 94.379 0 158.006 63.574 63.626 63.574 63.626 158.43 0 47.409-16.215 93.127-16.216 45.718-57.53 101.694-41.314 55.975-111.138 127.412-69.823 71.437-176.204 169.199l-49.174 43.978Zm-.283-100.936q100.045-92.612 164.566-157.708t102.206-113.998q37.685-48.902 52.369-87.12 14.685-38.218 14.685-75.34 0-63.355-40.649-104.475-40.649-41.119-103.649-41.119-50.349 0-92.851 31.783-42.503 31.782-70.503 88.717h-52.217q-26.859-56.5-70.188-88.5t-92.204-32q-62.394 0-102.762 40.599t-40.368 105.353q0 38.151 15.184 76.807 15.183 38.655 52.835 88.06 37.653 49.405 101.556 113.989 63.903 64.583 161.99 154.952Zm1.413-290.412Z"></path>
            </svg>
          </a><a
          class="js-back-project-now tracking-widest flex-1 border-zec-cyan bg-zec-cyan py-2 text-white inline-block w-full text-base transition-transform hover:scale-105 focus:scale-105 active:scale-90 rounded font-bold py-1"
          data-click-event="list_options" href="/projects/iris-6-0/orders/back_project">立即購買</a>
        </div>

      </div>
    </div>
  </div>


  <div class="container"></div>

  <div class="container my-8">
    <div class="lg:-mx-4 flex">
      <div class="w-full px-4 mb-8">
        <div>
          <details v-for="item in faqList" :key="item.faq_id" class="group p-4 even:bg-slate-100 reset-details rounded"
                   id="faq-62888">
            <summary class="flex items-center justify-between py-1 cursor-pointer">
              <div class="w-full text-xs text-gray-400">
                更新於
                {{ item.update_time }}
              </div>
              <h3 class="flex-1 font-semibold pt-2">
                {{ item.question }}
              </h3>
              <svg aria-hidden="true"
                   class="flex-initial w-5 h-5 rotate-0 transform text-gray-500 stroke-2 group-open:rotate-180 transition-transform"
                   fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path d="M19 9l-7 7-7-7" strokelinecap="round" strokelinejoin="round"></path>
              </svg>
            </summary>
            <div class="text-gray-600 leading-relaxed text-sm mt-2 max-w-3xl pl-4 list-disc nested-media">
              <p>{{ item.answer }}</p>
            </div>
          </details>

        </div>
        <div class="text-sm font-bold text-center mt-16">
          <i class="icon-question-sign"></i>
          還有其他問題嗎？
          <router-link :to="`/message/new?product_id=${productDetail.product_id}`">直接問提案人！</router-link>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped lang="scss">

</style>
