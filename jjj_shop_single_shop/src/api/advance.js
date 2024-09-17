import request from '@/utils/request'

let AdvanceApi = {
  /*商品列表*/
  productList(data, errorback) {
    return request._post('/shop/plus.advance.Product/index', data, errorback);
  },
  /*添加商品*/
  getProduct(data, errorback) {
    return request._get('/shop/plus.advance.Product/add', data, errorback);
  },
  /*添加商品*/
  addProduct(data, errorback) {
    return request._post('/shop/plus.advance.Product/add', data, errorback);
  },
  /*预售商品详情*/
  detail(data, errorback) {
    return request._get('/shop/plus.advance.Product/edit', data, errorback);
  },
  /*保存预售商品*/
  saveData(data, errorback) {
    return request._post('/shop/plus.advance.Product/edit', data, errorback);
  },
  delProduct(data, errorback) {
    return request._post('/shop/plus.advance.Product/delete', data, errorback);
  },
  /*保存设置*/
  saveSetting(data, errorback) {
    return request._post('/shop/plus.advance.Setting/index', data, errorback);
  },
  /*获取设置*/
  getSetting(data, errorback) {
    return request._get('/shop/plus.advance.Setting/index', data, errorback);
  },
  /*获取设置*/
  getDiySkill(data, errorback) {
    return request._post('/shop/plus.advance.Product/getDiyProduct', data, errorback);
  },
}
export default AdvanceApi;
