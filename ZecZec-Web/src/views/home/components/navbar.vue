<script setup lang="ts">
import { useAppStore } from "@/store";
import { onMounted, ref } from "vue";

const appStore = useAppStore();
const categoryList = ref([]);

onMounted(async () => {
    categoryList.value = await appStore.getCategory();
});
</script>

<template>
    <div class="bg-zec-bg py-8">
        <div
            class="container border-t py-2 border-gray-200 text-center lg:mb-0 border-b tracking-widest"
        >
            <div
                class="-mt-2 px-2 left-50 text-xs offset-center absolute text-gray-500 bg-zec-bg"
            >
                探索專案類別
            </div>
            <div class="block whitespace-nowrap overflow-auto py-2">
                <router-link
                    v-for="item in categoryList"
                    :key="item.category_id"
                    class="text-sm px-4 sm:px-6 py-2 text-black hover:text-zec-green"
                    :to="`/category?category_id=${item.category_id}`"
                >
                    {{ item.name }}
                </router-link>
            </div>
        </div>
    </div>
</template>

<style scoped lang="scss"></style>
