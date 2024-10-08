import axios from "axios";
import type {AxiosRequestConfig, AxiosResponse} from "axios";
import {ElMessage, ElMessageBox} from "element-plus";
import {useUserStore} from "@/store";
import {getToken} from "@/utils/auth";

export interface HttpResponse<T = unknown> {
  msg: string;
  code: number;
  data: T;
}

if (import.meta.env.VITE_API_BASE_URL) {
  axios.defaults.baseURL = import.meta.env.VITE_API_BASE_URL;
}

axios.interceptors.request.use(
  (config: AxiosRequestConfig) => {
    // let each request carry token
    // this example using the JWT token
    // Authorization is a custom headers key
    // please modify it according to the actual situation
    config.headers["appId"] = 10001;

    const token = getToken();
    if (token) {
      if (!config.headers) {
        config.headers = {};
      }
      // config.headers.Authorization = `Bearer ${token}`;
      config.headers["token"] = token;
    }
    // 新增app id
    // 根據請求方法設定不同的 app_id
    if (config.method.toUpperCase() === "GET") {
      config.params = config.params || {};
      config.params.app_id = 10001; // 為 GET 請求設定特定的 app_id
      config.params.token = token; // 為 GET 請求設定特定的 app_id
    } else if (config.method.toUpperCase() === "POST") {
      config.data = config.data || {};
      config.data.app_id = 10001; // 為 POST 請求設定特定的 app_id
      config.data.token = token; // 為 POST 請求設定特定的 app_id
      if (config.headers["Content-Type"] === "multipart/form-data") {
        // 为 POST 请求设置特定的 app_id 和 token
        config.data.append("app_id", 10001);
        config.data.append("token", token);
      }
    }
    return config;
  },
  (error) => {
    // do something
    return Promise.reject(error);
  },
);
// add response interceptors
axios.interceptors.response.use(
  (response: AxiosResponse<HttpResponse>) => {
    const res = response.data;
    // if the custom code is not 20000, it is judged as an error.
    if (res.code !== 1) {
      ElMessage.error(res.msg || "Error");
      // 50008: Illegal token; 50012: Other clients logged in; 50014: Token expired;
      if ([401].includes(res.code) && response.config.url !== "/api/user/info") {
        ElMessageBox.confirm(
          "您未登錄, 您可以取消保持在當前頁面, 或去登入。",
          "確認登出",
          {
            confirmButtonText: "重新登入",
            cancelButtonText: "取消",
            type: "warning",
          },
        )
          .then(async () => {
            const userStore = useUserStore();

            await userStore.logout();
            // window.location.reload();
          })
          .catch(() => {
            console.log('cancel')
          });
      }
      return Promise.reject(new Error(res.msg || "Error"));
    }
    return res;
  },
  (error) => {
    ElMessage.error(error.msg || "Request Error");
    return Promise.reject(error);
  },
);
