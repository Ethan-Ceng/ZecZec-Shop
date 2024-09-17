<script setup lang="ts">
import {computed, onMounted, reactive, ref, watch} from "vue";
import {useRoute, useRouter} from "vue-router";
import {useUserStore} from "@/store";
import {storeToRefs} from "pinia";
import useLoading from "@/hooks/loading";
import {getProductDetail} from "@/api/product";
import get from "lodash/get";
import {ArrowRightBold, Calendar, Check, CircleCheckFilled, InfoFilled} from "@element-plus/icons-vue";
import {getRegion} from "@/api/common";
import {ElMessage, FormInstance, FormRules} from "element-plus";
import {buyOrder, getBuyOrder, payOrder} from "@/api/order";
import {addAddress, editAddress} from "@/api/user";

const route = useRoute()
const router = useRouter()

const userStore = useUserStore()
const {userDetail} = storeToRefs(userStore);

const {loading, setLoading} = useLoading(false)
const productId = ref(null)
const productDetail = ref({})

const productSkuId = ref(null)
const productSku = ref({})
const specSkuIdList = ref([]) // 加購商品 spec_sku_id
const productRecommend = ref([])
const showProductRecommend = ref(true)


// 下單資料
interface RuleForm {
  payment_method: string
  extra_payment: number
  province_id: string
  city_id: string
  region_id: string
  detail: string
  postcode: string
  name: string
  phone: string
  remark: string
  invoice_type: string
  invoice_carrier: string
  invoice_heading: string
  consented: boolean
}

const formStateRef = ref<FormInstance>()

const formState = reactive<RuleForm>({
  payment_method: 'credit_card',
  extra_payment: 0,
  province_id: '', // 國家
  city_id: '', // 城市
  region_id: '', // 街道
  detail: '', // 詳細地址
  postcode: '',
  name: '', // 聯係人
  phone: '', // 手機號
  remark: '',
  invoice_type: 'personal', // 發票型別
  invoice_carrier: '', // 發票型別
  invoice_ubn: '', // 發票統編
  invoice_heading: '', // 發票統編
  consented: false, // 發票統編
})

const rules = reactive<FormRules<RuleForm>>({
  province_id: [{required: true, message: '請選擇', trigger: 'blur'},],
  city_id: [{required: true, message: '請選擇', trigger: 'blur'},],
  region_id: [{required: true, message: '請選擇', trigger: 'blur'},],
  name: [
    {
      required: true,
      message: '請再次確認是否與證件名稱相符，若不正確可能造成投遞失敗。',
      trigger: 'change',
    },
  ],
  phone: [
    {required: true, message: '請輸入手機號碼', trigger: 'blur'},
    {min: 8, max: 20, message: '請填寫真實手機號碼，至少 8 碼', trigger: 'blur'},
  ]
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
        sku: product_sku
      }
      productRecommend.value = product_sku.filter(item => item.type - 0 !== 0)
      productSku.value = product_sku.find(item => item.product_sku_id - 0 === productSkuId.value - 0)
      console.log(productDetail.value, productSku.value)
      // 查詢訂單資訊
      await getOrderInfo()
    }

    console.log(tableData.value)
  } catch (err) {
    // console.warn(err)
  } finally {
    setLoading(false)
  }
}

const regionList = ref([]) // 行政地區
const provinceList = ref([]) // 國家

const fetchRegion = async () => {
  try {
    const {data} = await getRegion();

    if (data && data.regionData) {
      regionList.value = data.regionData || []
      provinceList.value = data.regionData[0] || []
      // 設定預設地址
      if (userDetail.value.addressDefault) {
        formState.province_id = get(userDetail.value, 'addressDefault.province_id')
        formState.city_id = get(userDetail.value, 'addressDefault.city_id')
        formState.region_id = get(userDetail.value, 'addressDefault.region_id')
        formState.detail = get(userDetail.value, 'addressDefault.detail')
        formState.name = get(userDetail.value, 'addressDefault.name')
        formState.phone = get(userDetail.value, 'addressDefault.phone')
      }
    }
  } catch (err) {
    // console.warn(err)
  }
}

const cityList = computed(() => {
  const provinceIndex = provinceList.value.findIndex(item => item.value === formState.province_id)
  if (provinceIndex !== -1) {
    return get(regionList.value, `[1][${provinceIndex}]`) || []
  }
  return []
})

const districtList = computed(() => {
  const provinceIndex = provinceList.value.findIndex(item => item.value === formState.province_id)
  if (provinceIndex !== -1) {
    const cityArray = get(regionList.value, `[1][${provinceIndex}]`) || []

    const cityIndex = cityArray.findIndex(item => item.value === formState.city_id)
    if (cityIndex !== -1) {
      const reginArray = get(regionList.value, `[2][${provinceIndex}][${cityIndex}]`) || []
      return reginArray
    }
  }
  return []
})


