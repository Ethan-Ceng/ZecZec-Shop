import axios from "axios";
import type { RouteRecordNormalized } from "vue-router";
import { UserState } from "@/store/modules/user/types";

export interface LoginData {
  email: string;
  password: string;
}

export interface LoginRes {
  token: string;
}

export function register(data: LoginData) {
  return axios.post<LoginRes>("/user.useropen/registerByEmail", data);
}

export function login(data: LoginData) {
  return axios.post<LoginRes>("/user.useropen/loginByEmail", data);
}

// 認證賬號
export function checkByEmail(data) {
  return axios({
    url: "/user.Useropen/checkByEmail",
    method: "post",
    data,
  });
}

export function logout() {
  return axios.post("/user.User/logOut");
}

// 獲取當前登入的使用者資訊
export function getUserInfo() {
  return axios.post<UserState>("/user.User/detail");
}

// 修改密碼
export function changePassword(data) {
  return axios({
    url: "/user.Useropen/changePassword",
    method: "post",
    data,
  });
}

// 忘記密碼
export function forgetPassword(data) {
  return axios({
    url: "/user.Useropen/forgetByEmail",
    method: "post",
    data,
  });
}

// 重設密碼
export function resetPassword(data) {
  return axios({
    url: "/user.Useropen/resetPasswordByEmail",
    method: "post",
    data,
  });
}

/**
 * 獲取使用者設定
 */
export function getUserSetting() {
  return axios.get("/user.index/setting");
}

/**
 * 更新使用者設定
 * @param data
 */
export function updateInfo(data) {
  return axios({
    url: "/user.user/updateInfo",
    method: "post",
    data,
  });
}

export function userAgent() {
  return axios({
    url: "/user.agent/apply",
    method: "get"
  });
}

export function userAgentCenter() {
  return axios({
    url: "/user.agent/center",
    method: "get"
  });
}

/**
 * 使用者下單時修改地址，新增成為預設地址，並返回id
 * {
 *     "name": "預設地址",
 *     "phone": "13800138000",
 *     "detail": "天安門廣場",
 *     "province_id": 1,
 *     "city_id": 2,
 *     "region_id": 3,
 *     "is_default": 1,
 *     "token": "23906f2d05b1ec40dd7f1f0d078ec661",
 *     "app_id": "10001"
 * }
 * @param data
 */
export function addAddress(data) {
  return axios({
    url: "/user.address/add",
    method: "post",
    data,
  });
}

export function editAddress(data) {
  return axios({
    url: "/user.address/edit",
    method: "post",
    data,
  });
}

export function getAddress(address_id) {
  return axios({
    url: "/user.address/detail",
    method: "post",
    params: {
      address_id: address_id,
    },
  });
}
