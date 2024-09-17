import request from '@/utils/request'

let SeckillApi = {
  /*秒杀活动列表*/
  activeList(data, errorback) {
    return request._post('/shop/plus.seckill.Active/index', data, errorback);
  },
  /*添加秒杀活动*/
  addActive(data, errorback) {
    return request._post('/shop/plus.seckill.Active/add', data, errorback);
  },
  /*获取秒杀活动数据*/
  getActive(data, errorback) {
    return request._get('/shop/plus.seckill.Active/edit', data, errorback);
  },
  /*保存秒杀活动*/
  saveActive(data, errorback) {
    return request._post('/shop/plus.seckill.Active/edit', data, errorback);
  },
  /*删除秒杀活动*/
  delActive(data, errorback) {
    return request._post('/shop/plus.seckill.Active/delete', data, errorback);
  },
  /*查看商品*/
  seeActive(data, errorback) {
    return request._post('/shop/plus.seckill.Active/see', data, errorback);
  },

  /*添加商品*/
  addProduct(data, errorback) {
    return request._post('/shop/plus.seckill.Product/add', data, errorback);
  },
  /*获取活动已选商品*/
  selectProduct(data, errorback) {
    return request._post('/shop/plus.seckill.Product/selectProduct', data, errorback);
  },
  /*秒杀商品详情*/
  detail(data, errorback) {
    return request._post('/shop/plus.seckill.Product/detail', data, errorback);
  },
  /*保存秒杀商品*/
  saveData(data, errorback) {
    return request._post('/shop/plus.seckill.Product/saveData', data, errorback);
  },
  delProduct(data, errorback) {
    return request._post('/shop/plus.seckill.Product/delete', data, errorback);
  },

  /*保存设置*/
  saveSetting(data, errorback) {
    return request._post('/shop/plus.seckill.Setting/index', data, errorback);
  },
  /*获取设置*/
  getSetting(data, errorback) {
    return request._get('/shop/plus.seckill.Setting/index', data, errorback);
  },
  /*获取设置*/
    getDiySkill(data, errorback) {
    return request._post('/shop/plus.seckill.Active/getDiyProduct', data, errorback);
},
}
export default SeckillApi;
