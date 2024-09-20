import axios from "axios";

// 获取配置
export function getSite() {
    return axios({
        url: "/index/loginSetting",
        method: "get",
    });
}

// 获得地区
export function getRegion() {
    return axios({
        url: "/settings/getRegion",
        method: "get",
    });
}

// 上传图片
export function uploadImage(formData) {
    return axios({
        url: "/file.upload/image",
        method: "post",
        data: formData,
        headers: {
            "Content-Type": "multipart/form-data",
            uploadImg: true,
        },
    });
}

// 隱私協議 用戶協議
export function getPolicy() {
  return axios({
    url: "/user.userapple/policy",
    method: "get",
  });
}
