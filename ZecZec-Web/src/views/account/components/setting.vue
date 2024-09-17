<script setup lang="ts">
import {computed, onMounted, reactive, ref} from "vue";
import {useUserStore} from "@/store";
import {storeToRefs} from "pinia";
import {ElMessage, FormInstance} from "element-plus";
import UploadImage from '@/components/upload-image/index.vue'
import {getRegion} from "@/api/common";
import get from "lodash/get";
import {updateInfo} from "@/api/user";


const userStore = useUserStore()

const {userDetail} = storeToRefs(userStore);


// 下單資料
interface RuleForm {
  nickName: string
  real_name: string
  email: string
  city_id: string
  region_id: string
  detail: string
  postcode: string
  name: string
  phone: string
  remark: string
  avatarUrl: string
  gender: number
  birthday: string
  intro: string
}

const formStateRef = ref<FormInstance>()

const formState = reactive<RuleForm>({
  nickName: '',
  real_name: '',
  email: '',
  city_id: '', // 城市
  region_id: '', // 街道
  detail: '', // 詳細地址
  postcode: '',
  name: '', // 聯係人
  phone: '', // 手機號
  remark: '',
  avatarUrl: '', // 頭像
  gender: 2,
  birthday: "",
  intro: '',
})


const regionList = ref([]) // 行政地區
const provinceList = ref([]) // 國家

const fetchRegion = async () => {
  try {
    const {data} = await getRegion();

    if (data && data.regionData) {
      regionList.value = data.regionData || []
      provinceList.value = data.regionData[0] || []
      // 設定預設地址
      if (userDetail.value.addressDefault) {
        formState.province_id = get(userDetail.value, 'addressDefault.province_id')
        formState.city_id = get(userDetail.value, 'addressDefault.city_id')
        formState.region_id = get(userDetail.value, 'addressDefault.region_id')
        formState.detail = get(userDetail.value, 'addressDefault.detail')
        formState.name = get(userDetail.value, 'addressDefault.name')
        formState.phone = get(userDetail.value, 'addressDefault.phone')
      }
    }

    formState.real_name = get(userDetail.value, 'real_name')
    formState.nickName = get(userDetail.value, 'nickName')
    formState.email = get(userDetail.value, 'email')
    formState.avatarUrl = get(userDetail.value, 'avatarUrl')
    formState.gender = get(userDetail.value, 'gender')
    formState.birthday = get(userDetail.value, 'birthday')
    formState.intro = get(userDetail.value, 'intro')

  } catch (err) {
    // console.warn(err)
  }
}

const cityList = computed(() => {
  const provinceIndex = provinceList.value.findIndex(item => item.value === formState.province_id)
  if (provinceIndex !== -1) {
    return get(regionList.value, `[1][${provinceIndex}]`) || []
  }
  return []
})

const districtList = computed(() => {
  const provinceIndex = provinceList.value.findIndex(item => item.value === formState.province_id)
  if (provinceIndex !== -1) {
    const cityArray = get(regionList.value, `[1][${provinceIndex}]`) || []

    const cityIndex = cityArray.findIndex(item => item.value === formState.city_id)
    if (cityIndex !== -1) {
      const reginArray = get(regionList.value, `[2][${provinceIndex}][${cityIndex}]`) || []
      return reginArray
    }
  }
  return []
})


onMounted(() => {
  fetchRegion()
})


const submitForm = async (formEl: FormInstance | undefined) => {
  if (!formEl) return;
  await formEl.validate((valid, fields) => {
    if (valid) {
      console.log("submit!");
      fetchRegister({...userDetail.value, ...formState});
    } else {
      console.log("error submit!", fields);
    }
  });
};

const fetchRegister = async (params) => {
  const {code} = await updateInfo(params);
  if (code === 1) {
    ElMessage.success("個人信息更新成功！");
    await userStore.fetchUserInfo()
  }
};
</script>

