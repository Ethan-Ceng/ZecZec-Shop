import { createApp } from 'vue'
import router from './router';
import store from './store';
import ElementPlus from 'element-plus'
import 'element-plus/dist/index.css'
import globalComponents from '@/components';
import App from './App.vue'

// 全域性樣式
import '@/assets/style/global.scss'
// svg icon
import 'virtual:svg-icons-register'
import '@/api/interceptor';
const app = createApp(App);

app.use(ElementPlus)
app.use(router);
app.use(store);
app.use(globalComponents);

app.mount('#app');
