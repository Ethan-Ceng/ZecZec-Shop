import {defineStore} from 'pinia';
import {ElNotification} from 'element-plus'
import type {RouteRecordNormalized} from 'vue-router';
import {getCategory} from "@/api/product";

// import { getMenuList } from '@/api/user';
interface AppState {
  device: string;
  category: []
}

const useAppStore = defineStore('app', {
  state: (): AppState => ({
    device: "desktop",
    categoryList: [],
  }),

  getters: {
    appCurrentSetting(state: AppState): AppState {
      return {...state};
    },
    appDevice(state: AppState) {
      return state.device;
    },
  },

  actions: {
    // Update app settings
    updateSettings(partial: Partial<AppState>) {
      // @ts-ignore-next-line
      this.$patch(partial);
    },

    toggleDevice(device: string) {
      this.device = device;
    },
    toggleMenu(value: boolean) {
      this.hideMenu = value;
    },
    // 獲取分類資料，如果不存在則請求介面
    async getCategory() {
      if (this.categoryList.length > 0) {
        // 如果字典資料已經存在，則直接返回
        return this.categoryList;
      } else {
        try {
          // 假設 fetchDictionary 是一個非同步函式，用於從介面獲取資料
          const {data} = await getCategory();
          let list = []
          if (data.list) {
            list = data.list
          }
          this.dictionary = list; // 儲存到 state 中
          return list;
        } catch (error) {
          console.error('Error fetching dictionary:', error);
          throw error;
        }
      }
    },
  },
});

export default useAppStore;
