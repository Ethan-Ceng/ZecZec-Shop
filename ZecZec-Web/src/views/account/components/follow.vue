<script setup lang="ts">
import {reactive, ref, onMounted} from "vue";
import {getFavoriteList, likeProduct} from "@/api/product";
import useLoading from "@/hooks/loading";
import {ElMessage} from "element-plus";

const {loading, setLoading} = useLoading(false)

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
      page: pageValue.current,
      list_rows: pageValue.pageSize,
    }
    if (extraParam) {
      params = {
        ...params,
        ...extraParam
      }
    }

    const {data} = await getFavoriteList(params)

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
  fetchTableData()
})

const fetchLike = async (productId) => {
  if (!productId) return
  setLoading(true)
  try {
    await likeProduct(productId)
    isLike.value = !isLike.value
    const message = isLike.value ? "加入追蹤計劃": "取消追蹤計劃"
    ElMessage.success(message)
    await fetchTableData({page: 1})
  } catch (err) {
    // console.warn(err)
  } finally {
    setLoading(false)
  }
}

</script>

<template>
  <div class="container px-4 lg:px-0 mt-4 mb-6">
    <h2 class="flex text-2xl">追蹤計畫</h2>
  </div>

  <div class="container pb-16" v-loading="loading">
    <div class="flex lg:-mx-4">
      <div v-for="item in tableData" :key="item.product_id" class="px-4 py-4 w-full xs:w-1/2 lg:w-1/4">
        <router-link
          class="block rounded mb-4 bg-zinc-100 aspect-ratio-project-cover"
           :to="`/project/${item.product_id}`"
          :style="`background-image: url('${item.product_image}')`">
          &nbsp;
        </router-link>
        <router-link class="truncate block font-bold text-black mb-2" to="/projects/jolly_dragonbaby">
          {{item.product_name }}
        </router-link>
          <el-button  class="button button-s w-full text-center text-neutral-600" data-disable-with="處理中...">取消追蹤 </el-button>
      </div>
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
