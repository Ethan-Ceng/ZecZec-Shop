<script setup lang="ts">
import {sendMessage} from "@/api/message.ts";
import {ElMessage, FormInstance, FormRules} from "element-plus";
import useLoading from "@/hooks/loading.ts";
import {onMounted, reactive, ref} from "vue";
import get from "lodash/get";
import {useRoute, useRouter} from "vue-router";

const route = useRoute()
const router = useRouter()

const {loading, setLoading} = useLoading();
const productId = ref(null)
const orderId = ref(null)
const content = ref(null)

const sendMsg = async () => {
  setLoading(true)
  try {
    let params = {
      product_id: productId.value,
      order_id: orderId.value,
      content: content.value
    }
    await sendMessage(params)
    ElMessage.success("傳送成功")
    router.replace('/message')
  } catch (e) {
  } finally {
    setLoading(false)
  }
}

onMounted(() => {
  const p_id = get(route, 'params.product_id')
  const o_id = get(route, 'params.order_id')
  if (p_id) {
    productId.value = p_id
  }

  if (o_id) {
    orderId.value = o_id
  }
})

</script>

<template>
  <div class="max-w-md mx-auto xs:my-16 p-8 bg-zinc-100 xs:rounded">
    <el-form class="new_message" label-position="top" size="large">
      <!--      <div class="font-bold mb-8">-->
      <!--        收件人-->
      <!--        <img class="w-8 round mx-2 align-middle" src="https://assets.zeczec.com/users/143175_square.jpg?1675389855">-->
      <!--        <a href="https://www.zeczec.com/users/uanuan">源源鋼藝 uanuan</a>-->
      <!--      </div>-->

      <el-form-item label="訊息內容">
        <el-input v-model="content" type="textarea"/>
      </el-form-item>

      <div class="mt-4">
        <el-button
          @click="sendMsg"
          class="button button-s text-zec-green bg-white">
          送出訊息
        </el-button>
      </div>
    </el-form>
  </div>
</template>

<style scoped lang="scss">

</style>
