import request from '@/utils/request'

let BuyApi = {
  /*保存礼包*/
  saveBuy(data, errorback) {
    return request._post('/shop/plus.buyactivity/add', data, errorback);
  },
  /*保存礼包*/
  detailBuy(data, errorback) {
    return request._get('/shop/plus.buyactivity/edit', data, errorback);
  },
  /*保存礼包*/
  editBuy(data, errorback) {
    return request._post('/shop/plus.buyactivity/edit', data, errorback);
  },
  /*列表*/
  BuyList(data, errorback) {
    return request._post('/shop/plus.buyactivity/index', data, errorback);
  },
  /*删除*/
  delBuy(data, errorback) {
    return request._post('/shop/plus.buyactivity/delete', data, errorback);
  },
}

export default BuyApi;
