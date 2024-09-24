import axios, { AxiosError } from "axios";
import { ElMessage } from "element-plus";
import storage from "@/utils/storage";

export interface Result<T = any> {
    code: number;
    data: T;
    msg: string;
}

const instance = axios.create({
    baseUrl: "/api",
    timeout: 8000,
    timeoutErrorMessage: "Network Timeout.",
    withCredentials: true,
});

// 請求攔截器
instance.interceptors.request.use(
    (config) => {
        const token = storage.get("www_token");
        if (token) {
            config.headers.Authorization = "Token::" + token;
        }
        return {
            ...config,
        };
    },
    (error: AxiosError) => {
        return Promise.reject(error);
    },
);

// 響應攔截
instance.interceptors.response.use(
    (response) => {
        const data: Result = response.data;
        if (data.code === 50001) {
            ElMessage.error(data.msg);
            storage.remove("www_token");
            location.href = "/login";
        } else if (data.code != 0) {
            if (response.config.showError === false) {
                return Promise.resolve(data);
            } else {
                ElMessage.error(data.msg);
                return Promise.reject(data);
            }
        }
        return data.data;
    },
    (error) => {
        ElMessage.error(error.message);
        return Promise.reject(error.message);
    },
);

interface IConfig {
    showLoading: boolean;
    showError: boolean;
}

export default {
    get<T>(url: string, params?: object, options?: IConfig): Promise<T> {
        return axios.get(url, { params, ...options });
    },
    post<T>(url: string, params?: object, options?: IConfig): Promise<T> {
        return axios.post(url, params, options);
    },
};
