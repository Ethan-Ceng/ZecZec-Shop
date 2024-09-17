<script setup lang="ts">
import {computed, onMounted, ref} from "vue";
import {useRoute} from "vue-router";
import useLoading from "@/hooks/loading";
import FacebookIcon from '@/assets/image/icon/facebook_rounded.png'
import TwitterIcon from '@/assets/image/icon/twitter_rounded.png'
import LineIcon from '@/assets/image/icon/line_rounded.png'
import CopyIcon from '@/assets/image/icon/copy_rounded.png'
import {getProductDetail, likeProduct} from "@/api/product";
import get from "lodash/get";
import isArray from "lodash/isArray";
import {ElMessage} from "element-plus";
import {Calendar, CircleCheckFilled, VideoPlay} from "@element-plus/icons-vue";


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

const bgColor = computed(() => {
  return {
    'text-zec-green': productDetail.value.type === 1,
    'text-zec-blue': productDetail.value.type === 2,
    'text-zec-purple': productDetail.value.type === 3
  };
})

const borderColor = computed(() => {
  return {
    'bg-zec-green border-zec-green': productDetail.value.type === 1,
    'bg-zec-blue border-zec-blue': productDetail.value.type === 2,
    'bg-zec-purple border-zec-purple': productDetail.value.type === 3
  };
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

    console.log(tableData.value)
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

onMounted(() => {
  const id = get(route, 'params.id')
  if (id) {
    productId.value = id
    fetchDetail(id)
  }
})

</script>

<template>
  <div class="container lg:my-8" data-live-update="/metrics/projects/in-z/ping">
    <div class="lg:-mx-4 flex">
      <div class="w-full lg:px-4 lg:w-7/10">
        <div class="relative right-0 w-full bottom-0 max-w-5xl">
          <!-- 產品是影片 :style="`background-image: url('${get(productDetail, 'product_image', '')}')`"-->
          <div v-if="productDetail.video_id" class="overflow-hidden bg-zinc-100 lg:rounded">
            <!--            <div class="absolute inset-0 js-video" id="main-project-video" style="opacity: 0;"></div>-->
            <video width="640%" height="360%" controls :poster="`${get(productDetail, 'product_image', '')}`">
              <source :src="`${get(productDetail, 'video.file_path', '')}`" type="video/mp4">
              <source :src="`${get(productDetail, 'video.file_path', '')}`" type="video/webm">
              <source :src="`${get(productDetail, 'video.file_path', '')}`" type="video/ogg">
              Your browser does not support the video tag.
            </video>

            <!--            <button class="absolute absolute-center cursor-pointer js-play-video rounded-full p-2 w-16 h-16 text-white flex items-center justify-center hover:bg-black bg-black/70">-->
            <!--              <el-icon size="32"><VideoPlay /></el-icon>-->
            <!--            </button>-->
          </div>

          <div v-else
               class="overflow-hidden aspect-ratio-project-cover bg-zinc-100 lg:rounded"
               :style="`background-image: url('${get(productDetail, 'product_image', '')}')`">
          </div>
        </div>
      </div>
      <div class="w-3/10 px-4 flex flex-col justify-center">
        <div class="lg:mt-0 mt-4 text-xs text-white">
          <div class="text-xs text-gray-500 mb-2 tracking-wider">
            <span>臺灣</span>
            <div class="inline-block mx-1">\</div>
            <a class="text-gray-500 inline-block" href="/categories?type=0">{{ productDetail.type_text }}</a>
            <div class="inline-block mx-1">\</div>
            <a class="text-gray-500 inline-block" href="/categories?category=8">{{
                get(productDetail, 'category.name')
              }}</a>
          </div>
          <div class="text-sm text-gray-500">
            <!--            <span class="text-gray-500">提案人</span>-->
            <!--            <a class="font-bold text-zec-green" href="/users/143175?tab=projects">源源鋼藝 uanuan</a>-->
          </div>
          <a class="block" href="/projects/in-z">
            <h1 class="text-lg font-bold my-4 leading-relaxed tracking-wide">
              {{ get(productDetail, 'product_name') }}</h1>
          </a>
          <div class="my-4 relative flex items-center flex-nowrap">
            <svg class="progress mr-4 succeeded sprint">
              <circle class="progress-run js-percentage-circle" cx="32" cy="32" r="32"
                      style="stroke-dasharray: 202, 200;"></circle>
              <text class="stroke js-percentage-raised" text-anchor="middle" transform="rotate(90)" x="32" y="-27">
                1046%
              </text>
              <text class="js-percentage-raised" text-anchor="middle" transform="rotate(90)" x="32" y="-27">1046%</text>
            </svg>

            <div class="flex-auto">
              <span class="text-gray-500">目標 NT$ {{ get(productDetail, 'target_money', '') }}</span>
              <h2 class="text-2xl font-bold whitespace-nowrap leading-relaxed text-black">
                <span class="sr-only">累計集資金額</span>
              </h2>
              <h3 class="js-sum-raised text-2xl font-bold text-black">
                NT$ {{ get(productDetail, 'total_money', '') }}
              </h3>

              <ul class="flex">
                <li class="flex items-center mr-3 cursor-default has-tooltip">
                <span class="n-line-tooltip">
                  <p class="mb-1">贊助人數：{{ get(productDetail, 'product_sales', '') }}</p>
                  <p>不重複贊助人數：{{ get(productDetail, 'product_sales', '') }}</p>
                </span>
                  <h2 class="inline-block mr-1">
                    <svg xmlns="http://www.w3.org/2000/svg" height="48" viewBox="0 96 960 960" width="48"
                         class="w-6 h-6 text-gray-500 fill-current">
                      <path
                        d="M480 575q-66 0-108-42t-42-108q0-66 42-108t108-42q66 0 108 42t42 108q0 66-42 108t-108 42ZM160 896v-94q0-38 19-65t49-41q67-30 128.5-45T480 636q62 0 123 15.5T731 696q31 14 50 41t19 65v94H160Z"></path>
                    </svg>
                  </h2>
                  <h3 class="font-bold text-lg text-black flex items-center text-zec-green">
                    <span class="js-backers-count">5,356</span>
                    人
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 20 20" fill="none"
                         class="ml-1">
                      <circle cx="10" cy="10" r="8.75" stroke="#229f2a" stroke-width="1.5"></circle>
                      <path
                        d="M9.40341 11.8196V11.6989C9.40578 11.2846 9.44247 10.9543 9.51349 10.7081C9.58688 10.4619 9.69342 10.263 9.8331 10.1115C9.97277 9.95999 10.1409 9.82268 10.3374 9.69957C10.4841 9.60488 10.6155 9.50663 10.7315 9.40483C10.8475 9.30303 10.9399 9.19058 11.0085 9.06747C11.0772 8.942 11.1115 8.80232 11.1115 8.64844C11.1115 8.48509 11.0724 8.34186 10.9943 8.21875C10.9162 8.09564 10.8108 8.00095 10.6783 7.93466C10.5481 7.86837 10.4036 7.83523 10.245 7.83523C10.0911 7.83523 9.94555 7.86955 9.80824 7.93821C9.67093 8.0045 9.55848 8.10393 9.47088 8.23651C9.38329 8.36671 9.33594 8.52888 9.32884 8.72301H7.87997C7.89181 8.24953 8.00545 7.8589 8.22088 7.55114C8.43632 7.241 8.72159 7.01018 9.0767 6.85866C9.43182 6.70478 9.82363 6.62784 10.2521 6.62784C10.7232 6.62784 11.1399 6.70597 11.5021 6.86222C11.8643 7.0161 12.1484 7.23982 12.3544 7.53338C12.5604 7.82694 12.6634 8.18087 12.6634 8.59517C12.6634 8.87216 12.6172 9.11837 12.5249 9.33381C12.4349 9.54687 12.3082 9.73627 12.1449 9.90199C11.9815 10.0653 11.7886 10.2133 11.5661 10.3459C11.379 10.4571 11.2251 10.5732 11.1044 10.6939C10.986 10.8146 10.8973 10.9543 10.8381 11.1129C10.7813 11.2715 10.7517 11.4669 10.7493 11.6989V11.8196H9.40341ZM10.1065 14.0923C9.86979 14.0923 9.66738 14.0095 9.49929 13.8438C9.33357 13.6757 9.25189 13.4744 9.25426 13.2401C9.25189 13.008 9.33357 12.8092 9.49929 12.6435C9.66738 12.4777 9.86979 12.3949 10.1065 12.3949C10.3314 12.3949 10.5291 12.4777 10.6996 12.6435C10.87 12.8092 10.9564 13.008 10.9588 13.2401C10.9564 13.3963 10.915 13.5395 10.8345 13.6697C10.7564 13.7976 10.6534 13.9006 10.5256 13.9787C10.3977 14.0545 10.258 14.0923 10.1065 14.0923Z"
                        fill="#229f2a"></path>
                    </svg>
                  </h3>
                </li>
                <li aria-label="剩餘時間" class="flex items-center tooltip tooltip-b cursor-default">
                  <h2 class="inline-block mr-1">
                    <span class="sr-only">剩餘時間</span>
                    <svg xmlns="http://www.w3.org/2000/svg" height="48" viewBox="0 96 960 960" width="48"
                         class="w-5 h-5 text-gray-500 fill-current">
                      <path
                        d="M316 916h328V789q0-70-47.5-120.5T480 618q-69 0-116.5 50.5T316 789v127Zm-156 60v-60h96V789q0-70 36.5-128.5T394 576q-65-26-101.5-85T256 362V236h-96v-60h640v60h-96v126q0 70-36.5 129T566 576q65 26 101.5 84.5T704 789v127h96v60H160Z"></path>
                    </svg>
                  </h2>
                  <h3 class="font-bold text-black text-lg js-time-left text-zec-green">25 天</h3>
                </li>
              </ul>
            </div>
          </div>
          <p class="text-sm text-gray-500 my-4 leading-relaxed tracking-wider">
            {{ get(productDetail, 'selling_point') }}</p>
        </div>
        <div class="text-xs leading-relaxed text-gray-500 border-t pt-4">
          <h2 class="mr-1 inline-block text-gray-500 text-xs">募資期間</h2>
          <h3 class="inline-block text-gray-500 text-xs">
            {{ get(productDetail, 'active_time[0]') }} – {{ get(productDetail, 'active_time[1]') }}
          </h3>
        </div>
        <div class="mt-4 flex items-center">
          <router-link class="inline-block text-xs text-gray-500 font-bold p-2 mr-4 border border-gray-300 rounded"
             target="_blank" data-click-event="contact_creator"
             :to="`/message/new?product_id=${get(productDetail, 'product_id')}`"
          >
            <svg xmlns="http://www.w3.org/2000/svg" height="48" viewBox="0 96 960 960" width="48"
                 class="inline-block w-4 h-4 text-gray-500 fill-current">
              <path
                d="M240 657h313v-60H240v60Zm0-130h480v-60H240v60Zm0-130h480v-60H240v60ZM80 976V236q0-23 18-41.5t42-18.5h680q23 0 41.5 18.5T880 236v520q0 23-18.5 41.5T820 816H240L80 976Zm60-145 75-75h605V236H140v595Zm0-595v595-595Z"></path>
            </svg>
            傳送訊息
          </router-link>
          <a class="inline-block mr-4 font-bold" target="_blank" data-share-method="Facebook"
             data-share-id="d186753078820"
             href="https://www.facebook.com/sharer/sharer.php?u=https://www.zeczec.com/projects/in-z?r=d186753078820"><img
            width="32" height="32" class="inline-block"
            :src="FacebookIcon">
          </a>
          <a class="inline-block mr-4 font-bold" target="_blank" data-share-method="Twitter"
             data-share-id="d186753078820"
             href="https://www.twitter.com/share?text=嘖嘖 x 瞬間解鎖🔓暢飲無憂 — iN Z 防爆飲料杯&amp;url=https://www.zeczec.com/projects/in-z?r=d186753078820"><img
            width="32" height="32" class="inline-block"
            :src="TwitterIcon">
          </a>
          <a class="inline-block mr-4 font-bold" target="_blank" data-share-method="Line"
             data-share-id="d186753078820"
             href="https://social-plugins.line.me/lineit/share?url=https://www.zeczec.com/projects/in-z?r=d186753078820&amp;text=嘖嘖 x 瞬間解鎖🔓暢飲無憂 — iN Z 防爆飲料杯"><img
            width="32" height="32" class="inline-block"
            :src="LineIcon">
          </a>

          <button aria-label="複製網址" class="pointer tooltip tooltip-b" data-copy="#share"
                  data-share-id="d186753078820" data-share-method="Copy" type="button">
            <img width="32" height="32" class="inline-block"
                 :src="CopyIcon">
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- 產品資訊 -->
  <div class="px-4 lg:px-0 lg:mt-0 mt-6 top-0 bg-gray-50 sticky">
    <div class="container cf text-sm relative">
      <!--      <div class="hidden lg:block w-full lg:pr-2 absolute lg:w-7/10" style="top: 100%; margin-top: 1px;">-->
      <!--        <div-->
      <!--          class="border-l border-b border-r border-neutral-200 bg-white rounded-t-none rounded overflow-hidden text-sm">-->
      <!--          <a class="py-2 px-4 mr-4 inline-block text-neutral-600 hover:border-neutral-600 border-b border-transparent"-->
      <!--             href="#project_content">-->
      <!--            <i class="material-icons text-sm align-middle">feed</i>-->
      <!--          </a>-->
      <!--          <a class="py-2 mr-4 inline-block text-neutral-600 hover:border-neutral-600 border-b border-transparent"-->
      <!--             href="#project_risk">風險與挑戰</a>-->
      <!--          <a class="py-2 mr-4 inline-block text-neutral-600 hover:border-neutral-600 border-b border-transparent"-->
      <!--             href="#project_return">退換貨規則</a>-->
      <!--          <a class="py-2 mr-4 inline-block text-neutral-600 hover:border-neutral-600 border-b border-transparent"-->
      <!--             href="#project_contact">客服聯絡方式</a>-->
      <!--          <a class="py-2 mr-4 inline-block text-neutral-600 hover:border-neutral-600 border-b border-transparent"-->
      <!--             href="#project_business_profile">登記資訊</a>-->
      <!--        </div>-->
      <!--      </div>-->
      <div class="-mx-4 flex">
        <div
          class="flex-1 px-4 overflow-auto whitespace-nowrap flex flex-nowrap items-center space-x-8 my-1 tracking-widest lg:w-7/10">

          <router-link
            class="inline-block py-2 hover:border-zec-green lg:border-b-2 text-black border-transparent border-zec-green font-bold"
            :to="`/project/${productDetail.product_id}`">專案內容
          </router-link>
          <router-link class="inline-block py-2 hover:border-zec-green lg:border-b-2 text-black border-transparent"
                       :to="`/project/${productDetail.product_id}/faqs`">常見問答
            <span class="text-xs font-bold ml-1 text-neutral-600">{{faqNum}}</span>
          </router-link>
          <router-link class="inline-block py-2 hover:border-zec-green lg:border-b-2 text-black border-transparent"
                       :to="`/project/${productDetail.product_id}/comments`">留言
            <span class="text-xs font-bold ml-1 text-neutral-600">{{ get(productDetail, 'comment_data_count') }}</span>
          </router-link>
        </div>
        <div class="px-4 py-3 text-center flex lg:static bg-gray-50 bottom-0 z-50 lg:w-3/10 fixed">
          <div
            class="p-2 inline-block flex-initial mr-2 transition-transform hover:scale-105 focus:scale-105 active:scale-90 border-2 border-current rounded tooltip tooltip-l"
            :class="bgColor"
            data-click-event="follow_project"
            aria-label="追蹤後會收到公開的專案更新和計畫結束提醒。"
            @click="fetchLike(productDetail.product_id)"
          >
            <svg v-if="isLike" xmlns="http://www.w3.org/2000/svg" height="48" viewBox="0 96 960 960" width="48"
                 class="w-6 h-6 fill-current">
              <path
                d="m479.761 937.631-43.065-39.087q-105.484-96.79-174.495-167.289-69.012-70.499-110-125.822-40.987-55.323-57.203-100.493-16.216-45.17-16.216-91.44 0-91.135 61.172-152.426 61.172-61.292 151.307-61.292 56.048 0 104.285 26.283 48.237 26.283 84.215 76.826 42.478-53.043 88.424-78.076 45.947-25.033 100.205-25.033 90.25 0 151.539 61.237 61.289 61.236 61.289 152.289 0 46.447-16.216 91.505-16.216 45.058-57.204 100.381-40.987 55.323-110.118 125.941-69.131 70.619-174.615 167.409l-43.304 39.087Z"></path>
            </svg>

            <svg v-else xmlns="http://www.w3.org/2000/svg" height="48" viewBox="0 96 960 960" width="48"
                 class="w-6 h-6 fill-current">
              <path
                d="m479.435 948.718-48.609-43.978q-106.231-97.889-175.847-168.98-69.616-71.091-110.93-127.066-41.314-55.975-57.53-101.96-16.215-45.986-16.215-93.289 0-94.915 63.544-158.528 63.544-63.613 157.087-63.613 55.885 0 103.877 25.304t84.623 74.543q43.13-51.739 88.77-75.793 45.639-24.054 99.859-24.054 94.379 0 158.006 63.574 63.626 63.574 63.626 158.43 0 47.409-16.215 93.127-16.216 45.718-57.53 101.694-41.314 55.975-111.138 127.412-69.823 71.437-176.204 169.199l-49.174 43.978Zm-.283-100.936q100.045-92.612 164.566-157.708t102.206-113.998q37.685-48.902 52.369-87.12 14.685-38.218 14.685-75.34 0-63.355-40.649-104.475-40.649-41.119-103.649-41.119-50.349 0-92.851 31.783-42.503 31.782-70.503 88.717h-52.217q-26.859-56.5-70.188-88.5t-92.204-32q-62.394 0-102.762 40.599t-40.368 105.353q0 38.151 15.184 76.807 15.183 38.655 52.835 88.06 37.653 49.405 101.556 113.989 63.903 64.583 161.99 154.952Zm1.413-290.412Z"></path>
            </svg>
          </div>
          <router-link
            class="js-back-project-now tracking-widest flex-1 py-2 text-white inline-block w-full text-base transition-transform hover:scale-105 focus:scale-105 active:scale-90 rounded font-bold py-1"
            :class="borderColor"
            data-click-event="list_options"
            :to="`/order/${productDetail.product_id}`">
            贊助專案
          </router-link>
        </div>

      </div>
    </div>
  </div>

  <!-- 產品詳情和套餐 -->
  <div class="container my-8">
    <div class="lg:-mx-4 flex">
      <div class="w-full px-4 mb-8 lg:w-7/10">
        <div class="mt-8 hidden lg:block"></div>
        <div class="mv-child-0 nested-media leading-relaxed overflow-hidden xs:overflow-visible" id="project_content">
          <div
            class="js-expand-project-content maxh7 mb-4 overflow-hidden relative maxh-none-ns mv-child-0 xs:overflow-visible">
            <div class="product-content" v-html="productDetail.content"></div>
          </div>
          <button
            class="mb-8 button text-neutral-600 w-full js-expand-project xs:hidden flex justify-between items-center"
            data-click-event="expand_project_details" style="box-shadow: 0 0 100px 100px white;" type="button">
            <i class="material-icons text-base leading-none">keyboard_arrow_down</i>
            展開計畫內容
            <i class="material-icons text-base leading-none">keyboard_arrow_down</i>
          </button>
        </div>
      </div>
      <!-- 套餐資訊  -->
      <div class="lg:px-4 px-0 xs:flex self-start flex-wrap w-full lg:w-3/10">

        <div v-for="productSku in productDetail.sku" :key="productSku.product_sku_id"
             class="lg:w-full px-4 lg:px-0 flex-none self-start xs:w-1/2"
             data-item-brand="in-z"
             data-item-id="135367"
             data-item-name="135367-in-z"
             data-item-value="890"
             data-visibility="view_item"
             data-gtm-vis-first-on-screen62771094_52="161"
             data-gtm-vis-total-visible-time62771094_52="3000"
             data-gtm-vis-has-fired62771094_52="1">
          <router-link
            class="p-4 border-2 border-inherit rounded text-neutral-200 mb-8 block border-rainbow "
            :to="{path: `/order/${productSku.product_id}/${productSku.product_sku_id}`}">
            <img width="600" height="200"
                 class="lazy placeholder-3:1 w-full h-auto mb-2 round-s entered loaded"
                 data-ll-status="loaded"
                 :data-src="get(productSku, 'image.file_path')"
                 :src="get(productSku, 'image.file_path')">
            <div class="text-gray-600 font-bold mt-4 mb-2">{{ get(productSku, 'spec_item.spec_value') }}</div>
            <div class="text-black font-bold text-xl flex items-center">
              NT$ {{ get(productSku, 'product_price') }}
              <span
                class="inline-block text-xs font-bold text-black bg-yellow-300 leading-relaxed px-2 ml-2 rounded-sm">69 折</span>
              <p class="w-full text-gray-500 font-normal text-xs">
                預定售價
                <span class="line-through">NT$ {{ get(productSku, 'line_price') }}</span>
                ，現省 NT$ {{ get(productSku, 'line_price') - get(productSku, 'product_price') }}
              </p>
            </div>
            <div class="text-xs my-2">
              <span class="text-xs text-white px-2 py-1 bg-zec-red font-bold inline-block">
              剩餘 {{ get(productSku, 'stock_num') }} 份
              </span>
              <span class="text-black px-2 py-1 bg-zinc-100 inline-block">
                已被贊助<span
                class="font-bold">{{
                  get(productSku, 'product_sales')
                }}</span>/ {{ get(productSku, 'stock_num') + get(productSku, 'product_sales') }} 次
              </span>
            </div>
            <div class="maxh5 maxh-none-ns overflow-y-auto break-all">
              <div class="text-black text-sm mv-child-0 flex flex-col space-y-4 leading-relaxed">
                <div v-html="get(productSku, 'detail')"></div>
              </div>
            </div>
            <ul class="mt-4 pt-4 border-t text-gray-800 text-xs flex">
              <li class="mr-2 -indent-4 pl-4 leading-relaxed">
                <el-icon size="14" color="#229f2a">
                  <CircleCheckFilled/>
                </el-icon>
                可選擇加購商品
              </li>
              <li class="mr-2 -indent-4 pl-4 leading-relaxed">
                <el-icon size="14" color="#229f2a">
                  <CircleCheckFilled/>
                </el-icon>
                可選 7-11 取貨
              </li>
              <li class="mr-2 -indent-4 pl-4 leading-relaxed">
                <el-icon size="14" color="#229f2a">
                  <CircleCheckFilled/>
                </el-icon>
                臺灣本島、離島免運
              </li>
              <li class="mr-2 -indent-4 pl-4 leading-relaxed">
                <el-icon size="14" color="#229f2a">
                  <CircleCheckFilled/>
                </el-icon>
                可寄送至 香港、澳門、馬來西亞 等地區
              </li>
            </ul>
            <div class="text-center text-xs text-gray-600 pt-4 mt-4 border-t">
              <el-icon size="14">
                <Calendar/>
              </el-icon>
              預計於 2025 年二月實現
            </div>
          </router-link>
        </div>

      </div>
    </div>
  </div>

</template>

<style scoped lang="scss">

</style>
