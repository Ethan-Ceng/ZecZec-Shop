<script setup lang="ts">
import FallbackImg from '@/assets/image/fallback.jpg'
import useLoading from "@/hooks/loading";
import {onMounted, reactive, ref, computed} from "vue";
import {getProductComment, getProductDetail, likeProduct} from "@/api/product";
import {useRoute} from "vue-router";
import get from "lodash/get";
import {ElMessage} from "element-plus";

const route = useRoute()

const {loading, setLoading} = useLoading(false)
const productId = ref(null)
const productDetail = ref({})
const isLike = ref(false)


const faqNum = computed(() => {
  try {
    if (productDetail.value.faq_ids) {
      let ids = productDetail.value.faq_ids.split(',')
      return ids.length
    }
  } catch (e) {
    return 0
  }
})

const fetchDetail = async (productId) => {
  if (!productId) return
  setLoading(true)
  try {
    const {data} = await getProductDetail(productId)

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

const fetchLike = async (productId) => {
  if (!productId) return
  setLoading(true)
  try {
    await likeProduct(productId)
    isLike.value = !isLike.value
    const message = isLike.value ? "加入追蹤計劃" : "取消追蹤計劃"
    ElMessage.success(message)
  } catch (err) {
    // console.warn(err)
  } finally {
    setLoading(false)
  }
}

// 列表
const tableData = ref([])
const pageValue = reactive({
  pageSize: 20,
  current: 1,
  total: 0
});

async function fetchTableData(extraParam = {}) {
  setLoading(true)
  try {
    let params = {
      product_id: productId.value,
      page: pageValue.current,
      list_rows: pageValue.pageSize,
    }
    if (extraParam) {
      params = {
        ...params,
        ...extraParam
      }
    }

    const {data} = await getProductComment(params)

    if (data && data?.list) {
      tableData.value = data?.list?.data || []
      pageValue.total = data?.list?.total || 0
    }

  } finally {
    setLoading(false)
  }
}

const onPageChange = (current: number) => {
  fetchTableData({page: current});
};


onMounted(() => {
  const id = get(route, 'params.id')
  if (id) {
    productId.value = id
    fetchDetail(id)
    fetchTableData()
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
               :style="`background-image: url('${get(productDetail, 'product_image', '')}')`"></div>
        </div>
        <div class="py-2 lg:px-2 px-4 flex-1">

          <h2 class="text-base mb-0 inline-block mt-1 text-base font-bold">
            {{ get(productDetail, 'category.name') }}
          </h2>
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
            class="text-xs font-bold ml-1 text-neutral-600"
            :to="`/project/${productDetail.product_id}`">專案內容
          </router-link>
          <router-link class="inline-block py-2 hover:border-zec-green lg:border-b-2 text-black border-transparent"
                       :to="`/project/${productDetail.product_id}/faqs`">常見問答
            <span class="text-xs font-bold ml-1 text-neutral-600">{{faqNum}}</span>
          </router-link>
          <router-link class="inline-block py-2 hover:border-zec-green lg:border-b-2 text-black border-transparent"
                       :to="`/project/${productDetail.product_id}/comments`">留言
            <span
              class="inline-block py-2 hover:border-zec-green lg:border-b-2 text-black border-transparent border-zec-green font-bold">{{
                get(productDetail, 'comment_data_count')
              }}</span>
          </router-link>

        </div>
        <div class="px-4 py-3 text-center w-full flex lg:static bg-gray-50 bottom-0 z-50 lg:w-3/10 fixed">
          <a
            class="p-2 inline-block flex-initial mr-2 transition-transform hover:scale-105 focus:scale-105 active:scale-90 text-zec-cyan border-2 border-current rounded tooltip tooltip-l"
            data-method="post"
            data-click-event="follow_project"
            aria-label="追蹤後會收到公開的專案更新和計畫結束提醒。"
            @click="fetchLike(productDetail.product_id)">
            <svg xmlns="http://www.w3.org/2000/svg" height="48" viewBox="0 96 960 960" width="48"
                 class="w-6 h-6 fill-current">
              <path
                d="m479.435 948.718-48.609-43.978q-106.231-97.889-175.847-168.98-69.616-71.091-110.93-127.066-41.314-55.975-57.53-101.96-16.215-45.986-16.215-93.289 0-94.915 63.544-158.528 63.544-63.613 157.087-63.613 55.885 0 103.877 25.304t84.623 74.543q43.13-51.739 88.77-75.793 45.639-24.054 99.859-24.054 94.379 0 158.006 63.574 63.626 63.574 63.626 158.43 0 47.409-16.215 93.127-16.216 45.718-57.53 101.694-41.314 55.975-111.138 127.412-69.823 71.437-176.204 169.199l-49.174 43.978Zm-.283-100.936q100.045-92.612 164.566-157.708t102.206-113.998q37.685-48.902 52.369-87.12 14.685-38.218 14.685-75.34 0-63.355-40.649-104.475-40.649-41.119-103.649-41.119-50.349 0-92.851 31.783-42.503 31.782-70.503 88.717h-52.217q-26.859-56.5-70.188-88.5t-92.204-32q-62.394 0-102.762 40.599t-40.368 105.353q0 38.151 15.184 76.807 15.183 38.655 52.835 88.06 37.653 49.405 101.556 113.989 63.903 64.583 161.99 154.952Zm1.413-290.412Z"></path>
            </svg>
          </a>
          <router-link
          class="js-back-project-now tracking-widest flex-1 border-zec-cyan bg-zec-cyan py-2 text-white inline-block w-full text-base transition-transform hover:scale-105 focus:scale-105 active:scale-90 rounded font-bold py-1"
          data-click-event="list_options"
          :to="`/order/${productDetail.product_id}`">立即購買</router-link>
        </div>

      </div>
    </div>
  </div>

  <div class="container"></div>

  <div class="container my-8">
    <div class="lg:-mx-4 flex">
      <div class="w-full px-4 mb-8">
        <div class="bg-zinc-100 p-2 text-xs mb-8">
          贊助人才能留言。如有專案相關提問，請聯絡
          <router-link class="font-bold text-black"
             :to="`/message/new?product_id=${productDetail.product_id}`">我們</router-link>。
        </div>
        <div class="js-comments"
             data-read-time-mark="last-read-time-for-project-18514"
             data-read-time-value="1725002882">

          <div
            v-for="(item, index) in tableData"
            :key="index"
            class="cf -ml-4 py-4 -mr-4 lg:mx-0 border-t border-neutral-200 text-sm js-comment"
          >
            <div class="px-4" data-created-at="1722924488">
              <div class="float-right">
                <span class="ml-2 text-neutral-600 text-xs" title="2024/08/06 14:08">
                  {{ item.create_time }}
                </span>
              </div>
              <img class="round mr-4 h-8 w-8 absolute" :src="item.users.avatarUrl">
              <div class="ml-8 pl-4">
                <span class="font-bold mr-2">{{ item.users.nickName }}</span>
                <span
                  class="text-zec-blue p-1 whitespace-nowrap leading-none rounded-sm text-xs mr-1 border border-current font-bold">贊助人</span>
                <div class="mt-2 mv-child-0"><p>{{ item.content }}</p></div>
              </div>

<!--              <div class="pl-8">-->
<!--                <div class="mt-4 pl-4" data-created-at="1724641975" id="comment-582737">-->
<!--                  <div class="float-right">-->
<!--                    <span class="text-xs text-neutral-600 ml-4" title="2024/08/26  11:12">-->
<!--                    4 天前-->
<!--                    </span>-->
<!--                  </div>-->
<!--                  <a class=" mr-2 font-bold" href="/users/tw-irisohyama">可易家電</a>-->
<!--                  <span-->
<!--                    class="bg-zec-green text-white border-zec-green p-1 whitespace-nowrap leading-none rounded-sm text-xs mr-1 border border-current font-bold">提案人</span>-->
<!--                  <div class="mt-1 flex flex-col space-y-2 py-2"><p>-->
<!--                    您好，如頁面出貨時間規劃，週週出貨喔，感謝您的詢問^^. </p></div>-->
<!--                </div>-->
<!--              </div>-->
            </div>
          </div>


          <!--          <div class="cf -ml-4 py-4 -mr-4 lg:mx-0 border-t border-neutral-200 text-sm js-comment" id="comment-562997">-->
          <!--            <div class="px-4" data-created-at="1718509993">-->
          <!--              <div class="float-right">-->
          <!--                <a class="ml-2 text-neutral-600 text-xs" href="#comment-562997" title="2024/06/16 11:53">-->
          <!--                  3 個月前-->
          <!--                </a>-->
          <!--              </div>-->
          <!--              <img class="round mr-4 h-8 w-8 absolute"-->
          <!--                   src="https://graph.facebook.com/1261437993876040/picture?width=60&amp;height=60&amp;access_token=171735882933694|35e7b5bf77a7c8d69193f308b50fb9e1">-->
          <!--              <div class="ml-8 pl-4">-->
          <!--                <a class="font-bold mr-2" href="/users/old899">Selina Hsu</a>-->
          <!--                <span-->
          <!--                  class="text-zec-blue p-1 whitespace-nowrap leading-none rounded-sm text-xs mr-1 border border-current font-bold">贊助人</span>-->
          <!--                <div class="mt-2 mv-child-0"><p>請問何時會出貨？</p></div>-->
          <!--              </div>-->
          <!--              <div class="pl-8">-->
          <!--                <div class="mt-4 pl-4" data-created-at="1718853333" id="comment-564529">-->
          <!--                  <div class="float-right">-->
          <!--                    <span class="text-xs text-neutral-600 ml-4" title="2024/06/20  11:15">-->
          <!--                    2 個月前-->
          <!--                    </span>-->
          <!--                  </div>-->
          <!--                  <a class=" mr-2 font-bold" href="/users/tw-irisohyama">可易家電</a>-->
          <!--                  <span-->
          <!--                    class="bg-zec-green text-white border-zec-green p-1 whitespace-nowrap leading-none rounded-sm text-xs mr-1 border border-current font-bold">提案人</span>-->
          <!--                  <div class="mt-1 flex flex-col space-y-2 py-2"><p>您好，出貨時間參考如下，謝謝詢問~-->
          <!--                    <br>6/18~6/24讚助-預定6/25出貨-->
          <!--                    <br>6/25~7/1讚助-預定7/2出貨-->
          <!--                    <br>7/2~7/8讚助-預定7/9出貨</p></div>-->
          <!--                </div>-->
          <!--                <div class="mt-4 pl-4" data-created-at="1721034883" id="comment-573036">-->
          <!--                  <div class="float-right">-->
          <!--                  <span class="text-xs text-neutral-600 ml-4" title="2024/07/15  17:14">-->
          <!--                  大約 2 個月前-->
          <!--                  </span>-->
          <!--                  </div>-->
          <!--                  <a class="text-neutral-600 mr-2 font-bold" href="/users/qbeeqdeejason-81e2">Jason Qbeeqdee</a>-->
          <!--                  <span-->
          <!--                    class="text-zec-blue p-1 whitespace-nowrap leading-none rounded-sm text-xs mr-1 border border-current font-bold">贊助人</span>-->
          <!--                  <div class="mt-1 flex flex-col space-y-2 py-2"><p>七月13讚助幾號出</p></div>-->
          <!--                </div>-->
          <!--                <div class="mt-4 pl-4" data-created-at="1721037791" id="comment-573065">-->
          <!--                  <div class="float-right">-->
          <!--                    <span class="text-xs text-neutral-600 ml-4" title="2024/07/15  18:03">-->
          <!--                    大約 2 個月前-->
          <!--                    </span>-->
          <!--                  </div>-->
          <!--                  <a class=" mr-2 font-bold" href="/users/tw-irisohyama">可易家電</a>-->
          <!--                  <span-->
          <!--                    class="bg-zec-green text-white border-zec-green p-1 whitespace-nowrap leading-none rounded-sm text-xs mr-1 border border-current font-bold">提案人</span>-->
          <!--                  <div class="mt-1 flex flex-col space-y-2 py-2"><p>您好，出貨時間參考如下，謝謝詢問~-->
          <!--                    <br>7/9~7/15讚助-預定7/16出貨</p></div>-->
          <!--                </div>-->
          <!--              </div>-->
          <!--            </div>-->
          <!--          </div>-->
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped lang="scss">

</style>
