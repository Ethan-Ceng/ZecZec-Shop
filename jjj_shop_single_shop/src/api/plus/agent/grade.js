import request from '@/utils/request'

let GradeApi = {
    /*等级列表*/
    gradelist(data, errorback) {
        return request._post('/shop/plus.agent.grade/index', data, errorback);
    },
    toAddgrade(data, errorback) {
        return request._get('/shop/plus.agent.grade/add', data, errorback);
    },
    /*添加等级*/
    addgrade(data, errorback) {
        return request._post('/shop/plus.agent.grade/add', data, errorback);
    },
    toEditgrade(data, errorback) {
        return request._get('/shop/plus.agent.grade/edit', data, errorback);
    },
    /*修改等级*/
    editGrade(data, errorback) {
        return request._post('/shop/plus.agent.grade/edit', data, errorback);
    },
    /*删除等级*/
    deletegrade(data, errorback) {
        return request._post('/shop/plus.agent.grade/delete', data, errorback);
    },
    /*等级日志*/
    loglist(data, errorback) {
        return request._post('/shop/plus.agent.grade/log', data, errorback);
    },
}

export default GradeApi;
