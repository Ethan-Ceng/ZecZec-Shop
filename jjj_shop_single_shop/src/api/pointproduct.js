import request from '@/utils/request'

let PointProductApi = {
    /*商品列表*/
    productList(data, errorback) {
        return request._post('/shop/plus.points.product/index', data, errorback);
    },
    /*添加商品*/
    addProduct(data, errorback) {
        return request._post('/shop/plus.points.product/add', data, errorback);
    },
    /*获取详情*/
    getProduct(data, errorback) {
        return request._get('/shop/plus.points.product/add', data, errorback);
    },
    /*获取积分商品*/
    getPointProduct(data, errorback) {
        return request._get('/shop/plus.points.product/edit', data, errorback);
    },
    /*编辑积分商品*/
    editPointProduct(data, errorback) {
        return request._post('/shop/plus.points.product/edit', data, errorback);
    },
    /*删除商品*/
    deleteProduct(data, errorback) {
        return request._post('/shop/plus.points.product/delete', data, errorback);
    },
    /*保存配置*/
    saveConfig(data, errorback) {
        return request._post('/shop/plus.points.product/settings', data, errorback);
    },
    /*保存配置*/
    getConfig(data, errorback) {
        return request._get('/shop/plus.points.product/settings', data, errorback);
    },
    /*获取兑换记录*/
    getExchange(data, errorback) {
        return request._post('/shop/plus.points.product/record', data, errorback);
    },

}
export default PointProductApi;
