<script setup lang="ts">
import { ref, reactive } from "vue";
import { useRouter } from "vue-router";
import Footer from "@/components/footer/index.vue";
import { ElMessage, FormInstance, FormRules } from "element-plus";
import { register } from "@/api/user";
import { storeToRefs } from "pinia";
import { useAppStore } from "@/store";
import get from "lodash/get";

const router = useRouter();
const appStore = useAppStore();
const { setting } = storeToRefs(appStore);

interface RuleForm {
    nickName: string;
    email: string;
    password: string;
    confirmPassword: string;
}

const formStateRef = ref<FormInstance>();

const formState = reactive<RuleForm>({
    nickName: "",
    email: "",
    password: "",
    confirmPassword: "",
});

const rules = reactive<FormRules<RuleForm>>({
    nickName: [
        { required: true, message: "請輸入名稱", trigger: "blur" },
        {
            min: 2,
            max: 20,
            message: "Length should be 2 to 20",
            trigger: "blur",
        },
    ],
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
    const { code } = await register(params);
    if (code === 1) {
        ElMessage.success("註冊成功，請查閲郵件確認註冊！");
        router.push({ path: "/login" });
    }
};
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
                <h1 class="font-bold text-3xl mb-4">註冊</h1>
                <h4 class="mb-8">
                    已經有帳號了嗎？<router-link
                        class="text-blue-600 font-bold underline"
                        to="/login"
                        >登入</router-link
                    >。
                </h4>

                <el-form
                    ref="formStateRef"
                    size="large"
                    :model="formState"
                    :rules="rules"
                    label-position="top"
                >
                    <el-form-item label="名稱" prop="nickName">
                        <el-input v-model="formState.nickName" />
                    </el-form-item>
                    <el-form-item label="電子信箱" prop="email">
                        <el-input v-model="formState.email" />
                    </el-form-item>
                    <el-form-item label="密碼（至少六碼）" prop="password">
                        <el-input
                            type="password"
                            v-model="formState.password"
                            show-password
                        />
                    </el-form-item>
                    <el-form-item label="再次確認密碼" prop="confirmPassword">
                        <el-input
                            type="password"
                            v-model="formState.confirmPassword"
                            show-password
                        />
                    </el-form-item>

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
                        註冊
                    </el-button>

                    <p class="text-center text-gray-800 text-sm">
                        繼續進行代表你同意
                        <router-link
                            class="text-blue-600"
                            target="_blank"
                            to="/agreement"
                            >服務條款</router-link
                        >。
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
