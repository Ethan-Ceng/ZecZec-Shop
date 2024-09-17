<script setup lang="ts">
import {useUserStore} from "@/store";
import {storeToRefs} from "pinia";
const userStore = useUserStore()
import FallbackImg from '@/assets/image/fallback.jpg'
import get from "lodash/get";

const {userDetail} = storeToRefs(userStore);

</script>

<template>
  <div class="mb-4 lg:py-8 p-4 cf">
    <div class="max-w-3xl mx-auto">
      <div class="flex flex-nowrap flex-row xs:flex-col mt-4">
        <img class="mx-auto block round self-start w-32" width="128" height="128" :src="FallbackImg">
<!--        <el-avatar shape="square" :size="128" :src="FallbackImg"/>-->

        <div class="self-center pl-0 xs:pl-8 w-full mt-4 xs:mt-0">
          <h2 class="text-2xl align-middle my-0 text-center xs:text-left">
            <router-link to="/profile">{{get(userDetail, 'nickName', "")}}</router-link>
          </h2>
          <div>
            <div class="mt-4 xs:mt-0 text-center xs:text-left">
              <div class="inline-block">
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="max-w-3xl mx-auto cf">
      <table class="w-full text-xs border-t pt-8 border-neutral-200 my-8 rounded block leading-loose">
        <tbody>
        <tr>
          <td class="text-right whitespace-nowrap" width="1%">贊助 0 次</td>
          <td class="pl-4 text-left">
            <div class="w-full bg-zinc-100">
              <div class="max-w-full align-middle bg-yellow-400" style="min-width: 3px; width: 0.0%; height: 4px;">
                &nbsp;
              </div>
            </div>
          </td>
        </tr>
        <tr>
          <td class="text-right whitespace-nowrap" width="1%">贊助了 0 個計畫類別</td>
          <td class="pl-4 text-left">
            <div class="w-full bg-zinc-100">
              <div class="max-w-full align-middle bg-neutral-400" style="min-width: 3px; width: 0.0%; height: 4px;">
                &nbsp;
              </div>
            </div>
          </td>
        </tr>
        <tr>
          <td class="text-right whitespace-nowrap" width="1%">推薦過 0 個朋友贊助</td>
          <td class="pl-4 text-left">
            <div class="w-full bg-zinc-100">
              <div class="max-w-full align-middle bg-sky-600" style="min-width: 3px; width: 0.0%; height: 4px;">&nbsp;
              </div>
            </div>
          </td>
        </tr>
        <tr>
          <td class="text-right whitespace-nowrap" width="1%">加入嘖嘖 2 天</td>
          <td class="pl-4 text-left">
            <div class="w-full bg-zinc-100">
              <div class="max-w-full align-middle bg-red-400"
                   style="min-width: 3px; width: 0.08908060146655292%; height: 4px;">&nbsp;
              </div>
            </div>
          </td>
        </tr>
        </tbody>
      </table>
      <nav class="flex text-sm text-center mt-4 justify-center">
        <a class="py-2 px-4 inline-block border-b text-black hover:border-neutral-600 border-neutral-600"
           href="?tab=orders">
          贊助計畫
        </a>
<!--        <a class="py-2 px-4 inline-block border-b text-black hover:border-neutral-600 border-neutral-200"-->
<!--           href="?tab=projects">-->
<!--          發起計畫-->
<!--        </a>-->
      </nav>
    </div>
    <div class="max-w-3xl bg-zinc-100 px-2 xs:px-0 py-8 text-center rounded mx-auto mt-8">
      {{get(userDetail, 'nickName', "")}}還沒贊助過計畫。
    </div>
    <div class="text-center"></div>
  </div>
</template>

<style scoped lang="scss">
</style>
