<script setup lang="ts">
import { reactive, ref } from "vue";
import { useRouter } from "vue-router";
import { storeToRefs } from "pinia";
import { useAppStore, useUserStore } from "@/store";
import { ElMessage, FormInstance, FormRules } from "element-plus";
import Footer from "@/components/footer/index.vue";
import useLoading from "@/hooks/loading";
import { forgetPassword } from "@/api/user";
import get from "lodash/get";

const router = useRouter();

const errorMessage = ref("");
const { loading, setLoading } = useLoading();
const userStore = useUserStore();
const appStore = useAppStore();
const { setting } = storeToRefs(appStore);

interface RuleForm {
    email: string;
}

const formStateRef = ref<FormInstance>();

const formState = reactive<RuleForm>({
    email: "",
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
        const { msg } = await forgetPassword(params);
        ElMessage.success(msg || "登入成功");
        await router.replace("/login");
    } catch (e) {
        errorMessage.value = (e as Error).message;
    } finally {
        setLoading(false);
    }
};
</script>

<template>
    <div class="flex min-h-screen items-stretch">
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
                <h1 class="font-bold text-3xl mb-4">忘記密碼</h1>
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
                    >。
                </h4>

                <el-form
                    ref="formStateRef"
                    size="large"
                    :model="formState"
                    :rules="rules"
                    label-width="auto"
                    label-position="top"
                >
                    <el-form-item label="輸入你註冊會員的電子信箱" prop="email">
                        <el-input v-model="formState.email" />
                    </el-form-item>

                    <el-button
                        :loading="loading"
                        type="success"
                        round
                        size="large"
                        class="w-full mb-8"
                        @click="submitForm(formStateRef)"
                    >
                        重設密碼
                    </el-button>
                    <p class="text-center text-gray-800 text-sm">
                        完成後，請依密碼重設信執行即可登入。
                    </p>
                </el-form>
            </div>
        </div>
    </div>
    <Footer />
</template>

<style scoped lang="scss">
.gallery {
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
</style>
