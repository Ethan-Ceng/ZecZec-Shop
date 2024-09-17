<script setup lang="ts">

import {onMounted, ref} from "vue";
import {useRoute} from "vue-router";
import useLoading from "@/hooks/loading";
import {getProductDetail} from "@/api/product";
import get from "lodash/get";
import {ArrowLeftBold, ArrowRightBold, CircleCheckFilled, Calendar} from '@element-plus/icons-vue'

const route = useRoute()
const {loading, setLoading} = useLoading(false)
const productId = ref(null)
const productDetail = ref({})

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

onMounted(() => {
  const id = get(route, 'params.id')
  if (id) {
    productId.value = id
    fetchDetail(id)
  }
})
</script>

<template>
  <div class="border-t border-neutral-200 border-b" v-loading="loading">
    <div class="container cf">
      <div class="w-full flex">
        <div class="inline-block lg:float-left w-full lg:w-1/3">
          <div
            class="aspect-ratio-project-cover bg-zinc-100 lg:mr-6"
            :style="`background-image: url('${get(productDetail, 'product_image', '')}')`"
          >
          </div>
        </div>

        <div class="py-2 lg:px-2 px-4 flex-1">
          <a href="/projects/in-z">
            <h2 class="text-base mb-0 inline-block mt-1 text-base font-bold">
              {{ get(productDetail, 'product_name', '') }}</h2>
          </a>
          <div class="text-neutral-600 text-xs mb-4">
            <!--            <span>提案人</span>-->
            <!--            <a class="font-bold text-zec-green" href="/users/uanuan">源源鋼藝 uanuan</a>-->
            <!--            <span class="mx-2">|</span>-->
            {{ get(productDetail, 'type_text', '') }}
          </div>
          <span class="font-bold">NT$ {{get(productDetail, 'total_money', '')}}</span>
          <span class="text-xs text-neutral-600">/ 目標 NT$ {{get(productDetail, 'target_money', '')}}</span>
        </div>
      </div>
    </div>
  </div>

  <!-- 套餐選擇 -->
  <div class="container my-8" v-loading="loading">
    <div class="lg:-mx-4 flex">
      <div class="w-full px-4 mb-8">
        <div class="text-center text-xs rounded bg-zinc-100 p-2 font-bold tracking-widest hidden xs:block">
          <el-icon>
            <ArrowLeftBold/>
          </el-icon>
          左右捲動看看更多選項
          <el-icon>
            <ArrowRightBold/>
          </el-icon>
        </div>

        <div
          class="flex xs:flex-wrap flex-wrap-reverse xs:flex-nowrap xs:whitespace-nowrap xs:w-auto overflow-x-auto scrollbar-top">

          <div v-for="productSku in productDetail.sku" :key="productSku.product_sku_id"
               class="w-full xs:flex-none whitespace-normal xs:mr-4 xs:pt-4 xs:w-1/2 lg:w-3/10">
            <router-link class="p-4 border-2 border-inherit rounded text-neutral-200 mb-8 block border-rainbow "
                         :to="{path: `/order/${productSku.product_id}/${productSku.product_sku_id}`}">
              <img width="600" height="200"
                   class="lazy placeholder-3:1 w-full h-auto mb-2 round-s entered loaded"
                   data-ll-status="loaded"
                   :data-src="get(productSku, 'image.file_path')"
                   :src="get(productSku, 'image.file_path')"
              >
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
              剩餘 {{ get(productSku, 'stock_num')}} 份
              </span>
                <span class="text-black px-2 py-1 bg-zinc-100 inline-block">已被贊助
                <span class="font-bold">{{ get(productSku, 'product_sales') }}</span>/ {{ get(productSku, 'stock_num') + get(productSku, 'product_sales') }} 次</span>
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
  </div>
</template>

<style scoped lang="scss">

</style>
