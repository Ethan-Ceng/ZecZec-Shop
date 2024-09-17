<script setup lang="ts">
import {onMounted, reactive} from "vue";
import FallbackImg from "@/assets/image/fallback.jpg";
import {userAgent, userAgentCenter} from "@/api/user.ts";
import get from "lodash/get";

const formState = reactive({
  email: '',
  password: '',
})


const fetchUserAgent = async () => {
  try {
    const {code, data} = await userAgent();

    if (code === 1&& data.is_agent) {
      await fetchAgentInfo()
    }


  } catch (err) {
    // console.warn(err)
  }
}


const fetchAgentInfo = async () => {
  try {
    const {code, data} = await userAgentCenter();

    if (code === 1) {
      console.log(data)
    }


  } catch (err) {
    // console.warn(err)
  }
}

onMounted(() => {
  fetchUserAgent()
})

</script>

<template>
  <div class="tab-pane-main px-4 lg:px-0 mt-4 mb-6">
    <div class="product">
      <h2 class="flex text-2xl">團隊成員</h2>

      <div class="container pb-16">
        <!-- 成員列表 -->
        <div class="border-t py-2 border-neutral-200">
          <div class="float-right">
            <div class="inline-block leading-none bg-zinc-100 rounded-sm p-1 mr-4 text-xs uppercase font-bold">
              尚未接受邀請
            </div>
            <form class="inline-block" id="edit_membership_13579" action="/memberships/13579" accept-charset="UTF-8"
                  method="post"><input type="hidden" name="_method" value="delete" autocomplete="off"><input
              type="hidden" name="authenticity_token"
              value="5Oy-UqV_3Ex_B20CA8LxyCJ004rceLADDbTJld9Zl_RLuiIqXBT4DFujVrtMgEZbGU4I3j2FmKL5lVzKgB4jYg"
              autocomplete="off"><input autocomplete="off" type="hidden" value="13579" name="membership[id]"
                                        id="membership_id">
              <input type="submit" name="commit" value="取消邀請" class="button-text text-xs text-zec-red pointer"
                     data-confirm="確定要取消邀請嗎？" data-disable-with="取消邀請">
            </form>
          </div>
          <a href="/users/sultanaparvinazadi">
            <img class="round w-8 h-8 bg-zinc-100 inline-block align-middle mr-1" alt="" :src="FallbackImg">
            sultanaparvinazadi
          </a>
        </div>
        <!-- 邀請成員 -->
        <div class="rounded mt-4 border-neutral-200 border p-4">
          <p class="pl-4 mt-0 border-l-8 border-orange-500">
            團隊成員可以修改此帳號所提案的計畫內容、回饋、推薦連結和常見問答，以及釋出更新。<b>不能「聯絡贊助人」或「下載贊助名單」。</b><br>
            公開成員會出現在<a href="/users/chen-xian-sen">團隊的個人頁面</a>上。
          </p>
          <el-form class="flex mt-4" :model="formState" label-width="auto" label-position="top">
            <el-form-item class="flex-1" label="邀請成員 Email">
              <el-input v-model="formState.email"/>
            </el-form-item>

            <div class="pr-2 flex-auto mt-1 hidden">
              <label for="membership_title">稱謂</label>
              <input class="mb-0 w-full" type="text" name="membership[title]" id="membership_title">
            </div>
            <el-form-item label="公開">
              <el-checkbox></el-checkbox>
            </el-form-item>

            <div class="pr-2 flex-auto hidden mt-1">
              <label for="membership_role">角色</label>
              <input readonly="readonly" value="admin" class="mb-0 w-full" type="text" name="membership[role]"
                     id="membership_role">
            </div>
            <div class="self-end flex-none mt-1 w-16">
              <el-button>儲存</el-button>
            </div>
          </el-form>
        </div>
      </div>
    </div>

    <div class="product">
      <h2 class="flex text-2xl">所屬團隊</h2>

      <div class="container pb-16">
        <div class="rounded text-sm py-16 text-center px-4 bg-zinc-100">無。</div>
      </div>
    </div>
  </div>
</template>

<style scoped lang="scss">

</style>
