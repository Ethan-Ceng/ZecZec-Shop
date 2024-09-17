import request from '@/utils/request'

let TableApi = {
    /*列表*/
    getList(data, errorback) {
        return request._post('/shop/plus.table.table/index', data, errorback);
    },
    /*添加*/
    add(data, errorback) {
        return request._post('/shop/plus.table.table/add', data, errorback);
    },
    /*修改*/
    toEdit(data, errorback) {
        return request._get('/shop/plus.table.table/edit', data, errorback);
    },
    /*修改*/
    edit(data, errorback) {
        return request._post('/shop/plus.table.table/edit', data, errorback);
    },
    /*删除*/
    del(data, errorback) {
        return request._post('/shop/plus.table.table/delete', data, errorback);
    },
    /*列表*/
    getRecordList(data, errorback) {
        return request._post('/shop/plus.table.record/index', data, errorback);
    },
    delRecord(data, errorback) {
        return request._post('/shop/plus.table.record/delete', data, errorback);
    },
}

export default TableApi;
