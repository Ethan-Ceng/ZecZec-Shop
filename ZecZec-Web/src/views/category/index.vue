<script setup lang="ts">
import { ref, onMounted, reactive } from "vue";
import { useRoute } from "vue-router";
import { Filter, Plus, Search } from "@element-plus/icons-vue";
import { useAppStore } from "@/store";
import useLoading from "@/hooks/loading";
import { getProductList } from "@/api/product";
import ProductCard from "@/components/product-card/index.vue";
import get from "lodash/get";

const route = useRoute();

const appStore = useAppStore();
const categoryList = ref([]);
const { loading, setLoading } = useLoading(false);

const searchParams = reactive({
    p_type: "",
    category_id: "",
    search: "",
});

// 列表
const tableData = ref([]);
const pageValue = reactive({
    pageSize: 32,
    current: 1,
    total: 0,
});

const fetchTableData = async (extraParam = {}) => {
    setLoading(true);
    try {
        let params = {
            page: pageValue.current,
            list_rows: pageValue.pageSize,
            ...searchParams,
        };

        if (extraParam) {
            params = {
                ...params,
                ...extraParam,
            };
        }

        const { data } = await getProductList(params);

        if (data && data?.list) {
            tableData.value = data?.list?.data || [];
            pageValue.total = data?.list?.total || 0;
        }

        console.log(tableData.value);
    } catch (err) {
        // console.warn(err)
    } finally {
        setLoading(false);
    }
};

const onPageChange = (current: number) => {
    fetchTableData({ page: current });
};

const onFlushed = () => {
    pageValue.current = 1;
    fetchTableData();
};

onMounted(async () => {
    categoryList.value = await appStore.getCategory();

    const typeId = get(route, "query.type");
    const categoryId = get(route, "query.category_id");
    if (typeId) {
        searchParams.p_type = typeId - 0;
    }
    if (categoryId) {
        searchParams.category_id = categoryId - 0;
    }

    await fetchTableData();
});
</script>

<template>
    <div class="bg-zec-bg">
        <div class="container lg:py-8 py-4 lg:px-0 px-4 cf">
            <div class="flex items-center justify-between space-y-2">
                <div class="flex items-center">
                    <div class="flex-auto">
                        <el-select
                            v-model="searchParams.p_type"
                            placeholder="專案性質"
                            size="large"
                            style="min-width: 140px"
                            @change="onFlushed"
                        >
                            <el-option label="全部" value="" />
                            <el-option label="群眾集資" :value="1" />
                            <el-option label="預購式專案" :value="2" />
                            <el-option label="訂閱式專案" :value="3" />
                            <el-option label="再登場專案" :value="4" />
                        </el-select>
                    </div>
                    <el-icon class="mx-2 align-middle">
                        <Plus />
                    </el-icon>
                    <div class="flex-auto">
                        <el-select
                            v-model="searchParams.category_id"
                            placeholder="主題分類"
                            size="large"
                            style="min-width: 140px"
                            @change="onFlushed"
                        >
                            <el-option label="全部" value="" />
                            <el-option
                                v-for="item in categoryList"
                                :key="item.category_id"
                                :value="item.category_id"
                                :label="item.name"
                            />
                        </el-select>
                    </div>
                </div>
                <div class="flex xs:w-auto xs:flex-row justify-end">
                    <div
                        class="inline-block align-middle xs:w-auto grow mb-2 xs:mb-0"
                    >
                        <el-input
                            v-model="searchParams.search"
                            style="width: 240px"
                            placeholder="搜尋計畫"
                            :prefix-icon="Search"
                            @change="onFlushed"
                        />
                    </div>
                    <div class="bg-white ml-2">
                        <el-dropdown trigger="click">
                            <el-button class="el-dropdown-link">
                                排序
                                <el-icon class="el-icon--right">
                                    <Filter />
                                </el-icon>
                            </el-button>
                            <template #dropdown>
                                <el-dropdown-menu>
                                    <el-dropdown-item
                                        >所有計畫</el-dropdown-item
                                    >
                                    <el-dropdown-item
                                        >現正熱門</el-dropdown-item
                                    >
                                    <el-dropdown-item
                                        >近期成功</el-dropdown-item
                                    >
                                    <el-dropdown-item
                                        >專案金額</el-dropdown-item
                                    >
                                    <el-dropdown-item
                                        >支援人次</el-dropdown-item
                                    >
                                    <el-dropdown-item
                                        >最後衝刺</el-dropdown-item
                                    >
                                    <el-dropdown-item
                                        >最新啟動</el-dropdown-item
                                    >
                                </el-dropdown-menu>
                            </template>
                        </el-dropdown>
                    </div>
                </div>
            </div>
        </div>
        <div class="container">
            <div class="flex flex-wrap lg:-mx-4">
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
    </div>
</template>

<style scoped lang="scss"></style>
