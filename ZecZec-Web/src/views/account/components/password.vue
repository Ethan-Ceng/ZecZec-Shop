<script setup lang="ts">
import {reactive, ref} from "vue";
import {changePassword} from "@/api/user";
import {ElMessage, FormInstance, FormRules} from "element-plus";
import useLoading from "@/hooks/loading";


const {loading, setLoading} = useLoading();

const formStateRef = ref<FormInstance>()

const formState = reactive({
  password: '',
  repassword: '',
})

const validatePass2 = (rule: any, value: any, callback: any) => {
  if (value === '') {
    callback(new Error('請輸入確認密碼'))
  } else if (value !== formState.password) {
    callback(new Error("兩次密碼不一致"))
  } else {
    callback()
  }
}

const rules = reactive<FormRules<RuleForm>>({
  password: [
    {required: true, message: '請輸入密碼', trigger: 'blur'},
    {min: 6, max: 20, message: '密碼長度 6 至 20', trigger: 'blur'},
  ],
  repassword: [{validator: validatePass2, trigger: 'blur'}]
})


const fetchPassword = async (params) => {
  setLoading(true)
  try {
    await changePassword(params)
    ElMessage.success("修改成功")
    formState.password = ''
    formState.repassword = ''
  } catch (e) {
    errorMessage.value = (err as Error).msg;
  } finally {
    setLoading(false)
  }
}

const submitForm = async (formEl: FormInstance | undefined) => {
  if (!formEl) return
  await formEl.validate((valid, fields) => {
    if (valid) {
      console.log('submit!')
      fetchPassword({...formState})
    } else {
      console.log('error submit!', fields)
    }
  })
}

</script>

<template>
  <el-form
    ref="formStateRef"
    class="password-form border p-4 border-neutral-200 rounded inline-block lg:my-16 my-4 text-left"
    :model="formState"
    label-width="auto"
    label-position="top"
    :rules="rules"
  >
    <el-form-item label="密碼" prop="password">
      <el-input v-model="formState.password"/>
    </el-form-item>
    <el-form-item label="再次輸入你的新密碼" prop="repassword">
      <el-input type="password" v-model="formState.repassword" show-password/>
    </el-form-item>

    <el-button :loading="loading" type="success" size="large" @click="submitForm(formStateRef)">
      儲存
    </el-button>
  </el-form>
</template>

<style scoped lang="scss">
.password-form {
  width: 100%;
}
</style>
