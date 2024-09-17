import request from '@/utils/request'

let AssembleApi = {


  /*拼团活动列表*/
  activeList(data, errorback) {
    return request._post('/shop/plus.assemble.active/index', data, errorback);
  },
  /*添加拼团活动*/
  addActive(data, errorback) {
    return request._post('/shop/plus.assemble.active/add', data, errorback);
  },
  /*获取拼团活动数据*/
  editActive(data, errorback) {
    return request._get('/shop/plus.assemble.active/edit', data, errorback);
  },
  /*保存拼团活动*/
  saveActive(data, errorback) {
    return request._post('/shop/plus.assemble.active/edit', data, errorback);
  },
  /*删除拼团活动*/
  delActive(data, errorback) {
    return request._post('/shop/plus.assemble.active/delete', data, errorback);
  },
  /*获取活动已选商品*/
  selectProduct(data, errorback) {
    return request._post('/shop/plus.assemble.product/selectProduct', data, errorback);
  },
  /*拼团商品详情*/
  detail(data, errorback) {
    return request._post('/shop/plus.assemble.product/detail', data, errorback);
  },
  /*保存拼团商品*/
  saveData(data, errorback) {
    return request._post('/shop/plus.assemble.product/saveData', data, errorback);
  },
  delProduct(data, errorback) {
    return request._post('/shop/plus.assemble.product/delete', data, errorback);
  },

  /*保存设置*/
  saveSetting(data, errorback) {
    return request._post('/shop/plus.assemble.setting/index', data, errorback);
  },
  /*获取设置*/
  getSetting(data, errorback) {
    return request._get('/shop/plus.assemble.setting/index', data, errorback);
  },

}
export default AssembleApi;
