<script setup lang="ts">
import {onMounted} from "vue";
import {storeToRefs} from "pinia";
import {useAppStore} from "@/store";

const appStore = useAppStore();

const { setting } = storeToRefs(appStore);

onMounted(async () => {
  await appStore.getSetting();
});
</script>

<template>
    <div class="border-t bg-gray-50">
        <footer
            class="max-w-xs md:max-w-5xl py-10 mx-auto flex flex-wrap px-3 justify-between"
        >
            <section class="w-full md:w-auto pb-10 md:pb-0 flex items-center">
              <img width="115" :src="setting.login_logo" :alt="setting.login_desc" />
<!--                <svg-icon name="logo" size="120px" />-->
              <span class="ml-4 text-2xl">{{setting.name}}</span>
            </section>
            <section>
                <h2 class="text-sm font-semibold mb-4">幫助</h2>
                <h3 class="text-sm mb-3">
                    <router-link
                        class="text-gray-600 hover:text-zec-blue"
                        to="/faq"
                        >常見問題</router-link
                    >
                </h3>
                <h3 class="text-sm mb-3">
                    <router-link
                        class="text-gray-600 hover:text-zec-blue"
                        to="/agreement"
                        >使用條款</router-link
                    >
                </h3>
                <h3 class="text-sm mb-3">
                    <router-link
                        class="text-gray-600 hover:text-zec-blue"
                        to="/privacy"
                        >隱私權政策</router-link
                    >
                </h3>
            </section>
            <section>
                <h2 class="text-sm font-semibold mb-4">關於</h2>
                <h3 class="text-sm mb-3">
                    <a class="text-gray-600 hover:text-zec-blue" href="/about"
                        >關於我們</a
                    >
                </h3>
                <h3 class="text-sm mb-3">
                    <a
                        class="text-gray-600 hover:text-zec-blue"
                        href="javascript:void(0)"
                        >商標資源</a
                    >
                </h3>
            </section>
        </footer>
        <div class="border-t">
            <p class="text-center text-gray-600 text-xs py-5">
                © 嘖室股份有限公司
            </p>
        </div>
    </div>
</template>

<style scoped lang="scss">
.footer {
    background-color: rgb(249, 250, 251);

    .footer-main {
        width: 1140px;
        margin: 0 auto;
        padding: 40px 12px;
        border-top: 1px solid rgb(229, 231, 235);
        display: flex;
        justify-content: space-between;
    }

    .facebook {
        &:hover {
            fill: rgb(24, 119, 242);
        }
    }

    .footer-meta {
        padding: 20px 0;
        line-height: 16px;
        border-top: 1px solid rgb(229, 231, 235);
        text-align: center;
    }
}
</style>
