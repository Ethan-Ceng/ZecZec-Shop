<script setup lang="ts">
import {onMounted, ref} from "vue";
import Header from '@/components/header/full-width.vue'
import {getMessageList, sendMessage, getMessageBoxDetail} from "@/api/message.ts";
import useLoading from "@/hooks/loading.ts";
import get from 'lodash/get';
import {ElMessage} from "element-plus";


const {loading, setLoading} = useLoading();

const faqList = ref([])
const fetchMsgList = async () => {
  setLoading(true)
  try {
    const {data} = await getMessageList()

    if (data && data.list) {
      // 獲取屬性值，作為套餐名稱
      faqList.value = data.list || []
    }

  } catch (err) {
    // console.warn(err)
  } finally {
    setLoading(false)
  }
}

const messageList = ref([])
const getMessageAllList = async (boxId) => {
  setLoading(true)
  try {
    const {data} = await getMessageBoxDetail(boxId)

    if (data) {
      // 獲取屬性值，作為套餐名稱
      messageList.value = data || []
    }

  } catch (err) {
    // console.warn(err)
  } finally {
    setLoading(false)
  }
}



const sendMsg = async () => {
  setLoading(true)
  try {
    let params = {
      message_box_id: messageBoxId.value,
    }
    await sendMessage(params)
    ElMessage.success("傳送成功")
  } catch (e) {
  } finally {
    setLoading(false)
  }
}

onMounted(() => {
  fetchMsgList()
})
</script>

<template>
  <Header/>
  <div class="flex flex-nowrap js-inbox" style="height: calc(100vh - 5em)">
    <div
      class="js-details reset-details w-full xs:w-auto xs:rounded-sm border-neutral-200 absolute xs:relative lg:w-1/4"
      open="open">
      <div class="js-summary inline-block block xs:hidden p-4 z-10 relative">
        <i class="material-icons">chevron_left</i>
      </div>
      <div class="xs:block bg-white -mt-16 xs:mt-0 h-full z-30 relative overflow-auto xs:w-16 lg:w-full border-r"
           style="height: calc(100vh - 5em)">
        <div class="border-b p-4 border-zinc-100 xs:hidden lg:block">
          <div class="button-group">
            <span class="button-s button text-white hover:text-white bg-zec-blue" >
              未讀 (0)
            </span>
            <span class="button-s button text-white hover:text-white bg-neutral-400">
              全部
            </span>
          </div>
        </div>
        <div v-if="faqList.length === 0" class="text-sm text-neutral-600 p-4">沒有對話。</div>
        <template v-for="item in faqList" :key="item.message_box_id">
<!--          bg-zinc-100-->
          <span @click="getMessageAllList(item.message_box_id)" class="block border-b flex border-zinc-100 p-4 hover:bg-zinc-100 js-conversation-link js-summary">
            <img class="w-8 h-8 round float-left self-center bg-neutral-200" :src="get(item, 'user.avatarUrl')">
            <div class="flex-1 block xs:hidden lg:block pl-4 overflow-hidden">
              <div class="text-xs float-right text-neutral-600">不到一分鐘前</div>
              <div class="truncate mr-2 text-black">{{get(item, 'user.nickName')}}</div>
              <div class="truncate text-neutral-600">
                <i class="material-icons text-neutral-600 text-sm">reply</i>
                halo
              </div>
            </div>
          </span>
        </template>

      </div>
    </div>
<!--    <div class="flex-auto mv-child-0 js-thread">-->
<!--      <div class="flex h-full w-full">-->
<!--        <div class="self-center text-center w-full text-neutral-600 text-sm">選擇一個對話</div>-->
<!--      </div>-->
<!--    </div>-->

    <div class="flex-auto mv-child-0 js-thread">
      <div class="flex flex-nowrap flex-col h-full relative">
        <div class="border-b border-zinc-100 p-4 w-full text-center flex flex-nowrap items-center">
          <div class="w-16"></div>
          <h2 class="flex-auto text-sm my-0 text-center relative z-10">和<span>Tech lab</span>的對話</h2>
          <div class="text-right w-16 relative z-10"></div>
        </div>
        <div class="hidden absolute right-0 top-0 z-10 text-center disabled js-next-page-spinner p-4">
          <i class="material-icons rotate text-2xl text-neutral-200">donut_large</i>
        </div>
        <div class="overflow-auto relative w-full js-message-screen grow">
          <div v-for="item in messageList" class="p-4 text-right">
            <div class="ml-16"><div class="py-2 mv-child-0 ml-4 px-4 lg:max-w-lg max-w-full border-neutral-200 rounded inline-block border text-left">
              <p>{{ item.content }}</p>
            </div><br>
              <div class="ml-4 text-xs text-neutral-600 tooltip help inline-block tooltip-l" data-title="2024/09/12  03:09">
                {{item.create_time}}
              </div>
            </div>
          </div>
        </div>
        <div class="border-t border-zinc-100">
          <el-form class="js-new-message" >

            <div class="flex p-4 border-t border-zinc-100">
              <textarea required="required" class="h-8 mr-4 flex-auto mb-0 js-oneline-textarea text-base" name="message[content]" id="message_content"></textarea>
              <input type="submit" name="commit" value="送出訊息" class="button button-s text-zec-green mb-0" data-disable-with="傳送中">
            </div>
          </el-form>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped lang="scss">

</style>
