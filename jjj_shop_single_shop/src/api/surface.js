import request from '@/utils/request'

let SurfaceApi = {
  /*电子面单配置列表*/
  labelList(data, errorback) {
    return request._post('/shop/plus.surface.setting/index', data, errorback);
  },
  /*添加电子面单配置*/
  addlabel(data, errorback) {
    return request._post('/shop/plus.surface.setting/add', data, errorback);
  },
  /*电子面单配置详情*/
  labelDetail(data, errorback) {
    return request._get('/shop/plus.surface.setting/edit',data, errorback);
  },
  /*修改电子面单配置*/
  editlabel(data, errorback) {
    return request._post('/shop/plus.surface.setting/edit', data, errorback);
  },
  /*删除电子面单配置*/
  deletelabel(data, errorback) {
    return request._post('/shop/plus.surface.setting/delete', data, errorback);
  },
  /*添加电子面单配置*/
  toAddlabel(data, errorback) {
    return request._get('/shop/plus.surface.setting/add', data, errorback);
  },
  
  /*电子面单模板列表*/
  templateList(data, errorback) {
    return request._post('/shop/plus.surface.template/index', data, errorback);
  },
  /*添加电子面单模板*/
  addtemplate(data, errorback) {
    return request._post('/shop/plus.surface.template/add', data, errorback);
  },
  /*电子面单模板详情*/
  templateDetail(data, errorback) {
    return request._get('/shop/plus.surface.template/edit',data, errorback);
  },
  /*修改电子面单模板*/
  edittemplate(data, errorback) {
    return request._post('/shop/plus.surface.template/edit', data, errorback);
  },
  /*删除电子面单模板*/
  deletetemplate(data, errorback) {
    return request._post('/shop/plus.surface.template/delete', data, errorback);
  },
}
export default SurfaceApi;
