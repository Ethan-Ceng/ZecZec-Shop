<script setup lang="ts">
import { ref, onMounted, onUnmounted, computed } from "vue";
import { useRoute } from "vue-router";
import { useAppStore } from "@/store";

const route = useRoute();
const appStore = useAppStore();

const iframeWidth = ref("100%");
const iframeHeight = ref("700px");

const chatUrl = computed(() => {
    // 使用环境变量来判断当前环境
    const baseUrl =
        import.meta.env.VITE_APP_ENV === "production"
            ? "/chat/index" // 生产环境使用相对路径，由 Nginx 处理
            : "http://localhost:8081/chat/index"; // 开发环境使用完整 URL

    const params = new URLSearchParams({
        noCanClose: "1",
        token: "e55af56ed680e3bcc214445e0ab4c203",
    });
    return `${baseUrl}?${params.toString()}`;
});

const updateIframeSize = () => {
    const width = window.innerWidth;

    if (width <= 390) {
        // iPhone 12 Mini
        iframeHeight.value = "500px";
    } else if (width <= 428) {
        // iPhone 14 Pro Max
        iframeHeight.value = "600px";
    } else if (width <= 768) {
        // Tablets
        iframeHeight.value = "650px";
    } else {
        iframeHeight.value = "700px";
    }

    // 使用百分比寬度，但設置最大寬度
    iframeWidth.value = "100%";
};

onMounted(() => {
    updateIframeSize();
    window.addEventListener("resize", updateIframeSize);
});

onUnmounted(() => {
    window.removeEventListener("resize", updateIframeSize);
});
</script>

<template>
    <div class="bg-zec-bg">
        <div class="container lg:py-8 py-4 lg:px-0 px-4 cf">
            <div class="flex items-center justify-center">
                <div class="w-full max-w-[900px]">
                    <iframe
                        :src="chatUrl"
                        frameborder="0"
                        :style="{
                            width: iframeWidth,
                            height: iframeHeight,
                            maxWidth: '900px',
                        }"
                    ></iframe>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped lang="scss">
.container {
    width: 100%;
    max-width: 1200px;
    margin: 0 auto;
}
</style>
