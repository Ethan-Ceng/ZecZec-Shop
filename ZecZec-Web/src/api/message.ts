import axios from 'axios';

/**
 * 傳送訊息
 * @param data
 */
export function getMessageList() {
  return axios({
    url: '/message.Message/lists',
    method: 'get',
  })
}



/**
 * 傳送訊息
 * @param data
 */
export function sendMessage(data) {
  return axios({
    url: '/message.Message/sendMessage',
    method: 'post',
    data
  })
}


/**
 * 傳送訊息
 * @param data
 */
export function getMessageBoxDetail(message_box_id) {
  return axios({
    url: '/message.Message/getMessageBoxDetail',
    method: 'get',
    params: {message_box_id}
  })
}

