<script setup lang="ts">
import { onMounted, reactive, ref } from "vue";
import { useRoute, useRouter } from "vue-router";
import { storeToRefs } from "pinia";
import { useAppStore, useUserStore } from "@/store";
import { ElMessage, FormInstance, FormRules } from "element-plus";
import Footer from "@/components/footer/index.vue";
import useLoading from "@/hooks/loading";
import { checkByEmail } from "@/api/user";
import get from "lodash/get";

const route = useRoute();
const router = useRouter();

const errorMessage = ref("");
const { loading, setLoading } = useLoading();
const userStore = useUserStore();
const appStore = useAppStore();
const { setting } = storeToRefs(appStore);

interface RuleForm {
    email: string;
    password: string;
}

const formStateRef = ref<FormInstance>();

const formState = reactive<RuleForm>({
    email: "",
    password: "",
});

const rules = reactive<FormRules<RuleForm>>({
    email: [
        {
            required: true,
            type: "email",
            message: "請輸入正確郵箱",
            trigger: "change",
        },
    ],
    password: [
        { required: true, message: "請輸入密碼", trigger: "blur" },
        { min: 6, max: 20, message: "密碼長度 6 至 20", trigger: "blur" },
    ],
});

const submitForm = async (formEl: FormInstance | undefined) => {
    if (!formEl) return;
    await formEl.validate((valid, fields) => {
        if (valid) {
            console.log("submit!");
            fetchRegister({ ...formState });
        } else {
            console.log("error submit!", fields);
        }
    });
};

const fetchRegister = async (params) => {
    setLoading(true);
    try {
        await userStore.login(params);
        ElMessage.success("登入成功");
        await router.replace("/");
    } catch (e) {
        errorMessage.value = (e as Error).message;
    } finally {
        setLoading(false);
    }
};

const goCheckByEmail = async (params) => {
    setLoading(true);
    try {
        const { msg } = await checkByEmail(params);
        ElMessage.success(msg || "success");
    } catch (e) {
        errorMessage.value = (e as Error).message;
    } finally {
        setLoading(false);
    }
};

onMounted(() => {
    const appId = get(route, "query.app_id");
    const email = get(route, "query.email");
    const code = get(route, "query.code");
    if (code) {
        goCheckByEmail({
            appId,
            email,
            code,
        });
    }
});
</script>

<template>
    <div class="login-wrapper">
        <div
            class="gallery flex-1 invisible sm:visible relative overflow-hidden"
        ></div>

        <div
            class="sm:max-w-xl flex items-center justify-center bg-white px-8 md:px-16 w-full sm:w-4/5"
        >
            <div class="max-w-sm w-full py-16">
                <a class="whitespace-nowrap flex items-center mb-16" href="/">
                    <h2
                        class="inline-block mr-6 relative after:absolute after:w-px after:h-6 after:m-auto after:bg-gray-500 after:inset-y-0 after:right-0"
                    >
                        <!--            <img class="mr-6 h-16" width="92" height="32" :src="'@/assets/svg/logo.svg'">-->
                        <!--<svg-icon class="mr-6 h-16" name="logo" size="80px" />-->
                        <img
                            width="200"
                            :src="setting.logoUrl"
                            :alt="setting.name"
                        />
                    </h2>
                    <h3 class="inline-block">
                        {{ get(setting, "login_desc") }}
                    </h3>
                </a>
                <h1 class="font-bold text-3xl mb-4">登入</h1>
                <h4 class="mb-8">
                    尚未成為會員？
                    <router-link
                        class="text-blue-600 font-bold underline"
                        to="/register"
                        >註冊帳號
                    </router-link>
                    。
                </h4>

                <el-form
                    ref="formStateRef"
                    size="large"
                    :model="formState"
                    :rules="rules"
                    label-width="auto"
                    label-position="top"
                >
                    <el-form-item label="電子信箱" prop="email">
                        <el-input v-model="formState.email" />
                    </el-form-item>
                    <el-form-item label="密碼" prop="password">
                        <el-input
                            type="password"
                            v-model="formState.password"
                            show-password
                        />
                    </el-form-item>

                    <div class="flex justify-between items-center mb-6">
                        <el-checkbox>記住我</el-checkbox>
                        <a
                            class="text-sm text-gray-800 hover:text-blue-600 underline"
                            href="/password"
                            >忘記密碼？</a
                        >
                    </div>

                    <el-button
                        :loading="loading"
                        type="success"
                        round
                        size="large"
                        class="w-full mb-8"
                        @click="submitForm(formStateRef)"
                    >
                        登入
                    </el-button>

                    <p class="text-center text-gray-800 text-sm">
                        繼續進行代表你同意
                        <router-link
                            class="text-blue-600"
                            target="_blank"
                            to="/agreement"
                            >服務條款
                        </router-link>
                        。
                        <!--            <a target="_blank" class="text-blue-600" href="javascript:void(0);" @click="$router.push('/agreement')">服務條款</a>。-->
                    </p>
                </el-form>
            </div>
        </div>
    </div>
    <Footer />
</template>

<style scoped lang="scss">
.login-wrapper {
    height: 100vh;
    display: flex;

    .gallery {
        flex: 1;

        &:after {
            content: "";
            position: absolute;
            background-image: url("@/assets/image/project-gallery.jpg");
            background-size: 1500px;
            background-repeat: repeat;
            background-position: center;
            width: 300%;
            height: 300%;
            top: -125%;
            left: -135%;
            transform: rotate(-20deg);
        }
    }
}
</style>
