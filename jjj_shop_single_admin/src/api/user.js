import request from '@/utils/request'

let UserApi = {
	/*用户登录*/
	login(data, errorback) {
		return request._post('/admin/passport/login', data, errorback);
	},
	/*修改密码*/
	editPassword(data, errorback) {
		return request._post('/admin/admin.user/renew', data, errorback);
	},
	/*退出登录*/
	loginOut(data, errorback) {
		return request._post('/admin/passport/logout', data, errorback);
	},
	/*获取版本*/
	getVersion(data, errorback) {
		return request._post('/admin/index/index', data, errorback);
	},
	/*基础配置*/
	base(data, errorback) {
		return request._post('/admin/index/base', data, errorback);
	},
}

export default UserApi;