<template>
  <div class="container p-4 lg:px-0 cf">
    <h2 class="inline-block text-2xl font-bold mr-4">個人設定</h2>
    <!--    <a class="mt-1 text-zec-blue" href="/users/chen-xian-sen">[檢視個人頁面]</a>-->
  </div>

  <div class="container">
    <div class="cf mb-16">

      <el-form
        class="text-sm"
        ref="formStateRef"
        size="large"
        :model="formState"
        label-position="top"
      >

        <div class="cf xs:-mx-4">
          <div class="w-full float-left px-4 xs:w-3/10">
            <div class="cf">
              <img class="w-full round mb-4" style="object-fit: contain" :src="formState.avatarUrl">
              <label class="font-bold" for="user_avatar">顯示圖片</label>
              <div class="mb-4">
                <UploadImage v-model="formState.avatarUrl" />
              </div>
              <input type="submit" name="commit" value="連結 Facebook"
                     class="font-bold bg-fb text-white hover:text-white py-3 block w-full text-center rounded-full hover:brightness-90 hover:shadow mb-8"
                     form="facebook-connect-form" data-disable-with="處理中...">
            </div>
          </div>
          <div class="w-full float-left px-4 xs:w-7/10">
            <div class="mb-4">
              <el-form-item label="名稱" prop="nickName">
                <el-input v-model="formState.nickName" maxlength="50" show-word-limit></el-input>
              </el-form-item>
            </div>
            <div class="mb-4">
              <el-form-item label="真實身分/名稱" prop="real_name">
                <el-input v-model="formState.real_name" maxlength="50" show-word-limit></el-input>
              </el-form-item>
            </div>
            <div class="mb-4">
              <el-form-item label="電子信箱" prop="email">
                <el-input v-model="formState.email"></el-input>
                <div class="mt-1 font-bold text-xs">信箱已驗證成功。若修改信箱，新信箱須重新驗證後才會儲存。</div>
              </el-form-item>
            </div>
            <!--            <div class="rounded my-8 border-neutral-200">-->
            <!--              <el-form-item class="mt-4" prop="consented">-->
            <!--                <el-checkbox v-model="formState.consented">-->
            <!--                  <div class="text-sm">允許資訊通知 - 如：計畫更新、站內訊息</div>-->
            <!--                  <div class="ml-1">訂閱嘖嘖電子報</div>-->
            <!--                </el-checkbox>-->
            <!--              </el-form-item>-->
            <!--            </div>-->
            <div class="mb-4">
              <el-form-item label="性別" prop="gender">
                <el-radio-group v-model="formState.gender">
                  <el-radio :value="1">男</el-radio>
                  <el-radio :value="0">女</el-radio>
                  <el-radio :value="2">其他</el-radio>
                </el-radio-group>
              </el-form-item>
            </div>
            <div class="mb-4">
              <el-form-item label="生日" prop="birthday">
                <el-date-picker v-model="formState.birthday" format="YYYY-MM-DD">
                </el-date-picker>
              </el-form-item>
            </div>
            <div class="flex mb-4">
              <div class="mt-4 flex-auto">
                <el-form-item label="來自" prop="province_id">
                  <el-select
                    v-model="formState.province_id"
                    placeholder="請選擇收件地點"
                  >
                    <el-option
                      v-for="item in provinceList"
                      :key="item.value"
                      :label="item.label"
                      :value="item.value"
                    />
                  </el-select>
                </el-form-item>
              </div>
              <div class="mt-4 flex-auto pl-4">
                <el-form-item label="縣市" prop="city_id">
                  <el-select
                    v-model="formState.city_id"
                    placeholder="請選擇縣市"
                  >
                    <el-option
                      v-for="item in cityList"
                      :key="item.value"
                      :label="item.label"
                      :value="item.value"
                    />
                  </el-select>
                </el-form-item>
              </div>

              <div class="mt-4 flex-auto pl-4">
                <el-form-item label="鄉鎮市區" prop="region_id">
                  <el-select
                    v-model="formState.region_id"
                    placeholder="請選擇鄉鎮市區"
                  >
                    <el-option
                      v-for="item in districtList"
                      :key="item.value"
                      :label="item.label"
                      :value="item.value"
                    />
                  </el-select>
                </el-form-item>
              </div>
            </div>
            <!--        <div class="mb-4">-->
            <!--          <label class="text-sm font-bold mb-2" for="user_slug">網址名稱</label>-->
            <!--          <input required="required" pattern="[a-zA-Z][a-zA-Z-_0-9]+" title="名稱必須以英文字母開頭，並且不包含 -, _ 以外的符號。" class="zec mb-2 rounded border-gray-300 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600" type="text" value="chen-xian-sen" name="user[slug]" id="user_slug">-->
            <!--          <div class="text-neutral-600 text-sm mb-4">-->
            <!--            你的個人頁面網址 - 目前為-->
            <!--            <span class="underline">-->
            <!--              https://www.zeczec.com/users/<span class="font-bold">chen-xian-sen</span></span>-->
            <!--            。-->
            <!--          </div>-->
            <!--          <p class="text-zec-red font-bold mt-0 text-sm">-->
            <!--          </p>-->
            <!--        </div>-->
            <!--            <div class="mb-4">-->
            <!--              <el-form-item label="Facebook 網址" prop="facebook_url">-->
            <!--                <el-input v-model="formState.facebook_url"></el-input>-->
            <!--              </el-form-item>-->
            <!--            </div>-->
            <!--            <div class="mb-4">-->
            <!--              <el-form-item label="Instagram 網址" prop="instagram_url">-->
            <!--                <el-input v-model="formState.instagram_url"></el-input>-->
            <!--              </el-form-item>-->
            <!--            </div>-->
            <!--            <div class="mb-4">-->
            <!--              <el-form-item label="YouTube 網址" prop="youtube_url">-->
            <!--                <el-input v-model="formState.youtube_url"></el-input>-->
            <!--              </el-form-item>-->
            <!--            </div>-->
            <!--            <div class="mb-4">-->
            <!--              <el-form-item label="網站網址" prop="website_url">-->
            <!--                <el-input v-model="formState.website_url"></el-input>-->
            <!--              </el-form-item>-->
            <!--            </div>-->
            <div class="mb-4">
              <el-form-item label="個人介紹" prop="intro">
                <el-input type="textarea" v-model="formState.intro"></el-input>
              </el-form-item>

<!--              <div class="text-neutral-600 text-sm">接受-->
<!--                <a class="muted" target="_blank" href="https://zh.wikipedia.org/zh-tw/Markdown">Markdown</a>-->
<!--                語法。-->
<!--              </div>-->
            </div>
            <div class="mt-8">
              <el-button
                type="success"
                round
                size="large"
                class="w-full mb-8"
                @click="submitForm(formStateRef)"
              >
                更新
              </el-button>
            </div>
          </div>
        </div>
      </el-form>
    </div>
  </div>
</template>

<style scoped lang="scss">

</style>
