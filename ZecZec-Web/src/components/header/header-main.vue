<script setup lang="ts">
import { ref, onMounted } from "vue";
import { storeToRefs } from "pinia";
import { CaretBottom, Message, Search } from "@element-plus/icons-vue";
import { useAppStore, useUserStore } from "@/store";
import avatarImg from "@/assets/image/avatar.png";

const userStore = useUserStore();
const appStore = useAppStore();

const { setting } = storeToRefs(appStore);
const { token, userDetail } = storeToRefs(userStore);

onMounted(async () => {
    await appStore.getSetting();
});
</script>

<template>
    <div class="text-sm flex items-center justify-between">
        <router-link clsas="inline-block order-none" to="/">
            <h1 class="hover-logo m-0 flex items-center">
                <div class="inline-block logo-safari-fix mr-3">
                    <img
                        width="200"
                        :src="setting.logoUrl"
                        :alt="setting.name"
                    />
                </div>
                <!--<span>{{ setting.name }}</span>-->
            </h1>
        </router-link>
        <div
            class="hidden xs:block xs:mt-0 mt-4 pt-2 px-2 -mb-1 -ml-4 -mr-4 text-center xs:text-left xs:p-0 xs:mx-0 items-center border-t xs:border-0 border-zinc-100 flex-auto order-last xs:order-1 w-full xs:w-auto"
        >
            <div class="divider xs:inline-block hidden">&nbsp;</div>
            <router-link
                class="xs:mr-4 hover:text-zec-blue text-gray-700 flex-auto xs:flex-none"
                to="/category"
                >認購專區
            </router-link>
            <router-link
                class="xs:mr-4 hover:text-zec-blue text-gray-700 flex-auto xs:flex-none"
                to="/service"
                >客服
            </router-link>
            <router-link
                class="xs:mr-4 hover:text-zec-blue text-gray-700 flex-auto xs:flex-none"
                to="#"
                >我的團隊
            </router-link>
            <router-link
                class="xs:mr-4 hover:text-zec-blue text-gray-700 flex-auto xs:flex-none"
                to="#"
                >會員專區
            </router-link>
        </div>

        <div v-if="token" class="order-2 flex items-center">
            <router-link
                class="p-2 font-bold tooltip tooltip-b inline-block text-zec-green hover:text-black xs:hidden"
                aria-label="認購專區"
                to="/category"
            >
                <el-icon class="text-lg align-middle font-bold" :size="18">
                    <Search />
                </el-icon>
            </router-link>
            <router-link
                aria-label="站內訊息"
                class="inline-block text-gray-500 hover:text-black p-2 mr-4 tooltip tooltip-b"
                to="/message"
            >
                <el-tooltip effect="dark" content="站內訊息" placement="bottom">
                    <el-icon class="text-lg align-middle" :size="18">
                        <Message />
                    </el-icon>
                </el-tooltip>
            </router-link>
            <el-dropdown trigger="click">
                <span class="el-dropdown-link">
                    <el-avatar
                        shape="square"
                        :size="30"
                        :src="
                            userDetail.avatarUrl
                                ? userDetail.avatarUrl
                                : avatarImg
                        "
                    />
                    <el-icon class="el-icon--right"><CaretBottom /></el-icon>
                </span>
                <template #dropdown>
                    <el-dropdown-menu>
                        <el-dropdown-item
                            class="lg:hidden"
                            @click="$router.push('/category')"
                            >探索</el-dropdown-item
                        >
                        <!--            <el-dropdown-item divided @click="$router.push('/profile')">個人頁面</el-dropdown-item>-->
                        <el-dropdown-item
                            divided
                            @click="$router.push('/account?type=order')"
                            >贊助記錄</el-dropdown-item
                        >
                        <el-dropdown-item
                            @click="$router.push('/account?type=follow')"
                            >追蹤計畫</el-dropdown-item
                        >
                        <el-dropdown-item
                            divided
                            @click="$router.push('/account?type=setting')"
                            >帳號設定</el-dropdown-item
                        >
                        <el-dropdown-item
                            @click="$router.push('/account?type=teams')"
                            >團隊設定</el-dropdown-item
                        >
                        <el-dropdown-item divided>
                            <el-button @click="userStore.logout"
                                >登出</el-button
                            >
                        </el-dropdown-item>
                    </el-dropdown-menu>
                </template>
            </el-dropdown>
        </div>

        <div v-else class="flex order-2 items-center">
            <el-space :size="12">
                <el-button type="info" text bg @click="$router.push('/login')"
                    >登入</el-button
                >
                <el-button
                    type="info"
                    text
                    bg
                    @click="$router.push('/register')"
                    >註冊</el-button
                >
            </el-space>
        </div>
    </div>
</template>

<style scoped lang="scss"></style>
