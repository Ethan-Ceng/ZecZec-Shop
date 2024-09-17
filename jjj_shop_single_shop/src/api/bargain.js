import request from '@/utils/request'

let Bargain = {

    /*砍价活动列表*/
    bargainList(data, errorback) {
        return request._post('/shop/plus.bargain.active/index', data, errorback);
    },
    /*删除*/
    deleteBargain(data, errorback) {
        return request._post('/shop/plus.bargain.active/delete', data, errorback);
    },
    /*添加砍价活动*/
    addActive(data, errorback) {
      return request._post('/shop/plus.bargain.active/add', data, errorback);
    },
    /*获取砍价活动数据*/
    editActive(data, errorback) {
      return request._get('/shop/plus.bargain.active/edit', data, errorback);
    },
    /*保存砍价活动*/
    saveActive(data, errorback) {
      return request._post('/shop/plus.bargain.active/edit', data, errorback);
    },
    /*获得商品详情*/
    detail(data, errorback) {
        return request._post('/shop/plus.bargain.bargain/detail', data, errorback);
    },
    /*保存*/
    saveData(data, errorback) {
        return request._post('/shop/plus.bargain.bargain/saveData', data, errorback);
    },
    /*保存*/
    editSetting(data, errorback) {
        return request._post('/shop/plus.bargain.setting/index', data, errorback);
    },
    /*获取*/
    settingDetail(data, errorback) {
        return request._get('/shop/plus.bargain.setting/index', data, errorback);
    },
    /*记录*/
    taskList(data, errorback) {
        return request._get('/shop/plus.bargain.task/index', data, errorback);
    },
    /*记录详情*/
    help(data, errorback) {
        return request._get('/shop/plus.bargain.task/help', data, errorback);
    },
}

export default Bargain;
