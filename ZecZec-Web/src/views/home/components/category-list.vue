<script setup lang="ts">
import {defineProps, computed, reactive, ref, onMounted} from "vue";
import useLoading from "@/hooks/loading";
import {getProductList} from "@/api/product";
import ProductCard from "@/components/product-card/index.vue";

// 1：群眾集資 2 預購式專案 bg-zec-blue 3 訂閱式專案 4 再登場專案
const props = defineProps({
  productType: {
    type: Number,
    default: () => 1
  }
})

const categoryTitle = computed(() => {
  if (props.productType === 1) {
    return '群眾集資'
  }

  if (props.productType === 2) {
    return '預購式專案'
  }

  if (props.productType === 3) {
    return '訂閱式專案'
  }

  if (props.productType === 4) {
    return '再登場專案'
  }
})

const colorClass = computed(() => {
  if (props.productType === 1) {
    return 'bg-zec-green hover:bg-zec-darkgreen'
  }

  if (props.productType === 2) {
    return 'bg-zec-blue hover:bg-zec-darkblue'
  }

  if (props.productType === 3) {
    return 'bg-zec-purple hover:bg-zec-darkpurple'
  }
  if (props.productType === 4) {
    return 'bg-zec-cyan hover:bg-zec-darkcyan'
  }
})

const {loading, setLoading} = useLoading(false)

// 列表
const tableData = ref([])
const pageValue = reactive({
  pageSize: 12,
  current: 1,
  total: 100
});

const fetchTableData = async (extraParam = {}) => {
  setLoading(true)
  try {
    let params = {
      p_type: props.productType,
      page: pageValue.current,
      list_row: pageValue.pageSize,
    }

    if (extraParam) {
      params = {
        ...params,
        ...extraParam
      }
    }

    const {data} = await getProductList(params)

    if (data && data?.list) {
      tableData.value = data?.list?.data || []
      pageValue.total = data?.list?.total || 0
    }

    console.log(tableData.value)
  } catch (err) {
    // console.warn(err)
  } finally {
    setLoading(false)
  }
}

const onPageChange = (current: number) => {
  fetchTableData({page: current});
};


onMounted(async () => {
  await fetchTableData()
})

</script>

<template>
  <div class="container" v-loading="loading">
    <div class="flex items-center justify-between p-4 mb-4 lg:px-0">
      <h2 class="text-2xl font-bold">{{ categoryTitle }}</h2>
      <router-link
        class="inline-block text-base font-bold px-3 py-1 rounded text-white"
        :class="colorClass"
        :to="`/category?type=${productType}`">更多 &gt;
      </router-link>
    </div>

    <div class="flex lg:-mx-4">
      <template v-for="item in tableData" :key="item.product_id">
        <product-card :data-source="item"></product-card>
      </template>
    </div>

    <div class="flex justify-center mb-16 mt-16">
      <el-pagination
        size="large"
        layout="prev, pager, next"
        v-model:current-page="pageValue.current"
        v-model:page-size="pageValue.pageSize"
        :total="pageValue.total"
        @current-change="onPageChange"
      />
    </div>
  </div>
</template>

<style scoped lang="scss">

</style>