onMounted(() => {
  const id = get(route, 'params.id')
  const skuId = get(route, 'params.skuId')
  console.log({id, skuId, route})
  if (id) {
    productId.value = id - 0
    productSkuId.value = skuId - 0
    fetchDetail(id)
  }

  fetchRegion()
})


// 儲存預設地址
const handleDelivery = async (params) => {
  try {
    if (params.address_id) {
      await editAddress(params)
    } else {
      await addAddress(params);
    }
    // 更新個人資訊
    await userStore.fetchUserInfo()
  } catch (err) {
    // console.warn(err)
  }
}

const orderInfo = ref({})
// 獲取訂單資訊
const getOrderInfo = async () => {
  setLoading(true)
  let orderPrice = productSku.value.product_price - 0

  let products = [
    {
      product_id: productId.value,
      product_sku_id: productSku.value.spec_sku_id,
      product_num: 1
    }
  ]
  if (specSkuIdList.value.length) {
    specSkuIdList.value.forEach(item => {
      products.push({
        product_id: productId.value,
        product_sku_id: item,
        product_num: 1
      })
      orderPrice = orderPrice + (item.product_price - 0)
    })
  }

  try {
    const {remark} = formState

    let params = {
      products,
      delivery: 0,
      coupon_id: 0,
      is_use_points: 1,
      store_id: 0,
      pay_source: 'h5',
      remark
    }
    const {code, data} = await getBuyOrder(params);
    if (code == 1 && data.orderInfo) {
      orderInfo.value = data.orderInfo
      console.log(orderInfo.value)
    } else {
      // 手動計算價格
      orderInfo.value = {
        order_price: orderPrice,
        express_price: 0,
        order_total_price: orderPrice,
      }

    }
  } catch (err) {
    // console.warn(err)
    // 手動計算價格
    orderInfo.value = {
      order_price: orderPrice,
      express_price: 0,
      order_total_price: orderPrice,
    }
  } finally {
    setLoading(false)
  }
}

const fetchOrder = async () => {
  setLoading(true)
  try {
    const {remark} = formState
    let products = [
      {
        product_id: productId.value,
        product_sku_id: productSku.value.spec_sku_id,
        product_num: 1
      }
    ]
    if (specSkuIdList.value.length) {
      specSkuIdList.value.forEach(item => {
        products.push({
          product_id: productId.value,
          product_sku_id: item,
          product_num: 1
        })
      })
    }
    let params = {
      products,
      delivery: 0,
      coupon_id: 0,
      is_use_points: 1,
      store_id: 0,
      pay_source: 'h5',
      remark
    }
    const {code, data: {order_id}} = await buyOrder(params);

    if (code === 1 && order_id) {
      ElMessage.success("訂購成功")
      // 支付訂單
      await goPayOrder(order_id)
    }
  } catch (err) {
    // console.warn(err)
  } finally {
    setLoading(false)
  }
}


const goPayOrder = async (order_id) => {
  setLoading(true);
  try {
    const params = {
      order_id,
      payType: 10,
      pay_source: "h5",
      use_balance: 1,
    }
    const {msg} = await payOrder(params);
    ElMessage.success(msg || "支付成功");
    await router.replace({path: `/order/detail/${order_id}`})
  } catch (e) {
    errorMessage.value = (err as Error).msg;
  } finally {
    setLoading(false);
  }
};

// 監聽頁面變化，重新整理訂單資訊
watch(
  [
    () => formState.province_id,
    () => formState.city_id,
    () => formState.region_id,
    () => formState.detail,
    () => formState.name,
    () => formState.phone,
  ],
  ([province_id, city_id, region_id, detail, name, phone]) => {
    if (province_id && city_id && region_id && detail && name && phone) {
      if (userDetail.value?.addressDefault?.address_id) {
        handleDelivery({
          address_id: userDetail.value?.addressDefault?.address_id,
          province_id, city_id, region_id, detail, name, phone,
          is_default: 1
        })
      } else {
        handleDelivery({
          province_id, city_id, region_id, detail, name, phone,
          is_default: 1
        })
      }
    }
  },
  {deep: true} // 啟用深層監聽
);

const handleSubmit = async (formEl: FormInstance | undefined) => {
  if (!formEl) return
  await formEl.validate((valid, fields) => {
    if (valid) {
      console.log('submit!')
      fetchOrder()
    } else {
      console.log('error submit!', fields)
    }
  })
}
</script>

