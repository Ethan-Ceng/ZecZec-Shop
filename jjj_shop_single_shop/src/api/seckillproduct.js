import request from '@/utils/request'

let SeckillProductApi = {

  /*添加秒杀商品*/
  addProduct(data, errorback) {
    return request._post('/shop/plus.seckill.product/add', data, errorback);
  },
  /*秒杀商品列表*/
  productList(data, errorback) {
    return request._post('/shop/plus.seckill.product/index', data, errorback);
  },
  /*秒杀商品详情*/
 detail(data, errorback) {
    return request._post('/shop/plus.seckill.product/detail', data, errorback);
  },
  
  /*修改秒杀商品*/
  saveData(data, errorback) {
     return request._post('/shop/plus.seckill.product/saveData', data, errorback);
   },
   /*删除秒杀商品*/
   deleteProduct(data, errorback) {
      return request._post('/shop/plus.seckill.product/delete', data, errorback);
    },

}
export default SeckillProductApi;
