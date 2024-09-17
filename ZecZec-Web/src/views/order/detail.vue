<script setup lang="ts">

import {useRoute} from "vue-router";
import useLoading from "@/hooks/loading";
import {onMounted, ref} from "vue";
import get from "lodash/get";
import {InfoFilled, Message, Check, CircleCheckFilled, Calendar} from "@element-plus/icons-vue";
import {getOrderDetail} from "@/api/order";

const route = useRoute()
const {loading, setLoading} = useLoading(false)
const orderId = ref(null)
const orderDetail = ref({})

const fetchDetail = async (orderId) => {
  if (!orderId) return
  setLoading(true)
  try {
    const {data} = await getOrderDetail(orderId)

    if (data && data.order) {
      // 獲取屬性值，作為套餐名稱
      orderDetail.value = data.order
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
    orderId.value = id
    fetchDetail(id)
  }
})
</script>

<template>
  <div class="container px-4 lg:px-0 my-8">
    <div class="rounded bg-yellow-100 p-4 flex flex-wrap items-start mb-6 shadow text-yellow-800">
      <el-icon class="mr-2 text-xl align-middle flex-initial" size="14">
        <InfoFilled/>
      </el-icon>
      <p class="flex-1">
        訂單已不可更動，需要修改贊助資料請
        <router-link class="inline-block font-bold underline text-yellow-800"
           :to="`/message/new?product_id=${orderDetail.product_id}`">聯絡提案團隊</router-link>。
      </p>
    </div>
    <h2 class="text-xl font-bold">
      <a class="text-gray-600" href="/account">列表 \</a>
      贊助細節
    </h2>
  </div>

  <div class="container px-4 lg:px-0 mt-8 mb-16 cf">
    <div class="lg:float-left w-full lg:w-3/10">
      <div class="p-4 border-2 border-inherit rounded text-neutral-200 mb-8 block  opacity-60">
        <img width="600"
             height="200"
             class="lazy placeholder-3:1 w-full h-auto mb-2 round-s entered loaded"
             :data-src="get(orderDetail, 'product[0].image.file_path')"
             :src="get(orderDetail, 'product[0].image.file_path')"
             data-ll-status="loaded">
        <div class="text-gray-600 font-bold mt-4 mb-2">{{get(orderDetail, 'product[0].product_name')}}</div>
        <div class="text-black font-bold text-xl flex items-center">
          NT$ {{get(orderDetail, 'product[0].product_price')}}
        </div>
        <div class="text-xs my-2">
          <span class="text-xs text-white px-2 py-1 bg-zec-red font-bold inline-block">
          SOLD OUT
          </span>
        </div>
        <div class="maxh5 maxh-none-ns overflow-y-auto break-all">
          <div class="text-black text-sm mv-child-0 flex flex-col space-y-4 leading-relaxed">
            <div v-html="get(orderDetail, 'product[0].detail')"></div>
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
      </div>
      <router-link class="inline-block mb-2 text-zec-blue border-2 border-current rounded px-4 py-1 font-bold"
         :to="`/message/new?product_id=${get(orderDetail, 'product_id')}`">
        <el-icon class="material-icons align-middle text-sm" size="14">
          <Message/>
        </el-icon>
        連絡提案人
      </router-link>
      <br>
    </div>
    <div class="lg:float-left w-full lg:pl-8 lg:w-7/10">
      <form class="p-4 rounded border-2 border-gray-200" id="edit_order_6281187" action="" accept-charset="UTF-8" method="post">
        <div class="font-bold mb-2">贊助計畫</div>
        <div class="cf mb-4">
          <a class="font-bold text-black text-base hover:text-zec-blue" href="/projects/in-z">
            {{get(orderDetail, 'product[0].product_name')}}
          </a>
        </div>
        <label class="font-bold mb-2 mt-4" for="order_sum">總金額</label>
        NT$ {{ get(orderDetail, 'pay_price') }}
        <div class="ml-2 inline-block">
          <justify-center
            class="text-sm px-2 py-1 border border-solid rounded-full bg-gray-50 text-gray-500 border-gray-500">
            {{ get(orderDetail, 'pay_status.text')}}
          </justify-center>
        </div>
        <div class="js-details reset-details">
          <button class="bg-zinc-100 px-1 text-xs button-text js-summary" type="button">細節</button>
          <div class="hidden p-4 text-sm bg-zinc-100 rounded mt-2">
            <div class="flex items-start text-sm py-2 mt-4">
              <div class="whitespace-nowrap font-bold flex-auto">選項金額</div>
              <div class="whitespace-nowrap text-right">
                NT$ {{ get(orderDetail, 'order_price') }}
              </div>
            </div>
            <div class="flex items-start text-sm py-2 border-t-1 border-gray-300">
              <div class="whitespace-nowrap font-bold flex-auto">
                運費 ：{{ get(orderDetail, 'address.region.province') }}
              </div>
              <div class="whitespace-nowrap text-right">
                + NT$ {{ get(orderDetail, 'express_price') }}
              </div>
            </div>
            <div class="flex items-start text-xl pb-2 pt-4 border-t-4 border-gray-300">
              <div class="whitespace-nowrap font-bold flex-auto">總價</div>
              <div class="whitespace-nowrap text-right">
                NT$ {{ get(orderDetail, 'order_price') }}
              </div>
            </div>
          </div>
        </div>
        <!-- 回饋調查 start -->
        <div class="my-4 hidden">
          <div class="p-4 rounded mb-4 bg-zinc-100">
            <h3 class="border-b border-gray-200 pb-1 text-base mb-4 font-bold">
              回饋調查
            </h3>
            <label class="font-bold mt-4">
              請選擇「杯身」顏色 (集資期間可登入嘖嘖帳號，至「贊助紀錄」更改)
            </label>
            <label class="font-bold mt-4">
              如有加購「肩揹帶」，請於此處選擇顏色
            </label>
            <label class="font-bold mt-4">
              如有加購「杯套」，請於此處選擇顏色
            </label>
            <label class="font-bold mt-4">
              如有加購「小包」，請於此處選擇顏色
            </label>
            <label class="font-bold mt-4">
              如有再加購「超早鳥獨享大杯」，請於此處選擇「杯身」顏色
            </label>
            <label class="font-bold mt-4">
              若有合併訂單，請於此欄註記
            </label>
            <label class="font-bold mt-4">
              若配送有特殊要求，請於此欄註記 (ex. 管理員代收)
            </label>
            <label class="font-bold mt-4">
              如有加購「杯袋」，請於此處選擇顏色
            </label>
          </div>
        </div>
        <!-- 回饋調查 end -->
        <div class="my-4 p-4 rounded bg-zinc-100 hidden" data-controller="invoice">
          <h3 class="border-b border-gray-200 pb-1 text-base mb-4 font-bold">發票資訊</h3>
          <label class="inline-block mr-4">
            <input data-invoice-target="personalSelector" data-action="invoice#selectPersonal" disabled="disabled"
                   type="radio" value="personal" checked="checked" name="order[invoice_type]"
                   id="order_invoice_type_personal">
            <span class="font-bold">個人發票</span>
          </label>
          <label class="inline-block">
            <input data-invoice-target="commercialSelector" data-action="invoice#selectCommercial" disabled="disabled"
                   type="radio" value="commercial" name="order[invoice_type]" id="order_invoice_type_commercial">
            <span class="font-bold">公司發票</span>
          </label>
          <fieldset class="" data-invoice-target="personalBlock">
            <label class="font-bold mb-1 mt-4" for="order_invoice_carrier">手機載具</label>
            <input pattern="\/[0-9A-Z.+-]{7}" placeholder="例：/AB1CD23"
                   class="w-64 rounded border border-gray-300 focus:outline-none focus:ring-1 focus:ring-zec-blue focus:border-zec-blue"
                   data-invoice-target="carrier" type="text" value="" name="order[invoice_carrier]"
                   id="order_invoice_carrier">
          </fieldset>
          <div class="flex hidden" data-invoice-target="commercialBlock">
            <fieldset class="mr-4">
              <label class="font-bold mb-1 mt-4" for="order_invoice_ubn">發票統編</label>
              <input pattern="[0-9]{8}" placeholder="例：12345678"
                     class="w-64 rounded border border-gray-300 focus:outline-none focus:ring-1 focus:ring-zec-blue focus:border-zec-blue"
                     data-invoice-target="ubn" required="required" disabled="" type="text" name="order[invoice_ubn]"
                     id="order_invoice_ubn">
            </fieldset>
            <fieldset>
              <label class="font-bold mb-1 mt-4" for="order_invoice_heading">發票抬頭</label>
              <input placeholder="組織名稱"
                     class="w-64 rounded border border-gray-300 focus:outline-none focus:ring-1 focus:ring-zec-blue focus:border-zec-blue"
                     data-invoice-target="heading" disabled="" type="text" name="order[invoice_heading]"
                     id="order_invoice_heading">
            </fieldset>
          </div>
        </div>
        <label class="font-bold mb-2 mt-4" for="order_recipient">收件人</label>
        {{ get(orderDetail, 'address.name') }}
        <label class="font-bold mb-2 mt-4" for="order_phone">聯絡電話</label>
        {{ get(orderDetail, 'address.phone') }}
<!--        <label class="font-bold mb-2 mt-4" for="order_postcode">郵遞區號</label>-->
        <label class="font-bold mb-2 mt-4" for="order_address">地址</label>
        {{ get(orderDetail, 'address.region.province') }} - {{ get(orderDetail, 'address.region.city') }} - {{ get(orderDetail, 'address.region.region') }}
        <label class="mt-4 font-bold" for="order_country">收件地點</label>
        <p>{{ get(orderDetail, 'address.detail') }}</p>
<!--        <label class="font-bold mb-2 mt-4" for="order_ship_to_store">超商取貨（選填）</label>-->
        <div class="mb-4">
        </div>
      </form>
    </div>
  </div>
</template>

<style scoped lang="scss">

</style>
