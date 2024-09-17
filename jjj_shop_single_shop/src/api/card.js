import request from '@/utils/request'

let CardApi = {
  /*文章列表*/
  cardlist(data, errorback) {
    return request._post('/shop/plus.card.card/index', data, errorback);
  },
  /*获取文章分类*/
  getCategory(data, errorback) {
    return request._post('/shop/plus.card.category/index', data, errorback);
  },
  /*添加文章*/
  toAddCard(data, errorback) {
    return request._get('/shop/plus.card.card/add', data, errorback);
  },
  /*添加文章*/
  addCard(data, errorback) {
    return request._post('/shop/plus.card.card/add', data, errorback);
  },
  /*文章详情*/
  toEditCard(data, errorback) {
    return request._get('/shop/plus.card.card/edit', data, errorback);
  },
  /*编辑文章*/
  editCard(data, errorback) {
    return request._post('/shop/plus.card.card/edit', data, errorback);
  },
  /*文章详情*/
  toCodeCard(data, errorback) {
    return request._get('/shop/plus.card.card/code', data, errorback);
  },
  /*编辑文章*/
  codeCard(data, errorback) {
    return request._post('/shop/plus.card.card/code', data, errorback);
  },
  /*删除文章*/
  deleteCard(data, errorback) {
    return request._post('/shop/plus.card.card/delete', data, errorback);
  },
  /*添加分类*/
  addCategiry(data, errorback) {
    return request._post('/shop/plus.card.category/add', data, errorback);
  },
  /*编辑分类*/
  editCategory(data, errorback) {
    return request._post('/shop/plus.card.category/edit', data, errorback);
  },
  /*删除分类*/
  deleteCategory(data, errorback) {
    return request._post('/shop/plus.card.category/delete', data, errorback);
  },
  codelist(data, errorback) {
    return request._post('/shop/plus.card.code/index', data, errorback);
  },
  /*文章详情*/
  toEditCode(data, errorback) {
    return request._get('/shop/plus.card.code/edit', data, errorback);
  },
  /*编辑文章*/
  editCode(data, errorback) {
    return request._post('/shop/plus.card.code/edit', data, errorback);
  },
  /*文章详情*/
  orderlist(data, errorback) {
    return request._get('/shop/plus.card.order/index', data, errorback);
  },
  /*编辑文章*/
  orderdetail(data, errorback) {
    return request._post('/shop/plus.card.order/detail', data, errorback);
  },
  delivery(data, errorback) {
    return request._post('/shop/plus.card.order/delivery', data, errorback);
  },
  getSetting(data, errorback) {
    return request._get('/shop/plus.card.setting/index', data, errorback);
  },
  editSetting(data, errorback) {
    return request._post('/shop/plus.card.setting/index', data, errorback);
  },
}
export default CardApi;
