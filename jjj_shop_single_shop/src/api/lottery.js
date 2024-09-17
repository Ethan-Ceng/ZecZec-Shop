import request from '@/utils/request'

let LotteryApi = {
  /*获取数据*/
  getData(data, errorback) {
    return request._post('/shop/plus.lottery/getLottery', data, errorback);
  },
  /*编辑*/
  EditLottery(data, errorback) {
    return request._post('/shop/plus.lottery/setting', data, errorback);
  },
  /*列表*/
  recordList(data, errorback) {
    return request._post('/shop/plus.lottery/record', data, errorback);
  },
  /*列表*/
  recordList(data, errorback) {
    return request._post('/shop/plus.lottery/record', data, errorback);
  },
  getAward(data, errorback) {
    return request._post('/shop/plus.lottery/award', data, errorback);
  },
}

export default LotteryApi;