<template>
  <div class="border-t border-neutral-200 border-b">
    <div class="container cf">
      <div class="w-full flex">
        <div class="inline-block lg:float-left w-full lg:w-1/3">
          <div class="aspect-ratio-project-cover bg-zinc-100 lg:mr-6"
               :style="`background-image: url('${get(productDetail, 'product_image', '')}')`">
          </div>
        </div>
        <div class="py-2 lg:px-2 px-4 flex-1">

          <a href="/projects/in-z">
            <h2 class="text-base mb-0 inline-block mt-1 text-base font-bold">{{
                get(productDetail, 'product_name', '')
              }}</h2>
          </a>
          <div class="text-neutral-600 text-xs mb-4">
            <!--            <span>提案人</span>-->
            <!--            <a class="font-bold text-zec-green" href="/users/uanuan">源源鋼藝 uanuan-->
            <!--            </a><span class="mx-2">|</span>-->
            {{ get(productDetail, 'type_text', '') }}
          </div>
          <span class="font-bold">NT$ {{ get(productDetail, 'total_money', '') }}</span>
          <span class="text-xs text-neutral-600">
            / 目標 NT$ {{ get(productDetail, 'total_money', '') }}
            </span>

        </div>
      </div>
    </div>
  </div>


  <!-- 下單詳情 -->
  <div class="container my-8">
    <div class="lg:-mx-4 flex">
      <div class="w-full px-4 mb-8">
        <el-form
          ref="formStateRef"
          size="large"
          :model="formState"
          :rules="rules"
          label-position="top"
          class="js-previewable-sum">
          <div class="flex mb-16 text-sm -mx-4">
            <div class="w-full px-4 lg:w-1/3">
              <router-link
                class="float-right m-4 rounded-full font-bold text-xs py-1 px-2 bg-neutral-200 text-center text-neutral-600 leading-none"
                :to="{path: `/order/${productSku.product_id}`}">更改回饋
              </router-link>
              <!-- sku資訊 -->
              <div class="p-4 border-2 border-inherit rounded text-neutral-200 mb-8 block  ">
                <img width="600"
                     height="200"
                     class="lazy placeholder-3:1 w-full h-auto mb-2 round-s entered loaded"
                     :data-src="get(productSku, 'image.file_path')"
                     :src="get(productSku, 'image.file_path')"
                     data-ll-status="loaded">
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
                  </span><span class="text-black px-2 py-1 bg-zinc-100 inline-block">
                  已被贊助
                  <span class="font-bold">{{ get(productSku, 'product_sales') }}</span>
                  / {{ get(productSku, 'stock_num') + get(productSku, 'product_sales') }} 次
                  </span></div>
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
              </div>
              <!-- 商品統計  -->
              <div class="flex items-center">
                <div class="whitespace-nowrap font-bold">
                  使用
                  <label class="mb-0 inline-block" for="order_coupon_code">折扣碼</label>
                </div>
                <div class="whitespace-nowrap text-right flex-1">
                  <input spellcheck="false"
                         class="text-right w-1/2 rounded border-2 boder-zec-blue text-zec-blue mb-0 focus:outline-none focus:ring-1 focus:ring-zec-blue focus:border-zec-blue js-coupon_code"
                         type="text" name="order[coupon_code]" id="order_coupon_code">
                </div>
              </div>
              <div class="block w-full border-0 js-preview-sum" id="preview">
                <div class="flex items-start text-sm py-2 mt-4">
                  <div class="whitespace-nowrap font-bold flex-auto">選項金額</div>
                  <div class="whitespace-nowrap text-right">
                    NT$ {{ get(orderInfo, 'order_price', 0) }}
                  </div>
                </div>
                <div class="flex items-start text-sm py-2 border-t-1 border-gray-300">
                  <div class="whitespace-nowrap font-bold flex-auto">
                    運費 ：臺灣（本島）
                  </div>
                  <div class="whitespace-nowrap text-right">
                    + NT$ {{ get(orderInfo, 'express_price', 0) }}
                  </div>
                </div>
