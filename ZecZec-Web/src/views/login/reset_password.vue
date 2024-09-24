<script setup lang="ts">
import { reactive, ref, onMounted } from "vue";
import { useRouter, useRoute } from "vue-router";
import { useUserStore } from "@/store";
import { ElMessage, FormInstance, FormRules } from "element-plus";
import Footer from "@/components/footer/index.vue";
import useLoading from "@/hooks/loading";
import get from "lodash/get";
import { resetPassword } from "@/api/user";

const route = useRoute();
const router = useRouter();

const errorMessage = ref("");
const { loading, setLoading } = useLoading();
const userStore = useUserStore();

interface RuleForm {
    password: string;
    confirmPassword: string;
}

const formStateRef = ref<FormInstance>();

const formState = reactive<RuleForm>({
    app_id: "",
    email: "",
    code: "",
    password: "",
    confirmPassword: "",
});

const rules = reactive<FormRules<RuleForm>>({
    password: [
        { required: true, message: "請輸入密碼", trigger: "blur" },
        { min: 6, max: 20, message: "密碼長度 6 至 20", trigger: "blur" },
    ],
    confirmPassword: [
        { required: true, message: "請輸入確認密碼", trigger: "blur" },
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
        const { msg } = await resetPassword(params);
        ElMessage.success(msg || "操作成功");
        await router.replace("/login");
    } catch (e) {
        errorMessage.value = (e as Error).message;
    } finally {
        setLoading(false);
    }
};

onMounted(() => {
    const app_id = get(route, "query.app_id");
    const email = get(route, "query.email");
    const code = get(route, "query.code");
    if (app_id || email || code) {
        formState.app_id = app_id;
        formState.email = email;
        formState.code = code;
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
                        <svg-icon class="mr-6 h-16" name="logo" size="80px" />
                    </h2>
                    <h3 class="inline-block">一起讓美好的事物發生</h3>
                </a>
                <h1 class="font-bold text-3xl mb-4">設定新密碼</h1>
                <h4 class="mb-8">
                    回到
                    <router-link
                        class="text-blue-600 font-bold underline"
                        to="/login"
                        >登入</router-link
                    >
                    或
                    <router-link
                        class="text-blue-600 font-bold underline"
                        to="/register"
                        >註冊帳號</router-link
                    >
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
                    <div class="mb-4">
                        <el-form-item
                            label="新密碼（至少六碼）"
                            prop="password"
                        >
                            <el-input
                                type="password"
                                v-model="formState.password"
                                show-password
                            />
                        </el-form-item>
                    </div>
                    <div class="mb-4">
                        <el-form-item
                            label="再次確認密碼"
                            prop="confirmPassword"
                        >
                            <el-input
                                type="password"
                                v-model="formState.confirmPassword"
                                show-password
                            />
                        </el-form-item>
                    </div>
                    <div class="flex justify-between items-center mb-6">
                        <el-checkbox>記住我</el-checkbox>
                        <!--              <a class="text-sm text-gray-800 hover:text-blue-600 underline" href="/users/password/new">忘記密碼？</a>-->
                    </div>
                    <el-button
                        type="success"
                        round
                        size="large"
                        class="w-full mb-8"
                        @click="submitForm(formStateRef)"
                    >
                        更改密碼
                    </el-button>
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
