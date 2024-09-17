import axios from 'axios';


export function getRegion() {
  return axios({
    url: '/settings/getRegion',
    method: 'get',
  })
}


export function uploadImage(formData) {
  return axios({
    url: '/file.upload/image',
    method: 'post',
    data: formData,
    headers: {
      "Content-Type": "multipart/form-data",
      "uploadImg": true,
    }
  })
}