<!--                <div class="flex items-start text-sm py-2 border-t-1 border-gray-300">-->
<!--                  <div class="whitespace-nowrap font-bold flex-auto">-->
<!--                    <i class="material-icons text-sm text-zec-red align-middle">favorite</i>-->
<!--                    加碼贊助-->
<!--                  </div>-->
<!--                  <div class="whitespace-nowrap text-right">-->
<!--                    +-->
<!--                    NT$ 1,000-->
<!--                  </div>-->
<!--                </div>-->
                <div class="flex items-start text-xl pb-2 pt-4 border-t-4 border-gray-300">
                  <div class="whitespace-nowrap font-bold flex-auto">總價</div>
                  <div class="whitespace-nowrap text-right">
                    NT$ {{ get(orderInfo, 'order_total_price', 0) }}
                  </div>
                </div>
              </div>
            </div>
            <div class="w-full px-4 lg:w-2/3">
              <div class="xs:hidden block pt-2" id="details"></div>
              <!-- 加購 -->


              <template v-if="productRecommend.length">
                <div v-show="showProductRecommend" class="js-option-details">
                  <div class="mb-8 border-b border-neutral-200">
                    <h3 class="font-bold mt-0 mb-4 text-sm">選擇加購商品</h3>
                    <el-checkbox-group v-model="specSkuIdList" @change="getOrderInfo">
                      <div class="flex -mx-4 flex-nowrap overflow-auto pl-4 flex-stretch">
                          <div v-for="productItem in productRecommend" :key="productItem.spec_sku_id"
                               class="shrink-0 w-64 pr-4">
                            <label class="bg-zinc-100 px-4 py-2 leading-none block mb-0">
                              <el-checkbox :value="productItem.spec_sku_id">選擇</el-checkbox>
                            </label>
                            <div class="p-4 border-2 border-inherit rounded text-neutral-200 mb-8 block  ">
                              <div class="text-gray-600 font-bold mt-4 mb-2">{{
                                  get(productItem, 'spec_item.spec_value')
                                }}
                              </div>
                              <div class="text-black font-bold text-xl flex items-center">
                                NT$ {{ get(productItem, 'product_price') }}
                                <span
                                  class="inline-block text-xs font-bold text-black bg-yellow-300 leading-relaxed px-2 ml-2 rounded-sm">69 折</span>
                                <p class="w-full text-gray-500 font-normal text-xs">
                                  預定售價
                                  <span class="line-through">NT$ {{ get(productItem, 'line_price') }}</span>
                                  ，現省 NT$ {{ get(productItem, 'line_price') - get(productItem, 'product_price') }}
                                </p>
                              </div>
                              <div class="text-xs my-2">
                          <span class="text-black px-2 py-1 bg-zinc-100 inline-block">
                            已被贊助
                            <span class="font-bold">{{ get(productItem, 'product_sales') }}</span>
                            次
                          </span>
                              </div>
                              <div class="maxh5 maxh-none-ns overflow-y-auto break-all">
                                <div class="text-black text-sm mv-child-0 flex flex-col space-y-4 leading-relaxed">
                                  <div v-html="get(productItem, 'detail')"></div>
                                </div>
                              </div>
                              <ul class="mt-4 pt-4 border-t text-gray-800 text-xs flex">
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
                          </div>
                      </div>
                    </el-checkbox-group>
                  </div>
                  <div class="js-variation-selection">
                    <include-fragment class="block mb-8 js-variations-fragment" data-src="/order_variations/135367">
                    </include-fragment>
                  </div>
                </div>
                <!-- 付款方式-->
                <button
                  v-show="showProductRecommend"
                  @click="showProductRecommend = !showProductRecommend"
                  class="button inline-block border-2 border-zec-green rounded p-4 w-full text-left text-zec-green hover:bg-zec-green hover:text-white font-bold js-show-payment-details"
                  type="button">
                  選擇付款方式
                  <el-icon class="align-middle text-sm ml-2">
                    <ArrowRightBold/>
                  </el-icon>
                </button>
                <button
                  v-show="!showProductRecommend"
                  @click="showProductRecommend = !showProductRecommend"
                  class="inline-block border-2 border-current rounded p-4 w-full text-left text-neutral-400 hover:text-neutral-600 font-bold js-show-option-details"
                  type="button">
                  <el-icon class="align-middle text-sm ml-2">
                    <Check/>
                  </el-icon>
                  顯示品項細節
                  <el-icon class="align-middle text-sm ml-2">
                    <ArrowRightBold/>
                  </el-icon>
                </button>
              </template>
              <div v-show="!showProductRecommend || productRecommend.length==0" class="mt-8 js-payment-step">
                <el-form-item label="付款方式" prop="payment_method">
                  <el-radio-group class="w-full" v-model="formState.payment_method">
                    <el-radio
                      value="credit_card"
                      class="w-full mb-2 rounded hover:bg-zinc-200 cursor-pointer bg-zinc-100 px-4 py-2 payment-method-option flex items-center min-h-14"
                      style="margin-right: 0"
                    >
                      <h3 class="ml-2 flex-1">
                        <span class="block font-bold">信用卡付款</span>
                        <p class="flex text-xs text-gray-800 flex mt-1">
                          <span>可用銀聯卡</span>、<span>可用國外卡</span>
                        </p>
                      </h3>
                    </el-radio>
                    <el-radio
                      value="atm"
                      class="w-full mb-2 rounded hover:bg-zinc-200 cursor-pointer bg-zinc-100 px-4 py-2 payment-method-option flex items-center min-h-14">
                      <h3 class="ml-2 flex-1">
                        <span class="block font-bold">ATM 轉帳或銀行臨櫃繳款</span>
                        <span class="mt-2 text-xs text-gray-800">需於指定時間內完成付款，超過時限則會取消交易</span>
                      </h3>
                    </el-radio>
                  </el-radio-group>
                </el-form-item>
                <!-- 付款說明 start -->
                <div v-show="formState.payment_method === 'credit_card'"
                     class="p-4 js-credit-card-warning border mt-2 rounded border-slate-200">
                  <ul class="list-disc m-0 pl-4 text-slate-600">
                    <li>您瞭解您的贊助是支援創意專案的一種方式，也瞭解創意實踐過程中充滿變數，專案不一定能確保回饋。</li>
                  </ul>
                </div>

                <div v-show="formState.payment_method === 'atm'"
                     class="p-4 js-atm-warning border mt-2 rounded border-slate-200">
                  <ul class="list-disc m-0 pl-4 text-slate-600">
                    <li>
                      您瞭解您的贊助是支援創意專案的一種方式，也瞭解創意實踐過程中充滿變數，專案不一定能確保回饋。
                    </li>
                    <li>
                      虛擬帳號轉帳因要等待銀行於工作天回傳，<b>限量的選項可能會在入帳前售罄並造成贊助失敗</b>。遇到例假日將會延後更新。若未來有遇到退款的情況，<b>退款金額會扣除轉帳交易所產生的手續費用</b>。臨櫃繳款的截止期限，必須在週一至週五（不含例假日）的下午三點半前完成，<b>週六的郵局轉帳將會造成交易失敗</b>。
                    </li>
                  </ul>
                </div>

                <div class="p-4 js-zero-card-warning border hidden mt-2 rounded border-slate-200">
                  <ul class="list-disc m-0 pl-4 text-slate-600">
                    <li>您瞭解您的贊助是支援創意專案的一種方式，也瞭解創意實踐過程中充滿變數，專案不一定能確保回饋。</li>
                    <li>
                      您知悉且明瞭本募資專案屬預購性質，您與提案者之法律關係為買賣，如確定申請分期付款，不得以任何理由拒絕付款。
                    </li>
                    <li>zingala 銀角零卡為中租控股（股票代號 5871）仲信資融提供之免信用卡分期之支付方式。</li>
                    <li>透過 zingala 銀角零卡分期系統審核方可使用，首次使用需備身份證件。</li>
                    <li>相關資料僅供作 zingala
                      銀角零卡分期服務驗證使用者身分，嘖嘖網站不會儲存亦不會提交給其他團體或個人。
                    </li>
                    <li>原「中租零卡」於 2022 年 5 月更名為「zingala 銀角零卡」。</li>
                  </ul>
                </div>
                <!-- 付款說明 end -->

