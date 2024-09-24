<script setup lang="ts">
import { onMounted, reactive, ref } from "vue";
import { useRoute, useRouter } from "vue-router";
import { getOrderList, payOrder } from "@/api/order";
import useLoading from "@/hooks/loading";
import get from "lodash/get";
import { ElMessage, FormInstance, FormRules } from "element-plus";

const { loading, setLoading } = useLoading(false);

// 列表
const tableData = ref([]);
const pageValue = reactive({
    dataType: "all",
    pageSize: 20,
    current: 1,
    total: 0,
});
const route = useRoute();
const router = useRouter();
const errorMessage = ref("");

async function fetchTableData(extraParam = {}) {
    setLoading(true);
    try {
        let params = {
            dataType: pageValue.dataType,
            page: pageValue.current,
            list_rows: pageValue.pageSize,
        };
        if (extraParam) {
            params = {
                ...params,
                ...extraParam,
            };
        }

        const { data } = await getOrderList(params);

        if (data && data?.list) {
            tableData.value = data?.list?.data || [];
            pageValue.total = data?.list?.total || 0;
        }
    } finally {
        setLoading(false);
    }
}

const goPayOrder = async (order_id, pay_price) => {
    setLoading(true);
    try {
        const params = {
            order_id: order_id,
            payType: 10,
            pay_source: "h5",
            use_balance: 1,
            pay_price: pay_price,
        };
        const { msg } = await payOrder(params);
        ElMessage.success(msg || "支付成功");
        await router.replace({ path: `/order/detail/${order_id}` });
    } catch (e) {
        errorMessage.value = (e as Error).message;
    } finally {
        setLoading(false);
    }
};

const onPageChange = (current: number) => {
    fetchTableData({ page: current });
};

const handleDataType = (type) => {
    pageValue.dataType = type;
    pageValue.page = 1;
    fetchTableData();
};

onMounted(() => {
    fetchTableData();
});

const getActiveClass = (dataType, type) => {
    if (dataType === type) {
        return "data-type text-black w-auto rounded px-4 py-1 font-black bg-gray-200 leading-relaxed";
    } else {
        return "data-type text-black w-auto rounded px-4 py-1 ml-1 lg:ml-3 bg-gray-50";
    }
};

// 定义时间转换函数
const convertHoursToDH = (hours) => {
    const days = Math.floor(hours / 24);
    const remainingHours = hours % 24;

    let result = [];
    if (days > 0) result.push(`${days}天`);
    if (remainingHours > 0 || days === 0) result.push(`${remainingHours}小時`);

    return result.join(" ");
};
</script>

