import axios from "axios";

// 获取配置
export function getSite() {
    return axios({
        url: "/index/nav?app_id=10001",
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