<!--                <div class="mb-2 mt-4">-->
<!--                  <el-form-item label="加碼贊助（選擇）" prop="extra_payment">-->
<!--                    <el-input v-model="formState.extra_payment" type="number">-->
<!--                      <template #prefix>-->
<!--                        <div class="inline-flex items-center text-lg text-gray-500 rounded-l whitespace-nowrap">NT $-->
<!--                        </div>-->
<!--                      </template>-->
<!--                    </el-input>-->
<!--                  </el-form-item>-->
<!--                </div>-->

                <div class="mb-2 mt-4">
                  <el-form-item label="收件地點" prop="province_id">
                    <el-select
                      v-model="formState.province_id"
                      placeholder="請選擇收件地點"
                    >
                      <el-option
                        v-for="item in provinceList"
                        :key="item.value"
                        :label="item.label"
                        :value="item.value"
                      />
                    </el-select>
                  </el-form-item>
                </div>

                <div class="flex">
                  <div class="mt-4 flex-auto">
                    <el-form-item label="縣市" prop="city_id">
                      <el-select
                        v-model="formState.city_id"
                        placeholder="請選擇縣市"
                      >
                        <el-option
                          v-for="item in cityList"
                          :key="item.value"
                          :label="item.label"
                          :value="item.value"
                        />
                      </el-select>
                    </el-form-item>
                  </div>

                  <div class="mt-4 flex-auto pl-4">
                    <el-form-item label="鄉鎮市區" prop="region_id">
                      <el-select
                        v-model="formState.region_id"
                        placeholder="請選擇鄉鎮市區"
                      >
                        <el-option
                          v-for="item in districtList"
                          :key="item.value"
                          :label="item.label"
                          :value="item.value"
                        />
                      </el-select>
                    </el-form-item>
                  </div>

                </div>
                <div class="flex mt-4">
                  <div class="flex-auto">
                    <el-form-item label="地址" prop="detail">
                      <el-input v-model="formState.detail" maxlength="200" show-word-limit></el-input>
                    </el-form-item>
                  </div>
                  <!--                  <div class="w-32 pl-4 js-postcode">-->
                  <!--                    <el-form-item label="郵遞區號" prop="postcode">-->
                  <!--                      <el-input v-model="formState.postcode"></el-input>-->
                  <!--                    </el-form-item>-->
                  <!--                  </div>-->
                </div>
                <!-- 取貨方式 -->
                <div class="mb-4 hidden">
                  <label class="font-bold mb-2">超商取貨（選填）</label>
                  <div class="js-store-info mr-4 inline-block" hidden="">
                    <div class="inline-block border-2 py-2 px-4 rounded leading-none js-store-info-text"></div>
                    <button class="button-text js-store-cancel pointer leading-none ml-1 px-2 text-zec-red text-xl"
                            type="button">×
                    </button>
                  </div>
                  <details class="inline-block js-choose-store reset-details"
                           data-json="{&quot;01&quot;:&quot;7-11&quot;,&quot;03&quot;:&quot;全家&quot;,&quot;05&quot;:&quot;萊爾富&quot;}">
                    <summary class="inline-block px-4 py-1 border border-current rounded mr-4">
                      選擇
                      <el-icon class="align-middle text-sm">
                        <ArrowRightBold/>
                      </el-icon>
                    </summary>
                    <div class="flex border border-black rounded mt-2">
                      <button class="hidden" type="submit">default submit</button>
                      <button
                        class="inline-block font-bold px-4 py-1 border-current border-r last:border-0 cursor-pointer"
                        formnovalidate="formnovalidate" name="store_select" type="submit" value="01">7-11
                      </button>
                    </div>
                  </details>
                  <input class="mb-4 w-full js-store-field" autocomplete="off" type="hidden" name="order[ship_to_store]"
                         id="order_ship_to_store">
                  <div
                    class="mt-2 py-2 px-4 rounded border border-blue-600 bg-blue-100 text-blue-600 mv-child-0 text-xs">
                    若有選擇此出貨方式，提案人將安排超商取貨；但若出貨時，選擇的超商因故不支援店取，提案人將把回饋品寄至收件地址。
                    <br>
                    提案人保有寄送方式的最終決定權。
                  </div>
                </div>
                <!-- 聯係人 -->
                <el-form-item label="收件人" prop="name">
                  <el-input v-model="formState.name" placeholder="請輸入真實姓名，以利出貨作業進行"></el-input>
                </el-form-item>
                <!--                <label class="px-2 mb-4 mt-2 text-blue-600 text-xs hidden" id="recipient-warning-message">-->
                <!--                  請再次確認是否與證件名稱相符，若不正確可能造成投遞失敗。-->
                <!--                </label>-->
                <el-form-item label="聯絡電話" prop="phone">
                  <el-input v-model="formState.phone" placeholder="請填寫真實手機號碼，以利取貨或聯絡收貨"
                            maxlength="20"></el-input>
                </el-form-item>

                <!-- 訂單關聯問卷 start -->
                <div class="p-4 rounded mb-4 bg-zinc-100 hidden">
                  <h3 class="border-b border-gray-200 pb-1 text-base mb-4 font-bold">
                    回饋調查
                    <div class="text-xs">
                      <div class="text-gray-500">計畫結束前，可以隨時到贊助紀錄修改答案。</div>
                    </div>
                  </h3>
                  <label class="font-bold mt-4">
                    請選擇「杯身」顏色 (集資期間可登入嘖嘖帳號，至「贊助紀錄」更改)
                    <select
                      class="zec w-full rounded px-2 mb-0 mt-1 focus:outline-none focus:ring-1 focus:ring-zec-blue focus:border-zec-blue"
                      name="order[survey_answers][99887]">
                      <option value="">選擇</option>
                      <option value="大杯｜不鏽鋼原色">
                        大杯｜不鏽鋼原色
                      </option>
                      <option value="大杯｜仙草黑">
                        大杯｜仙草黑
                      </option>
                      <option value="大杯｜砂糖白">
                        大杯｜砂糖白
                      </option>
                      <option value="大杯｜香草米">
                        大杯｜香草米
                      </option>
                      <option value="大杯｜丹寧藍">
                        大杯｜丹寧藍
                      </option>
                      <option value="大杯｜葡萄紫">
                        大杯｜葡萄紫
                      </option>
                    </select>
                  </label>
                  <label class="font-bold mt-4">
                    如有加購「肩揹帶」，請於此處選擇顏色
                    <select
                      class="zec w-full rounded px-2 mb-0 mt-1 focus:outline-none focus:ring-1 focus:ring-zec-blue focus:border-zec-blue"
                      name="order[survey_answers][100030]">
                      <option value="">選擇</option>
                      <option value="肩揹帶｜芝麻">
                        肩揹帶｜芝麻
                      </option>
                      <option value="肩揹帶｜沙灘">
                        肩揹帶｜沙灘
                      </option>
                      <option value="肩揹帶｜深焙">
                        肩揹帶｜深焙
                      </option>
                    </select>
                  </label>
                  <label class="font-bold mt-4">
                    如有加購「杯套」，請於此處選擇顏色
                    <select
                      class="zec w-full rounded px-2 mb-0 mt-1 focus:outline-none focus:ring-1 focus:ring-zec-blue focus:border-zec-blue"
                      name="order[survey_answers][100031]">
                      <option value="">選擇</option>
                      <option value="杯套｜芝麻">
                        杯套｜芝麻
                      </option>
                      <option value="杯套｜沙灘">
                        杯套｜沙灘
                      </option>
                      <option value="杯套｜深焙">
                        杯套｜深焙
                      </option>
                    </select>
                  </label>
                  <label class="font-bold mt-4">
                    如有加購「小包」，請於此處選擇顏色
                    <select
                      class="zec w-full rounded px-2 mb-0 mt-1 focus:outline-none focus:ring-1 focus:ring-zec-blue focus:border-zec-blue"
                      name="order[survey_answers][100032]">
                      <option value="">選擇</option>
                      <option value="小包｜奶茶米">
                        小包｜奶茶米
                      </option>
                      <option value="小包｜冬瓜綠">
                        小包｜冬瓜綠
                      </option>
                      <option value="小包｜珍珠黑">
                        小包｜珍珠黑
                      </option>
                    </select>
                  </label>
                  <label class="font-bold mt-4">
                    如有再加購「超早鳥獨享大杯」，請於此處選擇「杯身」顏色
                    <select
                      class="zec w-full rounded px-2 mb-0 mt-1 focus:outline-none focus:ring-1 focus:ring-zec-blue focus:border-zec-blue"
                      name="order[survey_answers][100109]">
                      <option value="">選擇</option>
                      <option value="大杯｜不鏽鋼原色">
                        大杯｜不鏽鋼原色
                      </option>
                      <option value="大杯｜仙草黑">
                        大杯｜仙草黑
                      </option>
                      <option value="大杯｜砂糖白">
                        大杯｜砂糖白
                      </option>
                      <option value="大杯｜香草米">
                        大杯｜香草米
                      </option>
                      <option value="大杯｜丹寧藍">
                        大杯｜丹寧藍
                      </option>
                      <option value="大杯｜葡萄紫">
                        大杯｜葡萄紫
                      </option>
                    </select>
                  </label>
                  <label class="font-bold mt-4">
                    若有合併訂單，請於此欄註記
                    <input
                      class="w-full mb-0 mt-1 border border-gray-300 rounded px-2 focus:outline-none focus:ring-1 focus:ring-zec-blue focus:border-zec-blue"
                      name="order[survey_answers][100137]" type="text" value="">
                  </label>
                  <label class="font-bold mt-4">
                    若配送有特殊要求，請於此欄註記 (ex. 管理員代收)
                    <input
                      class="w-full mb-0 mt-1 border border-gray-300 rounded px-2 focus:outline-none focus:ring-1 focus:ring-zec-blue focus:border-zec-blue"
                      name="order[survey_answers][100138]" type="text" value="">
                  </label>
                  <label class="font-bold mt-4">
                    如有加購「杯袋」，請於此處選擇顏色
                    <select
                      class="zec w-full rounded px-2 mb-0 mt-1 focus:outline-none focus:ring-1 focus:ring-zec-blue focus:border-zec-blue"
                      name="order[survey_answers][100182]">
                      <option value="">選擇</option>
                      <option value="杯袋｜芝麻">
                        杯袋｜芝麻
                      </option>
                      <option value="杯袋｜沙灘">
                        杯袋｜沙灘
                      </option>
                      <option value="杯袋｜深焙">
                        杯袋｜深焙
                      </option>
                    </select>
                  </label>
                </div>
                <!-- 訂單關聯問卷 end -->
                <div class="my-4 p-4 rounded bg-zinc-100" data-controller="invoice">
                  <el-form-item label="發票資訊" prop="invoice_type">
                    <el-radio-group v-model="formState.invoice_type">
                      <el-radio value="personal">個人發票</el-radio>
                      <el-radio value="commercial">公司發票</el-radio>
                    </el-radio-group>
                  </el-form-item>

                  <el-form-item class="hidden" label="手機載具" prop="invoice_carrier">
                    <el-input v-model="formState.invoice_carrier" placeholder="例：/AB1CD23"
                              maxlength="20"></el-input>
                  </el-form-item>

                  <div class="flex" v-show="formState.invoice_type === 'commercial'">
                    <el-form-item class="mr-4" label="發票統編" prop="invoice_ubn">
                      <el-input v-model="formState.invoice_ubn" placeholder="例：12345678"
                                maxlength="20"></el-input>
                    </el-form-item>
                    <el-form-item class="mr-4" label="發票抬頭" prop="invoice_heading">
                      <el-input v-model="formState.invoice_heading" placeholder="例：12345678"
                                maxlength="20"></el-input>
                    </el-form-item>
                  </div>
                </div>
                <!--  分期付款 start  -->
                <div
                  class="bg-red-100 p-4 js-zero-card-warning border border-red-200 mt-2 rounded hidden js-zero-card-section">
                  <el-icon class="material-icons text-sm align-middle" size="14"><InfoFilled /></el-icon>
                  以下資料僅供作 zingala 銀角零卡分期服務驗證使用者身分，嘖嘖網站不會儲存亦不會提交給其他團體或個人。
                  <label class="mt-4 font-bold" for="order_installment">zingala 銀角零卡分期/ 分期期數
                    <select
                      class="zec px-2 mb-0 w-full rounded js-zero-card-input border-red-200 focus:outline-none focus:ring-1 focus:ring-zec-blue focus:border-zec-blue"
                      disabled="disabled" name="order[installment]" id="order_installment">
                      <option value="">- 選擇分期期數 -</option>
                    </select>
                  </label>
                  <label class="mt-4 font-bold" for="order_zero_card_buyer_name">zingala 銀角零卡分期/ 姓名
                    <input placeholder="姓名" maxlength="10"
                           class="w-full border-red-200 rounded js-zero-card-input focus:outline-none focus:ring-1 focus:ring-zec-blue focus:border-zec-blue"
                           size="10" type="text" name="order[zero_card_buyer_name]" id="order_zero_card_buyer_name">
                  </label>
                  <label class="mt-4 font-bold" for="order_zero_card_buyer_phone">zingala 銀角零卡分期/ 電話
                    <input placeholder="電話" maxlength="20" minlength="8" pattern="[+]{0,1}[0-9]+"
                           class="w-full border-red-200 rounded js-zero-card-input focus:outline-none focus:ring-1 focus:ring-zec-blue focus:border-zec-blue"
                           size="20" type="text" name="order[zero_card_buyer_phone]" id="order_zero_card_buyer_phone">
                  </label>
                  <label class="mt-4 font-bold" for="order_zero_card_buyer_email">zingala 銀角零卡分期/ Email
                    <input placeholder="Email" type="email"
                           class="w-full border-red-200 rounded js-zero-card-input focus:outline-none focus:ring-1 focus:ring-zec-blue focus:border-zec-blue"
                           name="order[zero_card_buyer_email]" id="order_zero_card_buyer_email">
                  </label>
                  <label class="mt-4 font-bold" for="order_zero_card_buyer_id">zingala 銀角零卡分期/ 身份證字號
                    <input placeholder="身份證字號" minlength="10" maxlength="10"
                           class="w-full border-red-200 rounded js-zero-card-input focus:outline-none focus:ring-1 focus:ring-zec-blue focus:border-zec-blue"
                           size="10" type="text" name="order[zero_card_buyer_id]" id="order_zero_card_buyer_id">
                  </label>
                </div>
                <!--  分期付款 end  -->
                <el-form-item class="mt-4" prop="consented">
                  <el-checkbox v-model="formState.consented">
                    <span class="text-sm">
                      我已閱讀並同意
                      <router-link to="/agreement" target="_blank">嘖嘖服務條款</router-link>與
                      <router-link href="/privacy" target="_blank">隱私權政策</router-link>。
                      </span>
                  </el-checkbox>
                </el-form-item>

                <el-button
                  type="success"
                  plain
                  @click="handleSubmit(formStateRef)"
                  class="block lg:inline-block text-zec-green font-bold border-2 border-zec-green mt-4 rounded px-16 py-2 hover:border-zec-green hover:bg-zec-green hover:text-white"
                >
                  立即贊助
                  <span class="font-bold ml-2">NT$<span class="js-sum">{{get(orderInfo, 'order_pay_price', 0)}}</span></span>
                </el-button>

                <a class="text-neutral-600 text-sm inline-block my-2 xs:hidden" href="#preview">
                  <i class="material-icons align-middle text-sm mr-1">help_outline</i>
                  金額細節
                </a>
                <p class="text-xs text-neutral-600 mt-2 leading-tight">
                  提案人有權決定是否接受贊助。<br>如提案人因故決定不接受贊助，將會取消贊助並主動退還贊助款項。
                </p>

              </div>
            </div>
          </div>
        </el-form>
      </div>
    </div>
  </div>

</template>

<style scoped lang="scss">

</style>