<template>
    <div class="container px-4 lg:px-0 pt-4 cf">
        <h2 class="flex mt-4">
            <span
                :class="getActiveClass(pageValue.dataType, 'all')"
                @click="() => handleDataType('all')"
                >贊助記錄
            </span>
            <span
                :class="getActiveClass(pageValue.dataType, 'payment')"
                @click="() => handleDataType('payment')"
                >等待付款記錄
            </span>
            <span
                :class="getActiveClass(pageValue.dataType, 'cancel')"
                @click="() => handleDataType('cancel')"
                >取消或退款記錄
            </span>
        </h2>
        <br />
        <div
            class="font-bold text-sm p-2 bg-zinc-100 rounded text-center mt-3 mb-4"
        >
            贊助編號為辨識贊助者身份的重要資訊，請妥善保管，切勿外流。
        </div>
    </div>

    <div v-if="tableData.length === 0" class="pb-16">
        <div class="container">
            <div class="mt-2">
                <div class="rounded text-sm py-16 text-center px-4 bg-zinc-100">
                    找不到記錄。
                </div>
            </div>
        </div>
    </div>

    <div v-else class="pb-16">
        <div class="container">
            <ul class="p-0 px-4 lg:px-0">
                <li
                    v-for="item in tableData"
                    :key="item.order_id"
                    class="border mb-8 py-2 cf text-sm border-neutral-200 relative bg-white"
                >
                    <div class="cf pt-2">
                        <div class="w-full float-left px-4 lg:w-4/5">
                            <router-link
                                class="text-zec-blue font-bold text-base mr-4"
                                :to="`/project/${get(item, 'product[0].product_id')}`"
                            >
                                {{ get(item, "product[0].product_name") }}
                            </router-link>
                            <span class="text-xs text-neutral-600">{{
                                get(item, "create_time")
                            }}</span>
                        </div>
                        <div
                            class="w-full float-left px-4 lg:text-right lg:w-1/5"
                        >
                            $ {{ get(item, "order_price") }}
                        </div>
                    </div>
                    <div class="cf">
                        <div class="w-full float-left px-4 mt-1 lg:text-right">
                            <justify-center
                                class="text-sm px-2 py-1 border border-solid rounded-full bg-gray-50 text-gray-500 border-gray-500"
                            >
                                {{ get(item, "state_text") }}
                            </justify-center>
                            <el-button
                                color="#098B5E"
                                plain
                                @click="
                                    goPayOrder(
                                        get(item, 'order_id'),
                                        get(item, 'total_price'),
                                    )
                                "
                                v-if="item.pay_status.value == 10"
                                class="text-sm px-2 py-1 border border-solid rounded-full bg-gray-50 text-gray-500 border-gray-500 ml-3"
                            >
                                立即付款
                            </el-button>
                        </div>
                    </div>
                    <div class="cf">
                        <div
                            class="w-full float-left lg:p-4 px-4 pt-4 lg:w-1/2"
                        >
                            <div class="text-neutral-600 truncate">
                                {{ get(item, "product[0].detail") }}
                            </div>
                            <div class="text-xs text-neutral-600">
                                <img
                                    width="600"
                                    height="200"
                                    class="lazy placeholder-3:1 w-full lg:w-1/3 h-auto mb-2 round-s entered loaded"
                                    :data-src="
                                        get(
                                            item,
                                            'product[0].productSku.image.file_path',
                                        )
                                    "
                                    :src="
                                        get(
                                            item,
                                            'product[0].productSku.image.file_path',
                                        )
                                    "
                                    data-ll-status="loaded"
                                />
                            </div>
                        </div>
                        <div
                            class="w-full float-left lg:p-4 px-4 pt-4 lg:text-right text-xs lg:w-1/2"
                        >
                            <div
                                class="text-xs pt-2"
                                v-if="item.pay_status.value == 10"
                            >
                                [訂單過期時間]
                                {{ get(item, "pay_end_time_format") }}
                            </div>
                            <div class="text-xs pt-2">
                                [贊助編號] {{ get(item, "order_no") }}
                            </div>
                            <div class="text-xs pt-2">
                                [回饋率]
                                {{
                                    get(
                                        item,
                                        "product[0].productSku.commission_percent",
                                    )
                                }}%
                            </div>
                            <div class="text-xs pt-2">
                                [回饋週期]
                                {{
                                    convertHoursToDH(
                                        get(
                                            item,
                                            "product[0].productSku.cycle",
                                        ),
                                    )
                                }}
                            </div>
                        </div>
                    </div>
                    <div
                        class="border-t border-neutral-200 text-xs px-4 pt-2 lg:mt-0 mt-4"
                    >
                        <div class="xs:float-right">
                            <router-link
                                class="font-bold text-black hover:text-zec-blue mr-4"
                                :to="`/order/detail/${item.order_id}`"
                            >
                                檢視細節
                            </router-link>
                            <!--
                            <router-link class="font-bold text-black hover:text-zec-blue"
                                        :to="`/message/new?product_id=${get(item, 'product[0].product_id')}`"
                            >
                                聯絡提案人
                            </router-link>
                            -->
                        </div>
                        <!--
                        分享連結
                        <button
                            aria-label="複製網址"
                            class="leading-none pointer tooltip tooltip-t button-text align-middle"
                            data-copy="#share-P18675CTCOU36DGXJEFL"
                            type="button"
                        >
                            <i class="material-icons text-sm">content_copy</i>
                        </button>
                        -->
                    </div>
                </li>
            </ul>
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

<style scoped lang="scss">
.data-type {
    cursor: pointer;
}
</style>
