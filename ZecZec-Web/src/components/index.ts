import { App } from 'vue';
// 全域性元件註冊
import SvgIcon from './svg-icon/index.vue'

export default {
  install(Vue: App) {
    Vue.component('SvgIcon', SvgIcon);
  },
};
