import request from '@/utils/request'

let AgentProductApi = {
    /*等级列表*/
    toEditProduct(data, errorback) {
        return request._get('/shop/plus.agent.product/edit', data, errorback);
    },
    editProduct(data, errorback) {
        return request._post('/shop/plus.agent.product/edit', data, errorback);
    },
    getList(data, errorback) {
        return request._get('/shop/plus.agent.product/index', data, errorback);
    },
    setAgent(data, errorback) {
        return request._post('/shop/plus.agent.product/setAgent', data, errorback);
    },
}

export default AgentProductApi;
